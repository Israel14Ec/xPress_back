<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\magnitude;
use App\Http\Requests\StoreMagnitude;

class MagnitudeController extends Controller
{
    //crear una nueva magnitud
    public function create (StoreMagnitude $request) {
        try {

            $magnitude = magnitude::create($request -> all());

            if($magnitude) {
                return response()->json (['msg' => 'Se guardo correctamente la magnitud', 'data' => $magnitude], 201); 
            }

            return response()->json(['msg' => 'No se pudo guardar, intente de nuevo'], 404);

        } catch (\Throwable $th) {
            return response()->json(['msg' => 'No se pudo crear una nueva magnitud para el material',
                'error'=> $th->getMessage()], 505);
        }
    }

    //Obtiene todo
    public function get() {
        try {
            $magnitude = magnitude::get();
            return response()->json($magnitude);

        } catch (\Throwable $th) {
            return response()->json(['msg' => 'No se pudo obtener los datos, de las magnitudes',
                'error' => $th->getMessage()], 505);

        }
    }

    //Modificar 
    public function update ($id, StoreMagnitude $request) {
        try {
            $magnitude = magnitude::find($id);

            if(!$magnitude) {
                return response()->json(['msg' => 'No se encontró la magnitud para guardar los cambios'], 404);
            }

            $magnitude->update($request->all());
            return response()->json(['msg' => 'Se edito la magnitud correctamente', 'data' => $magnitude], 201);
            
        } catch (\Throwable $th) {
            return response()->json(['msg' => 'No se pudo actualizar la magnitud, ocurrio un error inesperado', 
                'error' => $th->getMessage()]);
        }
    }

    //Eliminar
    public function delete ($id) {
        try {
            $magnitude = magnitude::find($id);
            if(!$magnitude){
                return response()->json(['msg' => 'No se encontro la magnitud'], 404);
            }
            
            $magnitude->delete();
            return response()->json(['msg' => 'La magnitud fue eliminado exitosamente', 
                'data' => $magnitude]);

        } catch (\Throwable $th) {
            return response()->json(['msg' => 'Algo sucedio mal, no se pudo eliminar',
                'error' => $th->getMessage()], 505);
        }
    }
}
