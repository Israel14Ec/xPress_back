<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class orderEquipment extends Notification
{
    use Queueable;

    public $idEquipment;
    public $toUser;
    public $subject;
    public $nameEquipment;
    public $message;
    public $date;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($idEquipment, $toUser, $subject, $nameEquipment, $message, $date)
    {
        $this->idEquipment = $idEquipment; 
        $this->toUser = $toUser; 
        $this->subject = $subject;
        $this->nameEquipment = $nameEquipment; 
        $this->message = $message; 
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


    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'id_construction_equipment' => $this->idEquipment,
            'to_user' => $this->toUser,
            'subject' => $this->subject,
            'title' => $this->nameEquipment,
            'description' => $this->message,
            'date' =>  $this->date
        ];
    }
}
