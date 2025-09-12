<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoriaController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:dueño|administrador');
    }

    /**
     * Listar todas las categorías
     */
    public function index()
    {
        $categorias = Categoria::withCount('productos')->get();
        return view('categorias.index', compact('categorias'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        return view('categorias.create');
    }

    /**
     * Almacenar nueva categoría
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:categorias',
            'descripcion' => 'nullable|string',
        ]);

        try {
            Categoria::create($request->all());

            return redirect()->route('categorias.index')
                ->with('success', 'Categoría creada exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al crear categoría: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostrar detalles de la categoría
     */
    public function show(Categoria $categoria)
    {
        $productos = $categoria->productos()->paginate(10);
        return view('categorias.show', compact('categoria', 'productos'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Categoria $categoria)
    {
        return view('categorias.edit', compact('categoria'));
    }

    /**
     * Actualizar categoría
     */
    public function update(Request $request, Categoria $categoria)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:categorias,nombre,' . $categoria->id,
            'descripcion' => 'nullable|string',
        ]);

        try {
            $categoria->update($request->all());

            return redirect()->route('categorias.index')
                ->with('success', 'Categoría actualizada exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar categoría: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Eliminar categoría
     */
    public function destroy(Categoria $categoria)
    {
        // Verificar si la categoría tiene productos asociados
        if ($categoria->productos()->count() > 0) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar la categoría porque tiene productos asociados.');
        }

        try {
            $categoria->delete();

            return redirect()->route('categorias.index')
                ->with('success', 'Categoría eliminada exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar categoría: ' . $e->getMessage());
        }
    }
}