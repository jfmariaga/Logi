<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Curso;
use App\Models\CursoPregunta;
use App\Models\CursoRespuesta;

class CursoPreguntas extends Component
{
    public $curso;
    public $preguntas = [];

    public $pregunta_id;
    public $pregunta = '';
    public $tipo = 'opcion_multiple';
    public $respuestas = [];

    protected $rules = [
        'pregunta' => 'required|min:5',
        'tipo' => 'required|in:opcion_multiple,verdadero_falso',
        'respuestas' => 'required|array|min:2',
        'respuestas.*.texto' => 'required|min:1',
    ];

    public function mount($curso_id)
    {
        $this->curso = Curso::findOrFail($curso_id);
    }

    public function getPreguntas()
    {
        $this->skipRender();

        return CursoPregunta::with('respuestas')
            ->where('curso_id', $this->curso->id)
            ->get()
            ->toArray();
    }

    public function save()
    {
        $this->validate();

        // ✅ debe haber exactamente una correcta
        $correctas = collect($this->respuestas)->where('correcta', true)->count();
        if ($correctas !== 1) {
            $this->addError('respuestas', 'Debe existir una sola respuesta correcta');
            return false;
        }

        if ($this->pregunta_id) {

            $p = CursoPregunta::findOrFail($this->pregunta_id);

            $p->update([
                'pregunta' => $this->pregunta,
                'tipo' => $this->tipo,
            ]);

            $p->respuestas()->delete();

        } else {

            $p = CursoPregunta::create([
                'curso_id' => $this->curso->id,
                'pregunta' => $this->pregunta,
                'tipo' => $this->tipo,
            ]);
        }

        foreach ($this->respuestas as $r) {
            CursoRespuesta::create([
                'pregunta_id' => $p->id,
                'respuesta' => $r['texto'],
                'es_correcta' => $r['correcta'],
            ]);
        }

        $this->limpiar();

        return $p->load('respuestas')->toArray();
    }

    public function eliminar($id)
    {
        $p = CursoPregunta::find($id);
        if ($p) {
            $p->delete();
            return true;
        }
        return false;
    }

    public function limpiar()
    {
        $this->reset(['pregunta_id','pregunta','respuestas']);
        $this->tipo = 'opcion_multiple';
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.curso-preguntas')
            ->title('Preguntas del Curso');
    }
}
