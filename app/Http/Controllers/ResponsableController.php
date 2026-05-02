<?php

namespace App\Http\Controllers;

use App\Models\Responsable;
use Illuminate\Http\Request;

class ResponsableController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin.responsables.index')->only('index');
        $this->middleware('can:admin.responsables.store')->only('store');
        $this->middleware('can:admin.responsables.show')->only('show');
        $this->middleware('can:admin.responsables.edit')->only('edit');
        $this->middleware('can:admin.responsables.update')->only('update');
        $this->middleware('can:admin.responsables.destroy')->only('destroy');
        $this->middleware('can:admin.responsables.restore')->only('restore');
    }

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
            'nombre' => 'required|string|max:50',
            'cargo'  => 'required|string|max:80',
            'email'  => 'nullable|email|max:150|unique:responsables,email',
            'celular'  => 'required|string|max:20',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder 50 caracteres.',

            'cargo.required' => 'El cargo es obligatorio.',
            'cargo.max' => 'El cargo no puede exceder 80 caracteres.',

            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.max' => 'El correo electrónico no puede exceder 150 caracteres.',
            'email.unique' => 'Ya existe un responsable con este correo electrónico.',

            'celular.required' => 'El celular es obligatorio.',
            'celular.max' => 'El celular no puede exceder 20 caracteres.',
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
            'nombre' => 'required|string|max:50',
            'cargo'  => 'required|string|max:80',
            'email'  => 'nullable|email|max:150|unique:responsables,email,' . $responsable->id,
            'celular'  => 'required|string|max:20',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder 50 caracteres.',

            'cargo.required' => 'El cargo es obligatorio.',
            'cargo.max' => 'El cargo no puede exceder 80 caracteres.',

            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.max' => 'El correo electrónico no puede exceder 150 caracteres.',
            'email.unique' => 'Ya existe un responsable con este correo electrónico.',
            'celular.required' => 'El celular es obligatorio.',
            'celular.max' => 'El celular no puede exceder 20 caracteres.',
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
