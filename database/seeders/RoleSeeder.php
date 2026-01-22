<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Deshabilitar las verificaciones de claves foráneas
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncar las tablas relacionadas con roles y permisos
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('roles')->truncate();
        DB::table('permissions')->truncate();

        // Habilitar nuevamente las verificaciones de claves foráneas
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Crear roles
        $roleSuperAdmin = Role::create(['name' => 'SuperAdmin']);
        $Gerente        = Role::create(['name' => 'Gerente']);
        $analista       = Role::create(['name' => 'Analista']);
        $contadora      = Role::create(['name' => 'Contadora']);
        $sarlaft        = Role::create(['name' => 'Sarlaft']);
        $gestion        = Role::create(['name' => 'Gestion humana']);
        $seguridad      = Role::create(['name' => 'Seguridad y salud en el trabajo']);
        $operado        = Role::create(['name' => 'Operador']);

    }
}
