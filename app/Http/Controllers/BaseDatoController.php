<?php

namespace App\Http\Controllers;

use App\Models\BaseDato;
use Illuminate\Http\Request;

class BaseDatoController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin.bases-datos.index')->only('index');
        $this->middleware('can:admin.bases-datos.store')->only('store');
        $this->middleware('can:admin.bases-datos.edit')->only('edit');
        $this->middleware('can:admin.bases-datos.update')->only('update');
        $this->middleware('can:admin.bases-datos.destroy')->only('destroy');
        $this->middleware('can:admin.bases-datos.restore')->only('restore');
    }
    public function index()
    {
        $basesDatos = BaseDato::orderBy('id', 'desc')->get();
        $basesDatosEliminadas = BaseDato::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->get();

        return view('admin.bases-datos.index', compact(
            'basesDatos',
            'basesDatosEliminadas'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'gestor' => 'required|string|max:45',
            'version' => 'required|string|max:10',
            'descripcion' => 'nullable|string|max:1000',
            'estado' => 'required|in:activo,inactivo',
        ], [
            'gestor.required' => 'El gestor es obligatorio.',
            'gestor.max' => 'El gestor no puede exceder 45 caracteres.',
            'version.required' => 'La versión es obligatoria.',
            'version.max' => 'La versión no puede exceder 10 caracteres.',
            'descripcion.max' => 'La descripción no puede exceder 1000 caracteres.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado debe ser activo o inactivo.',
        ]);

        $baseDatos = BaseDato::create($validated);

        return response()->json([
            'success' => true,
            'base_datos' => $baseDatos
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BaseDato $basesDato)
    {
        return response()->json([
            'base_datos' => $basesDato
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BaseDato $basesDato)
    {
        $validated = $request->validate([
            'gestor' => 'required|string|max:45',
            'version' => 'required|string|max:10',
            'descripcion' => 'nullable|string|max:1000',
            'estado' => 'required|in:activo,inactivo',
        ], [
            'gestor.required' => 'El gestor es obligatorio.',
            'gestor.max' => 'El gestor no puede exceder 45 caracteres.',
            'version.required' => 'La versión es obligatoria.',
            'version.max' => 'La versión no puede exceder 10 caracteres.',
            'descripcion.max' => 'La descripción no puede exceder 1000 caracteres.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado debe ser activo o inactivo.',
        ]);

        $basesDato->update($validated);

        return response()->json([
            'success' => true,
            'base_datos' => $basesDato->fresh()
        ]);
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(BaseDato $basesDato)
    {
        $basesDato->delete();

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Restore the specified resource from trash.
     */
    public function restore($id)
    {
        $baseDatos = BaseDato::onlyTrashed()->findOrFail($id);
        $baseDatos->restore();

        return response()->json([
            'success' => true,
            'base_datos' => $baseDatos
        ]);
    }
}
