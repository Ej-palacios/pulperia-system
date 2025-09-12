<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{
    /**
     * Listar todas las compras
     */
    public function index()
    {
        $compras = Compra::with('proveedor')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('compras.index', compact('compras'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $proveedores = Proveedor::all();
        $productos = Producto::where('activo', true)->get();
        return view('compras.create', compact('proveedores', 'productos'));
    }

    /**
     * Almacenar nueva compra
     */
    public function store(Request $request)
    {
        $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|numeric|min:0.1',
            'productos.*.precio_compra' => 'required|numeric|min:0.01',
            'tipo_pago' => 'required|in:contado,credito',
            'numero_factura' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            // Calcular totales
            $subtotal = 0;
            foreach ($request->productos as $productoCompra) {
                $subtotal += $productoCompra['cantidad'] * $productoCompra['precio_compra'];
            }

            // Crear la compra
            $compra = Compra::create([
                'proveedor_id' => $request->proveedor_id,
                'user_id' => auth()->id(),
                'numero_factura' => $request->numero_factura,
                'subtotal' => $subtotal,
                'total' => $subtotal, // En compras podría haber impuestos u otros cargos
                'tipo_pago' => $request->tipo_pago,
                'estado' => 'completada',
            ]);

            // Procesar detalles de compra
            foreach ($request->productos as $productoCompra) {
                $producto = Producto::find($productoCompra['id']);
                
                // Crear detalle de compra
                DetalleCompra::create([
                    'compra_id' => $compra->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $productoCompra['cantidad'],
                    'precio_compra' => $productoCompra['precio_compra'],
                    'subtotal' => $productoCompra['cantidad'] * $productoCompra['precio_compra'],
                ]);

                // Actualizar stock y precio de compra del producto
                $producto->stock += $productoCompra['cantidad'];
                $producto->costo_compra = $productoCompra['precio_compra'];
                $producto->save();
            }

            DB::commit();

            return redirect()->route('compras.index')
                ->with('success', 'Compra registrada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al registrar la compra: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostrar detalles de la compra
     */
    public function show(Compra $compra)
    {
        $compra->load(['detalles.producto', 'proveedor', 'user']);
        return view('compras.detalle', compact('compra'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Compra $compra)
    {
        // Solo permitir edición de compras del mismo día
        if ($compra->created_at->format('Y-m-d') !== now()->format('Y-m-d')) {
            return redirect()->back()
                ->with('error', 'Solo se pueden editar compras del día actual.');
        }

        $compra->load(['detalles.producto', 'proveedor']);
        $proveedores = Proveedor::all();
        $productos = Producto::where('activo', true)->get();

        return view('compras.edit', compact('compra', 'proveedores', 'productos'));
    }

    /**
     * Actualizar compra
     */
    public function update(Request $request, Compra $compra)
    {
        // Validar que la compra sea del día actual
        if ($compra->created_at->format('Y-m-d') !== now()->format('Y-m-d')) {
            return redirect()->back()
                ->with('error', 'Solo se pueden editar compras del día actual.');
        }

        DB::beginTransaction();

        try {
            // Lógica de actualización de compra
            // (Implementar según necesidades específicas)

            DB::commit();

            return redirect()->route('compras.show', $compra)
                ->with('success', 'Compra actualizada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al actualizar compra: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar compra
     */
    public function destroy(Compra $compra)
    {
        // Solo permitir eliminación para compras recientes (mismo día)
        if ($compra->created_at->format('Y-m-d') !== now()->format('Y-m-d')) {
            return redirect()->back()
                ->with('error', 'Solo se pueden eliminar compras del día actual.');
        }

        DB::beginTransaction();

        try {
            // Revertir stock de productos
            foreach ($compra->detalles as $detalle) {
                $producto = Producto::find($detalle->producto_id);
                $producto->stock -= $detalle->cantidad;
                $producto->save();
            }

            // Eliminar detalles y la compra
            $compra->detalles()->delete();
            $compra->delete();

            DB::commit();

            return redirect()->route('compras.index')
                ->with('success', 'Compra eliminada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al eliminar la compra: ' . $e->getMessage());
        }
    }

    /**
     * Generar recibo de compra
     */
    public function generarRecibo(Compra $compra)
    {
        $compra->load(['detalles.producto', 'proveedor', 'user']);
        
        // Implementar generación de PDF
        return response()->json([
            'success' => true,
            'compra' => $compra
        ]);
    }

    /**
     * Marcar compra como recibida
     */
    public function marcarRecibida(Compra $compra)
    {
        try {
            $compra->estado = 'recibida';
            $compra->save();

            return redirect()->back()
                ->with('success', 'Compra marcada como recibida.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al marcar compra como recibida: ' . $e->getMessage());
        }
    }
}