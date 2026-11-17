<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AgregarPermisoEliminarFormulariosSeeder extends Seeder
{
    public function run()
    {
        $permiso = Permission::firstOrCreate([
            'name' => 'eliminar formularios',
            'guard_name' => 'web',
        ]);

        $role = Role::where('name', 'SuperAdmin')->first();

        if ($role) {
            $permiso->syncRoles([$role]);
            $this->command->info('Permiso "eliminar formularios" creado y asignado a SuperAdmin.');
        } else {
            $this->command->warn('No se encontró el rol SuperAdmin.');
        }
    }
}
