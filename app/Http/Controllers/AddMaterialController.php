<?php

namespace App\Http\Controllers;

use App\Models\add_material;
use App\Models\material;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAddMaterial;

class AddMaterialController extends Controller
{
    //Crea un nuevo registro en la tabla
    public function addStock(StoreAddMaterial $request) {
        $id_material = $request->input('id_material');
        $new_stock = $request->input('stock');

        \DB::beginTransaction();
        try {
            // Crear el registro en add_material
            $add_material = add_material::create($request->all());
    
            // Obtener el material y actualizar su stock
            $material = material::findOrFail($id_material);
            $material->stock += $new_stock;
            
            // Calcular el nuevo total_value
            $material->total_value = $material->unit_value * $material->stock;

            $material->save();

            \DB::commit();
            return response()->json(['msg' => 'Stock actualizado correctamente', 'data' => $add_material], 201);
        } catch (\Throwable $th) {
            \DB::rollBack();
            return response()->json(['msg' => 'No se pudo crear el registro','error' => $th->getMessage()], 500);
        }
    }

    //Obtiene todos los registros
    public function get(){
        try {
            $addMaterial = add_material::with('material')->get();
            return response()->json($addMaterial, 200);

        } catch (\Throwable $th) {
            return response()->json(['msg' => 'No se pudo obtener los datos', 'error'], 500);
        }
    }

    //Obtiene el add_material segun el id_material
    public function getAddByMaterialId($id){
        try {
            $material = material::findOrFail($id);
            $addMaterial = add_material::where('id_material', $id)->orderBy('created_at', 'desc')->get();
            return response()->json($addMaterial, 200);

        } catch (\Throwable $th) {
            return response()->json(['msg' => 'No se pudo obtener los datos, id no válido', 'error' => $th->getMessage()], 500);
        }
    }

    //Editar registros
    public function update($id ,Request $request){
        try {
            $newStock = $request->input('stock');
            $addMaterial = add_material::find($id);

            if(!$addMaterial) {
                return response()->json('El id proporcionado no es válido', 404);
            }

            // Obtener el material y actualizar su stock
            $material = material::findOrFail($addMaterial->id_material);
            
            if($newStock != $addMaterial->stock) { // Verificar si el stock ha cambiado
                if($newStock > $addMaterial->stock) {
                    // Si el nuevo stock es mayor, sumar la diferencia al stock del material
                    $difference = $newStock - $addMaterial->stock;
                    $material->stock += $difference;
                }
                elseif ($newStock < $addMaterial->stock) {
                    // Si el nuevo stock es menor, restar la diferencia al stock del material
                    $difference = $addMaterial->stock - $newStock;
                    $material->stock -= $difference;
                }
    
                // Calcular el nuevo total_value
                $material->total_value = $material->unit_value * $material->stock;
                $material->save();
            }

            $addMaterial->update($request->all());
            return response()->json(['data' =>$material, 'msg'=> 'Se actualizo correctamente'], 201);

        } catch (\Throwable $th) {
            return response()->json(['msg' => 'No se pudo editar el registro, intentelo de nuevo', 
            'error'=> $th->getMessage()], 500);
        }
    }
    
    public function deleteAddMaterial($id)
    {
        try {
            $add_material = add_material::findOrFail($id);

            // Verificar si han transcurrido menos de 30 minutos desde la creación
            $created = $add_material->created_at;
            $now = now();
            $diff = $now->diffInMinutes($created);

            if ($diff > 30) {
                return response()->json(['msg' => 'No se puede eliminar el registro después de 30 minutos'], 403);
            }

            // Obtener el material asociado
            $material = material::findOrFail($add_material->id_material);
            $material->stock -= $add_material->stock;
            $material->save();
            $add_material->delete();

            return response()->json(['msg' => 'Registro eliminado con éxito y stock ajustado'], 200);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'No se pudo eliminar'], 500);
        }
    }
}
