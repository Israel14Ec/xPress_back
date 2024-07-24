<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\material;
use App\Http\Requests\StoreMaterial;
use App\Notifications\materialUnavailable;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MaterialController extends Controller
{

    //Agregar un nuevo material
    public function createMaterial(StoreMaterial $request){

        try {
            $data = Material::create($request->all());
            if($data){
                // Cargar la relación magnitude_value y su relación magnitude
                $data->load(['magnitude_value' => function ($query) {
                    $query->select('id_magnitude_value', 'value', 'id_magnitude')->with(['magnitude' => function ($query) {
                        $query->select('id_magnitude', 'symbol');
                    }]);
                }]);
    
                // Extraer los valores necesarios
                $magnitudeValue = $data->magnitude_value->value;
                $symbol = $data->magnitude_value->magnitude->symbol;
    
                return response()->json(['msg' => 'Se guardó correctamente el material', 'data' => [
                    'id_material' => $data->id_material,
                    'name_material' => $data->name_material,
                    'description' => $data->description,
                    'image' => $data->image,
                    'unit_value' => $data->unit_value,
                    'stock' => $data->stock,
                    'total_value' => $data->total_value,
                    'id_magnitude_value' => $data->id_magnitude_value,
                    'value' => $magnitudeValue,
                    'symbol' => $symbol,
                ]], 201);
            }
    
            return response()->json(['msg' => 'No se pudo lograr, intente de nuevo'], 404);
    
        } catch (\Throwable $th) {
            return response()->json(['msg' => $th->getMessage()], 500);
        }
    }
        

    //Obtiene todos los materiales y su respectiva magnitud
    public function getMaterial (Request $request){
        try {
            $data = DB::table('materials')
                ->join('magnitude_values', 'materials.id_magnitude_value', '=', 'magnitude_values.id_magnitude_value')
                ->join('magnitudes', 'magnitude_values.id_magnitude', '=', 'magnitudes.id_magnitude')
                ->select(
                    'materials.id_material', 
                    'materials.name_material', 
                    'materials.description', 
                    'materials.image', 
                    'materials.unit_value', 
                    'materials.stock', 
                    'materials.total_value',
                    "magnitude_values.value",
                    "magnitudes.name",
                    "magnitudes.symbol"
                )
                ->orderBy('name_material', 'asc')
                ->get();
            return response()->json($data, 200);
        } catch (\Throwable $th) {
            return response()->json(['msg' => $th->getMessage()], 500);
        }
    }

    //Obtiene materiales que su stock sea mayor a 0
    public function getMaterialStock(Request $request){
        try {
            $data = Material::select('id_material', 'name_material', 'description', 'image', 'unit_value', 'stock', 'total_value')
                            ->where('stock', '>', 0) 
                            ->get();
            return response()->json($data, 200);
        } catch (\Throwable $th) {
            return response()->json(['msg' => $th->getMessage()], 500);
        }
    }
    

    //Busca un material
    public function getMaterialById($id){
        
        try {
            $data = Material::find($id);

            if(!$data) {
                return response()->json (['msg' => 'No se encontraron los registros'], 404);
            }
            return response()->json(['msg' => $data], 200);
            
        } catch (\Throwable $th) {
            return response()->json(['msg' => $th->getMessage()], 500);
        }
    }

    //Editar un material
    public function updateMaterial ($id, Request $request) {

        try {
            $data = Material::find($id);
            if (!$data) {
                return response()->json(['msg' => 'No se encontró el material para guardar los cambios'], 404);
            }
            $data->update($request->all());
            return response()->json(['msg' => 'Se edito el material correctamente', 'data' => $data], 201);
            
        } catch (\Throwable $th) {
            return response()->json(['msg' => $th->getMessage()], 500);
        }
    }

    //Elimina un material
    public function deleteMaterial($id) {
        try {
            $material = Material::find($id);
    
            if (!$material) {
                return response()->json(['msg' => 'El Material no existe'], 404);
            }
    
            $material->delete();
            return response()->json(['msg' => 'Material eliminado exitosamente', 'data' => $material]);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'No se pudo eliminar'], 500);
        }
    }

    //Manda notificación de un material que se necesita para un trabajo 
    public function notiMaterialUnavailable(Request $request) {
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
                $admin->notify(new materialUnavailable(
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
                event(new \App\Events\MaterialUnavailable($eventData, $admin->id_user)); //Emitir el evento
            }
            
            return response()->json(['msg' => 'Se envio la notificación del material'], 201);

        } catch (\Throwable $th) {
            return response()->json(['msg' => 'No se pudo enviar la notificación para el material', 
            'error' => $th->getMessage()], 500);
        }
    }
    
}
