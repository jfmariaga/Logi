<?php

namespace App\Livewire\Sedes;

use Livewire\Component;

use App\Models\Sede;

class Sedes extends Component
{
    public $sedes       = [];
    public $form        = [
                'nombre'            => '',
                'direccion'         => '',
                'activo'            => 1,
                'contacto'          => '',
                'telefono_contacto' => '',
                'latitud'           => '',
                'longitud'          => '',
                'radio_metros'      => 150,
            ];
    public $form_old    = null; // para editar

    public function getData()
    {
        $this->skipRender();
        return Sede::orderBy('nombre')->get();
    }

    public function save()
    {
        $this->validate([
            'form.nombre'       => 'required',
            'form.direccion'    => 'required',
            'form.latitud'      => 'required|numeric|max:999',
            'form.longitud'     => 'required|numeric|max:999',
        ]);

        if( $this->form_old ){

            // dd( $this->form_old );
            $sede = Sede::find( $this->form_old['id'] );
            $sede->nombre            = $this->form['nombre'];
            $sede->direccion         = $this->form['direccion'];
            $sede->contacto          = $this->form['contacto'];
            $sede->telefono_contacto = $this->form['telefono_contacto'];
            $sede->latitud           = $this->form['latitud'];
            $sede->longitud          = $this->form['longitud'];
            $sede->radio_metros      = $this->form['radio_metros'];
            $sede->activo            = $this->form['activo'];

            $sede->save();
        }else{
            $sede = Sede::create([
                'nombre'            => $this->form['nombre'], 
                'direccion'         => $this->form['direccion'], 
                'contacto'          => $this->form['contacto'], 
                'telefono_contacto' => $this->form['telefono_contacto'], 
                'latitud'           => $this->form['latitud'], 
                'longitud'          => $this->form['longitud'], 
                'radio_metros'      => $this->form['radio_metros'] ?? 150, 
                'activo'            => $this->form['activo']
            ]);
        }
        
        if ($sede) {
            return $sede->toArray();
        } else {
            return false;
        }
    }

    public function desactivarUser($user_id)
    {
        $user = User::find($user_id);
        if ($user) {
            $user->update(['status' => 0]);
            $user->load(['roles']);
            $this->reset();
            $this->mount();
            return $user->toArray();
        } else {
            return false;
        }
    }

    public function limpiar()
    {
        $this->reset();
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.sedes.sedes')->title('Sedes de trabajo');
    }
}
