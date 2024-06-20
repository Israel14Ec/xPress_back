<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
class GmailDesauth extends Controller
{
    // Otros métodos del controlador

    public function logout(Request $request)
    {
        $userId = auth()->id(); // Obtener el ID del usuario autenticado
        $cacheKey = 'google_access_token_' . $userId;
        
        // Eliminar el token de acceso de la caché
        Cache::forget($cacheKey);
        


        // Redirigir al usuario a la página de inicio de sesión
        return response()->json(['message' => 'Logout exitoso'], Response::HTTP_OK);
    }
}
