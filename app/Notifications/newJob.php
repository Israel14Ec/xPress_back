<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class newJob extends Notification
{
    use Queueable;

    public $jobId;
    public $toUser;
    public $subject; //Asunto de la notificación
    public $jobTitle;
    public $jobDescription;
    public $date;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($jobId, $toUser, $subject, $jobTitle, $jobDescription, $date)
    {
        $this->jobId = $jobId;
        $this->toUser = $toUser;
        $this->subject = $subject;
        $this->jobTitle = $jobTitle;
        $this->jobDescription = $jobDescription;
        $this->date= $date;
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
     * Get the broadcastable representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\BroadcastMessage
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
    //Arreglo a almacenar en la DB
    public function toArray($notifiable)
    {
        return [
            'id_job' => $this->jobId,
            'to_user' => $this->toUser,
            'subject' => $this->subject,
            'title' => $this->jobTitle,
            'description' => $this->jobDescription,
            'date' => $this->date
        ];
    }
}
