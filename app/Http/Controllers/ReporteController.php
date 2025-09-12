<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Credito;
use App\Models\Producto;
use App\Models\Compra;
use App\Models\Gasto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReporteController extends Controller
{
    /**
     * Dashboard de reportes
     */
    public function index()
    {
        return view('reportes.index');
    }

    /**
     * Reporte de ventas general
     */
    public function ventas(Request $request)
    {
        $fechaInicio = $request->fecha_inicio ? Carbon::parse($request->fecha_inicio) : Carbon::now()->startOfMonth();
        $fechaFin = $request->fecha_fin ? Carbon::parse($request->fecha_fin) : Carbon::now();

        $ventas = Venta::with('cliente')
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $totalVentas = $ventas->sum('total');
        $ventasContado = $ventas->where('tipo_pago', 'contado')->sum('total');
        $ventasCredito = $ventas->where('tipo_pago', 'credito')->sum('total');

        return view('reportes.ventas', compact(
            'ventas', 'totalVentas', 'ventasContado', 'ventasCredito', 'fechaInicio', 'fechaFin'
        ));
    }

    /**
     * Reporte de ventas diarias
     */
    public function ventasDiario(Request $request)
    {
        $fecha = $request->fecha ? Carbon::parse($request->fecha) : Carbon::today();

        $ventas = Venta::with('cliente')
            ->whereDate('created_at', $fecha)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalVentas = $ventas->sum('total');

        return view('reportes.ventas-diario', compact('ventas', 'totalVentas', 'fecha'));
    }

    /**
     * Reporte de ventas semanales
     */
    public function ventasSemanal(Request $request)
    {
        $fechaInicio = $request->fecha_inicio ? Carbon::parse($request->fecha_inicio) : Carbon::now()->startOfWeek();
        $fechaFin = $request->fecha_fin ? Carbon::parse($request->fecha_fin) : Carbon::now()->endOfWeek();

        $ventas = Venta::with('cliente')
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalVentas = $ventas->sum('total');

        return view('reportes.ventas-semanal', compact('ventas', 'totalVentas', 'fechaInicio', 'fechaFin'));
    }

    /**
     * Reporte de ventas mensuales
     */
    public function ventasMensual(Request $request)
    {
        $year = $request->year ?: Carbon::now()->year;
        $month = $request->month ?: Carbon::now()->month;

        $ventas = Venta::with('cliente')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalVentas = $ventas->sum('total');

        return view('reportes.ventas-mensual', compact('ventas', 'totalVentas', 'year', 'month'));
    }

    /**
     * Reporte de ventas por productos
     */
    public function ventasProductos(Request $request)
    {
        $fechaInicio = $request->fecha_inicio ? Carbon::parse($request->fecha_inicio) : Carbon::now()->startOfMonth();
        $fechaFin = $request->fecha_fin ? Carbon::parse($request->fecha_fin) : Carbon::now();

        $productosVenta = DB::table('detalle_ventas')
            ->join('ventas', 'detalle_ventas.venta_id', '=', 'ventas.id')
            ->join('productos', 'detalle_ventas.producto_id', '=', 'productos.id')
            ->whereBetween('ventas.created_at', [$fechaInicio, $fechaFin])
            ->select(
                'productos.nombre',
                DB::raw('SUM(detalle_ventas.cantidad) as total_vendido'),
                DB::raw('SUM(detalle_ventas.subtotal) as total_ingresos')
            )
            ->groupBy('productos.id', 'productos.nombre')
            ->orderBy('total_ingresos', 'desc')
            ->get();

        return view('reportes.ventas-productos', compact('productosVenta', 'fechaInicio', 'fechaFin'));
    }

    /**
     * Reporte de créditos
     */
    public function creditos(Request $request)
    {
        $estado = $request->estado;

        $creditos = Credito::with(['cliente', 'venta'])
            ->when($estado, function($query) use ($estado) {
                return $query->where('estado', $estado);
            })
            ->orderBy('fecha_limite', 'asc')
            ->paginate(20);

        $totalPendiente = $creditos->sum('saldo_pendiente');

        return view('reportes.creditos', compact('creditos', 'totalPendiente', 'estado'));
    }

    /**
     * Reporte de créditos vencidos
     */
    public function creditosVencidos()
    {
        $creditos = Credito::with(['cliente', 'venta'])
            ->where('fecha_limite', '<', Carbon::now())
            ->where('saldo_pendiente', '>', 0)
            ->orderBy('fecha_limite', 'asc')
            ->paginate(20);

        $totalPendiente = $creditos->sum('saldo_pendiente');

        return view('reportes.creditos-vencidos', compact('creditos', 'totalPendiente'));
    }

    /**
     * Reporte de cobranza
     */
    public function cobranza()
    {
        $cobranza = DB::table('abonos')
            ->join('creditos', 'abonos.credito_id', '=', 'creditos.id')
            ->join('clientes', 'abonos.cliente_id', '=', 'clientes.id')
            ->select(
                'clientes.nombre as cliente',
                DB::raw('SUM(abonos.monto) as total_cobrado'),
                DB::raw('COUNT(abonos.id) as total_abonos')
            )
            ->groupBy('clientes.id', 'clientes.nombre')
            ->orderBy('total_cobrado', 'desc')
            ->get();

        return view('reportes.cobranza', compact('cobranza'));
    }

    /**
     * Reporte de inventario
     */
    public function inventario()
    {
        $productos = Producto::with('categoria')
            ->orderBy('nombre')
            ->get();

        $valorTotal = $productos->sum(function($producto) {
            return $producto->stock * $producto->costo_compra;
        });

        return view('reportes.inventario', compact('productos', 'valorTotal'));
    }

    /**
     * Reporte de stock bajo
     */
    public function stockBajo()
    {
        $productos = Producto::with('categoria')
            ->where('stock', '<=', DB::raw('stock_minimo'))
            ->orderBy('stock', 'asc')
            ->get();

        return view('reportes.inventario-stock-bajo', compact('productos'));
    }

    /**
     * Reporte de inventario valorizado
     */
    public function inventarioValorizado()
    {
        $productos = Producto::with('categoria')
            ->select('*', DB::raw('stock * costo_compra as valor_total'))
            ->orderBy('valor_total', 'desc')
            ->get();

        $valorTotal = $productos->sum('valor_total');

        return view('reportes.inventario-valorizado', compact('productos', 'valorTotal'));
    }

    /**
     * Reporte de movimientos de inventario
     */
    public function movimientosInventario(Request $request)
    {
        $fechaInicio = $request->fecha_inicio ? Carbon::parse($request->fecha_inicio) : Carbon::now()->startOfMonth();
        $fechaFin = $request->fecha_fin ? Carbon::parse($request->fecha_fin) : Carbon::now();

        $movimientos = DB::table('movimientos_inventario')
            ->join('productos', 'movimientos_inventario.producto_id', '=', 'productos.id')
            ->join('users', 'movimientos_inventario.user_id', '=', 'users.id')
            ->whereBetween('movimientos_inventario.created_at', [$fechaInicio, $fechaFin])
            ->select(
                'movimientos_inventario.*',
                'productos.nombre as producto',
                'users.name as usuario'
            )
            ->orderBy('movimientos_inventario.created_at', 'desc')
            ->paginate(20);

        return view('reportes.movimientos-inventario', compact('movimientos', 'fechaInicio', 'fechaFin'));
    }

    /**
     * Reporte de compras
     */
    public function compras(Request $request)
    {
        $fechaInicio = $request->fecha_inicio ? Carbon::parse($request->fecha_inicio) : Carbon::now()->startOfMonth();
        $fechaFin = $request->fecha_fin ? Carbon::parse($request->fecha_fin) : Carbon::now();

        $compras = Compra::with('proveedor')
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $totalCompras = $compras->sum('total');

        return view('reportes.compras', compact('compras', 'totalCompras', 'fechaInicio', 'fechaFin'));
    }

    /**
     * Reporte de compras por proveedores
     */
    public function comprasProveedores(Request $request)
    {
        $fechaInicio = $request->fecha_inicio ? Carbon::parse($request->fecha_inicio) : Carbon::now()->startOfMonth();
        $fechaFin = $request->fecha_fin ? Carbon::parse($request->fecha_fin) : Carbon::now();

        $comprasProveedores = DB::table('compras')
            ->join('proveedores', 'compras.proveedor_id', '=', 'proveedores.id')
            ->whereBetween('compras.created_at', [$fechaInicio, $fechaFin])
            ->select(
                'proveedores.nombre as proveedor',
                DB::raw('COUNT(compras.id) as total_compras'),
                DB::raw('SUM(compras.total) as total_monto')
            )
            ->groupBy('proveedores.id', 'proveedores.nombre')
            ->orderBy('total_monto', 'desc')
            ->get();

        return view('reportes.compras-proveedores', compact('comprasProveedores', 'fechaInicio', 'fechaFin'));
    }

    /**
     * Reporte de gastos
     */
    public function gastos(Request $request)
    {
        $fechaInicio = $request->fecha_inicio ? Carbon::parse($request->fecha_inicio) : Carbon::now()->startOfMonth();
        $fechaFin = $request->fecha_fin ? Carbon::parse($request->fecha_fin) : Carbon::now();

        $gastos = Gasto::with('user')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->orderBy('fecha', 'desc')
            ->paginate(20);

        $totalGastos = $gastos->sum('monto');

        return view('reportes.gastos', compact('gastos', 'totalGastos', 'fechaInicio', 'fechaFin'));
    }

    /**
     * Reporte de gastos por categorías
     */
    public function gastosCategorias(Request $request)
    {
        $fechaInicio = $request->fecha_inicio ? Carbon::parse($request->fecha_inicio) : Carbon::now()->startOfMonth();
        $fechaFin = $request->fecha_fin ? Carbon::parse($request->fecha_fin) : Carbon::now();

        $gastosCategorias = DB::table('gastos')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->select(
                'categoria',
                DB::raw('SUM(monto) as total_gastado'),
                DB::raw('COUNT(id) as total_registros')
            )
            ->groupBy('categoria')
            ->orderBy('total_gastado', 'desc')
            ->get();

        return view('reportes.gastos-categorias', compact('gastosCategorias', 'fechaInicio', 'fechaFin'));
    }

    /**
     * Reportes financieros
     */
    public function financieros(Request $request)
    {
        $fechaInicio = $request->fecha_inicio ? Carbon::parse($request->fecha_inicio) : Carbon::now()->startOfMonth();
        $fechaFin = $request->fecha_fin ? Carbon::parse($request->fecha_fin) : Carbon::now();

        // Ventas
        $totalVentas = Venta::whereBetween('created_at', [$fechaInicio, $fechaFin])->sum('total');
        
        // Compras
        $totalCompras = Compra::whereBetween('created_at', [$fechaInicio, $fechaFin])->sum('total');
        
        // Gastos
        $totalGastos = Gasto::whereBetween('fecha', [$fechaInicio, $fechaFin])->sum('monto');
        
        // Utilidad bruta
        $utilidadBruta = $totalVentas - $totalCompras;
        
        // Utilidad neta
        $utilidadNeta = $utilidadBruta - $totalGastos;

        return view('reportes.financieros', compact(
            'totalVentas', 'totalCompras', 'totalGastos', 'utilidadBruta', 'utilidadNeta', 'fechaInicio', 'fechaFin'
        ));
    }

    /**
     * Balance general
     */
    public function balance()
    {
        // Activos
        $valorInventario = Producto::sum(DB::raw('stock * costo_compra'));
        $cuentasPorCobrar = Credito::where('saldo_pendiente', '>', 0)->sum('saldo_pendiente');
        
        // Pasivos
        $cuentasPorPagar = 0; // Implementar si se manejan compras a crédito
        
        // Patrimonio
        $utilidadAcumulada = 0; // Implementar cálculo de utilidades acumuladas

        return view('reportes.balance', compact(
            'valorInventario', 'cuentasPorCobrar', 'cuentasPorPagar', 'utilidadAcumulada'
        ));
    }

    /**
     * Flujo de caja
     */
    public function flujoCaja(Request $request)
    {
        $fechaInicio = $request->fecha_inicio ? Carbon::parse($request->fecha_inicio) : Carbon::now()->startOfMonth();
        $fechaFin = $request->fecha_fin ? Carbon::parse($request->fecha_fin) : Carbon::now();

        // Ingresos
        $ingresosVentas = Venta::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->where('tipo_pago', 'contado')
            ->sum('total');
        
        $ingresosAbonos = DB::table('abonos')
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->sum('monto');
        
        $totalIngresos = $ingresosVentas + $ingresosAbonos;

        // Egresos
        $egresosCompras = Compra::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->where('tipo_pago', 'contado')
            ->sum('total');
        
        $egresosGastos = Gasto::whereBetween('fecha', [$fechaInicio, $fechaFin])->sum('monto');
        
        $totalEgresos = $egresosCompras + $egresosGastos;

        // Flujo neto
        $flujoNeto = $totalIngresos - $totalEgresos;

        return view('reportes.flujo-caja', compact(
            'ingresosVentas', 'ingresosAbonos', 'totalIngresos',
            'egresosCompras', 'egresosGastos', 'totalEgresos',
            'flujoNeto', 'fechaInicio', 'fechaFin'
        ));
    }

    /**
     * Exportar reportes
     */
    public function exportar(Request $request)
    {
        $request->validate([
            'tipo_reporte' => 'required|in:ventas,creditos,inventario,compras,gastos',
            'formato' => 'required|in:excel,pdf',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date',
        ]);

        // Implementar lógica de exportación según el tipo de reporte
        $tipo = $request->tipo_reporte;
        $formato = $request->formato;

        return response()->json([
            'success' => true,
            'message' => "Reporte de {$tipo} exportado en formato {$formato}",
            'download_url' => "#" // URL de descarga generada
        ]);
    }

    /**
     * Exportar reporte de ventas
     */
    public function exportarVentas(Request $request)
    {
        $request->validate([
            'formato' => 'required|in:excel,pdf',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date',
        ]);

        // Implementar exportación específica de ventas
        return response()->json([
            'success' => true,
            'message' => 'Reporte de ventas exportado',
            'download_url' => "#"
        ]);
    }

    /**
     * Exportar reporte de inventario
     */
    public function exportarInventario(Request $request)
    {
        $request->validate([
            'formato' => 'required|in:excel,pdf',
        ]);

        // Implementar exportación específica de inventario
        return response()->json([
            'success' => true,
            'message' => 'Reporte de inventario exportado',
            'download_url' => "#"
        ]);
    }
}