@extends('layouts.app', ['title' => 'Dashboard'])

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('page-title')
    Dashboard
@endsection

@section('content')
<div class="row">
    <!-- Resumen de Ventas -->
    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Ventas Hoy</h6>
                        <h3>C$ {{ number_format($ventasHoy, 2) }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-shopping-cart fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Clientes con Crédito -->
    <div class="col-md-3 mb-4">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Créditos Pendientes</h6>
                        <h3>C$ {{ number_format($totalCreditos, 2) }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-credit-card fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Productos Stock Bajo -->
    <div class="col-md-3 mb-4">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Stock Bajo</h6>
                        <h3>{{ $stockBajoCount }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Inventario -->
    <div class="col-md-3 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Valor Inventario</h6>
                        <h3>C$ {{ number_format($valorInventario, 2) }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-boxes fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Ventas Recientes -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Ventas Recientes</h5>
            </div>
            <div class="card-body">
                <x-table :headers="['#', 'Cliente', 'Total', 'Fecha']">
                    @forelse($ventasRecientes as $venta)
                    <tr>
                        <td>{{ $venta->id }}</td>
                        <td>{{ $venta->cliente->nombre ?? 'Cliente General' }}</td>
                        <td>C$ {{ number_format($venta->total, 2) }}</td>
                        <td>{{ $venta->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">No hay ventas recientes</td>
                    </tr>
                    @endforelse
                </x-table>
            </div>
        </div>
    </div>

    <!-- Productos Stock Bajo -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Productos con Stock Bajo</h5>
            </div>
            <div class="card-body">
                <x-table :headers="['Producto', 'Stock Actual', 'Stock Mínimo']">
                    @forelse($productosStockBajo as $producto)
                    <tr>
                        <td>{{ $producto->nombre }}</td>
                        <td>
                            <span class="badge bg-danger">{{ $producto->stock }}</span>
                        </td>
                        <td>{{ $producto->stock_minimo }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center">Todo el stock está en niveles adecuados</td>
                    </tr>
                    @endforelse
                </x-table>
            </div>
        </div>
    </div>
</div>
@endsection