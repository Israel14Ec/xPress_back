<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class reportWorkOrderComplete extends Notification
{
    use Queueable;

    public $idWorkOrder;
    public $toUser;
    public $subject;
    public $jobTitle;
    public $jobDescription;
    public $date;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($idWorkOrder, $toUser, $subject, $jobTitle, $jobDescription, $date)
    {
        $this->idWorkOrder = $idWorkOrder;
        $this->toUser = $toUser;
        $this->subject = $subject;
        $this->jobTitle = $jobTitle;
        $this->jobDescription = $jobDescription;
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
            'id_work_order' => $this->idWorkOrder,
            'to_user' => $this->toUser, 
            'subject' => $this->subject,
            'title' => $this->jobTitle,
            'description' => $this->jobDescription,
            'date' =>  $this->date
        ];
    }
}
