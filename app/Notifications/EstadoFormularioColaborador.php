<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EstadoFormularioColaborador extends Notification
{
    use Queueable;

    public function __construct(
        public string $estado,
        public ?string $motivo = null,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Estado del formulario de conocimiento')
            ->greeting('Hola ' . ($notifiable->name ?? ''))
            ->line('Su FORMULARIO DE CONOCIMIENTO DE COLABORADORES ha sido ' . $this->estado . '.');

        if ($this->estado === 'rechazado') {
            $mail->line('Observaciones: ' . ($this->motivo ?: 'Debe revisar y corregir la información.'))
                ->action('Corregir formulario', route('formulario.conocimiento'));
        }

        return $mail;
    }
}
