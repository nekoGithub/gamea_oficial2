<?php

namespace App\Http\Controllers;

use App\Models\Sistema;
use App\Models\Ssl;
use App\Models\Unidad;
use Illuminate\Http\Request;

class SistemaController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin.sistemas.index')->only('index');
        $this->middleware('can:admin.sistemas.store')->only('store');
        $this->middleware('can:admin.sistemas.edit')->only('edit');
        $this->middleware('can:admin.sistemas.update')->only('update');
        $this->middleware('can:admin.sistemas.destroy')->only('destroy');
        $this->middleware('can:admin.sistemas.restore')->only('restore');
    }

    public function index()
    {
        $sistemas = Sistema::with(['unidad', 'ssl'])
            ->orderBy('id', 'desc')
            ->get();

        $sistemasEliminados = Sistema::onlyTrashed()
            ->with(['unidad', 'ssl'])
            ->orderBy('deleted_at', 'desc')
            ->get();

        $unidades = Unidad::where('estado', 'activa')
            ->orderBy('nombre')
            ->get();

        $ssls = Ssl::whereIn('estado', ['valido', 'proximo_vencer'])
            ->orderBy('emisor')
            ->get();

        return view('admin.sistemas.index', compact(
            'sistemas',
            'sistemasEliminados',
            'unidades',
            'ssls'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:80',
            'sigla' => 'nullable|string|max:20|unique:sistemas,sigla',
            'dominio' => 'required|string|max:120|unique:sistemas,dominio',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|array|min:1',
            'tipo.*' => 'in:interno,externo',
            'unidad_id' => 'required|exists:unidades,id',
            'ssl_id' => 'nullable|exists:ssls,id',
            'estado' => 'required|in:activo,inactivo',
        ], [
            'nombre.required' => 'El nombre del sistema es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder 80 caracteres.',
            'sigla.max' => 'La sigla no puede exceder 20 caracteres.',
            'sigla.unique' => 'Esta sigla ya está registrada.',
            'dominio.required' => 'El dominio es obligatorio.',
            'dominio.max' => 'El dominio no puede exceder 120 caracteres.',
            'dominio.unique' => 'Este dominio ya está registrado.',
            'tipo.required' => 'El tipo de sistema es obligatorio.',
            'tipo.in' => 'El tipo debe ser interno o externo.',
            'unidad_id.required' => 'Debes seleccionar una unidad.',
            'unidad_id.exists' => 'La unidad seleccionada no existe.',
            'ssl_id.exists' => 'El certificado SSL seleccionado no existe.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado debe ser activo o inactivo.',
        ]);

        $sistema = Sistema::create($validated);

        return response()->json([
            'success' => true,
            'sistema' => $sistema->fresh()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sistema $sistema)
    {
        $sistema->load(['unidad', 'ssl']);

        return response()->json([
            'sistema' => $sistema
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sistema $sistema)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:80',
            'sigla' => 'nullable|string|max:20|unique:sistemas,sigla,' . $sistema->id,
            'dominio' => 'required|string|max:120|unique:sistemas,dominio,' . $sistema->id,
            'descripcion' => 'nullable|string',
            'tipo' => 'required|array|min:1',
            'tipo.*' => 'in:interno,externo',
            'unidad_id' => 'required|exists:unidades,id',
            'ssl_id' => 'nullable|exists:ssls,id',
            'estado' => 'required|in:activo,inactivo',
        ], [
            'nombre.required' => 'El nombre del sistema es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder 80 caracteres.',
            'sigla.max' => 'La sigla no puede exceder 20 caracteres.',
            'sigla.unique' => 'Esta sigla ya está registrada.',
            'dominio.required' => 'El dominio es obligatorio.',
            'dominio.max' => 'El dominio no puede exceder 120 caracteres.',
            'dominio.unique' => 'Este dominio ya está registrado.',
            'tipo.required' => 'El tipo de sistema es obligatorio.',
            'tipo.in' => 'El tipo debe ser interno o externo.',
            'unidad_id.required' => 'Debes seleccionar una unidad.',
            'unidad_id.exists' => 'La unidad seleccionada no existe.',
            'ssl_id.exists' => 'El certificado SSL seleccionado no existe.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado debe ser activo o inactivo.',
        ]);

        $sistema->update($validated);

        return response()->json([
            'success' => true,
            'sistema' => $sistema->fresh()
        ]);
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(Sistema $sistema)
    {
        $sistema->delete();

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Restore the specified resource from trash.
     */
    public function restore($id)
    {
        $sistema = Sistema::onlyTrashed()->findOrFail($id);
        $sistema->restore();

        return response()->json([
            'success' => true,
            'sistema' => $sistema->fresh()
        ]);
    }
}
