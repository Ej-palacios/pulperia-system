<?php

namespace App\Http\Controllers;

use App\Models\Credito;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreditoController extends Controller
{
    /**
     * Listar todos los créditos
     */
    public function index()
    {
        $creditos = Credito::with(['cliente', 'venta'])
            ->where('saldo_pendiente', '>', 0)
            ->orderBy('fecha_limite', 'asc')
            ->paginate(20);
            
        return view('creditos.index', compact('creditos'));
    }

    /**
     * Mostrar detalles del crédito
     */
    public function show(Credito $credito)
    {
        $credito->load(['cliente', 'venta.detalles.producto', 'abonos.user']);
        return view('creditos.detalle', compact('credito'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Credito $credito)
    {
        $credito->load(['cliente', 'venta']);
        return view('creditos.edit', compact('credito'));
    }

    /**
     * Actualizar crédito
     */
    public function update(Request $request, Credito $credito)
    {
        $request->validate([
            'fecha_limite' => 'required|date',
            'estado' => 'required|in:pendiente,parcialmente_pagado,pagado,vencido',
            'notas' => 'nullable|string|max:1000',
        ]);

        try {
            $credito->update($request->all());

            return redirect()->route('creditos.show', $credito)
                ->with('success', 'Crédito actualizado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar crédito: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar crédito
     */
    public function destroy(Credito $credito)
    {
        // Verificar si el crédito tiene abonos
        if ($credito->abonos()->count() > 0) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar el crédito porque tiene abonos asociados.');
        }

        try {
            $credito->delete();

            return redirect()->route('creditos.index')
                ->with('success', 'Crédito eliminado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar crédito: ' . $e->getMessage());
        }
    }

    /**
     * Cambiar estado del crédito
     */
    public function cambiarEstado(Request $request, Credito $credito)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,parcialmente_pagado,pagado,vencido',
        ]);

        try {
            $credito->estado = $request->estado;
            $credito->save();

            return redirect()->back()
                ->with('success', 'Estado del crédito actualizado.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar estado: ' . $e->getMessage());
        }
    }

    /**
     * Marcar crédito como vencido
     */
    public function marcarComoVencido(Credito $credito)
    {
        try {
            $credito->estado = 'vencido';
            $credito->save();

            return redirect()->back()
                ->with('success', 'Crédito marcado como vencido.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al marcar crédito como vencido: ' . $e->getMessage());
        }
    }
}