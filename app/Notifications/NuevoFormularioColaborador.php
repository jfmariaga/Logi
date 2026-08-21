<?php

namespace App\Notifications;

use App\Models\FormularioConocimientoColaborador;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NuevoFormularioColaborador extends Notification
{
    use Queueable;

    public function __construct(
        public FormularioConocimientoColaborador $formulario,
        public bool $esActualizacion = false,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $colaborador = $this->formulario->colaborador;
        $nombre = trim(($colaborador?->name ?? '') . ' ' . ($colaborador?->last_name ?? ''));
        $accion = $this->esActualizacion ? 'actualizado' : 'enviado';

        return (new MailMessage)
            ->subject('Formulario de conocimiento de colaborador ' . $accion)
            ->greeting('Hola ' . ($notifiable->name ?? ''))
            ->line('El colaborador ' . ($nombre ?: 'sin nombre') . ' ha ' . $accion . ' su formulario de conocimiento y está pendiente de revisión.')
            ->action('Revisar formulario', route('formularios.colaboradores.detalle', $this->formulario->id))
            ->line('Por favor revisa la información suministrada.');
    }
}
