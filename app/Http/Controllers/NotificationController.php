<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    //Obtener las notificaciones de un usuario
    public function get()
    {
        try {
            $user = Auth::user();
            $notifications = $user->notifications; 
            return response()->json($notifications);
        } catch (\Throwable $th) {
            return response()->json(['msg'=> 'No se pudo obtener todas las notificaciones','error' => $th->getMessage()], 500);
        }

    }

   // Obtener las notificaciones leídas de un usuario
    public function getRead()
    {
        try {
            $user = Auth::user();
            $readNotifications = $user->notifications->whereNotNull('read_at')->values();
            return response()->json($readNotifications);
        } catch (\Throwable $th) {
            return response()->json(['msg'=> 'No se pudo obtener las notificaciones leídas','error' => $th->getMessage()], 500);
        }
    }




    //Obtener notificaciones no leidas de un usuario
    public function getUnRead()
    {
        try {
            $user = Auth::user();
            $notifications =$user->unreadNotifications; // o $user->unreadNotifications para no leídas
            return response()->json($notifications);

        } catch (\Throwable $th) {
            return response()->json(['msg'=> 'No se pudo obtener las notificaciones','error' => $th->getMessage()], 500);
        }

    }

    //Marcar las notificaciones como leidas en función del ID
    public function markAsRead($notificationId)
    {
        try {
            $user = Auth::user();
            $notification = $user->notifications()->where('id', $notificationId)->first();
    
            if ($notification) {
                $notification->markAsRead();
                return response()->json(['msg' => 'Notificación Leida']);
            }
    
            return response()->json(['msg' => 'Notificación no encontrada'], 404);

        } catch (\Throwable $th) {
             return response()->json(['msg'=> 'No se pudo marcar como leido','error' => $th->getMessage()], 500);
        }
   
    }

    // Eliminar notificaciones por el id
    public function deleteNotification(Request $request)
    {
        try {

            $notificationId = $request->input('id');
            $user = Auth::user();
            $notification = $user->notifications()->where('id', $notificationId);

              if ($notification) {
                $notification->delete();
                return response()->json(['msg' => 'Notificación eliminada']);
            }
    
            return response()->json(['msg' => 'Notificación no encontrada'], 404);

        } catch (\Throwable $th) {
            return response()->json(['msg' => 'No se pudo eliminar la notificación', 'error' => $th->getMessage()], 500);
        }
    }


}
