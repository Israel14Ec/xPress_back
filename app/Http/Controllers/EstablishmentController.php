<?php

namespace App\Http\Controllers;

use App\Models\establishment;
use Illuminate\Http\Request;

class EstablishmentController extends Controller
{
    //Agregar un establecimiento
    public function createEstablishment(Request $request){
        try {
            $data = establishment::create($request->all());
            return response()->json (['msg' => 'Se guardo correctamente el establecimiento'], 201);

        } catch (\Throwable $th) {
            return response()->json(['msg' => $th->getMessage()], 500);
        }
    }

    //Obtener los establecimientos
    public function getEstablishment() {
        try {
            $data = Establishment::select('id_establishment', 'name_establishment', 'description', 'location')->get();
            return response()->json($data, 200);

        } catch (\Throwable $th) {
            return response()->json(['msg' => $th->getMessage()], 500);
        }
    }

    //Eliminar un establecimiento
    public function delete($id) {
        try {
            $establishment = Establishment::find($id); //Comprueba si existe el establecimiento
            if (!$establishment) {
                return response()->json(['msg' => 'El establecimiento no existe'], 404);
            }
    
            $establishment->delete();
            
            return response()->json(['msg' => 'Establecimiento eliminado exitosamente'], 200);
        } catch (\Throwable $th) {
            return response()->json(
                ['error' => $th->getMessage(), 'msg' => 'No se pudo eliminar']
            , 500);
        }
    }

    public function update (Request $request, $id){
        try {
            $data = Establishment::find($id);
            if (!$data) {
                return response()->json(['msg' => 'No se encontró el departamento'], 404);
            }
            $data->update($request->all());
            return response()->json(['msg' => 'Se edito el establecimiento', 'data' => $data], 201);
            
        } catch (\Throwable $th) {
            return response()->json(['msg' => 'No se pudo actualizar', 'error' => $th]);
        }
    }
    
}
