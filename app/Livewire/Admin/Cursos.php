<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Curso;
use App\Models\CursoAsignacion;
use App\Models\CursoIntento;
use App\Models\User;

class Cursos extends Component
{
    public $cursos = [];
    public $curso_id, $titulo, $descripcion, $fecha_inicio, $fecha_fin, $activo;
    public $max_intentos, $tiempo_minutos, $nota_minima;

    protected function rules()
    {
        return [
            'titulo' => 'required|string|max:255',
            'activo' => 'required|boolean',
            'max_intentos' => 'required|integer|min:1',
            'tiempo_minutos' => 'nullable|integer|min:1',
            'nota_minima' => 'required|numeric|min:0|max:5',
        ];
    }

    // public function getCursos()
    // {
    //     $this->skipRender();

    //     return Curso::withCount([
    //         // inscritos (asignados por usuario o rol)
    //         'asignaciones as inscritos',

    //         // usuarios que hicieron al menos un intento
    //         'intentos as iniciaron' => function ($q) {
    //             $q->select(\DB::raw('count(distinct user_id)'));
    //         },

    //         // aprobados
    //         'intentos as aprobados' => function ($q) {
    //             $q->where('aprobado', 1)
    //                 ->select(\DB::raw('count(distinct user_id)'));
    //         },



    //         // reprobados (último intento no aprobado)
    //     ])
    //         ->orderByDesc('id')
    //         ->get()
    //         ->map(function ($c) {

    //             $reprobados = CursoIntento::where('curso_id', $c->id)
    //                 ->select('user_id')
    //                 ->groupBy('user_id')
    //                 ->havingRaw('MAX(aprobado) = 0')
    //                 ->count();

    //             $faltan = max(($c->inscritos ?? 0) - (($c->aprobados ?? 0) + $reprobados), 0);


    //             return [
    //                 'id' => $c->id,
    //                 'titulo' => $c->titulo,
    //                 'descripcion' => $c->descripcion,
    //                 'fecha_inicio' => $c->fecha_inicio,
    //                 'fecha_fin' => $c->fecha_fin,
    //                 'activo' => $c->activo,
    //                 'inscritos' => $c->inscritos ?? 0,
    //                 'aprobados' => $c->aprobados ?? 0,
    //                 'reprobados' => $reprobados,
    //                 'faltan' => $faltan,
    //             ];
    //         })
    //         ->toArray();
    // }

    public function getCursos()
    {
        $this->skipRender();

        return Curso::orderByDesc('id')->get()->map(function ($c) {

            /* ===== INSCRITOS ===== */

            $asignadosUser = CursoAsignacion::where('curso_id', $c->id)
                ->whereNotNull('user_id')
                ->pluck('user_id');

            $roles = CursoAsignacion::where('curso_id', $c->id)
                ->whereNotNull('rol_id')
                ->pluck('rol_id');

            $usuariosPorRol = User::whereHas('roles', function ($q) use ($roles) {
                $q->whereIn('id', $roles);
            })->pluck('id');

            $usuariosIds = $asignadosUser->merge($usuariosPorRol)->unique();

            $inscritos = $usuariosIds->count();

            /* ===== USUARIOS CON INTENTOS ===== */

            $usuariosConIntentos = CursoIntento::where('curso_id', $c->id)
                ->whereIn('user_id', $usuariosIds)
                ->select('user_id')
                ->distinct()
                ->pluck('user_id');

            $iniciaron = $usuariosConIntentos->count();

            /* ===== APROBADOS ===== */

            $aprobadosIds = CursoIntento::where('curso_id', $c->id)
                ->whereIn('user_id', $usuariosIds)
                ->where('aprobado', true)
                ->select('user_id')
                ->distinct()
                ->pluck('user_id');

            $aprobados = $aprobadosIds->count();

            /* ===== REPROBADOS (agotaron intentos y ninguno aprobado) ===== */

            $reprobados = 0;

            // foreach ($usuariosConIntentos as $uid) {

            //     if ($aprobadosIds->contains($uid)) continue;

            //     $intentos = CursoIntento::where('curso_id', $c->id)
            //         ->where('user_id', $uid)
            //         ->count();

            //     if ($intentos >= $c->max_intentos) {
            //         $reprobados++;
            //     }
            // }

            $reprobados = CursoIntento::where('curso_id', $c->id)
                ->select('user_id')
                ->groupBy('user_id')
                ->havingRaw('MAX(aprobado) = 0')
                ->count();

            /* ===== PENDIENTES ===== */

            $pendientes = max($inscritos - ($aprobados + $reprobados), 0);

            return [
                'id' => $c->id,
                'titulo' => $c->titulo,
                'descripcion' => $c->descripcion,
                'fecha_inicio' => $c->fecha_inicio,
                'fecha_fin' => $c->fecha_fin,
                'activo' => $c->activo,

                'inscritos' => $inscritos,
                'aprobados' => $aprobados,
                'reprobados' => $reprobados,
                'faltan' => $pendientes,
            ];
        })->toArray();
    }


    public function save()
    {
        $this->validate([
            'titulo' => 'required|string|max:255',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'activo' => 'required|boolean',
        ]);

        if ($this->curso_id) {
            $curso = Curso::find($this->curso_id);

            if ($curso) {
                $curso->update([
                    'titulo' => $this->titulo,
                    'descripcion' => $this->descripcion,
                    'fecha_inicio' => $this->fecha_inicio,
                    'fecha_fin' => $this->fecha_fin,
                    'activo' => $this->activo,
                    'max_intentos' => $this->max_intentos,
                    'tiempo_minutos' => $this->tiempo_minutos,
                    'nota_minima' => $this->nota_minima,
                ]);
            }
        } else {
            $curso = Curso::create([
                'titulo' => $this->titulo,
                'descripcion' => $this->descripcion,
                'fecha_inicio' => $this->fecha_inicio,
                'fecha_fin' => $this->fecha_fin,
                'activo' => $this->activo,
                'max_intentos' => $this->max_intentos,
                'tiempo_minutos' => $this->tiempo_minutos,
                'nota_minima' => $this->nota_minima,
            ]);
        }

        if ($curso) {
            $this->limpiar();
            return $curso->toArray();
        } else {
            return false;
        }
    }

    public function desactivar($id)
    {
        $curso = Curso::find($id);

        if ($curso) {
            $curso->update(['activo' => 0]);
            return $curso->toArray();
        }

        return false;
    }

    public function limpiar()
    {
        $this->reset();
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.cursos')->title('Cursos');
    }
}
