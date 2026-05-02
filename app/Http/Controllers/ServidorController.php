<?php

namespace App\Http\Controllers;

use App\Models\Servidor;
use App\Models\SistemaOperativo;
use Illuminate\Http\Request;

class ServidorController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin.servidores.index')->only('index');
        $this->middleware('can:admin.servidores.store')->only('store');
        $this->middleware('can:admin.servidores.edit')->only('edit');
        $this->middleware('can:admin.servidores.update')->only('update');
        $this->middleware('can:admin.servidores.destroy')->only('destroy');
        $this->middleware('can:admin.servidores.restore')->only('restore');
    }

    public function index()
    {
        $servidores = Servidor::with('sistemaOperativo')
            ->orderBy('id', 'desc')
            ->get();

        $servidoresEliminados = Servidor::with('sistemaOperativo')
            ->onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->get();

        $sistemasOperativos = SistemaOperativo::where('estado', 'activo')
            ->orderBy('nombre')
            ->get();

        return view('admin.servidores.index', compact(
            'servidores',
            'servidoresEliminados',
            'sistemasOperativos'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:45',
            'ip_interna' => 'required|ip|max:45|unique:servidores,ip_interna',
            'ip_externa' => 'nullable|ip|max:45|unique:servidores,ip_externa',
            'mac_address' => 'nullable|string|max:20|regex:/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/',
            'descripcion' => 'nullable|string',
            'sistema_operativo_id' => 'required|exists:sistemas_operativos,id',
            'tipo_servidor' => 'required|in:físico,virtual',
            'estado' => 'required|in:activo,inactivo',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder 150 caracteres.',
            'ip_interna.required' => 'La IP interna es obligatoria.',
            'ip_interna.ip' => 'Debe ingresar una dirección IP interna válida.',
            'ip_interna.unique' => 'Esta IP interna ya está asignada a otro servidor.',
            'ip_externa.ip' => 'Debe ingresar una dirección IP externa válida.',
            'ip_externa.unique' => 'Esta IP externa ya está asignada a otro servidor.',
            'mac_address.regex' => 'La dirección MAC debe tener formato válido (ej: 00:1A:2B:3C:4D:5E).',
            'mac_address.max' => 'La dirección MAC no puede exceder 20 caracteres.',
            'descripcion.max' => 'La descripción es demasiado larga.',
            'sistema_operativo_id.required' => 'Debe seleccionar un sistema operativo.',
            'sistema_operativo_id.exists' => 'El sistema operativo seleccionado no existe.',
            'tipo_servidor.required' => 'Debe seleccionar el tipo de servidor.',
            'tipo_servidor.in' => 'El tipo de servidor debe ser físico o virtual.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado debe ser activo o inactivo.',
        ]);

        $servidor = Servidor::create($validated);
        $servidor->load('sistemaOperativo');

        return response()->json([
            'success' => true,
            'servidor' => $servidor
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Servidor $servidore)
    {
        $servidore->load('sistemaOperativo');

        // ✅ Verificar si tiene versiones activas (estable)
        $tieneVersionesActivas = $servidore->versiones()
            ->where('estado', 'estable')
            ->exists();

        return response()->json([
            'servidor' => $servidore,
            'tiene_versiones_activas' => $tieneVersionesActivas // ← NUEVO
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Servidor $servidore)
    {
        // ✅ Verificar si intenta inactivar servidor con versiones activas
        if ($request->estado === 'inactivo') {
            $tieneVersionesActivas = $servidore->versiones()
                ->where('estado', 'estable')
                ->exists();

            if ($tieneVersionesActivas) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede inactivar un servidor con versiones activas en producción.'
                ], 422);
            }
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:45',
            'ip_interna' => 'required|ip|max:45|unique:servidores,ip_interna,' . $servidore->id,
            'ip_externa' => 'nullable|ip|max:45|unique:servidores,ip_externa,' . $servidore->id,
            'mac_address' => 'nullable|string|max:20|regex:/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/',
            'descripcion' => 'nullable|string',
            'sistema_operativo_id' => 'required|exists:sistemas_operativos,id',
            'tipo_servidor' => 'required|in:físico,virtual',
            'estado' => 'required|in:activo,inactivo',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder 150 caracteres.',
            'ip_interna.required' => 'La IP interna es obligatoria.',
            'ip_interna.ip' => 'Debe ingresar una dirección IP interna válida.',
            'ip_interna.unique' => 'Esta IP interna ya está asignada a otro servidor.',
            'ip_externa.ip' => 'Debe ingresar una dirección IP externa válida.',
            'ip_externa.unique' => 'Esta IP externa ya está asignada a otro servidor.',
            'mac_address.regex' => 'La dirección MAC debe tener formato válido (ej: 00:1A:2B:3C:4D:5E).',
            'mac_address.max' => 'La dirección MAC no puede exceder 20 caracteres.',
            'sistema_operativo_id.required' => 'Debe seleccionar un sistema operativo.',
            'sistema_operativo_id.exists' => 'El sistema operativo seleccionado no existe.',
            'tipo_servidor.required' => 'Debe seleccionar el tipo de servidor.',
            'tipo_servidor.in' => 'El tipo de servidor debe ser físico o virtual.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado debe ser activo o inactivo.',
        ]);

        $servidore->update($validated);
        $servidore->load('sistemaOperativo');

        return response()->json([
            'success' => true,
            'servidor' => $servidore->fresh(['sistemaOperativo'])
        ]);
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(Servidor $servidore)
    {
        $servidore->delete();

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Restore the specified resource from trash.
     */
    public function restore($id)
    {
        $servidor = Servidor::withTrashed()->findOrFail($id);
        $servidor->restore();
        $servidor->load('sistemaOperativo');

        return response()->json([
            'success' => true,
            'servidor' => $servidor
        ]);
    }
}
