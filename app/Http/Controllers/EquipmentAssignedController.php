<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreEquipmentAssigned;
use App\Http\Controllers\UserController;
use App\Models\work_orders;
use App\Models\construction_equipment;
use App\Models\equipment_assigned;
use App\Notifications\orderEquipment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EquipmentAssignedController extends Controller
{
    //Asignar los equipos de construcción a la orden de trabajo
    public static function EquipmentAssign($equipments, $workOrderId) {
        
        try {
            
            $subject = 'Pedido de equipo';
            $message = 'Se solicito un equipo para un trabajo, revise los pedidos';
            $currentDate = Carbon::now()->toDateString(); //Fecha actual
            
            $idStatus = 4; // El material no está disponible
            $workOrder = work_orders::findOrFail($workOrderId); // Orden de trabajo
            $administrators = UserController::searchAdmin();  

            // Asignar equipos a la orden de trabajo
            foreach ($equipments as $equipmentId) {
                $equipment = construction_equipment::findOrFail($equipmentId);

                //NOTIFICAR EQUIPO-------------------
                foreach ($administrators as $admin) {

                    $admin->notify(new orderEquipment(
                        $equipmentId,
                        $admin->id_user,
                        $subject,
                        $equipment->name_equipment,
                        $message,
                        $currentDate
                    ));

                    $notificationId = $admin->notifications()->latest()->first()->id; //Ultimo ID
                    $eventData = [
                        'id' => $notificationId,
                        'id_construction_equipment' => $equipmentId,
                        'to_user' => $admin->id_user,
                        'subject' => $subject,
                        'title' =>  $equipment->name_equipment,
                        'description' => $message,
                        'date' => $currentDate
                    ];

                    event(new \App\Events\EquipmentAssigned($eventData, $admin->id_user)); //Emitir el evento

                    
                }

                $workOrder->equipmentAssigned()->attach($equipmentId, ['created_at' => now(), 'updated_at' => now()]);
                //Cambia el estado del equipo asociado
                ConstructionEquipmentController::updateStatusEquipment($equipmentId, $idStatus);
            }
    
            return $equipments;
    
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function createEquipmentAssigned(Request $request) {
        try {

            $workOrderId = $request->input('id_work_order');
            $equipments = $request->input('id_equipments');

            $subject = 'Pedido de equipo';
            $message = 'Se solicito un equipo para un trabajo, revise los pedidos';
            $currentDate = Carbon::now()->toDateString(); //Fecha actual

            $idStatus = 4; //El equipo está pedido
            $workOrder = work_orders::findOrFail($workOrderId); // Orden de trabajo
            $administrators = UserController::searchAdmin();  

            foreach($equipments as $equipmentId) {
                $equipment = construction_equipment::findOrFail($equipmentId);

                //Notificar equipo
                foreach($administrators as $admin) {
                    $admin->notify(new orderEquipment(
                        $equipmentId,
                        $admin->id_user,
                        $subject,
                        $equipment->name_equipment,
                        $message,
                        $currentDate
                    ));

                    $notificationId = $admin->notifications()->latest()->first()->id; //Ultimo ID
                    $eventData = [
                        'id' => $notificationId,
                        'id_construction_equipment' => $equipmentId,
                        'to_user' => $admin->id_user,
                        'subject' => $subject,
                        'title' =>  $equipment->name_equipment,
                        'description' => $message,
                        'date' => $currentDate
                    ];

                    event(new \App\Events\EquipmentAssigned($eventData, $admin->id_user)); //Emitir el evento
                }

                $workOrder->equipmentAssigned()->attach($equipmentId, ['created_at' => now(), 'updated_at' => now()]);
                
                //Cambia el estado del equipo asociado
                ConstructionEquipmentController::updateStatusEquipment($equipmentId, $idStatus);
            }

            $assignedEquipments = $workOrder->equipmentAssigned()->withPivot(
                'id_work_order',
                'id_construction_equipment',
                'id_equipment_assigned',
                'is_delivered'
            )->get();

            return response()->json([
                'data' => $assignedEquipments,
                'msg' => 'Se realizó el pedido de equipo correctamente'
            ],201);

        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage(),
                'msg' => 'No se pudo realizar la solicitud del equipo, inténtelo de nuevo'
            ], 500);
        }
    }

    public function equipmentAssignedDelete ($idEquipmentAssigned) {
        try {
            \DB::beginTransaction();
      
            $subject = 'Cancelación de pedido de equipo';
            $message = 'Se cancelo la solicitud del pedido de un equipo';
            $currentDate = Carbon::now()->toDateString(); //Fecha actual

            $idStatus = 3; //El equipo está disponible
            $administrators = UserController::searchAdmin();  

            $equipmentAssigned = equipment_assigned::findOrFail($idEquipmentAssigned);
            $equipment = construction_equipment::findOrFail($equipmentAssigned->id_construction_equipment);

            
            if($equipmentAssigned->is_delivered == false){
                $equipmentAssigned->delete();
                ConstructionEquipmentController::updateStatusEquipment($equipment->id_construction_equipment, $idStatus);
            } else {
              return response()->json([
                'msg' => 'No se puede eliminar la solicitud del equipo por que ya fue marcado como entregado'
              ], 403);
            }

            //Notificar que ya no se requiere el equipo
             foreach($administrators as $admin) {
                $admin->notify(new orderEquipment(
                    $equipment->id_construction_equipment,
                    $admin->id_user,
                    $subject,
                    $equipment->name_equipment,
                    $message,
                    $currentDate
                ));

                $notificationId = $admin->notifications()->latest()->first()->id; //Ultimo ID
                $eventData = [
                    'id' => $notificationId,
                    'id_construction_equipment' => $equipment->id_construction_equipment,
                    'to_user' => $admin->id_user,
                    'subject' => $subject,
                    'title' =>  $equipment->name_equipment,
                    'description' => $message,
                    'date' => $currentDate
                ];

                event(new \App\Events\EquipmentAssigned($eventData, $admin->id_user)); //Emitir el evento
            }
        
            \DB::commit();
            return response()->json([
                'msg' => 'Se elimino el pédido de equipo', 'data' => $equipment
            ]);

        } catch (\Throwable $th) {

            \DB::rollBack();
            return response()->json([
                'msg' => 'No se pudo eliminar la solicitud de pedido de material',
                'error' => $th->getMessage()
            ]);
        }
    }

    //Trae a los equipos pedidos
    public function undeliveredOrdersEquipment() {
        try {
            $undeliveredOrders = DB::table('equipment_assigneds')
                ->join('construction_equipments', 'equipment_assigneds.id_construction_equipment' ,'=','construction_equipments.id_construction_equipment')
                ->join('work_orders', 'work_orders.id_work_order', '=', 'equipment_assigneds.id_work_order')
                ->join('jobs', 'jobs.id_job', '=', 'work_orders.id_job')
                ->join('establishments', 'jobs.id_establishment', '=', 'establishments.id_establishment')
                ->where('equipment_assigneds.is_delivered', false)
                ->select(
                    'equipment_assigneds.id_equipment_assigned',
                    'equipment_assigneds.created_at as date_order',
                    'construction_equipments.name_equipment',
                    'construction_equipments.description as equipment_description',
                    'construction_equipments.image',
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
            
            return response()->json($undeliveredOrders);

        } catch (\Throwable $th) {
            return response()->json(
                ['msg' => 'Algo salio mal, no se pudo obtener los datos de pedidos de materiales',
                'error' => $th->getMessage()], 505);
        }
    }

    //Equipos entregados
    public function deliveredOrdersEquipment(Request $request) {
        try {

            $perPage = intval($request->input('per_page'));
            $currentPage = intval($request->input('page'));

            $undeliveredOrders = DB::table('equipment_assigneds')
                ->join('construction_equipments', 'equipment_assigneds.id_construction_equipment' ,'=','construction_equipments.id_construction_equipment')
                ->join('work_orders', 'work_orders.id_work_order', '=', 'equipment_assigneds.id_work_order')
                ->join('jobs', 'jobs.id_job', '=', 'work_orders.id_job')
                ->join('establishments', 'jobs.id_establishment', '=', 'establishments.id_establishment')
                ->where('equipment_assigneds.is_delivered', true)
                ->select(
                    'equipment_assigneds.id_equipment_assigned',
                    'equipment_assigneds.created_at as date_order',
                    'construction_equipments.name_equipment',
                    'construction_equipments.description as equipment_description',
                    'construction_equipments.image',
                    'work_orders.instructions',
                    'work_orders.assigned_date',
                    'work_orders.end_date',
                    'jobs.name_job',
                    'jobs.num_caf',
                    'establishments.name_establishment',
                    'establishments.description as establishments_description',
                    'establishments.location'

                )
                ->orderBy('equipment_assigneds.created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $currentPage);
            
            return response()->json($undeliveredOrders);

        } catch (\Throwable $th) {
            return response()->json(
                ['msg' => 'Algo salio mal, no se pudo obtener los datos de pedidos de los equipos',
                'error' => $th->getMessage()], 505);
        }
    }

    //Obtiene el equipment_assigneds, segun el id
    public function getEquipmentAssignedbyId($id){
        try {

            $undeliveredOrders = DB::table('equipment_assigneds')
            ->join('construction_equipments', 'equipment_assigneds.id_construction_equipment' ,'=','construction_equipments.id_construction_equipment')
            ->join('work_orders', 'work_orders.id_work_order', '=', 'equipment_assigneds.id_work_order')
            ->join('jobs', 'jobs.id_job', '=', 'work_orders.id_job')
            ->join('establishments', 'jobs.id_establishment', '=', 'establishments.id_establishment')
            ->where('equipment_assigneds.id_equipment_assigned', $id)
            ->select(
                'equipment_assigneds.id_equipment_assigned',
                'equipment_assigneds.created_at as date_order',
                'construction_equipments.name_equipment',
                'construction_equipments.description as equipment_description',
                'construction_equipments.image',
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

            if(!$undeliveredOrders){
                return response(['msg' => 'No se encontró el pedido del equipo'], 404);
            }
        
            return response()->json($undeliveredOrders);
            
        } catch (\Throwable $th) {
            return response()->json(
                ['msg' => 'No se encontró el pedido del equipo buscado',
                'error' => $th->getMessage()], 505);
        }
    }

    //ACEPTA LOS PEDIDOS DE EQUIPO
    public function acceptOrder($id) {
        try {
            $equipmentAssigned = equipment_assigned::find($id);
            if(!$equipmentAssigned){
                return response()->json(['msg' => 'No se encontro el equipo '], 404);
            }

            //modificar el equipo
            $equipmentAssigned->is_delivered = true;
            $equipmentAssigned->save();

            return response(['data' => $equipmentAssigned, 
                'msg' => 'Se estableció el equipo como entregado'], 201);

        } catch (\Throwable $th) {
            return response()->json(
                ['msg' => 'No se pudo regitsrar la entrega, intentelo más tarde',
                'error' => $th->getMessage()], 505);
        }
    }

    //Obtiene los equipos para hacer el desalojo
    public function getEquipmentEviction() {
        try {
            
            $idWorkOrderFinish = 3;

            $results = DB::table('equipment_assigneds as ea')
                ->join('construction_equipments as ce', 'ea.id_construction_equipment', '=', 'ce.id_construction_equipment')
                ->join('work_orders as wo', 'ea.id_work_order', '=', 'wo.id_work_order')
                ->join('jobs', 'wo.id_job', '=', 'jobs.id_job')
                ->join('establishments', 'jobs.id_establishment', '=', 'establishments.id_establishment')
                ->select(
                    'ea.id_equipment_assigned',
                    'ea.eviction_completed',
                    'ce.name_equipment',
                    'ce.description as equipment_description',
                    'ce.image as equipment_image',
                    'jobs.name_job',
                    'jobs.description as job_description',
                    'jobs.num_caf',
                    'establishments.name_establishment',
                    'establishments.description as establishment_description',
                    'establishments.location'
                )
                ->where('ea.eviction_completed', false)
                ->where('ea.is_delivered', true)
                ->where('wo.id_order_statuses', '=', $idWorkOrderFinish)
                ->get();
            
            return response()->json($results);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 
            'msg' => 'No se pudo obtener los datos de los equipos para hacer el desalojo, recargue la página']
            , 505);
        }
    }

    //Marca como desalojado a un equipo por el id
    public function completedEvictionEquipment($idEquipmentAssigned) {
        try {
            $equipmentAssigned = equipment_assigned::findOrFail($idEquipmentAssigned);
            $equipmentAssigned->eviction_completed = true;
            $equipmentAssigned->save();

            $idStatus = 3; //Estado del equipo para que este disponible

            //Cambio al estado disponible
            $equipment = construction_equipment::with('status_equipment')
                ->with('type_equipment')->findOrFail($equipmentAssigned->id_construction_equipment);
            $equipment->id_status_equipment = $idStatus;
            $equipment->save();

            // Volver a cargar las relaciones después de guardar
            $equipment->load('status_equipment', 'type_equipment');

            $response = [
                "id_construction_equipment" => $equipment->id_construction_equipment,
                "name_equipment" => $equipment->name_equipment,
                "description" => $equipment->description,
                "unit_value" => $equipment->unit_value,
                "image" => $equipment->image,
                "name_status_equipment" => $equipment->status_equipment->name_status_equipment,
                "color" => $equipment->status_equipment->color,
                "name_type_equipment" => $equipment->type_equipment->name_type_equipment, 
            ];

            return response()->json([
                'msg' => 'El equipo está disponible', 
                'data' => $response
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage(), 
                'msg' => 'No se pudo confirmar el desalojo'
            ]);
        }
    }

    
    
}
