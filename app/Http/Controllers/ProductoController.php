<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductoRequest;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\MovimientoInventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    /**
     * Listar todos los productos
     */
    public function index()
    {
        $productos = Producto::with('categoria')->get();
        return view('productos.index', compact('productos'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $categorias = Categoria::all();
        return view('productos.create', compact('categorias'));
    }

    /**
     * Almacenar nuevo producto
     */
    public function store(StoreProductoRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();

            // Manejo de imagen: subida o URL externa
            if ($request->hasFile('imagen')) {
                $imagePath = $request->file('imagen')->store('productos', 'public');
                $data['imagen'] = $imagePath;
            } elseif ($request->filled('imagen_url')) {
                $data['imagen'] = $request->imagen_url;
            }

            $producto = Producto::create($data);

            // Registrar movimiento inicial de inventario
            if ($data['stock'] > 0) {
                MovimientoInventario::create([
                    'producto_id' => $producto->id,
                    'user_id' => auth()->id(),
                    'cantidad' => $data['stock'],
                    'tipo' => 'entrada',
                    'motivo' => 'Stock inicial',
                    'referencia_type' => 'App\Models\Producto',
                    'referencia_id' => $producto->id
                ]);
            }

            DB::commit();

            return redirect()->route('productos.index')
                ->with('success', 'Producto creado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al crear producto: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostrar detalles del producto
     */
    public function show(Producto $producto)
    {
        $movimientos = $producto->movimientos()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('productos.detalle', compact('producto', 'movimientos'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Producto $producto)
    {
        $categorias = Categoria::all();
        return view('productos.edit', compact('producto', 'categorias'));
    }

    /**
     * Actualizar producto
     */
    public function update(StoreProductoRequest $request, Producto $producto)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();

            // Manejo de imagen: subida o URL externa
            if ($request->hasFile('imagen')) {
                // Eliminar imagen anterior local si existía y era local
                if ($producto->imagen && !str_starts_with($producto->imagen, 'http')) {
                    Storage::disk('public')->delete($producto->imagen);
                }
                $imagePath = $request->file('imagen')->store('productos', 'public');
                $data['imagen'] = $imagePath;
            } elseif ($request->filled('imagen_url')) {
                $data['imagen'] = $request->imagen_url;
            }

            $producto->update($data);

            DB::commit();

            return redirect()->route('productos.index')
                ->with('success', 'Producto actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al actualizar producto: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Eliminar producto
     */
    public function destroy(Producto $producto)
    {
        // Verificar si el producto tiene movimientos
        if ($producto->movimientos()->count() > 0) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar el producto porque tiene movimientos asociados.');
        }

        try {
            // Eliminar imagen si existe
            if ($producto->imagen) {
                Storage::disk('public')->delete($producto->imagen);
            }

            $producto->delete();

            return redirect()->route('productos.index')
                ->with('success', 'Producto eliminado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar producto: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar movimientos del producto
     */
    public function movimientos(Producto $producto)
    {
        $movimientos = $producto->movimientos()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('productos.movimientos', compact('producto', 'movimientos'));
    }

    /**
     * Ajustar stock del producto
     */
    public function ajustarStock(Request $request, Producto $producto)
    {
        $request->validate([
            'cantidad' => 'required|numeric',
            'motivo' => 'required|string|max:255',
            'tipo' => 'required|in:entrada,salida',
        ]);

        DB::beginTransaction();

        try {
            $cantidad = $request->tipo === 'entrada' 
                ? $request->cantidad 
                : -$request->cantidad;

            // Registrar movimiento
            MovimientoInventario::create([
                'producto_id' => $producto->id,
                'user_id' => auth()->id(),
                'cantidad' => abs($cantidad),
                'tipo' => $request->tipo,
                'motivo' => $request->motivo,
                'referencia_type' => 'App\Models\Producto',
                'referencia_id' => $producto->id
            ]);

            // Actualizar stock
            $producto->stock += $cantidad;
            $producto->save();

            DB::commit();

            return redirect()->back()
                ->with('success', 'Stock ajustado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al ajustar stock: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar historial del producto
     */
    public function historial(Producto $producto)
    {
        $historial = $producto->movimientos()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('productos.historial', compact('producto', 'historial'));
    }

    /**
     * Búsqueda de productos (API)
     */
    public function buscar(Request $request)
    {
        $query = $request->get('q');
        
        $productos = Producto::where('nombre', 'like', "%{$query}%")
            ->orWhere('codigo_barras', 'like', "%{$query}%")
            ->where('activo', true)
            ->where('stock', '>', 0)
            ->take(10)
            ->get();

        return response()->json($productos);
    }

    /**
     * Obtener productos activos (API)
     */
    public function apiActivos()
    {
        $productos = Producto::with('categoria')
            ->where('activo', true)
            ->get();

        return response()->json($productos);
    }

    /**
     * Obtener producto específico (API)
     */
    public function apiShow(Producto $producto)
    {
        return response()->json($producto->load('categoria'));
    }

    /**
     * Generar código de barras
     */
    public function generarCodigoBarras($codigo)
    {
        // Implementar generación de código de barras
        return response()->json(['codigo' => $codigo]);
    }
}