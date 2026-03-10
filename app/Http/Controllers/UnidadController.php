<?php

namespace App\Http\Controllers;

use App\Models\Responsable;
use App\Models\Unidad;
use Illuminate\Http\Request;

class UnidadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $unidades = Unidad::orderBy('id', 'desc')->get();
        $unidadesEliminadas = Unidad::onlyTrashed()->orderBy('deleted_at', 'desc')->get();
        $responsables = Responsable::orderBy('nombre')->get();

        return view('admin.unidades.index', compact('unidades', 'unidadesEliminadas', 'responsables'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:150',
            'sigla' => 'required|string|max:20',
            'celular' => 'nullable|string|max:20',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:activa,inactiva',
            'responsables' => 'required|array',
            'responsables.*' => 'exists:responsables,id',
        ]);

        $unidad = Unidad::create($validated);

        if ($request->filled('responsables')) {
            $unidad->responsables()->sync($request->responsables);
        }

        return response()->json([
            'success' => true,
            'unidad' => $unidad->load('responsables')
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Unidad $unidad)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Unidad $unidad)
    {
        return response()->json([
            'unidad' => $unidad->load('responsables')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unidad $unidad)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:150',
            'sigla' => 'required|string|max:20',
            'celular' => 'nullable|string|max:20',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:activa,inactiva',
            'responsables' => 'required|array',
            'responsables.*' => 'exists:responsables,id'
        ]);

        $unidad->update($validated);

        $unidad->responsables()->sync($request->responsables ?? []);

        return response()->json([
            'success' => true,
            'unidad' => $unidad->load('responsables')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unidad $unidad)
    {
        $unidad->delete();

        return response()->json([
            'success' => true,
            'message' => 'Unidad eliminada correctamente'
        ]);
    }

    public function restore($id)
    {
        try {
            $unidad = Unidad::withTrashed()->findOrFail($id);
            $unidad->restore();

            $unidad->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Unidad restaurada exitosamente',
                'unidad' => $unidad
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al restaurar: ' . $e->getMessage()
            ], 500);
        }
    }

    public function detalle($id)
    {
        try {
            $unidad = Unidad::with('responsables')->findOrFail($id);

            return response()->json([
                'success' => true,
                'unidad' => $unidad,
                'responsables' => $unidad->responsables ?? []
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar la unidad'
            ], 500);
        }
    }
}
