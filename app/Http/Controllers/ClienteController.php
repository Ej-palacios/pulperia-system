<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClienteRequest;
use App\Models\Cliente;
use App\Models\Credito;
use App\Models\Abono;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
{
    /**
     * Listar todos los clientes
     */
    public function index()
    {
        $clientes = Cliente::withSum('creditos', 'monto')
            ->withSum('abonos', 'monto')
            ->get()
            ->map(function ($cliente) {
                $cliente->saldo = $cliente->creditos_sum_monto - $cliente->abonos_sum_monto;
                return $cliente;
            });

        return view('clientes.index', compact('clientes'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        return view('clientes.create');
    }

    /**
     * Almacenar nuevo cliente
     */
    public function store(StoreClienteRequest $request)
    {
        try {
            Cliente::create($request->validated());

            return redirect()->route('clientes.index')
                ->with('success', 'Cliente creado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al crear cliente: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostrar detalles del cliente
     */
    public function show(Cliente $cliente)
    {
        $creditos = Credito::where('cliente_id', $cliente->id)
            ->with('venta')
            ->orderBy('created_at', 'desc')
            ->get();

        $abonos = Abono::where('cliente_id', $cliente->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        $saldo = $creditos->sum('monto') - $abonos->sum('monto');

        return view('clientes.detalle', compact('cliente', 'creditos', 'abonos', 'saldo'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    /**
     * Actualizar cliente
     */
    public function update(StoreClienteRequest $request, Cliente $cliente)
    {
        try {
            $cliente->update($request->validated());

            return redirect()->route('clientes.index')
                ->with('success', 'Cliente actualizado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar cliente: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Eliminar cliente
     */
    public function destroy(Cliente $cliente)
    {
        // Verificar si el cliente tiene créditos pendientes
        $saldo = $cliente->creditos()->sum('monto') - $cliente->abonos()->sum('monto');
        
        if ($saldo > 0) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar el cliente porque tiene un saldo pendiente de C$ ' . number_format($saldo, 2));
        }

        try {
            $cliente->delete();

            return redirect()->route('clientes.index')
                ->with('success', 'Cliente eliminado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar cliente: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar créditos del cliente
     */
    public function creditos(Cliente $cliente)
    {
        $creditos = Credito::where('cliente_id', $cliente->id)
            ->with('venta')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('clientes.creditos', compact('cliente', 'creditos'));
    }

    /**
     * Mostrar abonos del cliente
     */
    public function abonos(Cliente $cliente)
    {
        $abonos = Abono::where('cliente_id', $cliente->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('clientes.abonos', compact('cliente', 'abonos'));
    }

    /**
     * Búsqueda de clientes (API)
     */
    public function buscar(Request $request)
    {
        $query = $request->get('q');
        
        $clientes = Cliente::where('nombre', 'like', "%{$query}%")
            ->orWhere('cedula', 'like', "%{$query}%")
            ->orWhere('telefono', 'like', "%{$query}%")
            ->take(10)
            ->get();

        return response()->json($clientes);
    }
}