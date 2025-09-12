<?php

namespace App\Http\Controllers;

use App\Models\Abono;
use App\Models\Credito;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AbonoController extends Controller
{
    /**
     * Mostrar formulario de creación
     */
    public function create(Credito $credito)
    {
        $credito->load('cliente');
        return view('abonos.create', compact('credito'));
    }

    /**
     * Almacenar nuevo abono
     */
    public function store(Request $request)
    {
        $request->validate([
            'credito_id' => 'required|exists:creditos,id',
            'monto' => 'required|numeric|min:0.01',
            'metodo_pago' => 'required|in:efectivo,transferencia,datáfono',
        ]);

        DB::beginTransaction();

        try {
            $credito = Credito::findOrFail($request->credito_id);
            
            // Validar que el monto no exceda el saldo pendiente
            if ($request->monto > $credito->saldo_pendiente) {
                return redirect()->back()
                    ->with('error', 'El monto excede el saldo pendiente del crédito.');
            }

            // Crear el abono
            $abono = Abono::create([
                'credito_id' => $credito->id,
                'cliente_id' => $credito->cliente_id,
                'user_id' => auth()->id(),
                'monto' => $request->monto,
                'metodo_pago' => $request->metodo_pago,
            ]);

            // Actualizar el crédito
            $credito->saldo_pendiente -= $request->monto;
            
            // Actualizar estado según el saldo
            if ($credito->saldo_pendiente <= 0) {
                $credito->estado = 'pagado';
            } else {
                $credito->estado = 'parcialmente_pagado';
            }
            
            $credito->save();

            // Actualizar saldo del cliente
            $cliente = Cliente::find($credito->cliente_id);
            $cliente->saldo -= $request->monto;
            $cliente->save();

            DB::commit();

            return redirect()->route('creditos.show', $credito->id)
                ->with('success', 'Abono registrado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al registrar el abono: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostrar detalles del abono
     */
    public function show(Abono $abono)
    {
        $abono->load(['credito', 'cliente', 'user']);
        return view('abonos.show', compact('abono'));
    }

    /**
     * Eliminar abono
     */
    public function destroy(Abono $abono)
    {
        DB::beginTransaction();

        try {
            $credito = $abono->credito;
            $cliente = $abono->cliente;

            // Revertir el abono en el crédito
            $credito->saldo_pendiente += $abono->monto;
            
            // Actualizar estado del crédito
            if ($credito->saldo_pendiente > 0) {
                $credito->estado = $credito->saldo_pendiente == $credito->monto 
                    ? 'pendiente' 
                    : 'parcialmente_pagado';
            }
            
            $credito->save();

            // Revertir saldo del cliente
            $cliente->saldo += $abono->monto;
            $cliente->save();

            // Eliminar el abono
            $abono->delete();

            DB::commit();

            return redirect()->route('creditos.show', $credito->id)
                ->with('success', 'Abono eliminado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al eliminar el abono: ' . $e->getMessage());
        }
    }
}