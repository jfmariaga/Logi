<?php

namespace App\Livewire\Empleado;

use Livewire\Component;
use App\Models\CursoIntento;
use App\Models\CursoPregunta;
use App\Models\CursoRespuestaUsuario;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class CursoEvaluacion extends Component
{
    public $intento;
    public $curso;
    public $preguntas;
    public $respuestasUsuario = []; // [pregunta_id => respuesta_id]
    public $finalizado = false;
    public $nota = null;
    public $aprobado = false;
    public $segundosRestantes = 0;

    public function mount(CursoIntento $intento)
    {
        abort_if($intento->user_id !== Auth::id(), 403);

        $this->intento = $intento;
        $this->curso = $intento->curso;

        $this->segundosRestantes = max(
            now()->diffInSeconds($intento->fecha_fin, false),
            0
        );

        if ($this->segundosRestantes <= 0) {
            $this->finalizarPorTiempo();
        }

        $this->preguntas = CursoPregunta::with('respuestas')
            ->where('curso_id', $this->curso->id)
            ->get();
    }

    #[On('tiempoAgotado')]
    public function tiempoAgotado()
    {
        $this->enviar();

    }

    /* ================= ENVIAR ================= */

    public function enviar()
    {
        if ($this->finalizado) return;

        $this->validate([
            'respuestasUsuario' => 'required|array|min:1',
        ]);

        $total = $this->preguntas->count();
        $correctas = 0;

        foreach ($this->preguntas as $p) {

            $respuestaId = $this->respuestasUsuario[$p->id] ?? null;
            if (!$respuestaId) continue;

            $respuesta = $p->respuestas->firstWhere('id', $respuestaId);

            $esCorrecta = $respuesta?->es_correcta ?? false;

            if ($esCorrecta) $correctas++;

            CursoRespuestaUsuario::create([
                'intento_id' => $this->intento->id,
                'pregunta_id' => $p->id,
                'respuesta_id' => $respuestaId,
                'es_correcta' => $esCorrecta,
            ]);
        }

        // 📊 nota sobre 5
        $nota = round(($correctas / $total) * 5, 2);

        $aprobado = $nota >= $this->curso->nota_minima;

        $this->intento->update([
            'nota' => $nota,
            'aprobado' => $aprobado,
            'fecha_fin' => now(),
        ]);

        $this->nota = $nota;
        $this->aprobado = $aprobado;
        $this->finalizado = true;
    }

    public function render()
    {
        return view('livewire.empleado.curso-evaluacion')
            ->title('Evaluación - ' . $this->curso->titulo);
    }
}
