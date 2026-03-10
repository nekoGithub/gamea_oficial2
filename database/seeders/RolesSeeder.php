<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::create([
            'name' => 'admin',
        ]);

        $viewer = Role::create([
            'name' => 'visitante',
        ]);

        Permission::create(['name' => 'dashboard', 'description' => 'Panel de control', 'grupo' => 'Panel de Control'])->syncRoles([$admin, $viewer]);

        Permission::create(['name' => 'admin.users.index', 'description' => 'Ver lista de usuarios', 'grupo' => 'Usuarios'])->syncRoles([$admin, $viewer]);
        Permission::create(['name' => 'admin.users.store', 'description' => 'Crear usuarios', 'grupo' => 'Usuarios'])->syncRoles([$admin]);
        Permission::create(['name' => 'admin.users.show', 'description' => 'Ver detalle de usuario', 'grupo' => 'Usuarios'])->syncRoles([$admin]);
        Permission::create(['name' => 'admin.users.edit', 'description' => 'Editar usuario', 'grupo' => 'Usuarios'])->syncRoles([$admin]);
        Permission::create(['name' => 'admin.users.update', 'description' => 'Actualizar usuario', 'grupo' => 'Usuarios'])->syncRoles([$admin]);
        Permission::create(['name' => 'admin.users.destroy', 'description' => 'Eliminar usuario', 'grupo' => 'Usuarios'])->syncRoles([$admin]);
        Permission::create(['name' => 'admin.users.restore', 'description' => 'Restaurar usuario', 'grupo' => 'Usuarios'])->syncRoles([$admin]);

        Permission::create(['name' => 'profile.show', 'description' => 'Perfil Usuario', 'grupo' => 'Perfil Usuario'])->syncRoles([$admin, $viewer]);

        Permission::create(['name' => 'admin.roles.index', 'description' => 'Ver lista de roles', 'grupo' => 'Roles'])->syncRoles([$admin, $viewer]);
        Permission::create(['name' => 'admin.roles.store', 'description' => 'Crear rol', 'grupo' => 'Roles'])->syncRoles([$admin]);
        Permission::create(['name' => 'admin.roles.edit', 'description' => 'Editar rol', 'grupo' => 'Roles'])->syncRoles([$admin]);
        Permission::create(['name' => 'admin.roles.update', 'description' => 'Actualizar rol', 'grupo' => 'Roles'])->syncRoles([$admin]);
        Permission::create(['name' => 'admin.roles.destroy', 'description' => 'Eliminar rol', 'grupo' => 'Roles'])->syncRoles([$admin]);
    }
}
