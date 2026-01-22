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

class Marcador extends Component
{
    public $documento = '';
    public $usuario = null;
    public $latitud;
    public $longitud;
    public $estadoActual = 'libre';
    public $modoTerminal = true;
    public $mensaje = null;
    public $ultimaEntradaFecha = null;

    public $registrando = false;

    public function mount()
    {
        if (Auth::check()) {
            $this->modoTerminal = false;
            $this->usuario = Auth::user();
            $this->cargarEstadoUsuario();
        }
    }

    /**
     * 🔎 Búsqueda automática por documento
     */
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


    /*
     *  Click botón
     */
    public function marcar($tipo)
    {
        if (!$this->usuario || $this->registrando) return;

        // VALIDACIONES DE ESTADO (CLAVE)
        if ($tipo === 'entrada' && $this->estadoActual === 'trabajando') {
            $this->dispatch('toast-ok', msg: 'Ya tienes una jornada activa');
            $this->registrando = false;
            return;
        }

        if ($tipo === 'salida' && $this->estadoActual === 'libre') {
            $this->dispatch('toast-ok', msg: 'No hay jornada activa para cerrar');
            $this->registrando = false;
            return;
        }

        $this->registrando = true;
        $this->dispatch('capturar-ubicacion', tipo: $tipo);
    }

    /**
     * Recibe GPS desde JS
     */
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

    /**
     *  Confirmado desde SweetAlert
     */
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


    /**
     * Guardar marcación
     */

    private function registrar($lat, $lng, $tipo)
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

        /* ============================
       ✅ 1. GUARDAR MARCACIÓN (igual que antes)
       ============================ */
        Marcacion::create([
            'user_id' => $this->usuario->id,
            'sede_id' => $sedeCercana->id,
            'tipo' => $tipo,
            'latitud' => $lat,
            'longitud' => $lng,
            'distancia_metros' => round($distanciaMinima),
            'en_sitio' => $enSitio,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'fecha_hora' => now(),
        ]);

        /* ============================
       ✅ 2. MANEJO DE JORNADAS (NUEVO)
       ============================ */

        if ($tipo === 'entrada') {

            // crear nueva jornada
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

            // cerrar última jornada abierta
            $jornada = Jornada::where('user_id', $this->usuario->id)
                ->where('cerrada', false)
                ->latest('inicio')
                ->first();

            if ($jornada) {

                $minutos = Carbon::parse($jornada->inicio)->diffInMinutes(now());

                // 👇 VALIDAR SI SALE EN OTRA SEDE
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

        /* ============================
       ✅ 3. ESTADO + MENSAJE (igual que antes)
       ============================ */

        $this->estadoActual = $tipo === 'entrada' ? 'trabajando' : 'libre';

        $this->dispatch(
            'toast-ok',
            msg: $enSitio
                ? "Marcación registrada en {$sedeCercana->nombre}"
                : "Marcación registrada fuera de sede"
        );

        $this->registrando = false;

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
