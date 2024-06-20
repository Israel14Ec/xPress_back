<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Google_Client;
use Illuminate\Support\Facades\Session;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        $client = new Google_Client();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
        $client->setScopes([
            'https://www.googleapis.com/auth/gmail.compose',
            'https://www.googleapis.com/auth/gmail.modify',
            'https://www.googleapis.com/auth/gmail.readonly',
        ]);
    
        $authUrl = $client->createAuthUrl();
        
        return response()->json(['authUrl' => $authUrl]);
    }
    

    public function handleGoogleCallback(Request $request)
    {
        $client = new Google_Client();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
        
        $code = $request->get('code');
        $client->fetchAccessTokenWithAuthCode($code);
        $accessToken = $client->getAccessToken();

// Guardar el token de acceso en la caché con una clave específica asociada al usuario
        $userId = auth()->id(); // Obtener el ID del usuario autenticado
        $cacheKey = 'google_access_token_' . $userId;
        Cache::put($cacheKey, $accessToken);

        return redirect('http://localhost:5173/admin/inicio');// Redirige al usuario a la página de inicio después de iniciar sesión con Google
    }
}
