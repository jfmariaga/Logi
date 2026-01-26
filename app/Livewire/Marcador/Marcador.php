<?php

namespace App\Livewire\Marcador;

use App\Helpers\Geo;
use App\Models\Marcacion;
use App\Models\Sede;
use App\Models\User;
use App\Models\Jornada;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class Marcador extends Component
{
    use WithFileUploads;

    public $documento = '';
    public $usuario = null;
    public $estadoActual = 'libre';
    public $modoTerminal = true;
    public $mensaje = null;
    public $ultimaEntradaFecha = null;
    public $registrando = false;

    /** 📸 selfie */
    public $selfie;

    public function mount()
    {
        if (Auth::check()) {
            $this->modoTerminal = false;
            $this->usuario = Auth::user();
            $this->cargarEstadoUsuario();
        }
    }

    /** 🔎 búsqueda por documento */
    public function updatedDocumento($value)
    {
        if (strlen($value) < 4) {
            $this->reset(['usuario', 'mensaje', 'ultimaEntradaFecha']);
            return;
        }

        $this->usuario = User::where('document', trim($value))->first();

        if (!$this->usuario) {
            $this->mensaje = 'Documento no encontrado';
            return;
        }

        $this->mensaje = null;
        $this->cargarEstadoUsuario();
    }

    private function cargarEstadoUsuario()
    {
        if (!$this->usuario) return;

        $ultima = Marcacion::where('user_id', $this->usuario->id)
            ->latest('fecha_hora')->first();

        $this->estadoActual = $ultima?->tipo === 'entrada'
            ? 'trabajando'
            : 'libre';

        $ultimaEntrada = Marcacion::where('user_id', $this->usuario->id)
            ->where('tipo', 'entrada')
            ->latest('fecha_hora')->first();

        $this->ultimaEntradaFecha = $ultimaEntrada?->fecha_hora;
    }

    /** 👉 click botón */
    public function marcar($tipo)
    {
        if (!$this->usuario || $this->registrando) return;

        if ($tipo === 'entrada' && $this->estadoActual === 'trabajando') {
            $this->dispatch('toast-ok', msg: 'Ya tienes una jornada activa');
            return;
        }

        if ($tipo === 'salida' && $this->estadoActual === 'libre') {
            $this->dispatch('toast-ok', msg: 'No hay jornada activa para cerrar');
            return;
        }

        $this->registrando = true;

        /** 👉 primero selfie */
        $this->dispatch('abrir-selfie', tipo: $tipo);
    }

    /** 📸 selfie confirmada → pedir GPS */
    #[On('selfie-capturada')]
    public function selfieCapturada($tipo)
    {

        $this->validate([
            'selfie' => 'required|image|max:4096'
        ]);

        /** 👉 GPS igual que antes */
        $this->dispatch('capturar-ubicacion', tipo: $tipo);
    }

    /** 📍 validar GPS */
    #[On('validar-ubicacion')]
    public function validarUbicacion($lat, $lng, $tipo)
    {
        $sedes = Sede::where('activo', true)->get();

        $sedeCercana = null;
        $distanciaMinima = null;

        foreach ($sedes as $sede) {
            $distancia = Geo::distancia(
                $sede->latitud,
                $sede->longitud,
                $lat,
                $lng
            );

            if ($distanciaMinima === null || $distancia < $distanciaMinima) {
                $distanciaMinima = $distancia;
                $sedeCercana = $sede;
            }
        }

        $enSitio = $distanciaMinima <= $sedeCercana->radio_metros;

        if (!$enSitio) {
            $this->registrando = false;

            $this->dispatch(
                'confirmar-fuera-sede',
                lat: $lat,
                lng: $lng,
                tipo: $tipo,
                sede: $sedeCercana->nombre,
                distancia: round($distanciaMinima)
            );
            return;
        }

        $this->registrar($lat, $lng, $tipo);
    }

    /** confirmado fuera de sede */
    #[On('ubicacion-capturada')]
    public function ubicacionCapturada($lat, $lng, $tipo)
    {
        $this->registrar($lat, $lng, $tipo);
    }

    #[On('liberar-boton')]
    public function liberarBoton()
    {
        $this->registrando = false;
    }

    /** guardar */
    private function registrar($lat, $lng, $tipo)
    {
        // dd($tipo);
        $sedes = Sede::where('activo', true)->get();

        $sedeCercana = null;
        $distanciaMinima = null;

        foreach ($sedes as $sede) {
            $distancia = Geo::distancia(
                $sede->latitud,
                $sede->longitud,
                $lat,
                $lng
            );

            if ($distanciaMinima === null || $distancia < $distanciaMinima) {
                $distanciaMinima = $distancia;
                $sedeCercana = $sede;
            }
        }

        $enSitio = $distanciaMinima <= $sedeCercana->radio_metros;

        /** 📸 guardar selfie */
        $fotoPath = $this->selfie->store('marcaciones/selfies', 'public');

        Marcacion::create([
            'user_id' => $this->usuario->id,
            'sede_id' => $sedeCercana->id,
            'tipo' => $tipo,
            'latitud' => $lat,
            'longitud' => $lng,
            'distancia_metros' => round($distanciaMinima),
            'en_sitio' => $enSitio,
            'foto' => $fotoPath,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'fecha_hora' => now(),
        ]);

        /** jornadas */
        if ($tipo === 'entrada') {

            Jornada::create([
                'user_id' => $this->usuario->id,
                'sede_id' => $sedeCercana->id,
                'inicio' => now(),
                'fuera_sede' => !$enSitio,
                'cerrada' => false,
            ]);

            $this->ultimaEntradaFecha = now();
        }

        if ($tipo === 'salida') {

            $jornada = Jornada::where('user_id', $this->usuario->id)
                ->where('cerrada', false)
                ->latest('inicio')
                ->first();

            if ($jornada) {

                $minutos = Carbon::parse($jornada->inicio)->diffInMinutes(now());
                $saleEnOtraSede = $jornada->sede_id != $sedeCercana->id;

                $jornada->update([
                    'fin' => now(),
                    'minutos_trabajados' => $minutos,
                    'cerrada' => true,
                    'sede_salida_id' => $sedeCercana->id,
                    'salida_fuera_sede' => !$enSitio || $saleEnOtraSede,
                ]);
            }
        }

        $this->estadoActual = $tipo === 'entrada' ? 'trabajando' : 'libre';

        $this->dispatch(
            'toast-ok',
            msg: $enSitio
                ? "Marcación registrada en {$sedeCercana->nombre}"
                : "Marcación registrada fuera de sede"
        );

        $this->registrando = false;
        $this->reset('selfie');

        if ($this->modoTerminal) {
            $this->cargarEstadoUsuario();
            $this->reset(['documento', 'usuario']);
        }
    }

    public function render()
    {
        return view('livewire.marcador.marcador');
    }
}
