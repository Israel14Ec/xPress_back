<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReportWorkComplete implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    //Evento para el jefe de departamento cuando finaliza una orden de trabajo
    public $message;
    public $userId;

     
    public function __construct($message, $userId)
    {
        $this->message = $message;
        $this->userId = $userId;
    }

    public function broadcastWith() {
        return [
            'data' => $this->message,
        ];
    }

    public function broadcastOn()
    {
        return new PrivateChannel('userAssignedJob.'.$this->userId); //Trabajo asignado
    }
}
