<?php

namespace App\Http\Controllers;

use App\Models\rol;
use Illuminate\Http\Request;

class RolController extends Controller
{
    //Obtiene todos los roles validos 
    public function get (){
        try {
            $data = rol::where('id_rol', '!=','4')
            ->get();
            return response()->json($data, 200);
        } catch (\Throwable $th) {
            return response()->json(['msg' => $th->getMessage()], 500);
        }
    }


    //Obtiene el rol en función del id
    public function getRolById ($id){
        try {
            $data = rol::select('id_rol', 'name_rol')
                ->find($id);
            return response()->json($data, 200);
        } catch (\Throwable $th) {
            return response()->json(['msg' => $th->getMessage()], 500);
        }
    }
    
}
