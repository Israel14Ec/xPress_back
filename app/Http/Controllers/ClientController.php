<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\client;
use App\Http\Requests\StoreClient;

class ClientController extends Controller
{   
    //Crea un nuevo cliente 
    public function create (StoreClient $request) {
        try {
            $client = client::create($request->all());
            if($client) {
                return response()->json (['msg' => 'Se agrego correctamente al cliente', 'data' => $client], 201); 
            }
            return response()->json(['msg' => 'No se pudo agregar al cliente, intentelo de nuevo'], 404);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'Algo salio mal no se pudo crear'], 500);
        }
    }

    //Obtiene todos los clientes no borrados
    public function get () {
        try {
            $client = client::get();
            return response()->json($client, 200);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'Algo salio mal no se pudo obtener los datos'], 500);
        }
    }

    //Obtiene a los clientes borrados lógicamente
    public function getDelete() {
        try {
            $deletedClients = Client::onlyTrashed()->get(); 
            return response()->json($deletedClients, 200);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'Algo salio mal no se pudo obtener los datos'], 500);
        }
    }

    //Actualiza a un cliente
    public function update ($id, StoreClient $request) {
        try {
            $client = client::find($id);
            if (!$client) {
                return response()->json(['msg' => 'No se encontró al usuario'], 404);
            }
            $client->update($request->all());
            return response()->json(['msg' => 'Se edito los datos del cliente correctamente', 'data' => $client], 201);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'Algo salio mal no se pudo actualizar los datos'], 500);
        }
    }

    //Elimina a un cliente
    public function deleteLogic ($id) {
        try {
            $client = client::find($id);
            if (!$client) {
                return response()->json(['msg' => 'El cliente no existe'], 404);
            }
            $client->delete();
            return response()->json(['msg' => 'El cliente fue borrado', 'data' => $client]);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'Algo salio mal no se pudo eliminar los datos'], 500);
        }
    }

    //Restaurar cliente borrado
    public function restoreClient ($id) {
        try {
            $client = Client::withTrashed()->find($id);
            $client->restore(); // Restaura el cliente borrado lógicamente
            return response()->json(['msg' => 'El cliente fue restaurado exitosamente', 'data'=>$client], 201);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'Algo salio mal no se pudo restaurar los datos'], 500);
        }
    }

}
