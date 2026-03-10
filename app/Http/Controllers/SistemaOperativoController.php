<?php

namespace App\Http\Controllers;

use App\Models\SistemaOperativo;
use Illuminate\Http\Request;

class SistemaOperativoController extends Controller
{
    public function index()
    {
        $sistemasOperativos = SistemaOperativo::orderBy('id', 'desc')->get();
        $sistemasOperativosEliminados = SistemaOperativo::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->get();

        return view('admin.sistemas-operativos.index', compact(
            'sistemasOperativos',
            'sistemasOperativosEliminados'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'version' => 'required|string|max:50',
            'descripcion' => 'nullable|string|max:1000',
            'estado' => 'required|in:activo,inactivo',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder 100 caracteres.',
            'version.required' => 'La versión es obligatoria.',
            'version.max' => 'La versión no puede exceder 50 caracteres.',
            'descripcion.max' => 'La descripción no puede exceder 1000 caracteres.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado debe ser activo o inactivo.',
        ]);

        $sistemaOperativo = SistemaOperativo::create($validated);

        return response()->json([
            'success' => true,
            'sistema_operativo' => $sistemaOperativo
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SistemaOperativo $sistemasOperativo)
    {
        return response()->json([
            'sistema_operativo' => $sistemasOperativo
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SistemaOperativo $sistemasOperativo)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'version' => 'required|string|max:50',
            'descripcion' => 'nullable|string|max:1000',
            'estado' => 'required|in:activo,inactivo',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder 100 caracteres.',
            'version.required' => 'La versión es obligatoria.',
            'version.max' => 'La versión no puede exceder 50 caracteres.',
            'descripcion.max' => 'La descripción no puede exceder 1000 caracteres.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado debe ser activo o inactivo.',
        ]);

        $sistemasOperativo->update($validated);

        return response()->json([
            'success' => true,
            'sistema_operativo' => $sistemasOperativo->fresh()
        ]);
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(SistemaOperativo $sistemasOperativo)
    {
        $sistemasOperativo->delete();

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Restore the specified resource from trash.
     */
    public function restore($id)
    {
        $sistemaOperativo = SistemaOperativo::onlyTrashed()->findOrFail($id);
        $sistemaOperativo->restore();

        return response()->json([
            'success' => true,
            'sistema_operativo' => $sistemaOperativo
        ]);
    }
}
