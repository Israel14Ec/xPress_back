<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\communication_type;
use App\Http\Requests\StoreCommunication;

class CommunicationTypeController extends Controller
{
    //crear communication
    public function create (StoreCommunication $request) {
        try {

            $communication = communication_type::create($request->all());
            if($communication) {
                return response()->json (['msg' => 'Se agrego correctamente al medio de comunicación', 'data' => $communication], 201); 
            }
            return response()->json(['msg' => 'No se pudo agregar al medio de comunicación, intentelo de nuevo'], 404);
            
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg'=> 'Algo salio mal no se pudo agregar'], 500);
        }
    }

    //Obtener todos los tipos
    public function get () {
        try {
            $communication = communication_type::get();
            return response()->json($communication, 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'Algo salio mal no se pudo obtener los datos'], 500);
        }
    }

    //Edita un tipo de comunicación
    public function update ($id, StoreCommunication $request) {
        try {
            $communication = communication_type::find($id);
            if (!$communication) {
                return response()->json(['msg' => 'No se encontró al medio de comunicacion'], 404);
            }
            $communication->update($request->all());
            return response()->json(['msg' => 'Se edito los datos del medio de comunicación correctamente', 'data' => $communication], 201);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'Algo salio mal no se pudo actualizar los datos'], 500);
        }
    }

    public function delete($id) {
        try {

            $communication = communication_type::find($id);
    
            if (!$communication) {
                return response()->json(['msg' => 'El medio de comunicación no existe'], 404);
            }
            $communication->delete();
            return response()->json(['msg' => 'Medio de comunicación eliminado exitosamente', 'data' => $communication]);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'No se puede eliminar los datos'], 500);
        }
    }
}
