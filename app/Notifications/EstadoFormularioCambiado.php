<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EstadoFormularioCambiado extends Notification
{
    public $estado;
    public $motivo;

    public function __construct($estado, $motivo = null)
    {
        $this->estado = $estado;
        $this->motivo = $motivo;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject('Estado de su formulario')
            ->line("Su formulario ha sido {$this->estado}");

        if ($this->motivo) {
            $mail->line("Motivo del rechazo: {$this->motivo}");
            $mail->line("Por favor ingresa nuevamente al sistema con tu usuario y contraseña, realiza las correcciones indicadas y envía nuevamente el formulario para su revisión.")
                ->action('Ingresar al sistema', url('/contrapartes'));
        }


        return $mail;
    }
}
