<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class FormularioConocimientoColaboradoresSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'SuperAdmin' => ['ver formulario colaboradores', 'ver listado colaboradores', 'ver formularios colaboradores', 'aprobar formularios colaboradores'],
            'Administrativo' => ['ver formulario colaboradores', 'ver listado colaboradores', 'ver formularios colaboradores'],
            'Operario' => ['ver formulario colaboradores'],
        ];

        foreach ($roles as $roleName => $permissionNames) {
            $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();

            if (!$role) {
                continue;
            }

            foreach ($permissionNames as $permissionName) {
                $permission = Permission::firstOrCreate([
                    'name' => $permissionName,
                    'guard_name' => 'web',
                ]);

                if (!$role->hasPermissionTo($permission)) {
                    $role->givePermissionTo($permission);
                }
            }
        }
    }
}
