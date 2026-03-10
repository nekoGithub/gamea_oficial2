<?php

namespace App\Http\Controllers;

use App\Models\Ssl;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SslController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ssls = Ssl::orderBy('id', 'desc')->get();
        $sslsEliminados = Ssl::onlyTrashed()->orderBy('deleted_at', 'desc')->get();

        return view('admin.ssls.index', compact('ssls', 'sslsEliminados'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'emisor' => 'required|string|max:150',
            'archivo_ssl' => 'nullable|file|mimes:rar,zip|max:2048',
            'fecha_emision' => 'required|date',
            'fecha_expiracion' => 'required|date|after:fecha_emision',
        ], [
            'emisor.required' => 'El emisor es obligatorio.',
            'emisor.max' => 'El emisor no puede exceder 150 caracteres.',
            'archivo_ssl.mimes' => 'El archivo debe ser un certificado SSL válido.',
            'archivo_ssl.max' => 'El archivo no debe superar 2MB.',
            'fecha_emision.required' => 'La fecha de emisión es obligatoria.',
            'fecha_emision.date' => 'La fecha de emisión debe ser válida.',
            'fecha_expiracion.required' => 'La fecha de expiración es obligatoria.',
            'fecha_expiracion.date' => 'La fecha de expiración debe ser válida.',
            'fecha_expiracion.after' => 'La fecha de expiración debe ser posterior a la fecha de emisión.',
        ]);

        // Manejar archivo SSL
        if ($request->hasFile('archivo_ssl')) {
            $file = $request->file('archivo_ssl');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('ssls', $filename, 'public');
            $validated['archivo_ssl'] = $path;
        }

        // Determinar estado automáticamente
        $validated['estado'] = $this->determinarEstado(
            $validated['fecha_emision'],
            $validated['fecha_expiracion']
        );

        $ssl = Ssl::create($validated);

        return response()->json([
            'success' => true,
            'ssl' => $ssl
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ssl $ssl)
    {
        return response()->json([
            'ssl' => $ssl
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ssl $ssl)
    {
        $validated = $request->validate([
            'emisor' => 'required|string|max:150',
            'archivo_ssl' => 'nullable|file|mimes:rar,zip|max:2048',
            'fecha_emision' => 'required|date',
            'fecha_expiracion' => 'required|date|after:fecha_emision',
        ], [
            'emisor.required' => 'El emisor es obligatorio.',
            'emisor.max' => 'El emisor no puede exceder 150 caracteres.',
            'archivo_ssl.mimes' => 'El archivo debe ser un certificado SSL válido.',
            'archivo_ssl.max' => 'El archivo no debe superar 2MB.',
            'fecha_emision.required' => 'La fecha de emisión es obligatoria.',
            'fecha_emision.date' => 'La fecha de emisión debe ser válida.',
            'fecha_expiracion.required' => 'La fecha de expiración es obligatoria.',
            'fecha_expiracion.date' => 'La fecha de expiración debe ser válida.',
            'fecha_expiracion.after' => 'La fecha de expiración debe ser posterior a la fecha de emisión.',
        ]);

        // Manejar archivo SSL
        if ($request->hasFile('archivo_ssl')) {
            // Eliminar archivo anterior si existe
            if ($ssl->archivo_ssl && Storage::disk('public')->exists($ssl->archivo_ssl)) {
                Storage::disk('public')->delete($ssl->archivo_ssl);
            }

            $file = $request->file('archivo_ssl');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('ssls', $filename, 'public');
            $validated['archivo_ssl'] = $path;
        }

        // Actualizar estado automáticamente
        $validated['estado'] = $this->determinarEstado(
            $validated['fecha_emision'],
            $validated['fecha_expiracion']
        );

        $ssl->update($validated);

        return response()->json([
            'success' => true,
            'ssl' => $ssl->fresh()
        ]);
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(Ssl $ssl)
    {
        $ssl->delete();

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Restore the specified resource from trash.
     */
    public function restore($id)
    {
        $ssl = Ssl::onlyTrashed()->findOrFail($id);
        $ssl->restore();

        // Actualizar estado al restaurar
        $ssl->estado = $this->determinarEstado($ssl->fecha_emision, $ssl->fecha_expiracion);
        $ssl->save();

        return response()->json([
            'success' => true,
            'ssl' => $ssl
        ]);
    }

    /**
     * Determinar el estado del SSL según las fechas.
     */
    private function determinarEstado($fechaEmision, $fechaExpiracion): string
    {
        $hoy = Carbon::now();
        $expiracion = Carbon::parse($fechaExpiracion);
        $diasRestantes = $hoy->diffInDays($expiracion, false);

        if ($diasRestantes < 0) {
            return 'vencido';
        } elseif ($diasRestantes <= 30) {
            return 'proximo_vencer';
        } else {
            return 'valido';
        }
    }

    public function detalle($id)
    {
        try {
            $ssl = Ssl::findOrFail($id);

            return response()->json([
                'success' => true,
                'ssl' => $ssl
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar el certificado SSL'
            ], 500);
        }
    }
}
