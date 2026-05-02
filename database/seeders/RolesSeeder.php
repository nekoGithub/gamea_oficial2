<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        // =============================================
        // ROLES
        // =============================================
        $admin    = Role::firstOrCreate(['name' => 'admin']);
        $tecnico  = Role::firstOrCreate(['name' => 'tecnico']);
        $visitante = Role::firstOrCreate(['name' => 'visitante']);

        // Helper para crear permiso y asignar roles
        $p = function (string $name, string $description, string $grupo, array $roles) {
            $permission = Permission::firstOrCreate(['name' => $name], [
                'description' => $description,
                'grupo'       => $grupo,
            ]);
            $permission->syncRoles($roles);
        };

        // =============================================
        // PANEL DE CONTROL
        // =============================================
        $p('dashboard', 'Panel de control', 'Panel de Control', [$admin, $tecnico, $visitante]);

        // =============================================
        // SISTEMAS
        // =============================================
        $p('admin.sistemas.index',   'Ver lista de sistemas',    'Sistemas', [$admin, $tecnico, $visitante]);
        $p('admin.sistemas.store',   'Crear sistema',            'Sistemas', [$admin, $tecnico]);
        $p('admin.sistemas.show',    'Ver detalle de sistema',   'Sistemas', [$admin, $tecnico, $visitante]);
        $p('admin.sistemas.edit',    'Editar sistema',           'Sistemas', [$admin, $tecnico]);
        $p('admin.sistemas.update',  'Actualizar sistema',       'Sistemas', [$admin, $tecnico]);
        $p('admin.sistemas.destroy', 'Eliminar sistema',         'Sistemas', [$admin]);
        $p('admin.sistemas.restore', 'Restaurar sistema',        'Sistemas', [$admin]);

        // =============================================
        // VERSIONES (de sistemas)
        // =============================================
        $p('admin.versiones.index',   'Ver versiones de sistema',   'Versiones', [$admin, $tecnico, $visitante]);
        $p('admin.versiones.store',   'Crear versión',              'Versiones', [$admin, $tecnico]);
        $p('admin.versiones.show',    'Ver detalle de versión',     'Versiones', [$admin, $tecnico, $visitante]);
        $p('admin.versiones.edit',    'Editar versión',             'Versiones', [$admin, $tecnico]);
        $p('admin.versiones.update',  'Actualizar versión',         'Versiones', [$admin, $tecnico]);
        $p('admin.versiones.destroy', 'Eliminar versión',           'Versiones', [$admin]);
        $p('admin.versiones.restore', 'Restaurar versión',          'Versiones', [$admin]);
        $p('admin.versiones.actual',  'Marcar versión como actual', 'Versiones', [$admin, $tecnico]);

        // =============================================
        // SERVIDORES
        // =============================================
        $p('admin.servidores.index',   'Ver lista de servidores',  'Servidores', [$admin, $tecnico, $visitante]);
        $p('admin.servidores.store',   'Crear servidor',           'Servidores', [$admin, $tecnico]);
        $p('admin.servidores.show',    'Ver detalle de servidor',  'Servidores', [$admin, $tecnico, $visitante]);
        $p('admin.servidores.edit',    'Editar servidor',          'Servidores', [$admin, $tecnico]);
        $p('admin.servidores.update',  'Actualizar servidor',      'Servidores', [$admin, $tecnico]);
        $p('admin.servidores.destroy', 'Eliminar servidor',        'Servidores', [$admin]);
        $p('admin.servidores.restore', 'Restaurar servidor',       'Servidores', [$admin]);

        // =============================================
        // TECNOLOGÍAS
        // =============================================
        $p('admin.tecnologias.index',   'Ver lista de tecnologías',  'Tecnologías', [$admin, $tecnico, $visitante]);
        $p('admin.tecnologias.store',   'Crear tecnología',          'Tecnologías', [$admin, $tecnico]);
        $p('admin.tecnologias.show',    'Ver detalle de tecnología', 'Tecnologías', [$admin, $tecnico, $visitante]);
        $p('admin.tecnologias.edit',    'Editar tecnología',         'Tecnologías', [$admin, $tecnico]);
        $p('admin.tecnologias.update',  'Actualizar tecnología',     'Tecnologías', [$admin, $tecnico]);
        $p('admin.tecnologias.destroy', 'Eliminar tecnología',       'Tecnologías', [$admin]);
        $p('admin.tecnologias.restore', 'Restaurar tecnología',      'Tecnologías', [$admin]);

        // =============================================
        // SISTEMAS OPERATIVOS
        // =============================================
        $p('admin.sistemas-operativos.index',   'Ver lista de sistemas operativos',  'Sistemas Operativos', [$admin, $tecnico, $visitante]);
        $p('admin.sistemas-operativos.store',   'Crear sistema operativo',           'Sistemas Operativos', [$admin, $tecnico]);
        $p('admin.sistemas-operativos.show',    'Ver detalle de sistema operativo',  'Sistemas Operativos', [$admin, $tecnico, $visitante]);
        $p('admin.sistemas-operativos.edit',    'Editar sistema operativo',          'Sistemas Operativos', [$admin, $tecnico]);
        $p('admin.sistemas-operativos.update',  'Actualizar sistema operativo',      'Sistemas Operativos', [$admin, $tecnico]);
        $p('admin.sistemas-operativos.destroy', 'Eliminar sistema operativo',        'Sistemas Operativos', [$admin]);
        $p('admin.sistemas-operativos.restore', 'Restaurar sistema operativo',       'Sistemas Operativos', [$admin]);

        // =============================================
        // BASES DE DATOS
        // =============================================
        $p('admin.bases-datos.index',   'Ver lista de bases de datos',  'Bases de Datos', [$admin, $tecnico, $visitante]);
        $p('admin.bases-datos.store',   'Crear base de datos',          'Bases de Datos', [$admin, $tecnico]);
        $p('admin.bases-datos.show',    'Ver detalle de base de datos', 'Bases de Datos', [$admin, $tecnico, $visitante]);
        $p('admin.bases-datos.edit',    'Editar base de datos',         'Bases de Datos', [$admin, $tecnico]);
        $p('admin.bases-datos.update',  'Actualizar base de datos',     'Bases de Datos', [$admin, $tecnico]);
        $p('admin.bases-datos.destroy', 'Eliminar base de datos',       'Bases de Datos', [$admin]);
        $p('admin.bases-datos.restore', 'Restaurar base de datos',      'Bases de Datos', [$admin]);

        // =============================================
        // DOCUMENTOS
        // =============================================
        $p('admin.documentos.index',   'Ver lista de documentos',  'Documentos', [$admin, $tecnico, $visitante]);
        $p('admin.documentos.store',   'Subir documento',          'Documentos', [$admin, $tecnico]);
        $p('admin.documentos.show',    'Ver detalle de documento', 'Documentos', [$admin, $tecnico, $visitante]);
        $p('admin.documentos.edit',    'Editar documento',         'Documentos', [$admin, $tecnico]);
        $p('admin.documentos.update',  'Actualizar documento',     'Documentos', [$admin, $tecnico]);
        $p('admin.documentos.destroy', 'Eliminar documento',       'Documentos', [$admin]);
        $p('admin.documentos.restore', 'Restaurar documento',      'Documentos', [$admin]);

        // =============================================
        // UNIDADES
        // =============================================
        $p('admin.unidades.index',   'Ver lista de unidades',  'Unidades', [$admin, $tecnico, $visitante]);
        $p('admin.unidades.store',   'Crear unidad',           'Unidades', [$admin]);
        $p('admin.unidades.show',    'Ver detalle de unidad',  'Unidades', [$admin, $tecnico, $visitante]);
        $p('admin.unidades.edit',    'Editar unidad',          'Unidades', [$admin]);
        $p('admin.unidades.update',  'Actualizar unidad',      'Unidades', [$admin]);
        $p('admin.unidades.destroy', 'Eliminar unidad',        'Unidades', [$admin]);
        $p('admin.unidades.restore', 'Restaurar unidad',       'Unidades', [$admin]);

        // =============================================
        // RESPONSABLES
        // =============================================
        $p('admin.responsables.index',   'Ver lista de responsables',  'Responsables', [$admin, $tecnico, $visitante]);
        $p('admin.responsables.store',   'Crear responsable',          'Responsables', [$admin]);
        $p('admin.responsables.show',    'Ver detalle de responsable', 'Responsables', [$admin, $tecnico, $visitante]);
        $p('admin.responsables.edit',    'Editar responsable',         'Responsables', [$admin]);
        $p('admin.responsables.update',  'Actualizar responsable',     'Responsables', [$admin]);
        $p('admin.responsables.destroy', 'Eliminar responsable',       'Responsables', [$admin]);
        $p('admin.responsables.restore', 'Restaurar responsable',      'Responsables', [$admin]);

        // =============================================
        // USUARIOS
        // =============================================
        $p('admin.users.index',   'Ver lista de usuarios',    'Usuarios', [$admin]);
        $p('admin.users.store',   'Crear usuario',            'Usuarios', [$admin]);
        $p('admin.users.show',    'Ver detalle de usuario',   'Usuarios', [$admin]);
        $p('admin.users.edit',    'Editar usuario',           'Usuarios', [$admin]);
        $p('admin.users.update',  'Actualizar usuario',       'Usuarios', [$admin]);
        $p('admin.users.destroy', 'Eliminar usuario',         'Usuarios', [$admin]);
        $p('admin.users.restore', 'Restaurar usuario',        'Usuarios', [$admin]);

        // =============================================
        // PERFIL
        // =============================================
        $p('profile.show', 'Ver perfil de usuario', 'Perfil', [$admin, $tecnico, $visitante]);

        // =============================================
        // ROLES
        // =============================================
        $p('admin.roles.index',   'Ver lista de roles', 'Roles', [$admin]);
        $p('admin.roles.store',   'Crear rol',          'Roles', [$admin]);
        $p('admin.roles.edit',    'Editar rol',         'Roles', [$admin]);
        $p('admin.roles.update',  'Actualizar rol',     'Roles', [$admin]);
        $p('admin.roles.destroy', 'Eliminar rol',       'Roles', [$admin]);

        // =============================================
        // CERTIFICADOS SSL
        // =============================================
        $p('admin.ssls.index',   'Ver lista de certificados SSL',  'SSL', [$admin, $tecnico, $visitante]);
        $p('admin.ssls.store',   'Crear certificado SSL',          'SSL', [$admin, $tecnico]);
        $p('admin.ssls.show',    'Ver detalle de certificado SSL', 'SSL', [$admin, $tecnico, $visitante]);
        $p('admin.ssls.edit',    'Editar certificado SSL',         'SSL', [$admin, $tecnico]);
        $p('admin.ssls.update',  'Actualizar certificado SSL',     'SSL', [$admin, $tecnico]);
        $p('admin.ssls.destroy', 'Eliminar certificado SSL',       'SSL', [$admin]);
        $p('admin.ssls.restore', 'Restaurar certificado SSL',      'SSL', [$admin]);

        // =============================================
        // CREDENCIALES
        // =============================================
        $p('admin.credenciales.index',   'Ver lista de credenciales',  'Credenciales', [$admin, $tecnico]);
        $p('admin.credenciales.store',   'Crear credencial',           'Credenciales', [$admin, $tecnico]);
        $p('admin.credenciales.show',    'Ver detalle de credencial',  'Credenciales', [$admin, $tecnico]);
        $p('admin.credenciales.edit',    'Editar credencial',          'Credenciales', [$admin, $tecnico]);
        $p('admin.credenciales.update',  'Actualizar credencial',      'Credenciales', [$admin, $tecnico]);
        $p('admin.credenciales.destroy', 'Eliminar credencial',        'Credenciales', [$admin]);
        $p('admin.credenciales.restore', 'Restaurar credencial',       'Credenciales', [$admin]);

        // =============================================
        // NOTIFICACIONES
        // =============================================
        $p('admin.notificaciones.index',  'Ver lista de notificaciones', 'Notificaciones', [$admin, $tecnico, $visitante]);
        $p('admin.notificaciones.show',   'Ver detalle de notificación', 'Notificaciones', [$admin, $tecnico, $visitante]);
        $p('admin.notificaciones.update', 'Marcar notificación leída',   'Notificaciones', [$admin, $tecnico, $visitante]);
        $p('admin.notificaciones.destroy', 'Eliminar notificación',       'Notificaciones', [$admin]);

        // =============================================
        // AUDITORÍAS
        // =============================================
        $p('admin.auditorias.index', 'Ver lista de auditorías',  'Auditorías', [$admin, $tecnico]);
        $p('admin.auditorias.show',  'Ver detalle de auditoría', 'Auditorías', [$admin, $tecnico]);

        // =============================================
        // REPORTES
        // =============================================
        $p('admin.reportes.index',    'Ver reportes',           'Reportes', [$admin, $tecnico, $visitante]);
        $p('admin.reportes.generar',  'Generar reporte PDF',    'Reportes', [$admin, $tecnico]);
        $p('admin.reportes.exportar', 'Exportar reporte Excel', 'Reportes', [$admin, $tecnico]);

        // =============================================
        // UPLOADS
        // =============================================
        $p('admin.uploads.index',   'Ver lista de uploads',  'Uploads', [$admin, $tecnico]);
        $p('admin.uploads.show',    'Ver detalle de upload', 'Uploads', [$admin, $tecnico]);
        $p('admin.uploads.destroy', 'Eliminar upload',       'Uploads', [$admin]);

        $this->command->info('✅ Roles y permisos creados correctamente');
        $this->command->table(
            ['Rol', 'Total Permisos'],
            [
                ['admin',     $admin->permissions()->count()],
                ['tecnico',   $tecnico->permissions()->count()],
                ['visitante', $visitante->permissions()->count()],
            ]
        );
    }
}
