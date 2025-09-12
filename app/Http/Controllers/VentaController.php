<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVentaRequest;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Credito;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VentaController extends Controller
{
    /**
     * Mostrar punto de venta
     */
    public function pos()
    {
        $productos = Producto::where('stock', '>', 0)->where('activo', true)->get();
        $clientes = Cliente::all();
        return view('ventas.pos', compact('productos', 'clientes'));
    }

    /**
     * Listar todas las ventas
     */
    public function index()
    {
        $ventas = Venta::with(['cliente', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('ventas.index', compact('ventas'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $productos = Producto::where('stock', '>', 0)->where('activo', true)->get();
        $clientes = Cliente::all();
        return view('ventas.create', compact('productos', 'clientes'));
    }

    /**
     * Almacenar nueva venta
     */
    public function store(StoreVentaRequest $request)
    {
        DB::beginTransaction();

        try {
            // Crear la venta
            $venta = Venta::create([
                'cliente_id' => $request->cliente_id,
                'user_id' => auth()->id(),
                'subtotal' => $request->subtotal,
                'impuestos' => $request->impuestos,
                'total' => $request->total,
                'tipo_pago' => $request->tipo_pago,
                'estado' => 'completada',
            ]);

            // Procesar detalles de venta
            foreach ($request->productos as $productoVenta) {
                $producto = Producto::find($productoVenta['id']);
                
                // Crear detalle de venta
                DetalleVenta::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $productoVenta['cantidad'],
                    'precio_unitario' => $productoVenta['precio'],
                    'subtotal' => $productoVenta['subtotal'],
                ]);

                // Actualizar stock
                $producto->stock -= $productoVenta['cantidad'];
                $producto->save();
            }

            // Si es crédito, registrar el crédito
            if ($request->tipo_pago === 'credito') {
                Credito::create([
                    'venta_id' => $venta->id,
                    'cliente_id' => $request->cliente_id,
                    'monto' => $request->total,
                    'saldo_pendiente' => $request->total,
                    'fecha_limite' => Carbon::now()->addDays(30),
                    'estado' => 'pendiente',
                ]);

                // Actualizar saldo del cliente
                $cliente = Cliente::find($request->cliente_id);
                $cliente->saldo += $request->total;
                $cliente->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'venta_id' => $venta->id,
                'message' => 'Venta procesada correctamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la venta: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Procesar venta desde POS
     */
    public function procesarVenta(Request $request)
    {
        return $this->store(new StoreVentaRequest($request->all()));
    }

    /**
     * Mostrar detalles de la venta
     */
    public function show(Venta $venta)
    {
        $venta->load(['detalles.producto', 'cliente', 'user', 'credito']);
        return view('ventas.show', compact('venta'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Venta $venta)
    {
        // Solo permitir edición de ventas del mismo día
        if ($venta->created_at->format('Y-m-d') !== now()->format('Y-m-d')) {
            return redirect()->back()
                ->with('error', 'Solo se pueden editar ventas del día actual.');
        }

        $venta->load(['detalles.producto', 'cliente']);
        $productos = Producto::where('activo', true)->get();
        $clientes = Cliente::all();

        return view('ventas.edit', compact('venta', 'productos', 'clientes'));
    }

    /**
     * Actualizar venta
     */
    public function update(Request $request, Venta $venta)
    {
        // Validar que la venta sea del día actual
        if ($venta->created_at->format('Y-m-d') !== now()->format('Y-m-d')) {
            return redirect()->back()
                ->with('error', 'Solo se pueden editar ventas del día actual.');
        }

        DB::beginTransaction();

        try {
            // Lógica de actualización de venta
            // (Implementar según necesidades específicas)

            DB::commit();

            return redirect()->route('ventas.show', $venta)
                ->with('success', 'Venta actualizada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al actualizar venta: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar venta (anular)
     */
    public function destroy(Venta $venta)
    {
        // Solo permitir anulación para ventas recientes (mismo día)
        if ($venta->created_at->format('Y-m-d') !== now()->format('Y-m-d')) {
            return redirect()->back()
                ->with('error', 'Solo se pueden anular ventas del día actual.');
        }

        DB::beginTransaction();

        try {
            // Revertir stock de productos
            foreach ($venta->detalles as $detalle) {
                $producto = Producto::find($detalle->producto_id);
                $producto->stock += $detalle->cantidad;
                $producto->save();
            }

            // Revertir crédito si existe
            if ($venta->tipo_pago === 'credito' && $venta->credito) {
                $cliente = Cliente::find($venta->cliente_id);
                $cliente->saldo -= $venta->credito->monto;
                $cliente->save();
                
                $venta->credito->delete();
            }

            // Eliminar detalles y la venta
            $venta->detalles()->delete();
            $venta->delete();

            DB::commit();

            return redirect()->route('ventas.index')
                ->with('success', 'Venta anulada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al anular la venta: ' . $e->getMessage());
        }
    }

    /**
     * Anular venta
     */
    public function anular(Venta $venta)
    {
        return $this->destroy($venta);
    }

    /**
     * Generar factura
     */
    public function generarFactura(Venta $venta)
    {
        $venta->load(['detalles.producto', 'cliente', 'user']);
        
        // Implementar generación de PDF
        return response()->json([
            'success' => true,
            'venta' => $venta
        ]);
    }

    /**
     * Reimprimir ticket
     */
    public function reimprimir(Venta $venta)
    {
        $venta->load(['detalles.producto', 'cliente']);
        
        // Implementar reimpresión de ticket
        return response()->json([
            'success' => true,
            'venta' => $venta
        ]);
    }

    /**
     * Imprimir ticket
     */
    public function imprimirTicket(Venta $venta)
    {
        $venta->load(['detalles.producto', 'cliente']);
        
        // Implementar impresión de ticket
        return response()->json([
            'success' => true,
            'venta' => $venta
        ]);
    }

    /**
     * Obtener venta específica (API)
     */
    public function apiShow(Venta $venta)
    {
        $venta->load(['detalles.producto', 'cliente', 'user', 'credito']);
        return response()->json($venta);
    }

    /**
     * Almacenar venta (API)
     */
    public function apiStore(Request $request)
    {
        return $this->store(new StoreVentaRequest($request->all()));
    }
}