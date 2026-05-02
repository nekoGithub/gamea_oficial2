<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Rolecontroller extends Controller
{

    public function __construct()
    {
        $this->middleware('can:admin.roles.index')->only('index');
        $this->middleware('can:admin.roles.store')->only('store');
        $this->middleware('can:admin.roles.edit')->only('edit');
        $this->middleware('can:admin.roles.update')->only('update');
        $this->middleware('can:admin.roles.destroy')->only('destroy');
    }
    public function index()
    {
        $roles = Role::with('permissions')->orderBy('id', 'desc')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:45|unique:roles,name',
        ], [
            'name.required' => 'El nombre del rol es obligatorio',
            'name.unique' => 'Ya existe un rol con este nombre',
            'name.max' => 'El nombre no puede exceder 45 caracteres',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $role = Role::create([
                'name' => $request->name,
                'guard_name' => 'web'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Rol creado exitosamente',
                'role' => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'created_at' => $role->created_at->toISOString(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el rol: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $rol = Role::findOrFail($id);

        $all_permissions = Permission::all()->groupBy(function ($permission) {
            $parts = explode('.', $permission->name);

            if (count($parts) >= 2) {
                $section = ucfirst($parts[1]);
                return $section;
            }

            return 'Otros';
        });

        $permissions = $rol->permissions->pluck('name')->toArray();

        return view('admin.roles.edit', compact('rol', 'all_permissions', 'permissions'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:45|unique:roles,name,' . $id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name'
        ], [
            'name.required' => 'El nombre del rol es obligatorio',
            'name.unique' => 'Ya existe un rol con este nombre',
            'name.max' => 'El nombre no puede exceder 45 caracteres',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $role = Role::findOrFail($id);

            $role->name = $request->name;
            $role->save();

            $permissions = $request->input('permissions', []);
            $role->syncPermissions($permissions);

            return response()->json([
                'success' => true,
                'message' => 'Rol actualizado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el rol: ' . $e->getMessage()
            ], 500);
        }
    }
}
