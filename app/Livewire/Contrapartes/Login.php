<?php

namespace App\Livewire\Contrapartes;

use Livewire\Component;
use App\Models\Tercero;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class Login extends Component
{
    public $identificacion;
    public $password;
    public $tipo;

    public $terceroEncontrado = null;

    protected $rules = [
        'tipo' => 'required',
        'identificacion' => 'required',
        'password' => 'required|min:4'
    ];

    protected $messages = [
        'tipo.required' => 'Debe seleccionar el tipo de persona',
        'identificacion.required' => 'Ingrese identificación',
        'password.required' => 'Ingrese una contraseña'
    ];

    protected $listeners = ['confirmarBorrado' => 'borrado'];

    public function ingresar()
    {
        $this->validate();

        $tercero = Tercero::where('identificacion', $this->identificacion)->first();

        // Si no existe → crear nuevo tercero
        if (!$tercero) {

            $tercero = Tercero::create([
                'identificacion' => $this->identificacion,
                'password' => Hash::make($this->password),
                'tipo' => $this->tipo,
                'estado' => 'en_proceso'
            ]);

            session(['tercero_id' => $tercero->id]);

            $this->dispatch('bienvenida');

            return redirect()->route('contrapartes.formulario');
        }

        // Existe pero contraseña incorrecta
        if (!Hash::check($this->password, $tercero->password)) {

            // NO permitir borrar si ya fue enviado o aprobado
            if (in_array($tercero->estado, ['enviado', 'aprobado'])) {
                $this->dispatch(
                    'toast-error',
                    msg: 'El formulario ya fue radicado. No puede reiniciarse. Contacte a Logisticarga para realizar modificaciones.'
                );
                return;
            }

            // solo en proceso o rechazado
            $this->terceroEncontrado = $tercero;
            $this->dispatch('confirmar-borrado');
            return;
        }


        // Credenciales correctas y ya tiene progreso
        session(['tercero_id' => $tercero->id]);

        $this->dispatch('continuar-proceso');

        return redirect()->route('contrapartes.formulario');
    }

    private function eliminarTerceroCompleto(Tercero $tercero)
    {
        foreach ($tercero->documentos as $doc) {
            if ($doc->archivo && Storage::exists($doc->archivo)) {
                Storage::delete($doc->archivo);
            }
        }

        if ($tercero->firma && $tercero->firma->archivo) {
            if (Storage::exists($tercero->firma->archivo)) {
                Storage::delete($tercero->firma->archivo);
            }
        }

        $tercero->delete();
    }

    public function borrado()
    {
        if ($this->terceroEncontrado) {
            $this->eliminarTerceroCompleto($this->terceroEncontrado);
        }

        $nuevoTercero = Tercero::create([
            'identificacion' => $this->identificacion,
            'password' => Hash::make($this->password),
            'tipo' => $this->tipo,
            'estado' => 'en_proceso'
        ]);

        session(['tercero_id' => $nuevoTercero->id]);

        $this->dispatch('toast-ok', msg: 'Proceso anterior eliminado. Iniciando uno nuevo.');

        return redirect()->route('contrapartes.formulario');
    }

    public function render()
    {
        return view('livewire.contrapartes.login')
            ->layout('components.layouts.contrapartes-externo');
    }
}
