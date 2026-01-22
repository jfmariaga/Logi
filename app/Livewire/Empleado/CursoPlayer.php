<?php

namespace App\Livewire\Empleado;

use Livewire\Component;
use App\Models\Curso;
use App\Models\CursoMaterial;
use App\Models\CursoProgreso;
use App\Models\CursoIntento;
use Illuminate\Support\Facades\Auth;

class CursoPlayer extends Component
{
    public $curso;
    public $materiales;
    public $materialActual;

    public $progreso;

    public $intentosUsados = 0;
    public $mejorNota = null;
    public $yaAprobo = false;

    public $puedeEvaluar = false;
    public $mensajeBloqueo = null;

    public $embedYoutube = null;

    public function mount(Curso $curso)
    {
        $this->curso = $curso;

        // 📚 Materiales en orden
        $this->materiales = CursoMaterial::where('curso_id', $curso->id)
            ->orderBy('orden')
            ->get();

        $this->materialActual = $this->materiales->first();

        if ($this->materialActual?->tipo === 'link') {
            $this->embedYoutube = $this->youtubeEmbed($this->materialActual->url);
        }

        // 📈 Progreso
        $this->progreso = CursoProgreso::firstOrCreate(
            [
                'curso_id' => $curso->id,
                'user_id' => Auth::id(),
            ],
            [
                'fecha_inicio' => now(),
            ]
        );

        // 📝 Intentos
        $intentos = CursoIntento::where('curso_id', $curso->id)
            ->where('user_id', Auth::id())
            ->get();

        $this->intentosUsados = $intentos->count();
        $this->mejorNota = $intentos->max('nota');
        $this->yaAprobo = $intentos->where('aprobado', true)->count() > 0;

        $this->verificarEvaluacion();
    }

    /* ================= MATERIAL ================= */

    public function cambiarMaterial($id)
    {
        $this->materialActual = $this->materiales->firstWhere('id', $id);
        $this->embedYoutube = null;

        if ($this->materialActual?->tipo === 'link') {
            $this->embedYoutube = $this->youtubeEmbed($this->materialActual->url);
        }

        // ✅ Si llegó al último material → marcar como completado
        if (
            $this->materialActual &&
            $this->materialActual->id === $this->materiales->last()?->id
        ) {
            if (!$this->progreso->fecha_materiales_completados) {
                $this->progreso->update([
                    'fecha_materiales_completados' => now(),
                ]);
            }
        }

        $this->verificarEvaluacion();
    }

    /* ================= EVALUACIÓN ================= */

    protected function verificarEvaluacion()
    {
        $this->puedeEvaluar = false;
        $this->mensajeBloqueo = null;

        if (!$this->progreso->fecha_materiales_completados) {
            $this->mensajeBloqueo = 'Debe completar todo el contenido antes de presentar la evaluación.';
            return;
        }

        if ($this->yaAprobo) {
            $this->mensajeBloqueo = 'Ya aprobó este curso. No es necesario volver a presentar la evaluación.';
            return;
        }

        if ($this->intentosUsados >= $this->curso->max_intentos) {
            $this->mensajeBloqueo = 'Ha agotado el número máximo de intentos permitidos.';
            return;
        }

        $this->puedeEvaluar = true;
    }

    // public function iniciarEvaluacion()
    // {
    //     if (!$this->puedeEvaluar) return;

    //     $numero = $this->intentosUsados + 1;

    //     $intento = CursoIntento::create([
    //         'curso_id' => $this->curso->id,
    //         'user_id' => Auth::id(),
    //         'intento_numero' => $numero,
    //         'fecha_inicio' => now(),
    //     ]);

    //     return redirect()->to(
    //         route('mis-cursos.evaluacion', $intento->id)
    //     );
    // }

    public function iniciarEvaluacion()
    {
        if (!$this->puedeEvaluar) return;

        $numero = $this->intentosUsados + 1;


        // Evitar múltiples intentos simultáneos
        // $ultimo = CursoIntento::where('curso_id', $this->curso->id)
        //     ->where('user_id', Auth::id())
        //     ->orderByDesc('intento_numero')
        //     ->first();

        // if ($ultimo && $ultimo->fecha_fin && now()->lessThan($ultimo->fecha_fin)) {
        //     // ya hay un intento activo
        //     return;
        // }

        $intento = CursoIntento::create([
            'curso_id' => $this->curso->id,
            'user_id' => Auth::id(),
            'intento_numero' => $numero,
            'fecha_inicio' => now(),
            'fecha_fin' => now()->addMinutes($this->curso->tiempo_minutos),
        ]);

        return redirect()->route('mis-cursos.evaluacion', $intento->id);
    }

    /* ================= YOUTUBE ================= */

    private function youtubeEmbed($url)
    {
        if (str_contains($url, 'youtu.be/')) {
            $id = explode('youtu.be/', $url)[1];
        } elseif (str_contains($url, 'watch?v=')) {
            parse_str(parse_url($url, PHP_URL_QUERY), $vars);
            $id = $vars['v'] ?? null;
        } else {
            return null;
        }

        return $id ? "https://www.youtube.com/embed/" . $id : null;
    }

    public function render()
    {
        return view('livewire.empleado.curso-player')
            ->title('Curso - ' . $this->curso->titulo);
    }
}
