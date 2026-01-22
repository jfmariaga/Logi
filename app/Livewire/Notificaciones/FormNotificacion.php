<?php

namespace App\Livewire\Notificaciones;

use Livewire\Component;

use Illuminate\Support\Facades\Auth;
use App\Models\Notificacion;

class FormNotificacion extends Component
{

    public $form_old = [];
    public $form = [
            'tipo'          => 'primary',
            'titulo'        => '',
            'descripcion'   => '',
            'fecha_expired' => ''
        ];

    public function save()
    {
        $this->validate([
            'form.tipo'             => 'required',
            'form.titulo'           => 'required',
            'form.descripcion'      => 'required',
            'form.fecha_expired'    => 'required'
        ]);

        // dd( $this->form );

        if( $this->form_old ){

            $noti = Notificacion::find( $this->form_old['id'] );

            $noti->tipo          = $this->form['tipo'];
            $noti->titulo        = $this->form['titulo'];
            $noti->descripcion   = $this->form['descripcion'];
            $noti->fecha_expired = $this->form['fecha_expired'];
            $noti->user_id       = Auth::check() ? Auth::id() : null;

            $noti->save();
        }else{
            $noti = Notificacion::create([
                'tipo'          => $this->form['tipo'],
                'titulo'        => $this->form['titulo'],
                'descripcion'   => $this->form['descripcion'],
                'fecha_expired' => $this->form['fecha_expired'],
                'user_id'       => Auth::check() ? Auth::id() : null
            ]);
        }

        $this->dispatch( 'getNotas' );
        
        if ($noti) {
            return true;
        } else {
            return false;
        }
    }


    public function render()
    {
        return view('livewire.notificaciones.form-notificacion');
    }
}
