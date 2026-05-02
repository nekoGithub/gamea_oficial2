<?php

namespace App\Http\Controllers;

use App\Models\Responsable;
use App\Models\Unidad;
use Illuminate\Http\Request;

class UnidadController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin.unidades.index')->only('index');
        $this->middleware('can:admin.unidades.store')->only('store');
        $this->middleware('can:admin.unidades.show')->only(['show', 'detalle']);
        $this->middleware('can:admin.unidades.edit')->only('edit');
        $this->middleware('can:admin.unidades.update')->only('update');
        $this->middleware('can:admin.unidades.destroy')->only('destroy');
        $this->middleware('can:admin.unidades.restore')->only('restore');
    }
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
            'nombre'          => 'required|string|max:100',
            'sigla'           => 'required|string|max:10',
            'celular' => ['nullable', 'digits:8'],
            'descripcion'     => 'nullable|string',
            'estado'          => 'required|in:activa,inactiva',
            'responsables'    => 'nullable|array',
            'responsables.*'  => 'exists:responsables,id',
        ], [
            'nombre.required'        => 'El nombre es obligatorio.',
            'nombre.max'             => 'El nombre no puede exceder 100 caracteres.',
            'sigla.required'         => 'La sigla es obligatoria.',
            'sigla.max'              => 'La sigla no puede exceder 10 caracteres.',
            'celular.digits' => 'El celular debe tener exactamente 8 dígitos.',
            'estado.required'        => 'El estado es obligatorio.',
            'estado.in'              => 'El estado debe ser activa o inactiva.',            
            'responsables.array'     => 'Los responsables no son válidos.',
            'responsables.*.exists'  => 'Uno o más responsables seleccionados no existen.',
        ]);

        $unidad = Unidad::create($validated);

        if ($request->filled('responsables')) {
            $unidad->responsables()->sync($request->responsables);
        }

        return response()->json([
            'success' => true,
            'unidad'  => $unidad->load('responsables')
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
            'nombre'          => 'required|string|max:100',
            'sigla'           => 'required|string|max:10',
            'celular' => ['nullable', 'digits:8'],
            'descripcion'     => 'nullable|string',
            'estado'          => 'required|in:activa,inactiva',
            'responsables'    => 'nullable|array',
            'responsables.*'  => 'exists:responsables,id',
        ], [
            'nombre.required'        => 'El nombre es obligatorio.',
            'nombre.max'             => 'El nombre no puede exceder 100 caracteres.',
            'sigla.required'         => 'La sigla es obligatoria.',
            'sigla.max'              => 'La sigla no puede exceder 10 caracteres.',
            'celular.digits' => 'El celular debe tener exactamente 8 dígitos.',
            'estado.required'        => 'El estado es obligatorio.',
            'estado.in'              => 'El estado debe ser activa o inactiva.',            
            'responsables.array'     => 'Los responsables no son válidos.',
            'responsables.*.exists'  => 'Uno o más responsables seleccionados no existen.',
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
