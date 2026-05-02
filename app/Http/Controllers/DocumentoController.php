<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use Illuminate\Http\Request;

class DocumentoController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:admin.documentos.index')->only('index');
        $this->middleware('can:admin.documentos.store')->only('store');
        $this->middleware('can:admin.documentos.edit')->only('edit');
        $this->middleware('can:admin.documentos.update')->only('update');
        $this->middleware('can:admin.documentos.destroy')->only('destroy');
        $this->middleware('can:admin.documentos.restore')->only('restore');
    }
    public function index()
    {
        $documentos = Documento::orderBy('id', 'desc')->get();
        $documentosEliminados = Documento::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->get();

        return view('admin.documentos.index', compact(
            'documentos',
            'documentosEliminados'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:45|unique:documentos,nombre',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder 45 caracteres.',
            'nombre.unique' => 'Ya existe un documento con este nombre.',
        ]);

        $documento = Documento::create($validated);

        return response()->json([
            'success' => true,
            'documento' => $documento->fresh()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Documento $documento)
    {
        return response()->json([
            'documento' => $documento
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Documento $documento)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:45|unique:documentos,nombre,' . $documento->id,
            'activo' => 'boolean',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder 45 caracteres.',
            'nombre.unique' => 'Ya existe un documento con este nombre.',
        ]);

        $documento->update($validated);

        return response()->json([
            'success' => true,
            'documento' => $documento->fresh()
        ]);
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(Documento $documento)
    {
        $documento->delete();

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Restore the specified resource from trash.
     */
    public function restore($id)
    {
        $documento = Documento::onlyTrashed()->findOrFail($id);
        $documento->restore();

        return response()->json([
            'success' => true,
            'documento' => $documento->fresh()
        ]);
    }
}
