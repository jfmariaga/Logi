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
        $roleSuperAdmin      = Role::create(['name' => 'SuperAdmin']);
        $roleAdministrativo  = Role::create(['name' => 'Administrativo']);
        $roleOperario        = Role::create(['name' => 'Operario']);


        // Crear permisos para dashboard
        Permission::create(['name' => 'ver dashboard'])->syncRoles([$roleSuperAdmin, $roleAdministrativo, $roleOperario]);
        Permission::create(['name' => 'dashboard programación'])->syncRoles([$roleSuperAdmin]);

        //Crear permisos para personal
        Permission::create(['name' => 'ver Sección personal'])->syncRoles([$roleSuperAdmin, $roleAdministrativo, $roleOperario]);

        //Crear permisos para Empresas
        Permission::create(['name' => 'ver Sección Empresas'])->syncRoles([$roleSuperAdmin]);

        //Crear permisos para pagina web
        Permission::create(['name' => 'ver Sección página web'])->syncRoles([$roleSuperAdmin]);

        // Crear permisos para Programacion (CRUD completo)
        Permission::create(['name' => 'ver programación'])->syncRoles([$roleSuperAdmin]);
        Permission::create(['name' => 'crear programación'])->syncRoles([$roleSuperAdmin]);
        Permission::create(['name' => 'editar programación'])->syncRoles([$roleSuperAdmin]);
        Permission::create(['name' => 'eliminar programación'])->syncRoles([$roleSuperAdmin]);

        // Crear permisos para ver maraciones
        Permission::create(['name' => 'ver marcaciones'])->syncRoles([$roleSuperAdmin]);

        // Crear permisos para capacitaciones (CRUD completo)
        Permission::create(['name' => 'ver capacitaciones'])->syncRoles([$roleSuperAdmin]);
        Permission::create(['name' => 'crear capacitaciones'])->syncRoles([$roleSuperAdmin]);
        Permission::create(['name' => 'editar capacitaciones'])->syncRoles([$roleSuperAdmin]);
        Permission::create(['name' => 'eliminar capacitaciones'])->syncRoles([$roleSuperAdmin]);

        Permission::create(['name' => 'ver materiales'])->syncRoles([$roleSuperAdmin]);
        Permission::create(['name' => 'ver preguntas'])->syncRoles([$roleSuperAdmin]);
        Permission::create(['name' => 'ver asignaciones'])->syncRoles([$roleSuperAdmin]);
        Permission::create(['name' => 'ver resultados'])->syncRoles([$roleSuperAdmin]);

        // Crear permisos para ver mis cursos
        Permission::create(['name' => 'ver mis cursos'])->syncRoles([$roleSuperAdmin]);

        // Crear permisos para informacion (CRUD completo)
        Permission::create(['name' => 'ver información de interes'])->syncRoles([$roleSuperAdmin]);
        Permission::create(['name' => 'crear información de interes'])->syncRoles([$roleSuperAdmin]);
        Permission::create(['name' => 'editar información de interes'])->syncRoles([$roleSuperAdmin]);
        Permission::create(['name' => 'eliminar información de interes'])->syncRoles([$roleSuperAdmin]);

         // Crear permisos para  usuarios (CRUD completo)
        Permission::create(['name' => 'ver usuarios'])->syncRoles([$roleSuperAdmin]);
        Permission::create(['name' => 'crear usuarios'])->syncRoles([$roleSuperAdmin]);
        Permission::create(['name' => 'editar usuarios'])->syncRoles([$roleSuperAdmin]);
        Permission::create(['name' => 'eliminar usuarios'])->syncRoles([$roleSuperAdmin]);

         // Crear permisos para  sedes (CRUD completo)
        Permission::create(['name' => 'ver sedes'])->syncRoles([$roleSuperAdmin]);
        Permission::create(['name' => 'crear sedes'])->syncRoles([$roleSuperAdmin]);
        Permission::create(['name' => 'editar sedes'])->syncRoles([$roleSuperAdmin]);
        Permission::create(['name' => 'eliminar sedes'])->syncRoles([$roleSuperAdmin]);

        // Crear permisos para  gestion documental (CRUD completo)
        Permission::create(['name' => 'ver gestión documental'])->syncRoles([$roleSuperAdmin]);
        Permission::create(['name' => 'crear gestión documental'])->syncRoles([$roleSuperAdmin]);
        Permission::create(['name' => 'editar gestión documental'])->syncRoles([$roleSuperAdmin]);
        Permission::create(['name' => 'eliminar gestión documental'])->syncRoles([$roleSuperAdmin]);

        // Crear permisos para Roles (CRUD completo)
        Permission::create(['name' => 'ver roles'])->syncRoles([$roleSuperAdmin]);
        Permission::create(['name' => 'crear roles'])->syncRoles([$roleSuperAdmin]);
        Permission::create(['name' => 'editar roles'])->syncRoles([$roleSuperAdmin]);
        Permission::create(['name' => 'eliminar roles'])->syncRoles([$roleSuperAdmin]);

         // Crear permisos para proveedores (CRUD completo)
        Permission::create(['name' => 'ver proveedores'])->syncRoles([$roleSuperAdmin]);
        Permission::create(['name' => 'crear proveedores'])->syncRoles([$roleSuperAdmin]);
        Permission::create(['name' => 'editar proveedores'])->syncRoles([$roleSuperAdmin]);
        Permission::create(['name' => 'eliminar proveedores'])->syncRoles([$roleSuperAdmin]);

        // Crear permisos para repositorio (CRUD completo)
        Permission::create(['name' => 'ver repositorio'])->syncRoles([$roleSuperAdmin]);
        Permission::create(['name' => 'crear repositorio'])->syncRoles([$roleSuperAdmin]);
        Permission::create(['name' => 'editar repositorio'])->syncRoles([$roleSuperAdmin]);
        Permission::create(['name' => 'eliminar repositorio'])->syncRoles([$roleSuperAdmin]);
    }
}
