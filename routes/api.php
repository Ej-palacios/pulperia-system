<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
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



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    // API para productos
    Route::get('/productos/activos', [ProductoController::class, 'apiActivos']);
    Route::get('/productos/{id}', [ProductoController::class, 'apiShow']);
    
    // API para clientes
    Route::get('/clientes', [ClienteController::class, 'apiIndex']);
    Route::get('/clientes/{id}', [ClienteController::class, 'apiShow']);
    
    // API para ventas
    Route::post('/ventas', [VentaController::class, 'apiStore']);
    Route::get('/ventas/{id}', [VentaController::class, 'apiShow']);
    
    // API para configuración
    Route::get('/configuracion', [ConfiguracionTiendaController::class, 'apiShow']);
    
    // API para usuarios
    Route::put('/usuarios/{user}/estado', [UserController::class, 'apiUpdateStatus']);
    
    // API para reportes
    Route::get('/reportes/ventas', [ReporteController::class, 'apiVentas']);
    Route::get('/reportes/inventario', [ReporteController::class, 'apiInventario']);
});

// Rutas públicas (si son necesarias)
Route::get('/health', function () {
    return response()->json(['status' => 'OK']);
});