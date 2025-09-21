<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Credito;
use Carbon\Carbon;
use DB;

class DashboardController extends Controller
{
    /**
     * Mostrar el dashboard principal
     */
    public function index()
    {
        $hoy = Carbon::today();
        $inicioSemana = Carbon::now()->startOfWeek();
        $inicioMes = Carbon::now()->startOfMonth();

        // Estadísticas de ventas
        $ventasHoy = Venta::whereDate('created_at', $hoy)->sum('total');
        $ventasSemana = Venta::whereBetween('created_at', [$inicioSemana, Carbon::now()])->sum('total');
        $ventasMes = Venta::whereBetween('created_at', [$inicioMes, Carbon::now()])->sum('total');

        // Total de créditos pendientes
        $totalCreditos = Credito::where('saldo_pendiente', '>', 0)->sum('saldo_pendiente');

        // Productos con stock bajo
        $productosStockBajo = Producto::where('stock', '<=', DB::raw('stock_minimo'))
            ->orderBy('stock', 'asc')
            ->take(5)
            ->get();

        // Contar productos con stock bajo
        $stockBajoCount = Producto::where('stock', '<=', DB::raw('stock_minimo'))->count();

        // Valor total del inventario
        $valorInventario = Producto::sum(DB::raw('stock * costo_compra'));

        // Ventas recientes
        $ventasRecientes = Venta::with('cliente')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'ventasHoy', 
            'ventasSemana', 
            'ventasMes',
            'totalCreditos',
            'productosStockBajo',
            'stockBajoCount',
            'valorInventario',
            'ventasRecientes'
        ));
    }
}