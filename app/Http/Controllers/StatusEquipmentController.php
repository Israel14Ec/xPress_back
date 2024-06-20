<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\status_equipment;
use App\Http\Requests\StoreStatusEquipment;

class StatusEquipmentController extends Controller
{
    //Crea un nuevo status
    public function createStatus (StoreStatusEquipment $request) {
        try {
            $status = status_equipment::create($request->all());
            if($status) {
                return response()->json (['msg' => 'Se guardo correctamente el material', 'data' => $status], 201); 
            }
            return response()->json(['msg' => 'No se pudo lograr, intentelo de nuevo'], 404);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'Algo salio mal no se pudo crear'], 500);
        }
    }

        
    //Obtiene todo
    public function getStatus (){
        try {
            $status = status_equipment::get();
            return response()->json($status, 200);
        } catch (\Throwable $th) {
            return response()->json([ 'error' => $th->getMessage(), 'msg' => 'Algo salio mal, intentelo de nuevo'], 500);
        }
    }


    //Edita un estado
    public function updateStatus ($id, Request $request) {
        try {
            $status = status_equipment::find($id);
    
            if(!$status){
                return response()->json(['msg' => 'No se encontró el tipo de equipo para guardar los cambios'], 404);
            }
            $status->update($request->all());
            return response()->json(['msg' => 'Se edito el tipo de equipo correctamente', 'data' => $status], 201);
    
        } catch (\Throwable $th) {
                return response()->json([ 'error' => $th->getMessage(), 'msg' => 'Algo salio mal en la ediciación, intentelo de nuevo'], 500);
        }
    }


    //Elimina un tipo de equipamiento
    public function deleteStatus ($id){
        try {
            $status = status_equipment::find($id);
            if(!$status) {
                return response()->json(['msg' => 'No se encontro resultados'], 404);
            }
            $status->delete();
            return response()->json(['msg' => 'Estado del material eliminado exitosamente', 'data' => $status]);
            
        } catch (\Throwable $th) {
            return response()->json([ 'error' => $th->getMessage(), 'msg' => 'No se pudo eliminar'], 500);
        }
    }
}
