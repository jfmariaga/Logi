<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Curso;
use App\Models\User;
use App\Models\CursoAsignacion;
use App\Models\CursoIntento;

class CursoResultados extends Component
{
    public $curso;

    public function mount(Curso $curso)
    {
        $this->curso = $curso;
    }

    /* ================= RESUMEN ================= */

    public function getResumen()
    {
        $this->skipRender();

        // ===== USUARIOS ASIGNADOS =====

        $asignadosUser = CursoAsignacion::where('curso_id', $this->curso->id)
            ->whereNotNull('user_id')
            ->pluck('user_id');

        $roles = CursoAsignacion::where('curso_id', $this->curso->id)
            ->whereNotNull('rol_id')
            ->pluck('rol_id');

        $usuariosPorRol = User::whereHas('roles', function ($q) use ($roles) {
            $q->whereIn('id', $roles);
        })->pluck('id');

        $usuariosIds = $asignadosUser->merge($usuariosPorRol)->unique();

        $inscritos = $usuariosIds->count();

        $usuariosConIntentos = CursoIntento::where('curso_id', $this->curso->id)
            ->whereIn('user_id', $usuariosIds)
            ->select('user_id')
            ->distinct()
            ->pluck('user_id');

        $iniciaron = $usuariosConIntentos->count();

        $aprobados = CursoIntento::where('curso_id', $this->curso->id)
            ->whereIn('user_id', $usuariosIds)
            ->where('aprobado', true)
            ->select('user_id')
            ->distinct()
            ->pluck('user_id');

        $aprobadosCount = $aprobados->count();

        $noAprobadosCount = $usuariosConIntentos
            ->diff($aprobados)
            ->count();

        $pendientes = $usuariosIds
            ->diff($usuariosConIntentos)
            ->count();

        return [
            'inscritos' => $inscritos,
            'iniciaron' => $iniciaron,
            'pendientes' => max($pendientes, 0),
            'aprobados' => $aprobadosCount,
            'no_aprobados' => $noAprobadosCount,
        ];
    }


    /* ================= TABLA ================= */

    public function getResultados()
    {
        $this->skipRender();

        /* ===== MISMA LOGICA DE INSCRITOS ===== */

        $asignadosUser = CursoAsignacion::where('curso_id', $this->curso->id)
            ->whereNotNull('user_id')
            ->pluck('user_id');

        $roles = CursoAsignacion::where('curso_id', $this->curso->id)
            ->whereNotNull('rol_id')
            ->pluck('rol_id');

        $usuariosPorRol = User::whereHas('roles', function ($q) use ($roles) {
            $q->whereIn('id', $roles);
        })->pluck('id');

        $usuariosIds = $asignadosUser->merge($usuariosPorRol)->unique();

        /* ===== TRAER TODOS LOS USUARIOS INSCRITOS ===== */

        $usuarios = User::whereIn('id', $usuariosIds)
            ->with([
                'cursoIntentos' => function ($q) {
                    $q->where('curso_id', $this->curso->id)
                        ->orderByDesc('intento_numero');
                },
                'roles'
            ])
            ->get();

        /* ===== MAPEAR RESULTADOS ===== */

        return $usuarios->map(function ($u) {

            $intentos = $u->cursoIntentos;
            $ultimo = $intentos->first();

            $estado = 'pendiente';

            if ($intentos->count() > 0) {
                if ($ultimo?->aprobado) {
                    $estado = 'aprobado';
                } else {
                    $estado = 'no_aprobado';
                }
            }

            return [
                'id' => $u->id,
                'nombre' => $u->name,
                'rol' => $u->roles->pluck('name')->join(', '),
                'intentos' => $intentos->count(),
                'nota' => $ultimo?->nota,
                'aprobado' => $ultimo?->aprobado,
                'estado' => $estado,
                'fecha' => $ultimo?->fecha_fin?->format('Y-m-d H:i'),
            ];
        })->toArray();
    }


    public function render()
    {
        return view('livewire.admin.curso-resultados')
            ->title('Resultados - ' . $this->curso->titulo);
    }
}
