<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class reportMaterial extends Notification
{
    use Queueable;

    public $idMaterial;
    public $toUser;
    public $subject;
    public $nameMaterial;
    public $message;
    public $date;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($idMaterial, $toUser, $subject, $nameMaterial, $description, $date)
    {
        $this->idMaterial = $idMaterial;
        $this->toUser = $toUser;
        $this->subject = $subject;
        $this->nameMaterial = $nameMaterial;
        $this->description = $description;
        $this->date = $date;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

  
    public function toArray($notifiable)
    {
        return [
            'id_material' => $this->idMaterial,
            'to_user' => $this->toUser, 
            'subject' => $this->subject,
            'title' => $this->nameMaterial,
            'description' => $this->description,
            'date' =>  $this->date
        ];
    }
}
