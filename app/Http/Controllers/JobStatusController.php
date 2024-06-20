<?php

namespace App\Http\Controllers;
use App\Models\job_status;
use App\Http\Requests\StoreJobStatus;

use Illuminate\Http\Request;

class JobStatusController extends Controller
{
    //crear
    public function create (StoreJobStatus $request) {

        try {
            $status = job_status::create($request->all());
            if($status){
                return response()->json (['msg' => 'Se agrego correctamente el estado de trabajo', 'data' => $status], 201); 
            }
            return response()->json(['msg' => 'No se pudo agregar el estado de trabajo, intentelo de nuevo'], 404);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(),'msg' => 'No se pudo crear el estado'], 500);
        }
    }

    //Obtener todos
    public function get () {
        try {
            $status = job_status::orderBy('step', 'asc')->get();
            return response()->json($status, 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(),'msg' => 'No se pudo obtener los datos'], 500);
        }
    }

    
    //Obtener todo en función del step
    public function getSteps () {
        try {
            $status = job_status::whereNotNull('step')->orderBy('step', 'asc')->get();
            return response()->json($status, 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(),'msg' => 'No se pudo obtener los datos'], 500);
        }
    }

    //Obtener un jobStatus en función del step
    public function getStatusJob ($step) {
        try {
            $status = job_status::where('step', $step)->first(); 
            if ($status) {
                return response()->json($status, 200);
            } else {
                return response()->json(['msg' => 'Estado de trabajo no encontrado'], 404);
            }

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(),'msg' => 'No se pudo obtener los datos'], 500);
        }
    }


    //Editar
    public function update ($id, StoreJobStatus $request) {
        try {
            $status = job_status::find($id);
            if (!$status) {
                return response()->json(['msg' => 'No se encontró el estado'], 404);
            }
            $status->update($request->all());
            return response()->json(['msg' => 'Se edito los datos del estado de trabajo correctamente', 'data' => $status], 201);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'Algo salio mal no se pudo actualizar los datos'], 500);
        }
    }

    //Eliminar
    public function delete($id) {
        try {

            $status = job_status::find($id);
    
            if (!$status) {
                return response()->json(['msg' => 'El estado de trabajo no existe'], 404);
            }
            $status->delete();
            return response()->json(['msg' => 'Medio de comunicación eliminado exitosamente', 'data' => $status]);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'No se puede eliminar los datos'], 500);
        }
    }
}
