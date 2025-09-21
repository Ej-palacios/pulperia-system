<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\CreditoController;
use App\Http\Controllers\AbonoController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\ProductoDanadoController;
use App\Http\Controllers\ArqueoCajaController;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\ConfiguracionTiendaController;
use App\Http\Controllers\ReporteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas Públicas
|--------------------------------------------------------------------------
*/

// Redirigir raíz a login para evitar ver ambos formularios a la vez
Route::get('/', function () {
    return redirect()->route('login');
})->name('welcome');

// Rutas de autenticación
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Rutas de registro
Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);

/*
|--------------------------------------------------------------------------
| Rutas Protegidas - Requieren Autenticación
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Gestión de Usuarios (Solo Admin/Dueño)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:dueño|administrador'])->group(function () {
        Route::resource('usuarios', UserController::class)->names([
            'index' => 'usuarios.index',
            'create' => 'usuarios.create',
            'store' => 'usuarios.store',
            'show' => 'usuarios.show',
            'edit' => 'usuarios.edit',
            'update' => 'usuarios.update',
            'destroy' => 'usuarios.destroy'
        ])->parameters([
            'usuarios' => 'user'
        ]);

        // Ruta adicional para cambiar estado de usuario
        Route::put('usuarios/{user}/estado', [UserController::class, 'updateStatus'])
            ->name('usuarios.estado.update');
    });

    /*
    |--------------------------------------------------------------------------
    | Gestión de Clientes
    |--------------------------------------------------------------------------
    */
    Route::resource('clientes', ClienteController::class)->names([
        'index' => 'clientes.index',
        'create' => 'clientes.create',
        'store' => 'clientes.store',
        'show' => 'clientes.show',
        'edit' => 'clientes.edit',
        'update' => 'clientes.update',
        'destroy' => 'clientes.destroy'
    ]);

    // Rutas adicionales para clientes
    Route::get('clientes/{cliente}/creditos', [ClienteController::class, 'creditos'])
        ->name('clientes.creditos');
    Route::get('clientes/{cliente}/abonos', [ClienteController::class, 'abonos'])
        ->name('clientes.abonos');

    /*
    |--------------------------------------------------------------------------
    | Gestión de Proveedores
    |--------------------------------------------------------------------------
    */
    Route::resource('proveedores', ProveedorController::class)->names([
        'index' => 'proveedores.index',
        'create' => 'proveedores.create',
        'store' => 'proveedores.store',
        'show' => 'proveedores.show',
        'edit' => 'proveedores.edit',
        'update' => 'proveedores.update',
        'destroy' => 'proveedores.destroy'
    ]);

    /*
    |--------------------------------------------------------------------------
    | Gestión de Categorías (Solo Admin/Dueño)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:dueño|administrador'])->group(function () {
        Route::resource('categorias', CategoriaController::class)->names([
            'index' => 'categorias.index',
            'create' => 'categorias.create',
            'store' => 'categorias.store',
            'show' => 'categorias.show',
            'edit' => 'categorias.edit',
            'update' => 'categorias.update',
            'destroy' => 'categorias.destroy'
        ]);
    });

    /*
    |--------------------------------------------------------------------------
    | Gestión de Productos
    |--------------------------------------------------------------------------
    */
    Route::resource('productos', ProductoController::class)->names([
        'index' => 'productos.index',
        'create' => 'productos.create',
        'store' => 'productos.store',
        'show' => 'productos.show',
        'edit' => 'productos.edit',
        'update' => 'productos.update',
        'destroy' => 'productos.destroy'
    ]);

    // Rutas adicionales para productos
    Route::get('productos/{producto}/movimientos', [ProductoController::class, 'movimientos'])
        ->name('productos.movimientos');
    Route::post('productos/{producto}/ajustar-stock', [ProductoController::class, 'ajustarStock'])
        ->name('productos.ajustar-stock');
    Route::get('productos/{producto}/historial', [ProductoController::class, 'historial'])
        ->name('productos.historial');

    /*
    |--------------------------------------------------------------------------
    | Gestión de Ventas
    |--------------------------------------------------------------------------
    */
    Route::prefix('ventas')->group(function () {
        // Punto de venta
        Route::get('pos', [VentaController::class, 'pos'])->name('ventas.pos');
        
        // Procesar venta desde POS
        Route::post('procesar', [VentaController::class, 'procesarVenta'])->name('ventas.procesar');
        
        // Resource routes
        Route::resource('/', VentaController::class)->names([
            'index' => 'ventas.index',
            'create' => 'ventas.create',
            'store' => 'ventas.store',
            'show' => 'ventas.show',
            'edit' => 'ventas.edit',
            'update' => 'ventas.update',
            'destroy' => 'ventas.destroy'
        ])->parameters(['' => 'venta']);

        // Rutas adicionales
        Route::get('{venta}/factura', [VentaController::class, 'generarFactura'])->name('ventas.factura');
        Route::post('{venta}/anular', [VentaController::class, 'anular'])->name('ventas.anular');
        Route::get('{venta}/reimprimir', [VentaController::class, 'reimprimir'])->name('ventas.reimprimir');
    });

    /*
    |--------------------------------------------------------------------------
    | Gestión de Créditos
    |--------------------------------------------------------------------------
    */
    Route::resource('creditos', CreditoController::class)->names([
        'index' => 'creditos.index',
        'create' => 'creditos.create',
        'store' => 'creditos.store',
        'show' => 'creditos.show',
        'edit' => 'creditos.edit',
        'update' => 'creditos.update',
        'destroy' => 'creditos.destroy'
    ])->except(['create', 'store']); // Los créditos se crean automáticamente con las ventas

    // Rutas adicionales para créditos
    Route::get('creditos/{credito}/estado', [CreditoController::class, 'cambiarEstado'])
        ->name('creditos.estado');
    Route::post('creditos/{credito}/vencer', [CreditoController::class, 'marcarComoVencido'])
        ->name('creditos.vencer');

    /*
    |--------------------------------------------------------------------------
    | Gestión de Abonos
    |--------------------------------------------------------------------------
    */
    Route::prefix('abonos')->group(function () {
        Route::get('crear/{credito}', [AbonoController::class, 'create'])->name('abonos.create');
        Route::post('store', [AbonoController::class, 'store'])->name('abonos.store');
        Route::get('{abono}', [AbonoController::class, 'show'])->name('abonos.show');
        Route::delete('{abono}', [AbonoController::class, 'destroy'])->name('abonos.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Gestión de Compras
    |--------------------------------------------------------------------------
    */
    Route::resource('compras', CompraController::class)->names([
        'index' => 'compras.index',
        'create' => 'compras.create',
        'store' => 'compras.store',
        'show' => 'compras.show',
        'edit' => 'compras.edit',
        'update' => 'compras.update',
        'destroy' => 'compras.destroy'
    ]);

    // Rutas adicionales para compras
    Route::get('compras/{compra}/recibo', [CompraController::class, 'generarRecibo'])
        ->name('compras.recibo');
    Route::post('compras/{compra}/recibir', [CompraController::class, 'marcarRecibida'])
        ->name('compras.recibir');

    /*
    |--------------------------------------------------------------------------
    | Gestión de Productos Dañados
    |--------------------------------------------------------------------------
    */
    Route::prefix('productos-danados')->group(function () {
        Route::get('/', [ProductoDanadoController::class, 'index'])->name('productos-danados.index');
        Route::get('crear', [ProductoDanadoController::class, 'create'])->name('productos-danados.create');
        Route::post('store', [ProductoDanadoController::class, 'store'])->name('productos-danados.store');
        Route::get('{productoDanado}', [ProductoDanadoController::class, 'show'])->name('productos-danados.show');
        Route::delete('{productoDanado}', [ProductoDanadoController::class, 'destroy'])->name('productos-danados.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Arqueo de Caja
    |--------------------------------------------------------------------------
    */
    Route::prefix('arqueo-caja')->group(function () {
        Route::get('/', [ArqueoCajaController::class, 'index'])->name('arqueo-caja.index');
        Route::get('crear', [ArqueoCajaController::class, 'create'])->name('arqueo-caja.create');
        Route::post('store', [ArqueoCajaController::class, 'store'])->name('arqueo-caja.store');
        Route::get('{arqueoCaja}', [ArqueoCajaController::class, 'show'])->name('arqueo-caja.show');
        Route::get('{arqueoCaja}/reporte', [ArqueoCajaController::class, 'generarReporte'])->name('arqueo-caja.reporte');
        
        // Arqueo rápido (para cierres diarios)
        Route::post('cierre-rapido', [ArqueoCajaController::class, 'cierreRapido'])->name('arqueo-caja.cierre-rapido');
    });

    /*
    |--------------------------------------------------------------------------
    | Gestión de Gastos
    |--------------------------------------------------------------------------
    */
    Route::resource('gastos', GastoController::class)->names([
        'index' => 'gastos.index',
        'create' => 'gastos.create',
        'store' => 'gastos.store',
        'show' => 'gastos.show',
        'edit' => 'gastos.edit',
        'update' => 'gastos.update',
        'destroy' => 'gastos.destroy'
    ]);

    // Rutas adicionales para gastos
    Route::get('gastos/categoria/{categoria}', [GastoController::class, 'porCategoria'])
        ->name('gastos.por-categoria');
    Route::get('gastos/mes/{year}/{month}', [GastoController::class, 'porMes'])
        ->name('gastos.por-mes');

    /*
    |--------------------------------------------------------------------------
    | Configuración de Tienda (Solo Dueño)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:dueño'])->prefix('configuracion')->group(function () {
        Route::get('/', [ConfiguracionTiendaController::class, 'edit'])->name('configuracion.edit');
        Route::put('/', [ConfiguracionTiendaController::class, 'update'])->name('configuracion.update');
        
        // Rutas adicionales de configuración
        Route::get('backup', [ConfiguracionTiendaController::class, 'backup'])->name('configuracion.backup');
        Route::post('restore', [ConfiguracionTiendaController::class, 'restore'])->name('configuracion.restore');
        Route::get('logs', [ConfiguracionTiendaController::class, 'logs'])->name('configuracion.logs');
    });

    /*
    |--------------------------------------------------------------------------
    | Reportes
    |--------------------------------------------------------------------------
    */
    Route::prefix('reportes')->group(function () {
        // Dashboard de reportes
        Route::get('/', [ReporteController::class, 'index'])->name('reportes.index');
        
        // Reportes de ventas
        Route::get('ventas', [ReporteController::class, 'ventas'])->name('reportes.ventas');
        Route::get('ventas/diario', [ReporteController::class, 'ventasDiario'])->name('reportes.ventas.diario');
        Route::get('ventas/semanal', [ReporteController::class, 'ventasSemanal'])->name('reportes.ventas.semanal');
        Route::get('ventas/mensual', [ReporteController::class, 'ventasMensual'])->name('reportes.ventas.mensual');
        Route::get('ventas/productos', [ReporteController::class, 'ventasProductos'])->name('reportes.ventas.productos');
        
        // Reportes de créditos
        Route::get('creditos', [ReporteController::class, 'creditos'])->name('reportes.creditos');
        Route::get('creditos/vencidos', [ReporteController::class, 'creditosVencidos'])->name('reportes.creditos.vencidos');
        Route::get('creditos/cobranza', [ReporteController::class, 'cobranza'])->name('reportes.cobranza');
        
        // Reportes de inventario
        Route::get('inventario', [ReporteController::class, 'inventario'])->name('reportes.inventario');
        Route::get('inventario/stock-bajo', [ReporteController::class, 'stockBajo'])->name('reportes.inventario.stock-bajo');
        Route::get('inventario/valorizado', [ReporteController::class, 'inventarioValorizado'])->name('reportes.inventario.valorizado');
        Route::get('inventario/movimientos', [ReporteController::class, 'movimientosInventario'])->name('reportes.inventario.movimientos');
        
        // Reportes de compras
        Route::get('compras', [ReporteController::class, 'compras'])->name('reportes.compras');
        Route::get('compras/proveedores', [ReporteController::class, 'comprasProveedores'])->name('reportes.compras.proveedores');
        
        // Reportes de gastos
        Route::get('gastos', [ReporteController::class, 'gastos'])->name('reportes.gastos');
        Route::get('gastos/categorias', [ReporteController::class, 'gastosCategorias'])->name('reportes.gastos.categorias');
        
        // Reportes financieros
        Route::get('financieros', [ReporteController::class, 'financieros'])->name('reportes.financieros');
        Route::get('financieros/balance', [ReporteController::class, 'balance'])->name('reportes.balance');
        Route::get('financieros/flujo-caja', [ReporteController::class, 'flujoCaja'])->name('reportes.flujo-caja');
        
        // Exportación de reportes
        Route::post('exportar', [ReporteController::class, 'exportar'])->name('reportes.exportar');
        Route::get('exportar/ventas', [ReporteController::class, 'exportarVentas'])->name('reportes.exportar.ventas');
        Route::get('exportar/inventario', [ReporteController::class, 'exportarInventario'])->name('reportes.exportar.inventario');
    });

    /*
    |--------------------------------------------------------------------------
    | Rutas de Utilidad
    |--------------------------------------------------------------------------
    */
    Route::prefix('util')->group(function () {
        // Búsqueda rápida
        Route::get('buscar/productos', [ProductoController::class, 'buscar'])->name('util.buscar.productos');
        Route::get('buscar/clientes', [ClienteController::class, 'buscar'])->name('util.buscar.clientes');
        Route::get('buscar/proveedores', [ProveedorController::class, 'buscar'])->name('util.buscar.proveedores');
        
        // Códigos de barras
        Route::get('codigo-barras/{codigo}', [ProductoController::class, 'generarCodigoBarras'])
            ->name('util.codigo-barras');
        
        // Impresión rápida
        Route::get('imprimir/ticket/{venta}', [VentaController::class, 'imprimirTicket'])
            ->name('util.imprimir.ticket');
        
        // Backup de datos
        Route::post('backup/crear', [ConfiguracionTiendaController::class, 'crearBackup'])
            ->name('util.backup.crear');
    });

});

/*
|--------------------------------------------------------------------------
| Rutas de Fallback
|--------------------------------------------------------------------------
*/

// Ruta para páginas no encontradas
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});

/*
|--------------------------------------------------------------------------
| Rutas de Health Check (Para monitoreo)
|--------------------------------------------------------------------------
*/

Route::get('health', function () {
    return response()->json([
        'status' => 'OK',
        'timestamp' => now(),
        'service' => 'Pulpería Management System'
    ]);
})->name('health.check');

/*
|--------------------------------------------------------------------------
| Rutas de Mantenimiento
|--------------------------------------------------------------------------
*/

Route::get('maintenance', function () {
    return response()->view('maintenance', [], 503);
})->name('maintenance');

/*
|--------------------------------------------------------------------------
| Rutas de Demo (Para desarrollo)
|--------------------------------------------------------------------------
*/

if (app()->environment('local')) {
    Route::prefix('demo')->group(function () {
        Route::get('datos', function () {
            // Generar datos de demostración
            return response()->json(['message' => 'Demo data generation endpoint']);
        })->name('demo.datos');
        
        Route::get('reset', function () {
            // Resetear datos de demo
            return response()->json(['message' => 'Demo reset endpoint']);
        })->name('demo.reset');
    });
}