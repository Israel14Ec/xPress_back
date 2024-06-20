<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\job_priorities;
use App\Http\Requests\StoreJobPriorities;

class JobPrioritiesController extends Controller
{
    //crear 
    public function create (StoreJobPriorities $request) {
        
        try {
            $priority = job_priorities::create($request->all());
            if($priority) {
                return response()->json (['msg' => 'Se agrego correctamente la prioridad de trabajo', 'data' => $priority], 201); 
            }
            return response()->json(['msg' => 'No se pudo agregar la prioridad intentelo de nuevo'], 404);
            
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg'=> 'Algo salio mal no se pudo agregar'], 500);
        }
    }

    //Obtener
    public function get () {
        try {
            $priority = job_priorities::get();
            return response()->json($priority, 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'Algo salio mal no se pudo obtener los datos'], 500);
        }
    }

    
    //Editar
    public function update ($id, StoreJobPriorities $request) {
        try {
            $priority = job_priorities::find($id);
            if (!$priority) {
                return response()->json(['msg' => 'No se encontró a la prioridad'], 404);
            }
            $priority->update($request->all());
            return response()->json(['msg' => 'Se edito los datos de la prioridad correctamente', 'data' => $priority], 201);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'Algo salio mal no se pudo actualizar los datos'], 500);
        }
    }

    //Eliminar
    public function delete($id) {
        try {

            $priority = job_priorities::find($id);
    
            if (!$priority) {
                return response()->json(['msg' => 'La prioridad no existe'], 404);
            }
            $priority->delete();
            return response()->json(['msg' => 'La prioridad se ha eliminado exitosamente', 'data' => $priority]);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'No se puede eliminar los datos'], 500);
        }
    }
}
