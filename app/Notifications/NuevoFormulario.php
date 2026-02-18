<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NuevoFormulario extends Notification
{
    use Queueable;

    public $tercero;

    public function __construct($tercero)
    {
        $this->tercero = $tercero;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Nuevo formulario recibido')
            ->greeting('Hola')
            ->line('Se ha completado un nuevo formulario del tercero:')
            ->line('Nombre: ' . $this->tercero->nombre)
            ->action('Ver formulario', url('/admin/terceros/' . $this->tercero->id .'/auditar'))
            ->line('Por favor revisa la información y los documentos adjuntos.');
    }
}
