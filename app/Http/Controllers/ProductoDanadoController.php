<?php

namespace App\Http\Controllers;

use App\Models\ProductoDanado;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductoDanadoController extends Controller
{
    /**
     * Listar todos los productos dañados
     */
    public function index()
    {
        $productosDanados = ProductoDanado::with(['producto', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('productos_danados.index', compact('productosDanados'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $productos = Producto::where('stock', '>', 0)->where('activo', true)->get();
        return view('productos_danados.create', compact('productos'));
    }

    /**
     * Almacenar nuevo producto dañado
     */
    public function store(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|numeric|min:0.1',
            'motivo' => 'required|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $producto = Producto::findOrFail($request->producto_id);
            
            // Validar que la cantidad no exceda el stock disponible
            if ($request->cantidad > $producto->stock) {
                return redirect()->back()
                    ->with('error', 'La cantidad excede el stock disponible del producto.');
            }

            // Registrar producto dañado
            $productoDanado = ProductoDanado::create([
                'producto_id' => $producto->id,
                'user_id' => auth()->id(),
                'cantidad' => $request->cantidad,
                'motivo' => $request->motivo,
                'costo_perdida' => $request->cantidad * $producto->costo_compra,
            ]);

            // Reducir stock del producto
            $producto->stock -= $request->cantidad;
            $producto->save();

            DB::commit();

            return redirect()->route('productos-danados.index')
                ->with('success', 'Producto dañado registrado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al registrar el producto dañado: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostrar detalles del producto dañado
     */
    public function show(ProductoDanado $productoDanado)
    {
        $productoDanado->load(['producto', 'user']);
        return view('productos_danados.show', compact('productoDanado'));
    }

    /**
     * Eliminar registro de producto dañado
     */
    public function destroy(ProductoDanado $productoDanado)
    {
        DB::beginTransaction();

        try {
            // Revertir stock del producto
            $producto = $productoDanado->producto;
            $producto->stock += $productoDanado->cantidad;
            $producto->save();

            // Eliminar el registro
            $productoDanado->delete();

            DB::commit();

            return redirect()->route('productos-danados.index')
                ->with('success', 'Registro de producto dañado eliminado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al eliminar el registro: ' . $e->getMessage());
        }
    }
}