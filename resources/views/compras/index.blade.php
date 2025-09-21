@extends('layouts.app', ['title' => 'Compras'])

@section('page-title')
    Gestión de Compras
@endsection

@section('page-subtitle')
    Administra las compras a proveedores
@endsection

@section('header-buttons')
<x-fluent-button 
    variant="primary" 
    size="medium"
    href="{{ route('compras.create') }}"
    icon="plus"
    iconPosition="left"
>
    Nueva Compra
</x-fluent-button>
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('css/views.css') }}">
@endsection

@section('content')
<div class="purchases-container">
    <!-- Filtros y búsqueda -->
    <div class="filters-section">
        <div class="search-container">
            <x-fluent-input 
                type="search" 
                placeholder="Buscar compras..."
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
                <option value="pendiente">Pendiente</option>
                <option value="recibida">Recibida</option>
                <option value="cancelada">Cancelada</option>
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

    <!-- Resumen de compras -->
    <div class="purchases-summary">
        <div class="summary-card">
            <div class="summary-icon">
                <i class="fas fa-shopping-basket"></i>
            </div>
            <div class="summary-content">
                <div class="summary-value" id="totalPurchases">{{ $resumen['total_compras'] ?? 0 }}</div>
                <div class="summary-label">Total Compras</div>
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
                <i class="fas fa-clock"></i>
            </div>
            <div class="summary-content">
                <div class="summary-value" id="pendingPurchases">{{ $resumen['compras_pendientes'] ?? 0 }}</div>
                <div class="summary-label">Pendientes</div>
            </div>
        </div>
        
        <div class="summary-card">
            <div class="summary-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="summary-content">
                <div class="summary-value" id="receivedPurchases">{{ $resumen['compras_recibidas'] ?? 0 }}</div>
                <div class="summary-label">Recibidas</div>
            </div>
        </div>
    </div>

    <!-- Tabla de compras -->
    <div class="table-container">
        <x-fluent-table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Proveedor</th>
                    <th>Productos</th>
                    <th>Subtotal</th>
                    <th>Impuestos</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="purchasesTableBody">
                @forelse(($compras ?? collect()) as $compra)
                <tr>
                    <td>#{{ $compra->id }}</td>
                    <td>{{ \App\Helpers\PulperiaHelper::formatDateTime($compra->fecha_compra) }}</td>
                    <td>{{ $compra->proveedor->nombre ?? 'N/A' }}</td>
                    <td>{{ $compra->detalles->count() }} productos</td>
                    <td>{{ \App\Helpers\PulperiaHelper::formatCurrency($compra->subtotal) }}</td>
                    <td>{{ \App\Helpers\PulperiaHelper::formatCurrency($compra->impuestos) }}</td>
                    <td>
                        <strong>{{ \App\Helpers\PulperiaHelper::formatCurrency($compra->total) }}</strong>
                    </td>
                    <td>
                        <span class="status-badge status-{{ $compra->estado }}">
                            {{ ucfirst($compra->estado) }}
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <x-fluent-button 
                                variant="ghost" 
                                size="small"
                                href="{{ route('compras.show', $compra) }}"
                                icon="eye"
                            />
                            <x-fluent-button 
                                variant="ghost" 
                                size="small"
                                href="{{ route('compras.edit', $compra) }}"
                                icon="edit"
                            />
                            <x-fluent-button 
                                variant="ghost" 
                                size="small"
                                href="{{ route('compras.recibo', $compra) }}"
                                icon="file-pdf"
                            />
                            @if($compra->estado === 'pendiente')
                            <x-fluent-button 
                                variant="ghost" 
                                size="small"
                                href="{{ route('compras.recibir', $compra) }}"
                                icon="check"
                                confirm="true"
                                confirmMessage="¿Marcar como recibida esta compra?"
                            />
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center">
                        <div class="empty-state">
                            <i class="fas fa-shopping-basket" style="font-size: 3rem; color: var(--fluent-gray-60); margin-bottom: 1rem;"></i>
                            <h3>No hay compras</h3>
                            <p>Comienza registrando tu primera compra</p>
                            <x-fluent-button 
                                variant="primary" 
                                size="medium"
                                href="{{ route('compras.create') }}"
                                icon="plus"
                                iconPosition="left"
                            >
                                Nueva Compra
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