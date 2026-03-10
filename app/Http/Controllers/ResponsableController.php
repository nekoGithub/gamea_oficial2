<?php

namespace App\Http\Controllers;

use App\Models\Responsable;
use Illuminate\Http\Request;

class ResponsableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $responsables = Responsable::orderBy('id', 'desc')->get();
        $responsablesEliminados = Responsable::onlyTrashed()->orderBy('deleted_at', 'desc')->get();

        return view('admin.responsables.index', compact(
            'responsables',
            'responsablesEliminados'
        ));
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
            'cargo'  => 'required|string|max:100',
            'email'  => 'required|email|max:150|unique:responsables,email',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder 150 caracteres.',

            'cargo.required' => 'El cargo es obligatorio.',
            'cargo.max' => 'El cargo no puede exceder 100 caracteres.',

            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.max' => 'El correo electrónico no puede exceder 150 caracteres.',
            'email.unique' => 'Ya existe un responsable con este correo electrónico.',
        ]);

        $responsable = Responsable::create($validated);

        return response()->json([
            'success' => true,
            'responsable' => $responsable->fresh()
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Responsable $responsable)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Responsable $responsable)
    {
        return response()->json([
            'responsable' => $responsable
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Responsable $responsable)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:150',
            'cargo'  => 'required|string|max:100',
            'email'  => 'required|email|max:150|unique:responsables,email,' . $responsable->id,
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder 150 caracteres.',

            'cargo.required' => 'El cargo es obligatorio.',
            'cargo.max' => 'El cargo no puede exceder 100 caracteres.',

            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.max' => 'El correo electrónico no puede exceder 150 caracteres.',
            'email.unique' => 'Ya existe un responsable con este correo electrónico.',
        ]);

        $responsable->update($validated);

        return response()->json([
            'success' => true,
            'responsable' => $responsable->fresh()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Responsable $responsable)
    {
        $responsable->delete();

        return response()->json([
            'success' => true
        ]);
    }

    public function restore($id)
    {
        $responsable = Responsable::onlyTrashed()->findOrFail($id);
        $responsable->restore();

        return response()->json([
            'success' => true,
            'responsable' => $responsable
        ]);
    }
}
