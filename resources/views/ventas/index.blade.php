@extends('layouts.app', ['title' => 'Ventas'])

@section('page-title')
    Historial de Ventas
@endsection

@section('page-subtitle')
    Gestiona y revisa todas las ventas realizadas
@endsection

@section('header-buttons')
<div class="header-actions">
    <x-fluent-button 
        variant="primary" 
        size="medium"
        href="{{ route('ventas.pos') }}"
        icon="cash-register"
        iconPosition="left"
    >
        Nueva Venta
    </x-fluent-button>
    
    <x-fluent-button 
        variant="secondary" 
        size="medium"
        href="{{ route('ventas.create') }}"
        icon="plus"
        iconPosition="left"
    >
        Venta Manual
    </x-fluent-button>
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('css/views.css') }}">
@endsection

@section('content')
<div class="sales-container">
    <!-- Filtros y búsqueda -->
    <div class="filters-section">
        <div class="search-container">
            <x-fluent-input 
                type="search" 
                placeholder="Buscar ventas..."
                icon="search"
                id="searchInput"
            />
        </div>
        
        <div class="filter-controls">
            <x-fluent-input 
                type="date" 
                label="Desde"
                id="dateFrom"
                value="{{ request('fecha_desde', now()->subDays(30)->format('Y-m-d')) }}"
            />
            
            <x-fluent-input 
                type="date" 
                label="Hasta"
                id="dateTo"
                value="{{ request('fecha_hasta', now()->format('Y-m-d')) }}"
            />
            
            <select class="fluent-select" id="statusFilter">
                <option value="">Todos los estados</option>
                <option value="completada">Completadas</option>
                <option value="anulada">Anuladas</option>
                <option value="credito">A Crédito</option>
                <option value="contado">Al Contado</option>
            </select>
            
            <x-fluent-button 
                variant="secondary" 
                size="medium"
                id="applyFilters"
                icon="filter"
                iconPosition="left"
            >
                Filtrar
            </x-fluent-button>
        </div>
    </div>

    <!-- Resumen de ventas -->
    <div class="sales-summary">
        <div class="summary-card">
            <div class="summary-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="summary-content">
                <div class="summary-value" id="totalSales">{{ $resumen['total_ventas'] ?? 0 }}</div>
                <div class="summary-label">Total Ventas</div>
            </div>
        </div>
        
        <div class="summary-card">
            <div class="summary-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="summary-content">
                <div class="summary-value" id="totalAmount">{{ \App\Helpers\PulperiaHelper::formatCurrency($resumen['monto_total'] ?? 0) }}</div>
                <div class="summary-label">Monto Total</div>
            </div>
        </div>
        
        <div class="summary-card">
            <div class="summary-icon">
                <i class="fas fa-credit-card"></i>
            </div>
            <div class="summary-content">
                <div class="summary-value" id="creditSales">{{ $resumen['ventas_credito'] ?? 0 }}</div>
                <div class="summary-label">Ventas a Crédito</div>
            </div>
        </div>
        
        <div class="summary-card">
            <div class="summary-icon">
                <i class="fas fa-cash-register"></i>
            </div>
            <div class="summary-content">
                <div class="summary-value" id="cashSales">{{ $resumen['ventas_contado'] ?? 0 }}</div>
                <div class="summary-label">Ventas al Contado</div>
            </div>
        </div>
    </div>

    <!-- Tabla de ventas -->
    <div class="table-container">
        <x-fluent-table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Tipo</th>
                    <th>Productos</th>
                    <th>Subtotal</th>
                    <th>Impuestos</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="salesTableBody">
                @forelse(($ventas ?? collect()) as $venta)
                <tr>
                    <td>#{{ $venta->id }}</td>
                    <td>{{ \App\Helpers\PulperiaHelper::formatDateTime($venta->fecha_venta) }}</td>
                    <td>{{ $venta->cliente->nombre ?? 'Cliente General' }}</td>
                    <td>
                        <span class="type-badge type-{{ $venta->tipo_pago }}">
                            {{ $venta->tipo_pago === 'contado' ? 'Contado' : 'Crédito' }}
                        </span>
                    </td>
                    <td>{{ $venta->detalles->count() }} productos</td>
                    <td>{{ \App\Helpers\PulperiaHelper::formatCurrency($venta->subtotal) }}</td>
                    <td>{{ \App\Helpers\PulperiaHelper::formatCurrency($venta->impuestos) }}</td>
                    <td>
                        <strong>{{ \App\Helpers\PulperiaHelper::formatCurrency($venta->total) }}</strong>
                    </td>
                    <td>
                        <span class="status-badge status-{{ $venta->anulada ? 'cancelled' : 'completed' }}">
                            {{ $venta->anulada ? 'Anulada' : 'Completada' }}
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <x-fluent-button 
                                variant="ghost" 
                                size="small"
                                href="{{ route('ventas.show', $venta) }}"
                                icon="eye"
                            />
                            <x-fluent-button 
                                variant="ghost" 
                                size="small"
                                href="{{ route('ventas.factura', $venta) }}"
                                icon="file-pdf"
                            />
                            @if(!$venta->anulada)
                            <x-fluent-button 
                                variant="ghost" 
                                size="small"
                                href="{{ route('ventas.anular', $venta) }}"
                                icon="times"
                                confirm="true"
                                confirmMessage="¿Está seguro de anular esta venta?"
                            />
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center">
                        <div class="empty-state">
                            <i class="fas fa-shopping-cart" style="font-size: 3rem; color: var(--fluent-gray-60); margin-bottom: 1rem;"></i>
                            <h3>No hay ventas</h3>
                            <p>Comienza realizando tu primera venta</p>
                            <x-fluent-button 
                                variant="primary" 
                                size="medium"
                                href="{{ route('ventas.pos') }}"
                                icon="cash-register"
                                iconPosition="left"
                            >
                                Ir al Punto de Venta
                            </x-fluent-button>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </x-fluent-table>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/views.js') }}"></script>
@endsection