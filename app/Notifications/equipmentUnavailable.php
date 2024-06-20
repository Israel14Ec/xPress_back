<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class equipmentUnavailable extends Notification
{
    use Queueable;

    public $jobId;
    public $toUser;
    public $from;
    public $idUserFrom; //Es el id del usuario que mando la notificación
    public $subject;
    public $title;
    public $description;
    public $date;
    public $priority; //Prioridad (true o false), esto me ayudara a notificar en el front mejor

    /**
     * Create a new notification instance.
     *
     * @return void
     */
      public function __construct($jobId, $toUser, $from, $idUserFrom, $subject, $title, $description, $date,
    $priority)
    {
        $this->jobId = $jobId;
        $this->toUser = $toUser;
        $this->from = $from;
        $this->idUserFrom = $idUserFrom;
        $this->subject = $subject;
        $this->title = $title;
        $this->description = $description;
        $this->date = $date;
        $this->priority = $priority;
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
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toBroadcast($notifiable)
    {
        return new \Illuminate\Notifications\Messages\BroadcastMessage([
            'data' => 'Información de la notificación'
        ]);
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
            'id_job' => $this->jobId,
            'to_user' => $this->toUser,
            'from' => $this->from,
            'id_user_from' => $this->idUserFrom,
            'subject' => $this->subject,
            'title' => $this->title,
            'description' => $this->description,
            'date' => $this->date,
            'priority' => $this->priority
        ];
    }
}
