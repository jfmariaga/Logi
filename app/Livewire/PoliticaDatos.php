<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class PoliticaDatos extends Component
{
    public function aceptarPolitica()
    {
        $user = Auth::user();

        if ($user->acepto_politica_datos) {
            return;
        }

        $user->update([
            'acepto_politica_datos' => true,
            'fecha_acepto_politica' => now(),
        ]);

        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.politica-datos');
    }
}
