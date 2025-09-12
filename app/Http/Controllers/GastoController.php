<?php

namespace App\Http\Controllers;

use App\Models\Gasto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GastoController extends Controller
{
    /**
     * Listar todos los gastos
     */
    public function index()
    {
        $gastos = Gasto::with('user')
            ->orderBy('fecha', 'desc')
            ->paginate(30);
            
        return view('gastos.index', compact('gastos'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $categorias = ['alquiler', 'luz', 'agua', 'transporte', 'mantenimiento', 'otros'];
        return view('gastos.create', compact('categorias'));
    }

    /**
     * Almacenar nuevo gasto
     */
    public function store(Request $request)
    {
        $request->validate([
            'descripcion' => 'required|string|max:255',
            'categoria' => 'required|string|max:100',
            'monto' => 'required|numeric|min:0.01',
            'fecha' => 'required|date',
            'comprobante' => 'nullable|string|max:255',
        ]);

        try {
            Gasto::create([
                'user_id' => auth()->id(),
                'descripcion' => $request->descripcion,
                'categoria' => $request->categoria,
                'monto' => $request->monto,
                'fecha' => $request->fecha,
                'comprobante' => $request->comprobante,
            ]);

            return redirect()->route('gastos.index')
                ->with('success', 'Gasto registrado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al registrar el gasto: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostrar detalles del gasto
     */
    public function show(Gasto $gasto)
    {
        $gasto->load('user');
        return view('gastos.show', compact('gasto'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Gasto $gasto)
    {
        $categorias = ['alquiler', 'luz', 'agua', 'transporte', 'mantenimiento', 'otros'];
        return view('gastos.edit', compact('gasto', 'categorias'));
    }

    /**
     * Actualizar gasto
     */
    public function update(Request $request, Gasto $gasto)
    {
        $request->validate([
            'descripcion' => 'required|string|max:255',
            'categoria' => 'required|string|max:100',
            'monto' => 'required|numeric|min:0.01',
            'fecha' => 'required|date',
            'comprobante' => 'nullable|string|max:255',
        ]);

        try {
            $gasto->update($request->all());

            return redirect()->route('gastos.index')
                ->with('success', 'Gasto actualizado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar el gasto: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Eliminar gasto
     */
    public function destroy(Gasto $gasto)
    {
        try {
            $gasto->delete();

            return redirect()->route('gastos.index')
                ->with('success', 'Gasto eliminado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar el gasto: ' . $e->getMessage());
        }
    }

    /**
     * Filtrar gastos por categoría
     */
    public function porCategoria($categoria)
    {
        $gastos = Gasto::with('user')
            ->where('categoria', $categoria)
            ->orderBy('fecha', 'desc')
            ->paginate(30);

        $total = $gastos->sum('monto');

        return view('gastos.por-categoria', compact('gastos', 'categoria', 'total'));
    }

    /**
     * Filtrar gastos por mes
     */
    public function porMes($year, $month)
    {
        $gastos = Gasto::with('user')
            ->whereYear('fecha', $year)
            ->whereMonth('fecha', $month)
            ->orderBy('fecha', 'desc')
            ->paginate(30);

        $total = $gastos->sum('monto');

        return view('gastos.por-mes', compact('gastos', 'year', 'month', 'total'));
    }
}