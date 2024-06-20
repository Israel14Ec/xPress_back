<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\construction_equipment;
use App\Http\Requests\StoreEquipment;
use App\Http\Controllers\UserController;
use App\Notifications\equipmentUnavailable;
use Carbon\Carbon;

class ConstructionEquipmentController extends Controller
{
    //Crea en la DB
    public function createEquipment (StoreEquipment $request) {
        try {
            $equipment = construction_equipment::create($request->all());
            if($equipment){
                return response()->json (['msg' => 'Se guardo correctamente el material', 'data' => $equipment], 201); 
            }

            return response()->json(['msg' => 'No se pudo lograr, intentelo de nuevo'], 404);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'Algo salio mal no se pudo crear'], 500);
        }
    }

    //Obtiene los equipos en funcion del status y equipment
    public function getEquipmentByStatusType($id_status_equipment = null, $id_type_equipment = null) { 
        try {
            $query = construction_equipment::query();  
    
            if($id_status_equipment) {
                $query->where('construction_equipments.id_status_equipment', '=', $id_status_equipment);
            }
    
            if ($id_type_equipment) {
                $query->where('construction_equipments.id_type_equipment', '=', $id_type_equipment);
            }
    
            $equipment = $query
                ->select(
                    'construction_equipments.id_construction_equipment',
                    'construction_equipments.name_equipment',
                    'construction_equipments.description',
                    'construction_equipments.unit_value',
                    'construction_equipments.image',
                    'status_equipments.name_status_equipment',
                    'status_equipments.color',
                    'type_equipments.name_type_equipment'
                )
                ->join('status_equipments', 'construction_equipments.id_status_equipment', '=', 'status_equipments.id_status_equipment')
                ->join('type_equipments', 'construction_equipments.id_type_equipment', '=', 'type_equipments.id_type_equipment')
                ->get();
    
            return response()->json($equipment, 200);
    
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 
            'msg' => 'Algo salio mal no se pudo obtener los datos, intentelo de nuevo'], 500);
        }
    }

    //Obtiene los equipos en funcion del id
    public function getEquipmentById($id) {
        try {
            $equipment = construction_equipment::find($id);
            if(!$equipment){
                return response()->json (['msg' => 'No se encontraron los registros'], 404);
            }
            return response()->json(['msg' => $equipment], 200);
            
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'Algo salio mal, intentelo de nuevo'], 500);
        }
    }


    //Cambia el estado de equipment
    public static function updateStatusEquipment($idEquipment, $idStatus) {
        
        $equipment = construction_equipment::findOrFail($idEquipment);
        $equipment->id_status_equipment = $idStatus;
        $equipment->save();

    }

    //Actualiza un registro
    public function updateEquipment($id, StoreEquipment $request) {
        try {
            $equipment = construction_equipment::find($id);

            if(!$equipment) {
                return response()->json(['msg' => 'No se encontró el equipo para guardar los cambios'], 404);
            }
            $equipment->update($request->all());
            return response()->json(['msg' => 'Se edito el equipo correctamente', 'data' => $equipment], 201);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'No se pudo editar'], 500);
        }
    }

    //Elimina un material
    public function deleteEquipment ($id) {
        try {
            $equipment = construction_equipment::find($id);
            if(!$equipment){
                return response()->json(['msg' => 'No se encontro resultados'], 404);
            }
            $equipment->delete();
            return response()->json(['msg' => 'Material eliminado exitosamente', 'data' => $equipment]);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'No se puede eliminar'], 500);
        }
    }

    //Manda notificación de un equipo que se necesita para un trabajo, pero no existe o no esta disponible
    public function notiEquipmentUnavailable (Request $request) {
        try {

            $idJob = $request->input('id_job');
            $from = $request->input('from');
            $idUserFrom = $request->input('id_user_from');
            $subject = $request->input('subject');
            $title = $request->input('title');
            $description = $request->input('description');
            $currentDate = Carbon::now()->toDateString(); //Fecha actual
            $priority = true;

            $administrators = UserController::searchAdmin();
            
            foreach($administrators as $admin) {
                $admin->notify(new equipmentUnavailable(
                    $idJob,
                    $admin->id_user,
                    $from,
                    $idUserFrom,
                    $subject,
                    $title,
                    $description,
                    $currentDate,
                    $priority
                ));
                
                $notificationId = $admin->notifications()->latest()->first()->id; //Ultimo ID

                $eventData = [
                    'id' => $notificationId,
                    'id_job' => $idJob,
                    'to_user' =>  $admin->id_user, 
                    'from' =>  $from,
                    'id_user_from' => $idUserFrom, 
                    'subject' => $subject,
                    'title' => $title,
                    'description' => $description, //message
                    'date' => $currentDate,
                    'priority' => $priority
                ];
                event(new \App\Events\EquipmentUnavailable($eventData, $admin->id_user)); //Emitir el evento
            }

            return response()->json(['msg' => 'Se envío la notificación del equipo'], 201);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 
            'msg' => 'No se pudo enviar la notificación para el equipo']);
        }
    }
    
}
