<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Curso;
use App\Models\CursoAsignacion;
use App\Models\CursoMaterial;
use App\Models\CursoPregunta;
use App\Models\User;
use Spatie\Permission\Models\Role;

class CursoAsignaciones extends Component
{
    public $curso;
    public $tipo = 'usuario'; // usuario | rol
    public $user_id;
    public $rol_id;
    public $filtro = null;

    public function mount(Curso $curso)
    {
        $this->curso = $curso;
        $this->filtro = request('filtro'); // aprobado | reprobado | pendiente

    }

    public function getAsignaciones()
    {
        $this->skipRender();

        return CursoAsignacion::with('user.roles')
            ->where('curso_id', $this->curso->id)
            ->get()
            ->map(function ($a) {

                $rolesUsuario = $a->user
                    ? $a->user->roles->pluck('name')->join(', ')
                    : null;

                return [
                    'id' => $a->id,
                    'user' => $a->user,
                    'roles_usuario' => $rolesUsuario,
                    'rol_id' => $a->rol_id,
                    'rol_nombre' => $a->rol_id
                        ? Role::find($a->rol_id)?->name
                        : null,
                ];
            })
            ->toArray();
    }

    public function save()
    {
        $this->validate([
            'tipo' => 'required|in:usuario,rol',
            'user_id' => 'required_if:tipo,usuario',
            'rol_id' => 'required_if:tipo,rol',
        ]);

        /** 🔒 Validar curso activo */
        if (!$this->curso->activo) {
            $this->addError('tipo', 'No se pueden asignar cursos inactivos.');
            return false;
        }

        /** 🔒 Validar duplicado */
        $exists = CursoAsignacion::where('curso_id', $this->curso->id)
            ->where(function ($q) {
                if ($this->tipo === 'usuario') {
                    $q->where('user_id', $this->user_id);
                } else {
                    $q->where('rol_id', $this->rol_id);
                }
            })->exists();

        if ($exists) {
            $this->addError('dup', 'Esta asignación ya existe.');
            return false;
        }

        /** 🔒 Si asigna usuario, validar que no tenga acceso por rol */
        if ($this->tipo === 'usuario') {

            $user = User::with('roles')->find($this->user_id);

            $rolesAsignados = CursoAsignacion::where('curso_id', $this->curso->id)
                ->whereNotNull('rol_id')
                ->pluck('rol_id')
                ->toArray();

            $rolesUsuario = $user->roles->pluck('id')->toArray();

            if (count(array_intersect($rolesAsignados, $rolesUsuario)) > 0) {
                $this->addError('user_id', 'Este usuario ya tiene acceso por su rol.');
                return false;
            }
        }

        /** 🔒 Si asigna rol, validar que no existan usuarios de ese rol */
        if ($this->tipo === 'rol') {

            $usuariosAsignados = CursoAsignacion::where('curso_id', $this->curso->id)
                ->whereNotNull('user_id')
                ->pluck('user_id')
                ->toArray();

            if (!empty($usuariosAsignados)) {

                $rol = Role::find($this->rol_id);

                $usuariosConRol = User::role($rol->name)
                    ->whereIn('id', $usuariosAsignados)
                    ->exists();

                if ($usuariosConRol) {
                    $this->addError(
                        'rol_id',
                        'Hay usuarios de este rol asignados individualmente. Elimínelos primero.'
                    );
                    return false;
                }
            }
        }

        $a = CursoAsignacion::create([
            'curso_id' => $this->curso->id,
            'user_id' => $this->tipo === 'usuario' ? $this->user_id : null,
            'rol_id' => $this->tipo === 'rol' ? $this->rol_id : null,
        ]);

        $this->reset(['user_id', 'rol_id']);
        $this->resetValidation();

        return [
            'id' => $a->id,
            'user' => $a->user?->load('roles'),
            'roles_usuario' => $a->user
                ? $a->user->roles->pluck('name')->join(', ')
                : null,
            'rol_nombre' => $a->rol_id ? Role::find($a->rol_id)?->name : null,
        ];
    }

    public function eliminar($id)
    {
        $a = CursoAsignacion::find($id);
        if ($a) {
            $a->delete();
            return true;
        }
        return false;
    }

    public function render()
    {
        // return view('livewire.admin.curso-asignaciones', [
        //     'usuarios' => User::with('roles')->orderBy('name')->get(),
        //     'roles' => Role::orderBy('name')->get(),
        // ])->title('Asignaciones de Curso');
        return view('livewire.admin.curso-asignaciones', [
            'usuarios' => User::with('roles')->orderBy('name')->get(),
            'roles' => Role::orderBy('name')->get(),
            'tieneMateriales' => CursoMaterial::where('curso_id', $this->curso->id)->exists(),
            'tienePreguntas' =>  CursoPregunta::where('curso_id', $this->curso->id)->exists(),
        ])->title('Asignaciones de Curso');
    }
}
