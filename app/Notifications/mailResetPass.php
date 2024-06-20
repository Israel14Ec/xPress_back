<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class mailResetPass extends Notification implements ShouldQueue
{
    use Queueable;

    protected $token;
    protected $name;
    
    public function __construct($token, $name)
    {
        $this->token = $token;
        $this->name = $name;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $token = $notifiable->token; 
        $url = 'http://localhost:5173/reestablecer_pass/' . $this->token;

        return (new MailMessage)
        ->subject('Recuperación de contraseña')
        ->greeting('¡Hola! '. $this->name)
        ->line('Has recibido este correo electrónico porque solicitaste restablecer tu contraseña.')
        ->action('Recuperar contraseña', $url)
        ->line('Si no solicitaste restablecer tu contraseña, puedes ignorar este mensaje.')
        ->salutation('¡Gracias por usar nuestra aplicación!');
    }

   
}
