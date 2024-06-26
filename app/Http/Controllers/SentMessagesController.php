<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google_Client;
use Google_Service_Gmail as Google_Service_Gmail;
use Illuminate\Support\Facades\Cache;


class SentMessagesController extends Controller
{
    public function listSentMessages(Request $request)
    {
        try {
            // Recuperar el token de acceso de la sesión
            $accessToken = $this->getAccessToken();

            // Crear un cliente de Google
            $client = new Google_Client();
            $client->setAccessToken($accessToken);

            // Crear un servicio de Gmail
            $service = new Google_Service_Gmail($client);

            // Obtener los mensajes del usuario
            $messages = $service->users_messages->listUsersMessages('me', [
                'maxResults' => 5, // Limitar el número de mensajes
                'labelIds' => ['SENT'], // Filtrar por etiquetas (por ejemplo, INBOX)
            ]);
            

            // Procesar los mensajes y devolverlos como respuesta
            $formattedMessages = [];
            foreach ($messages->getMessages() as $message) {
                
                $messageId = $message->getId();
                $messageInfo = $service->users_messages->get('me', $messageId, ['format' => 'full']);
               
                $snippet = $messageInfo->getSnippet();
                $headers = $messageInfo->getPayload()->getHeaders();
                $subject = '';
                $from = '';
                $to = '';
                foreach ($headers as $header) {
                    if ($header['name'] === 'Subject') {
                        $subject = $header['value'];
                    } elseif ($header['name'] === 'From') {
                        $from = $header['value'];
                    } elseif ($header['name'] === 'to') {
                        $to = $header['value'];
                        
                    }
                }
                $formattedMessages[] = [
                    'id' => $messageId,
                    'subject' => $subject,
                    'from' => $from,
                    'to' => $to,
                    'snippet' => $snippet,
                ];
            }

            return response()->json(['messages' => $formattedMessages]);

        } catch (\Exception $e) {
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
