<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewWorkOrder extends Notification
{
    use Queueable;

    public $workOrderId;
    public $userId;
    public $subject;
    public $jobName;
    public $description;
    public $instructions;
    public $assignedDate;

    public function __construct($workOrderId, $userId, $subject, $jobName, $description, $instructions, $assignedDate)
    {
        $this->workOrderId = $workOrderId;
        $this->userId = $userId;
        $this->subject = $subject;
        $this->jobName = $jobName;
        $this->description = $description;
        $this->instructions = $instructions;
        $this->assignedDate = $assignedDate;
    }

    public function via($notifiable)
    {
        return ['broadcast', 'database'];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'data' => [
                'id_work_order' => $this->workOrderId,
                'to_user' => $this->userId,
                'subject' => $this->subject,
                'title' => $this->jobName,
                'description' => $this->description,
                'instructions' => $this->instructions,
                'date' => $this->assignedDate,
            ]
        ]);
    }

    public function toArray($notifiable)
    {
        return [
            'id_work_order' => $this->workOrderId,
            'to_user' => $this->userId,
            'subject' => $this->subject,
            'title' => $this->jobName,
            'description' => $this->description,
            'instructions' => $this->instructions,
            'date' => $this->assignedDate,
        ];
    }
}
