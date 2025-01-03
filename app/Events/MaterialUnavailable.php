<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MaterialUnavailable implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    //Evento para el admin cuando un material queda en 0
    public $message;
    public $userId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
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

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('materialUnavailable.'.$this->userId);
    }
}
