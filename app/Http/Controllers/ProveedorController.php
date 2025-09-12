<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProveedorRequest;
use App\Models\Proveedor;
use App\Models\Compra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProveedorController extends Controller
{
    /**
     * Listar todos los proveedores
     */
    public function index()
    {
        $proveedores = Proveedor::with(['compras' => function($query) {
            $query->select('proveedor_id', DB::raw('SUM(total) as total_compras'));
            $query->groupBy('proveedor_id');
        }])->get();

        return view('proveedores.index', compact('proveedores'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        return view('proveedores.create');
    }

    /**
     * Almacenar nuevo proveedor
     */
    public function store(StoreProveedorRequest $request)
    {
        try {
            Proveedor::create($request->validated());

            return redirect()->route('proveedores.index')
                ->with('success', 'Proveedor creado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al crear proveedor: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostrar detalles del proveedor
     */
    public function show(Proveedor $proveedor)
    {
        $compras = Compra::where('proveedor_id', $proveedor->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('proveedores.detalle', compact('proveedor', 'compras'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Proveedor $proveedor)
    {
        return view('proveedores.edit', compact('proveedor'));
    }

    /**
     * Actualizar proveedor
     */
    public function update(StoreProveedorRequest $request, Proveedor $proveedor)
    {
        try {
            $proveedor->update($request->validated());

            return redirect()->route('proveedores.index')
                ->with('success', 'Proveedor actualizado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar proveedor: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Eliminar proveedor
     */
    public function destroy(Proveedor $proveedor)
    {
        // Verificar si el proveedor tiene compras asociadas
        if ($proveedor->compras()->count() > 0) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar el proveedor porque tiene compras asociadas.');
        }

        try {
            $proveedor->delete();

            return redirect()->route('proveedores.index')
                ->with('success', 'Proveedor eliminado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar proveedor: ' . $e->getMessage());
        }
    }

    /**
     * Búsqueda de proveedores (API)
     */
    public function buscar(Request $request)
    {
        $query = $request->get('q');
        
        $proveedores = Proveedor::where('nombre', 'like', "%{$query}%")
            ->orWhere('contacto', 'like', "%{$query}%")
            ->orWhere('telefono', 'like', "%{$query}%")
            ->take(10)
            ->get();

        return response()->json($proveedores);
    }
}