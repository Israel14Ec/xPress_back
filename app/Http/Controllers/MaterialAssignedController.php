<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\UserController;
use App\Models\work_orders;
use App\Models\material;
use App\Models\material_assigned;
use App\Http\Requests\StoreMaterialAssigned;
use App\Http\Requests\StoreMaterialAssignedEdit;
use App\Notifications\reportMaterial;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MaterialAssignedController extends Controller
{
    
    //Función estática para asignar material
    public static function AssignMaterial($materials, $workOrderId) {
        try {
            
            $message = ''; //Mensaje que se notificara
            $subject = 'Pedido de material';
            $currentDate = Carbon::now()->toDateString(); //Fecha actual

            $workOrder = work_orders::findOrFail($workOrderId); // Orden de trabajox
    
            // Preparar los datos para attach
            $attachData = [];
    
            foreach ($materials as $materialData) {

                $materialId = $materialData['id_material'];
                $amount = $materialData['amount'];
                $material = material::findOrFail($materialId); 

                //NOTIFICAR MATERIAL ------------
                $administrators = UserController::searchAdmin();  
                foreach ($administrators as $admin) {
       
                    $admin->notify(new reportMaterial(
                        $materialId,
                        $admin->id_user,
                        $subject, //subject
                        $material->name_material,
                        'Se solicito: '.$amount. ' unidades del material', //message
                        $currentDate
                        
                    ));
                    
                    $notificationId = $admin->notifications()->latest()->first()->id; //Ultimo ID
                    $eventData = [
                        'id' => $notificationId,
                        'id_material' => $materialId, 
                        'to_user' => $admin->id_user,
                        'subject' => $subject,
                        'title' => $material->name_material,
                        'description' => 'Se solicito: '.$amount. ' unidades del material', //message
                        'date' => $currentDate
                    ];
                    event(new \App\Events\MaterialAssigned($eventData, $admin->id_user)); //Emitir el evento

                }

                //Asociar con la tabla: material_assigneds -------------
                $materialId = $materialData['id_material'];
                $amount = $materialData['amount'];
                // Agregar datos para la asignación
                $attachData[$materialId] = ['amount' => $amount, 'created_at' => now(), 'updated_at' => now()];
            }       
    
            // Asignar materiales con cantidad a la orden de trabajo
            $workOrder->materialAssigned()->attach($attachData);
    
            return $attachData;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    //Crear un pedido de material para una ordén de trabajo
    public function createAssignedMaterial(Request $request) {
        try {

            \DB::beginTransaction();
            $materials = $request->input('materials');
            $idWorkOrder = $request->input('id_work_order');
            $currentDate = Carbon::now()->toDateString(); // Fecha actual
    
            $workOrder = work_orders::findOrFail($idWorkOrder);
            $subject = 'Pedido de material';
            $attachData = [];
            $administrators = UserController::searchAdmin();

            foreach ($materials as $materialData) {

                $materialId = $materialData['id_material'];
                $amount = $materialData['amount'];
                $material = material::findOrFail($materialId);
    
                // Notificar a los administradores
                
                foreach ($administrators as $admin) {
                    $admin->notify(new ReportMaterial(
                        $materialId,
                        $admin->id_user,
                        $subject,
                        $material->name_material,
                        'Se solicitó: ' . $amount . ' unidades del material',
                        $currentDate
                    ));
    
                    $notificationId = $admin->notifications()->latest()->first()->id;
                    $eventData = [
                        'id' => $notificationId,
                        'id_material' => $materialId, 
                        'to_user' => $admin->id_user,
                        'subject' => $subject,
                        'title' => $material->name_material,
                        'description' => 'Se solicitó: ' . $amount . ' unidades del material', //message
                        'date' => $currentDate
                    ];
                    event(new \App\Events\MaterialAssigned($eventData, $admin->id_user)); // Emitir el evento
                }
            }

            // Preparar datos para la asignación
            $attachData[$materialId] = ['amount' => $amount, 'created_at' => now(), 'updated_at' => now()];
       
            // Asignar materiales con cantidad a la orden de trabajo
            $workOrder->materialAssigned()->attach($attachData);

            // Cargar solo los materiales asignados recientemente
            $assignedMaterials = $workOrder->materialAssigned()->withPivot(
                'id_material_assigned' ,'amount', 'delivered_amount', 'is_delivered')
                ->get();
    
            \DB::commit();
            return response()->json([
                'data' => $assignedMaterials,
                'msg' => 'Se solicito correctamente más material'
            ], 201);
    
        } catch (\Throwable $th) {
            \DB::rollBack();
            return response()->json([
                'msg' => 'No se pudo solicitar el pedido de material',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    

    //Editar asignación del material cuando no fue entregado
    public function AssignMaterialEdit (StoreMaterialAssignedEdit $request) {

        try {
                    
            $idMaterialAssigned = $request->input('id_material_assigned');
            $newAmount = $request->input('amount');    
            
            $assignedMaterial = material_assigned::findOrFail($idMaterialAssigned);
            $material = material::findOrFail($assignedMaterial->id_material);

            //No fue entregado
            if($assignedMaterial->is_delivered == false) {
                if( $newAmount <= $material->stock) {
                    //Actualiza
                    $assignedMaterial->amount = $newAmount;
                    $assignedMaterial->save();

                } else {
                    return response()->json([
                        'msg' => 
                        'No hay suficiente stock en el inventario para cubrir la solicitud del material '. $material->name_material
                    ], 422);
                }
    
            } 
            //Ya fue entregado
            else {
                return response()->json([
                    'msg' => 'No se puede editar la cantidad solicitada por que ya fue entregada'
                ], 409);
            }

     
            return response()->json([
                'data' => $assignedMaterial, 'msg' => 'Se realizó la edición del material asignado correctamente'
            ], 200);

        } catch (\Throwable $th) {
            \DB::rollBack();
            return response()->json([
                'msg' => 'No se pudo editar la información, por favor inténtelo de nuevo', 'error' => $th->getMessage()], 500);
        }
    }

    //Eliminar asignación de material
    public function deletedAssignedMaterial ($idMaterialAssigned) {
        try {
            \DB::beginTransaction();
            $materialAssigned = material_assigned::findOrFail($idMaterialAssigned);
            $material = material::findOrFail($materialAssigned->id_material);
            $subject = 'Se cancelo el pedido de material: '.$material->name_material;
            $currentDate = Carbon::now()->toDateString(); //Fecha actual


            if($materialAssigned->is_delivered == true) {
                return response()->json([
                    'msg' => 'No se puede eliminar el pedido de material ya que fue entregado'
                ], 403);
            } else {

                $materialAssigned->delete();
                //Notificar al administrador que se cancelo el pédido de material
                
                $administrators = UserController::searchAdmin();  
                foreach ($administrators as $admin) {
                    $admin->notify(new reportMaterial(
                        $material->id_material,
                        $admin->id_user,
                        $subject, 
                        $material->name_material,
                        'Se cancelo el pedido del material', //message
                        $currentDate
                        
                    ));
                    
                    $notificationId = $admin->notifications()->latest()->first()->id; //Ultimo ID
                    $eventData = [
                        'id' => $notificationId,
                        'id_material' => $material->id_material, 
                        'to_user' => $admin->id_user,
                        'subject' => $subject,
                        'title' => $material->name_material,
                        'description' => 'Se cancelo el pedido del material', //messag //message
                        'date' => $currentDate
                    ];
                    event(new \App\Events\MaterialAssigned($eventData, $admin->id_user)); //Emitir el evento
                }
            }

            \DB::commit();
            return response()->json([
                'data' => $materialAssigned,'msg' => 'Se elimino el pédido de material correctamente'
            ], 202);

        } catch (\Throwable $th) {
            \DB::rollBack();
            return response()->json([
                'error' => $th->getMessage(),
                'msg' => 'No se pudo eliminar el pédido de material'
            ]);
        }
    }

    //Obtiene todos los pedidos de los materiales
    public function undeliveredOrdersMaterial () {
        try {
            $undeliveredOrders = DB::table('material_assigneds')
            ->join('materials', 'material_assigneds.id_material', '=', 'materials.id_material')
            ->join('magnitude_values', 'materials.id_magnitude_value', '=', 'magnitude_values.id_magnitude_value')
            ->join('magnitudes', 'magnitude_values.id_magnitude', '=', 'magnitudes.id_magnitude')
            ->join('work_orders', 'material_assigneds.id_work_order', '=', 'work_orders.id_work_order')
            ->join('jobs', 'work_orders.id_job', '=', 'jobs.id_job')
            ->join('establishments', 'jobs.id_establishment', '=', 'establishments.id_establishment')
            ->where('material_assigneds.is_delivered', false)
            ->select(
                'material_assigneds.id_material_assigned',
                'material_assigneds.amount',
                'material_assigneds.created_at as date_order',
                'materials.name_material',
                'materials.description as material_description',
                DB::raw("CONCAT(magnitude_values.value, ' ', magnitudes.symbol) AS magnitude"),
                'materials.image',
                'work_orders.instructions',
                'work_orders.assigned_date',
                'work_orders.end_date',
                'jobs.name_job',
                'jobs.num_caf',
                'establishments.name_establishment',
                'establishments.description as establishments_description',
                'establishments.location'
            )
            ->get();

            return response()->json($undeliveredOrders ,202);

        } catch (\Throwable $th) {
            return response()->json(['msg' => 'No se pudo obtener los datos de los pedidos de los materiales',
                'error' => $th->getMessage()], 505);
        }
    }

    //Obtiene los pedidos de materiales entregados paginado
    public function deliveredOrdersMaterial(Request $request) {
        try {

            $perPage = intval($request->input('per_page'));
            $currentPage = intval($request->input('page'));
   
            $deliveredOrders = DB::table('material_assigneds')
            ->join('materials', 'material_assigneds.id_material', '=', 'materials.id_material')
            ->join('magnitude_values', 'materials.id_magnitude_value', '=', 'magnitude_values.id_magnitude_value')
            ->join('magnitudes', 'magnitude_values.id_magnitude', '=', 'magnitudes.id_magnitude')
            ->join('work_orders', 'material_assigneds.id_work_order', '=', 'work_orders.id_work_order')
            ->join('jobs', 'work_orders.id_job', '=', 'jobs.id_job')
            ->join('establishments', 'jobs.id_establishment', '=', 'establishments.id_establishment')
            ->where('material_assigneds.is_delivered', true)
            ->select(
                'material_assigneds.id_material_assigned',
                'material_assigneds.amount',
                'material_assigneds.delivered_amount',
                'material_assigneds.created_at as date_order',
                'materials.name_material',
                'materials.description as material_description',
                DB::raw("CONCAT(magnitude_values.value, ' ', magnitudes.symbol) AS magnitude"),
                'materials.image',
                'work_orders.instructions',
                'work_orders.assigned_date',
                'work_orders.end_date',
                'jobs.name_job',
                'jobs.num_caf',
                'establishments.name_establishment',
                'establishments.description as establishments_description',
            )
            ->orderBy('material_assigneds.created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $currentPage);

            return response()->json($deliveredOrders);
            
        } catch (\Throwable $th) {
            return response()->json(['msg' => 'No se pudo obtener los datos de los materiales entregados',
            'error' => $th->getMessage()], 505);
        }
    }

    //Obtiene el material_assigneds, según el id
    public function getMaterialAssignedById($id) {
        try {

            $materialAssigned = DB::table('material_assigneds')
            ->join('materials', 'material_assigneds.id_material', '=', 'materials.id_material')
            ->join('magnitude_values', 'materials.id_magnitude_value', '=', 'magnitude_values.id_magnitude_value')
            ->join('magnitudes', 'magnitude_values.id_magnitude', '=', 'magnitudes.id_magnitude')
            ->join('work_orders', 'material_assigneds.id_work_order', '=', 'work_orders.id_work_order')
            ->join('jobs', 'work_orders.id_job', '=', 'jobs.id_job')
            ->join('establishments', 'jobs.id_establishment', '=', 'establishments.id_establishment')
            ->where('material_assigneds.id_material_assigned', "=", $id)
            ->select(
                'material_assigneds.id_material_assigned',
                'material_assigneds.amount',
                'material_assigneds.created_at as date_order',
                'materials.name_material',
                'materials.description as material_description',
                'materials.stock',
                DB::raw("CONCAT(magnitude_values.value, ' ', magnitudes.symbol) AS magnitude"),
                'materials.image',
                'work_orders.instructions',
                'work_orders.assigned_date',
                'work_orders.end_date',
                'jobs.name_job',
                'jobs.num_caf',
                'establishments.name_establishment',
                'establishments.description as establishments_description',
                'establishments.location'
            )
            ->first();

            if(!$materialAssigned) {
                return response()->json(['msg' => 'No se encontró la ordén del material'], 404);
            }

            return response()->json($materialAssigned ,202);

        } catch (\Throwable $th) {
            return response()->json(['msg' => 'No se encontro el pedido del material',
                'error' => $th->getMessage()], 505);
        }
    }

    //ACEPTA LOS PEDIDOS DE MATERIAL (MODIFICA EL STOCK DE MATERIAL. SU VALOR, CAMBIA EL ESTADO DE amountAccept), 
    public function acceptOrders ($id, Request $request) {
        
        \DB::beginTransaction();
        try {
            $materialAssigned = material_assigned::find($id);
            $amountAccept =  $request->input('delivered_amount');
        
            if(!$materialAssigned) {
                return response()->json(['msg' => 'No se encontro el material '], 404);
            }

            $material = material::findOrFail($materialAssigned->id_material); //Busca material
            
            if($amountAccept <= 0 ){
                return response()->json(['msg' => 'La cantidad entregada no puede ser de menor a 1'], 400);
            }

            if ($material->stock < $amountAccept) {
                return response()->json([
                    'msg' => 'No existe stock suficiente del material, revise el material'], 400);
            }

            //Modificar material
            $material->stock = $material->stock - $amountAccept;
            $material->total_value = $material->unit_value *  $material->stock; // Actualiza el valor total.
            $material->save();

            //modificar material_assigned
            $materialAssigned->is_delivered = true; //Cambia a true
            $materialAssigned->delivered_amount = $amountAccept;
            $materialAssigned->save();

            \DB::commit();
            return response(['msg' => 'Se estableció el material como entregado', 
                'material' => $material, 'material_assigned' => $materialAssigned],
                200
            );

        } catch (\Throwable $th) {
            \DB::rollBack();
            return response()->json(['msg' => 'No se pudo regitsrar la entrega, intentelo más tarde',
                'error' => $th->getMessage()], 505);
        }
    }    
}
