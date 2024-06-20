<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\type_equipment;
use App\Http\Requests\StoreTypeEquipment;

class TypeEquipmentController extends Controller
{
    //Crea un nuevo tipo de equipamiento
    public function createTypeEquipment(StoreTypeEquipment $request) {

        try {
            $typeEquipment = type_equipment::create($request->all());
            return response()->json(['msg' => 'Se creo un nuevo tipo de equipamiento', 'data' => $typeEquipment], 201);
        } catch (\Throwable $th) {
            return response()->json([ 'msg' => $th->getMessage()], 500);
        }
    } 
    
    //Obtiene todo
    public function getTypeEquipment (){
        try {
            $typeEquipment = type_equipment::get();
            return response()->json($typeEquipment, 200);
        } catch (\Throwable $th) {
            return response()->json([ 'msg' => $th->getMessage()], 500);
        }
    }

    //Edita un tipo de equipamiento
    public function updateTypeEquipment ($id, Request $request) {
        try {
            $typeEquipment = type_equipment::find($id);

            if(!$typeEquipment){
                return response()->json(['msg' => 'No se encontró el tipo de equipo para guardar los cambios'], 404);
            }
            $typeEquipment->update($request->all());
            return response()->json(['msg' => 'Se edito el tipo de equipo correctamente', 'data' => $typeEquipment], 201);

        } catch (\Throwable $th) {
            return response()->json([ 'error' => $th->getMessage()], 500);
        }
    }

    //Elimina un tipo de equipamiento
    public function deleteTypeEquipment ($id){
        try {
            $typeEquipment = type_equipment::find($id);
            if(!$typeEquipment) {
                return response()->json(['msg' => 'El tipo de material no existe'], 404);
            }
            $typeEquipment->delete();
            return response()->json(['msg' => 'Tipo de material eliminado exitosamente', 'data' => $typeEquipment]);
            
        } catch (\Throwable $th) {
            return response()->json([ 'error' => $th->getMessage()], 500);
        }
    }
}
