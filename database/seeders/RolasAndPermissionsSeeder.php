<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolasAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //limpiar caché de permisos antes de iniciar
    app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

    $permissions = [
        'especialidades.ver', 'especialidades.crear', 'especialidades.editar', 'especialidades.eliminar',
        'doctores.ver',       'doctores.crear',       'doctores.editar',       'doctores.eliminar',
        'pacientes.ver',      'pacientes.crear',      'pacientes.editar',      'pacientes.eliminar',
        'citas.ver',          'citas.crear',          'citas.editar',          'citas.eliminar',
        'usuarios.ver',       'usuarios.crear',       'usuarios.editar',       'usuarios.eliminar',
        'usuarios.gestionar_roles', 'usuarios.gestionar_permisos',
        'roles.ver',          'roles.crear',          'roles.editar',          'roles.eliminar',
        'roles.gestionar_permisos',
        'permisos.ver',
    ];

    foreach ($permissions as $permission){
        Permission::firstOrCreate([
            'name' => $permission,
            'guard_name' => 'web',
        ]);
    }

    $adminRole = Role::firstOrCreate([
        'name' => 'admin',
        'guard_name' => 'web',
    ]);

    $adminRole->syncPermissions($permissions);

    }
}
