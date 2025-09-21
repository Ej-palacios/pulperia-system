@extends('layouts.app', ['title' => 'Dashboard'])

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('page-title')
    Dashboard
@endsection

@section('page-subtitle')
    Resumen general del negocio y métricas importantes
@endsection

@section('header-buttons')
    <x-fluent-button variant="primary" icon="plus" href="{{ route('ventas.pos') }}">
        Nueva Venta
    </x-fluent-button>
    <x-fluent-button variant="ghost" icon="refresh" onclick="refreshDashboard()">
        Actualizar
    </x-fluent-button>
@endsection

@section('content')
<!-- Stats Cards -->
<div class="dashboard-stats">
    <div class="stats-grid">
        <x-fluent-card variant="primary" class="stat-card-link">
            <div class="stat-content">
                <div class="stat-label">Ventas Hoy</div>
                <div class="stat-value">@currency($ventasHoy ?? 0)</div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
        </x-fluent-card>

        <x-fluent-card variant="warning" class="stat-card-link">
            <div class="stat-content">
                <div class="stat-label">Créditos Pendientes</div>
                <div class="stat-value">@currency($totalCreditos ?? 0)</div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-credit-card"></i>
            </div>
        </x-fluent-card>

        <x-fluent-card variant="error" href="{{ route('productos.index', ['filter[stock]' => 'bajo']) }}">
            <div class="stat-content">
                <div class="stat-label">Stock Bajo</div>
                <div class="stat-value">{{ $stockBajoCount ?? 0 }} Productos</div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
        </x-fluent-card>

        <x-fluent-card variant="success" class="stat-card-link">
            <div class="stat-content">
                <div class="stat-label">Valor Inventario</div>
                <div class="stat-value">@currency($valorInventario ?? 0)</div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-boxes"></i>
            </div>
        </x-fluent-card>
    </div>
</div>

<!-- Main Content Grid -->
<div class="dashboard-content">
    <div class="content-grid">
        <x-fluent-card
            title="Ventas Recientes"
            subtitle="Últimas transacciones realizadas"
            class="grid-col-span-2"
        >
            <x-slot name="actions">
                <x-fluent-button variant="ghost" size="small" icon="external-link-alt" href="{{ route('ventas.index') }}">
                    Ver todas
                </x-fluent-button>
            </x-slot>

            <x-fluent-table>
                <x-slot name="header">
                    <th>#</th>
                    <th>Cliente</th>
                    <th>Total</th>
                    <th>Fecha</th>
                </x-slot>

                @forelse($ventasRecientes as $venta)
                    <tr>
                        <td>{{ $venta->id }}</td>
                        <td>{{ $venta->cliente->nombre ?? 'Cliente General' }}</td>
                        <td><span class="currency">@currency($venta->total)</span></td>
                        <td>{{ $venta->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">No hay ventas recientes</td>
                    </tr>
                @endforelse
            </x-fluent-table>
        </x-fluent-card>

        <x-fluent-card
            title="Productos con Stock Bajo"
            subtitle="Productos que necesitan reposición"
        >
            <x-slot name="actions">
                <x-fluent-button variant="ghost" size="small" icon="external-link-alt" href="{{ route('productos.index', ['filter[stock]' => 'bajo']) }}">
                    Ver todos
                </x-fluent-button>
            </x-slot>

            <x-fluent-table>
                <x-slot name="header">
                    <th>Producto</th>
                    <th>Stock</th>
                    <th>Acción</th>
                </x-slot>

                @forelse($productosStockBajo as $producto)
                    <tr>
                        <td>{{ $producto->nombre }}</td>
                        <td><span class="badge badge-error">{{ $producto->stock }}</span></td>
                        <td>
                            <x-fluent-button variant="secondary" size="small" icon="plus">
                                Reponer
                            </x-fluent-button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted">No hay productos con stock bajo</td>
                    </tr>
                @endforelse
            </x-fluent-table>
        </x-fluent-card>

        <x-fluent-card
            title="Ventas por Día"
            subtitle="Últimos 7 días"
            class="grid-col-span-2"
        >
            <x-slot name="actions">
                <div class="chart-controls">
                    <x-fluent-button variant="ghost" size="small" class="active" data-period="7d">7D</x-fluent-button>
                    <x-fluent-button variant="ghost" size="small" data-period="30d">30D</x-fluent-button>
                    <x-fluent-button variant="ghost" size="small" data-period="90d">90D</x-fluent-button>
                </div>
            </x-slot>

            <div class="chart-container">
                <canvas id="salesChart" width="400" height="200"></canvas>
            </div>
        </x-fluent-card>

        <x-fluent-card
            title="Actividad Reciente"
            subtitle="Últimas acciones del sistema"
        >
            <div class="activity-list">
                <div class="activity-item">
                    <div class="activity-icon success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-title">Venta completada</div>
                        <div class="activity-description">Venta #001234 por @currency(1250)</div>
                        <div class="activity-time">Hace 5 minutos</div>
                    </div>
                </div>
                
                <div class="activity-item">
                    <div class="activity-icon warning">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-title">Stock bajo</div>
                        <div class="activity-description">Producto "Coca Cola" tiene stock bajo</div>
                        <div class="activity-time">Hace 15 minutos</div>
                    </div>
                </div>
                
                <div class="activity-item">
                    <div class="activity-icon info">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-title">Nuevo cliente</div>
                        <div class="activity-description">Cliente "Juan Pérez" registrado</div>
                        <div class="activity-time">Hace 1 hora</div>
                    </div>
                </div>
            </div>
        </x-fluent-card>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions">
    <h3 class="section-title">Acciones Rápidas</h3>
    <div class="actions-grid">
        <x-fluent-button variant="primary" size="large" icon="cash-register" href="{{ route('ventas.pos') }}">
            Nueva Venta
        </x-fluent-button>
        
        <x-fluent-button variant="secondary" size="large" icon="shopping-basket" href="{{ route('compras.create') }}">
            Nueva Compra
        </x-fluent-button>
        
        <x-fluent-button variant="ghost" size="large" icon="user-plus" href="{{ route('clientes.create') }}">
            Nuevo Cliente
        </x-fluent-button>
        
        <x-fluent-button variant="ghost" size="large" icon="box" href="{{ route('productos.create') }}">
            Nuevo Producto
        </x-fluent-button>
        
        <x-fluent-button variant="ghost" size="large" icon="chart-bar" href="{{ route('reportes.ventas') }}">
            Ver Reportes
        </x-fluent-button>
        
        <x-fluent-button variant="ghost" size="large" icon="cog" href="{{ route('configuracion.edit') }}">
            Configuración
        </x-fluent-button>
    </div>
</div>
@endsection

@section('styles')
<style>
/* Dashboard Layout */
.dashboard-stats {
    margin-bottom: var(--fluent-space-xl);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: var(--fluent-space-lg);
}

.dashboard-content {
    margin-bottom: var(--fluent-space-xl);
}

.content-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--fluent-space-lg);
}

.quick-actions {
    margin-bottom: var(--fluent-space-xl);
}

.actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: var(--fluent-space-md);
}

/* Stat Card Customization */
.stats-grid .fluent-card .card-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: var(--fluent-space-md);
    padding: var(--fluent-space-lg);
}

.stat-card-link {
    cursor: default;
}

.stat-content {
    display: flex;
    flex-direction: column;
}

.stat-label {
    font-size: var(--fluent-font-size-sm);
    color: var(--fluent-gray-80);
    margin-bottom: var(--fluent-space-xs);
}

.stat-value {
    font-size: var(--fluent-font-size-2xl);
    font-weight: 700;
    color: var(--fluent-gray-120);
    line-height: 1.2;
}

.stat-icon {
    font-size: var(--fluent-font-size-2xl);
    color: var(--fluent-gray-100);
}

/* Grid & Chart */
.grid-col-span-2 {
    grid-column: span 2;
}

.chart-controls {
    display: flex;
    gap: var(--fluent-space-xs);
}

.chart-controls .fluent-button.active {
    background-color: var(--fluent-primary-light);
    color: var(--fluent-primary);
}

.chart-container {
    height: 200px;
    position: relative;
}

/* Activity List */
.activity-list {
    display: flex;
    flex-direction: column;
    gap: var(--fluent-space-lg);
}

.activity-item {
    display: flex;
    align-items: flex-start;
    gap: var(--fluent-space-md);
}

.activity-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: var(--fluent-font-size-sm);
    flex-shrink: 0;
}

.activity-icon.success { background-color: var(--fluent-success-light); color: var(--fluent-success); }
.activity-icon.warning { background-color: var(--fluent-warning-light); color: var(--fluent-warning); }
.activity-icon.info { background-color: var(--fluent-info-light); color: var(--fluent-info); }

.activity-content { flex: 1; min-width: 0; }
.activity-title { font-weight: 600; font-size: var(--fluent-font-size-sm); color: var(--fluent-gray-120); margin-bottom: 2px; }
.activity-description { font-size: var(--fluent-font-size-xs); color: var(--fluent-gray-80); margin-bottom: 4px; }
.activity-time { font-size: var(--fluent-font-size-xs); color: var(--fluent-gray-70); }

/* General */
.section-title {
    font-size: var(--fluent-font-size-lg);
    font-weight: 600;
    color: var(--fluent-gray-120);
    margin-bottom: var(--fluent-space-lg);
}

.currency { font-weight: 600; color: var(--fluent-success); }
.badge { display: inline-flex; align-items: center; padding: var(--fluent-space-xs) var(--fluent-space-sm); border-radius: var(--fluent-radius-sm); font-size: var(--fluent-font-size-xs); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
.badge-error { background-color: var(--fluent-error-light); color: var(--fluent-error); }

/* Responsive */
@media (max-width: 1200px) {
    .content-grid { grid-template-columns: repeat(2, 1fr); }
    .grid-col-span-2 { grid-column: span 1; }
}

@media (max-width: 768px) {
    .content-grid, .stats-grid, .actions-grid { grid-template-columns: 1fr; }
}
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
            datasets: [{
                label: 'Ventas',
                data: [1200, 1900, 3000, 5000, 2000, 3000, 4500],
                borderColor: '#0078d4',
                backgroundColor: 'rgba(0, 120, 212, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: value => 'C$ ' + value.toLocaleString() }
                }
            }
        }
    });
    
    const chartBtns = document.querySelectorAll('.chart-controls button');
    chartBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            chartBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const period = this.dataset.period;
            // AJAX call to get new data for the period would go here
            console.log('Loading data for period:', period);
        });
    });
});

function refreshDashboard() {
    // Implement actual refresh logic, e.g., via Livewire or AJAX
    location.reload();
}
</script>
@endsection