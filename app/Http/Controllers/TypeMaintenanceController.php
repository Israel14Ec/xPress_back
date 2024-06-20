<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\type_maintenance;
use App\Http\Requests\StoreMaintenance;

class TypeMaintenanceController extends Controller
{
    //Crear
    public function create (StoreMaintenance $request) {

        try {
            $maintenance = type_maintenance::create($request->all());
            return response()->json(['msg' => 'Se creo un nuevo tipo de equipamiento', 'data' => $maintenance], 201);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'Algo salio mal no se pudo crear'], 500);
        }
    } 

    //Obtener 
    public function get () {
        try {
            $maintenance = type_maintenance::get();
            return response()->json($maintenance, 200);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'Algo salio mal no se pudo obtener los datos'], 500);
        }
    }

    //Editar
    public function update ($id, StoreMaintenance $request) {
        try {
            $maintenance = type_maintenance::find($id);
            if (!$maintenance) {
                return response()->json(['msg' => 'No se encontró el estado'], 404);
            }
            $maintenance->update($request->all());
            return response()->json(['msg' => 'Se edito los datos correctamente', 'data' => $maintenance], 201);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'Algo salio mal no se pudo actualizar los datos'], 500);
        }
    }

  //Eliminar
  public function delete($id) {
        try {

            $maintenance = type_maintenance::find($id);

            if (!$maintenance) {
                return response()->json(['msg' => 'El tipo de mantenimiento no existe'], 404);
            }
            $maintenance->delete();
            return response()->json(['msg' => 'Datos eliminados exitosamente', 'data' => $maintenance]);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'No se puede eliminar los datos'], 500);
        }
    }

}
