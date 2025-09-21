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
        <!-- Ventas Hoy -->
        <div class="stat-card stat-card-primary">
            <div class="stat-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Ventas Hoy</div>
                <div class="stat-value">@currency($ventasHoy ?? 0)</div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i>
                    <span>+12% vs ayer</span>
                </div>
            </div>
        </div>

        <!-- Créditos Pendientes -->
        <div class="stat-card stat-card-warning">
            <div class="stat-icon">
                <i class="fas fa-credit-card"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Créditos Pendientes</div>
                <div class="stat-value">@currency($totalCreditos ?? 0)</div>
                <div class="stat-change negative">
                    <i class="fas fa-arrow-down"></i>
                    <span>-5% vs mes anterior</span>
                </div>
            </div>
        </div>

        <!-- Stock Bajo -->
        <div class="stat-card stat-card-error">
            <div class="stat-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Stock Bajo</div>
                <div class="stat-value">{{ $stockBajoCount ?? 0 }}</div>
                <div class="stat-change neutral">
                    <i class="fas fa-minus"></i>
                    <span>Productos</span>
                </div>
            </div>
        </div>

        <!-- Valor Inventario -->
        <div class="stat-card stat-card-success">
            <div class="stat-icon">
                <i class="fas fa-boxes"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Valor Inventario</div>
                <div class="stat-value">@currency($valorInventario ?? 0)</div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i>
                    <span>+8% vs mes anterior</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="dashboard-content">
    <div class="content-grid">
        <!-- Ventas Recientes -->
        <div class="content-card">
            <div class="card-header">
                <div class="card-title-section">
                    <h3 class="card-title">Ventas Recientes</h3>
                    <p class="card-subtitle">Últimas transacciones realizadas</p>
                </div>
                <div class="card-actions">
                    <x-fluent-button variant="ghost" size="small" icon="external-link-alt" href="{{ route('ventas.index') }}">
                        Ver todas
                    </x-fluent-button>
                </div>
            </div>
            <div class="card-content">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($ventasRecientes) && $ventasRecientes->count() > 0)
                                @foreach($ventasRecientes as $venta)
                                <tr>
                                    <td>{{ $venta->id }}</td>
                                    <td>{{ $venta->cliente->nombre ?? 'Cliente General' }}</td>
                                    <td><span class="currency">C$ {{ number_format($venta->total, 2) }}</span></td>
                                    <td>{{ $venta->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No hay ventas recientes</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Productos Stock Bajo -->
        <div class="content-card">
            <div class="card-header">
                <div class="card-title-section">
                    <h3 class="card-title">Productos con Stock Bajo</h3>
                    <p class="card-subtitle">Productos que requieren reposición</p>
                </div>
                <div class="card-actions">
                    <x-fluent-button variant="ghost" size="small" icon="external-link-alt" href="{{ route('productos.index') }}">
                        Ver todos
                    </x-fluent-button>
                </div>
            </div>
            <div class="card-content">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Stock Actual</th>
                                <th>Stock Mínimo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($productosStockBajo) && $productosStockBajo->count() > 0)
                                @foreach($productosStockBajo as $producto)
                                <tr>
                                    <td>{{ $producto->nombre }}</td>
                                    <td><span class="badge bg-danger">{{ $producto->stock }}</span></td>
                                    <td>{{ $producto->stock_minimo }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-plus"></i> Reponer
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Todo el stock está en niveles adecuados</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Gráfico de Ventas -->
        <div class="content-card chart-card">
            <div class="card-header">
                <div class="card-title-section">
                    <h3 class="card-title">Ventas por Día</h3>
                    <p class="card-subtitle">Últimos 7 días</p>
                </div>
                <div class="card-actions">
                    <div class="chart-controls">
                        <button class="chart-btn active" data-period="7d">7D</button>
                        <button class="chart-btn" data-period="30d">30D</button>
                        <button class="chart-btn" data-period="90d">90D</button>
                    </div>
                </div>
            </div>
            <div class="card-content">
                <div class="chart-container">
                    <canvas id="salesChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Actividad Reciente -->
        <div class="content-card">
            <div class="card-header">
                <div class="card-title-section">
                    <h3 class="card-title">Actividad Reciente</h3>
                    <p class="card-subtitle">Últimas acciones del sistema</p>
                </div>
            </div>
            <div class="card-content">
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
                    
                    <div class="activity-item">
                        <div class="activity-icon success">
                            <i class="fas fa-shopping-basket"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Compra registrada</div>
                            <div class="activity-description">Compra #001122 por @currency(2500)</div>
                            <div class="activity-time">Hace 2 horas</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
/* Dashboard Styles */
.dashboard-stats {
    margin-bottom: var(--fluent-space-xl);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: var(--fluent-space-lg);
}

.stat-card {
    background-color: var(--fluent-white);
    border-radius: var(--fluent-radius-lg);
    padding: var(--fluent-space-lg);
    border: 1px solid var(--fluent-gray-30);
    box-shadow: var(--fluent-shadow-sm);
    display: flex;
    align-items: center;
    gap: var(--fluent-space-md);
    transition: all var(--fluent-transition-normal);
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background-color: var(--fluent-primary);
}

.stat-card-primary::before {
    background-color: var(--fluent-primary);
}

.stat-card-success::before {
    background-color: var(--fluent-success);
}

.stat-card-warning::before {
    background-color: var(--fluent-warning);
}

.stat-card-error::before {
    background-color: var(--fluent-error);
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--fluent-shadow-lg);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: var(--fluent-radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: var(--fluent-font-size-xl);
    color: var(--fluent-white);
    flex-shrink: 0;
}

.stat-card-primary .stat-icon {
    background-color: var(--fluent-primary);
}

.stat-card-success .stat-icon {
    background-color: var(--fluent-success);
}

.stat-card-warning .stat-icon {
    background-color: var(--fluent-warning);
}

.stat-card-error .stat-icon {
    background-color: var(--fluent-error);
}

.stat-content {
    flex: 1;
    min-width: 0;
}

.stat-label {
    font-size: var(--fluent-font-size-sm);
    color: var(--fluent-gray-80);
    margin-bottom: var(--fluent-space-xs);
    font-weight: 500;
}

.stat-value {
    font-size: var(--fluent-font-size-2xl);
    font-weight: 700;
    color: var(--fluent-gray-120);
    margin-bottom: var(--fluent-space-xs);
    line-height: 1.2;
}

.stat-change {
    display: flex;
    align-items: center;
    gap: var(--fluent-space-xs);
    font-size: var(--fluent-font-size-xs);
    font-weight: 500;
}

.stat-change.positive {
    color: var(--fluent-success);
}

.stat-change.negative {
    color: var(--fluent-error);
}

.stat-change.neutral {
    color: var(--fluent-gray-80);
}

/* Dashboard Content */
.dashboard-content {
    margin-bottom: var(--fluent-space-xl);
}

.content-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: var(--fluent-space-lg);
}

.content-card {
    background-color: var(--fluent-white);
    border-radius: var(--fluent-radius-lg);
    border: 1px solid var(--fluent-gray-30);
    box-shadow: var(--fluent-shadow-sm);
    overflow: hidden;
}

.content-card.chart-card {
    grid-column: span 2;
}

.card-header {
    padding: var(--fluent-space-lg);
    border-bottom: 1px solid var(--fluent-gray-30);
    background-color: var(--fluent-gray-10);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.card-title-section {
    flex: 1;
}

.card-title {
    font-size: var(--fluent-font-size-lg);
    font-weight: 600;
    color: var(--fluent-gray-120);
    margin: 0 0 var(--fluent-space-xs) 0;
}

.card-subtitle {
    font-size: var(--fluent-font-size-sm);
    color: var(--fluent-gray-80);
    margin: 0;
}

.card-actions {
    display: flex;
    align-items: center;
    gap: var(--fluent-space-sm);
}

.card-content {
    padding: var(--fluent-space-lg);
}

/* Chart Controls */
.chart-controls {
    display: flex;
    gap: var(--fluent-space-xs);
}

.chart-btn {
    padding: var(--fluent-space-xs) var(--fluent-space-sm);
    border: 1px solid var(--fluent-gray-40);
    background-color: var(--fluent-white);
    color: var(--fluent-gray-100);
    border-radius: var(--fluent-radius-sm);
    cursor: pointer;
    transition: all var(--fluent-transition-fast);
    font-size: var(--fluent-font-size-xs);
    font-weight: 500;
}

.chart-btn:hover {
    background-color: var(--fluent-gray-20);
    border-color: var(--fluent-gray-50);
}

.chart-btn.active {
    background-color: var(--fluent-primary);
    border-color: var(--fluent-primary);
    color: var(--fluent-white);
}

.chart-container {
    height: 200px;
    position: relative;
}

/* Activity List */
.activity-list {
    display: flex;
    flex-direction: column;
    gap: var(--fluent-space-md);
}

.activity-item {
    display: flex;
    align-items: flex-start;
    gap: var(--fluent-space-md);
    padding: var(--fluent-space-md);
    border-radius: var(--fluent-radius-md);
    transition: background-color var(--fluent-transition-fast);
}

.activity-item:hover {
    background-color: var(--fluent-gray-10);
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

.activity-icon.success {
    background-color: var(--fluent-success-light);
    color: var(--fluent-success);
}

.activity-icon.warning {
    background-color: var(--fluent-warning-light);
    color: var(--fluent-warning);
}

.activity-icon.info {
    background-color: var(--fluent-info-light);
    color: var(--fluent-info);
}

.activity-content {
    flex: 1;
    min-width: 0;
}

.activity-title {
    font-weight: 600;
    font-size: var(--fluent-font-size-sm);
    color: var(--fluent-gray-120);
    margin-bottom: var(--fluent-space-xs);
}

.activity-description {
    font-size: var(--fluent-font-size-xs);
    color: var(--fluent-gray-80);
    margin-bottom: var(--fluent-space-xs);
}

.activity-time {
    font-size: var(--fluent-font-size-xs);
    color: var(--fluent-gray-70);
}

/* Quick Actions */
.quick-actions {
    margin-bottom: var(--fluent-space-xl);
}

.section-title {
    font-size: var(--fluent-font-size-lg);
    font-weight: 600;
    color: var(--fluent-gray-120);
    margin-bottom: var(--fluent-space-lg);
}

.actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: var(--fluent-space-md);
}

/* Currency styling */
.currency {
    font-weight: 600;
    color: var(--fluent-success);
}

/* Badge styling */
.badge {
    display: inline-flex;
    align-items: center;
    padding: var(--fluent-space-xs) var(--fluent-space-sm);
    border-radius: var(--fluent-radius-sm);
    font-size: var(--fluent-font-size-xs);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-error {
    background-color: var(--fluent-error-light);
    color: var(--fluent-error);
}

/* Responsive */
@media (max-width: 1024px) {
    .content-grid {
        grid-template-columns: 1fr;
    }
    
    .content-card.chart-card {
        grid-column: span 1;
    }
    
    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .actions-grid {
        grid-template-columns: 1fr;
    }
    
    .card-header {
        flex-direction: column;
        align-items: stretch;
        gap: var(--fluent-space-md);
    }
    
    .card-actions {
        justify-content: flex-start;
    }
}
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize sales chart
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
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'C$ ' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
    
    // Chart period controls
    const chartBtns = document.querySelectorAll('.chart-btn');
    chartBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            chartBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Update chart data based on period
            const period = this.dataset.period;
            // This would typically make an AJAX call to get new data
            console.log('Loading data for period:', period);
        });
    });
});

// Refresh dashboard function
function refreshDashboard() {
    PulperiaUtils.showLoading('Actualizando dashboard...');
    
    // Simulate API call
    setTimeout(() => {
        PulperiaUtils.hideLoading();
        PulperiaUtils.showToast('Dashboard actualizado', 'success');
        // Reload page or update data via AJAX
        location.reload();
    }, 1500);
}
</script>
@endsection