<?php

namespace App\Livewire\Empleado;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Curso;
use App\Models\CursoAsignacion;
use App\Models\CursoIntento;

class MisCursos extends Component
{
    public $cursos = [];

    public function mount()
    {
        $this->loadCursos();
    }

    public function loadCursos()
    {
        $user = Auth::user();
        $roles = $user->roles->pluck('id');

        // cursos asignados
        $cursoIdsAsignados = CursoAsignacion::where('user_id', $user->id)
            ->orWhereIn('rol_id', $roles)
            ->pluck('curso_id');

        // cursos donde ya tuvo intentos (historial)
        $cursoIdsConIntentos = CursoIntento::where('user_id', $user->id)
            ->pluck('curso_id');

        // unir ambos
        $cursoIdsFinales = $cursoIdsAsignados
            ->merge($cursoIdsConIntentos)
            ->unique();

        $this->cursos = Curso::whereIn('id', $cursoIdsFinales)
            ->withCount(['preguntas', 'materiales'])
            ->get()
            ->map(function ($c) use ($user) {

                $intentos = CursoIntento::where('curso_id', $c->id)
                    ->where('user_id', $user->id)
                    ->count();

                $ultimo = CursoIntento::where('curso_id', $c->id)
                    ->where('user_id', $user->id)
                    ->latest()
                    ->first();

                return [
                    'id' => $c->id,
                    'titulo' => $c->titulo,
                    'descripcion' => $c->descripcion,
                    'materiales' => $c->materiales_count,
                    'preguntas' => $c->preguntas_count,
                    'max_intentos' => $c->max_intentos,
                    'intentos' => $intentos,
                    'aprobado' => $ultimo?->aprobado,
                    'nota' => $ultimo?->nota,
                    'activo' => $c->activo,
                    'finalizado' => $ultimo?->fecha_fin !== null,
                ];
            })
            ->toArray();
    }


    public function render()
    {
        return view('livewire.empleado.mis-cursos')
            ->title('Mis Cursos');
    }
}
