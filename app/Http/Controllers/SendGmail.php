<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google_Service_Gmail_Message as Google_Service_Gmail_Message;
use Google_Service_Gmail as Google_Service_Gmail;
use Illuminate\Support\Facades\Validator;
use Google_Client;
use Illuminate\Support\Facades\Cache;

class SendGmail extends Controller
{
    /**
     * Función para enviar un mensaje.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request)
    {
        try {
            
            // Convertir el objeto $request a un array y luego a JSON
            $data = json_decode(json_encode($request->all()), true);

            // Imprimir los datos obtenidos para depuración
            print_r($data);


            // Validar los datos del formulario de envío de mensaje
            $validator = Validator::make($data, [
                'to' => 'required|email',
                'subject' => 'required',
                'content' => 'required',
            ]);

            // Si la validación falla, devolver errores
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            // Recuperar el token de acceso de la sesión
            $accessToken = $this->getAccessToken();

            // Crear un cliente de Google
            $client = new Google_Client();
            $client->setAccessToken($accessToken);

            // Crear un servicio de Gmail
            $service = new Google_Service_Gmail($client);

            // Crear el mensaje
            $message = new Google_Service_Gmail_Message();
            $message->setRaw(
                base64_encode(
                    "to: " . $data['to'] . "\r\n" .
                    "Subject: " . $data['subject'] . "\r\n\r\n" .
                    $data['content']
                )
            );

            // Enviar el mensaje
            $sentMessage = $service->users_messages->send('me', $message);

            // Devolver una respuesta JSON de éxito
            return response()->json(['message' => 'Mensaje enviado con éxito'], 200);
        } catch (\Exception $e) {
            // Devolver una respuesta JSON de error
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Función para obtener el token de acceso de la sesión.
     *
     * @return string
     */
    private function getAccessToken()
    {
        // Obtener el ID del usuario autenticado
        $userId = auth()->id();
    
        // Construir la clave de caché específica para el token de acceso del usuario
        $cacheKey = 'google_access_token_' . $userId;
    
        // Recuperar el token de acceso de la caché
        return Cache::get($cacheKey);
    }
}
