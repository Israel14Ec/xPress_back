<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\magnitude_value;
use App\Models\magnitude;
use App\Http\Requests\StoreMagnitudeValue;

class MagnitudeValueController extends Controller
{
    //Crear un nuevo valor para la magnitud
    public function create (StoreMagnitudeValue $request) {
        try {
            $value = magnitude_value::create($request -> all());
            $value->load('magnitude'); //Traigo la magnitude

            if($value) {
                return response()->json (['msg' => 'Se guardo correctamente el valor de la magnitud', 
                'data' => $value], 201);     
            }

            
            return response()->json(['msg' => 'No se pudo guardar, intentelo de nuevo'], 404);

        } catch (\Throwable $th) {
            return response()->json(['msg' => 'No se pudo crear el valor de la magnitud', 'error' => $th->getMessage()], 505);
        }
    }

    //Obtiene todo
    public function get(){
        try {
            $value = magnitude_value::with('magnitude')->get();
            return response()->json($value);
            
        } catch (\Throwable $th) {
            return response()->json(['msg' =>'No se pudo obtener los datos del valor de la magnitud', 'error' => $th->getMessage()], 505);
        }
    }

    public function getFormatMagnitude() {
        try {
            $values = magnitude_value::join('magnitudes', 'magnitude_values.id_magnitude', '=', 'magnitudes.id_magnitude')
                ->select('magnitude_values.id_magnitude_value', DB::raw("CONCAT(magnitude_values.value, ' ', magnitudes.symbol) AS magnitude"))
                ->get();
    
            return response()->json($values, 200);
        } catch (\Throwable $th) {
            return response()->json(['msg' => 'Error al obtener los datos de la consulta', 'error' => $th->getMessage()], 500);
        }
    }

    //Editar
    public function update($id, StoreMagnitudeValue $request) {
        try {
            $value = magnitude_value::find($id);
            if(!$value) {
                return response()->json(['msg' => 'No se encontró el valor de la magnitud para guardar los cambios'], 404);
            }

            $value->update($request->all());
            $value->load('magnitude'); //Traigo la magnitude
            
            return response()->json(['msg' => 'Se edito los datos del valor de la magnitud correctamente', 
                'data' => $value], 201);

        } catch (\Throwable $th) {
            return response()->json(['msg' => 'No se pudo actualizar los datos de los valores de la magnitud', 
            'error' => $th->getMessage()], 505);
        }
    }

    //Elimina
    public function delete($id) {
        try {
            $value = magnitude_value::find($id);
            if(!$value) {
                return response()->json(['msg' => 'No se encontro el valor de la magnitud'], 404);
            }

            $value->delete();
            
            return response()->json(['msg' => 'El valor de la magnitud fue eliminado exitosamente', 
                'data' => $value]);

        } catch (\Throwable $th) {
            return response()->json(['msg' => 'No se pudo eliminar la magnitud, debido a que se necesita mantener 
            el registro como parte del historial', 
            'error' => $th->getMessage()], 505);
        }
    }
}
