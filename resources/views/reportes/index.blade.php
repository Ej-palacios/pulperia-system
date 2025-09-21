@extends('Layouts.app')

@section('title', 'Reportes')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-icon">
                <i class="fas fa-chart-bar"></i>
            </div>
            <div class="header-text">
                <h1>Reportes</h1>
                <p>Análisis y estadísticas del negocio</p>
            </div>
        </div>
        <div class="header-actions">
            <button class="fluent-button primary" onclick="exportarReporte()">
                <i class="fas fa-download"></i>
                Exportar
            </button>
        </div>
    </div>

    <!-- Filtros de fecha -->
    <div class="filters-section">
        <div class="filter-group">
            <label class="filter-label">Período:</label>
            <select class="fluent-select" id="periodoFilter">
                <option value="hoy">Hoy</option>
                <option value="semana">Esta semana</option>
                <option value="mes" selected>Este mes</option>
                <option value="trimestre">Este trimestre</option>
                <option value="año">Este año</option>
                <option value="personalizado">Personalizado</option>
            </select>
        </div>
        
        <div class="filter-group" id="fechaPersonalizada" style="display: none;">
            <label class="filter-label">Desde:</label>
            <input type="date" class="fluent-input" id="fechaInicio">
            
            <label class="filter-label">Hasta:</label>
            <input type="date" class="fluent-input" id="fechaFin">
        </div>
        
        <button class="fluent-button" onclick="aplicarFiltros()">
            <i class="fas fa-filter"></i>
            Aplicar Filtros
        </button>
    </div>

    <!-- Tarjetas de resumen -->
    <div class="summary-cards">
        <div class="summary-card">
            <div class="card-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="card-content">
                <h3 id="totalVentas">$0.00</h3>
                <p>Total Ventas</p>
            </div>
        </div>
        
        <div class="summary-card">
            <div class="card-icon">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <div class="card-content">
                <h3 id="totalCompras">$0.00</h3>
                <p>Total Compras</p>
            </div>
        </div>
        
        <div class="summary-card">
            <div class="card-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="card-content">
                <h3 id="gananciaNeta">$0.00</h3>
                <p>Ganancia Neta</p>
            </div>
        </div>
        
        <div class="summary-card">
            <div class="card-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="card-content">
                <h3 id="totalPerdidas">$0.00</h3>
                <p>Pérdidas por Daños</p>
            </div>
        </div>
    </div>

    <!-- Tabs de reportes -->
    <div class="reports-tabs">
        <div class="tab-buttons">
            <button class="tab-button active" onclick="cambiarTab('ventas')">
                <i class="fas fa-shopping-cart"></i>
                Ventas
            </button>
            <button class="tab-button" onclick="cambiarTab('compras')">
                <i class="fas fa-shopping-bag"></i>
                Compras
            </button>
            <button class="tab-button" onclick="cambiarTab('inventario')">
                <i class="fas fa-boxes"></i>
                Inventario
            </button>
            <button class="tab-button" onclick="cambiarTab('creditos')">
                <i class="fas fa-credit-card"></i>
                Créditos
            </button>
            <button class="tab-button" onclick="cambiarTab('perdidas')">
                <i class="fas fa-exclamation-triangle"></i>
                Pérdidas
            </button>
        </div>

        <!-- Tab de Ventas -->
        <div class="tab-content active" id="tab-ventas">
            <div class="chart-container">
                <canvas id="ventasChart"></canvas>
            </div>
            <div class="table-container">
                <h3>Top Productos Vendidos</h3>
                <div class="fluent-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad Vendida</th>
                                <th>Total Vendido</th>
                                <th>% del Total</th>
                            </tr>
                        </thead>
                        <tbody id="topProductosVendidos">
                            <tr>
                                <td colspan="4" class="text-center">Cargando datos...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tab de Compras -->
        <div class="tab-content" id="tab-compras">
            <div class="chart-container">
                <canvas id="comprasChart"></canvas>
            </div>
            <div class="table-container">
                <h3>Top Proveedores</h3>
                <div class="fluent-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Proveedor</th>
                                <th>Compras Realizadas</th>
                                <th>Total Comprado</th>
                                <th>% del Total</th>
                            </tr>
                        </thead>
                        <tbody id="topProveedores">
                            <tr>
                                <td colspan="4" class="text-center">Cargando datos...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tab de Inventario -->
        <div class="tab-content" id="tab-inventario">
            <div class="chart-container">
                <canvas id="inventarioChart"></canvas>
            </div>
            <div class="table-container">
                <h3>Productos con Bajo Stock</h3>
                <div class="fluent-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Stock Actual</th>
                                <th>Stock Mínimo</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody id="productosBajoStock">
                            <tr>
                                <td colspan="4" class="text-center">Cargando datos...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tab de Créditos -->
        <div class="tab-content" id="tab-creditos">
            <div class="chart-container">
                <canvas id="creditosChart"></canvas>
            </div>
            <div class="table-container">
                <h3>Créditos Pendientes</h3>
                <div class="fluent-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Monto Pendiente</th>
                                <th>Días Vencido</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody id="creditosPendientes">
                            <tr>
                                <td colspan="4" class="text-center">Cargando datos...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tab de Pérdidas -->
        <div class="tab-content" id="tab-perdidas">
            <div class="chart-container">
                <canvas id="perdidasChart"></canvas>
            </div>
            <div class="table-container">
                <h3>Productos Dañados</h3>
                <div class="fluent-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad Dañada</th>
                                <th>Costo de Pérdida</th>
                                <th>Motivo</th>
                            </tr>
                        </thead>
                        <tbody id="productosDanados">
                            <tr>
                                <td colspan="4" class="text-center">Cargando datos...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/views.js') }}"></script>
<script>
// Variables globales
let charts = {};
let currentTab = 'ventas';

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    inicializarReportes();
    aplicarFiltros();
});

function inicializarReportes() {
    // Configurar filtro de período
    document.getElementById('periodoFilter').addEventListener('change', function() {
        const fechaPersonalizada = document.getElementById('fechaPersonalizada');
        if (this.value === 'personalizado') {
            fechaPersonalizada.style.display = 'flex';
        } else {
            fechaPersonalizada.style.display = 'none';
        }
    });
}

function cambiarTab(tabName) {
    // Ocultar todos los tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Desactivar todos los botones
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Mostrar tab seleccionado
    document.getElementById('tab-' + tabName).classList.add('active');
    document.querySelector(`[onclick="cambiarTab('${tabName}')"]`).classList.add('active');
    
    currentTab = tabName;
    
    // Cargar datos del tab
    cargarDatosTab(tabName);
}

function aplicarFiltros() {
    const periodo = document.getElementById('periodoFilter').value;
    let fechaInicio, fechaFin;
    
    const hoy = new Date();
    
    switch(periodo) {
        case 'hoy':
            fechaInicio = fechaFin = hoy.toISOString().split('T')[0];
            break;
        case 'semana':
            const inicioSemana = new Date(hoy);
            inicioSemana.setDate(hoy.getDate() - hoy.getDay());
            fechaInicio = inicioSemana.toISOString().split('T')[0];
            fechaFin = hoy.toISOString().split('T')[0];
            break;
        case 'mes':
            const inicioMes = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
            fechaInicio = inicioMes.toISOString().split('T')[0];
            fechaFin = hoy.toISOString().split('T')[0];
            break;
        case 'trimestre':
            const inicioTrimestre = new Date(hoy.getFullYear(), Math.floor(hoy.getMonth() / 3) * 3, 1);
            fechaInicio = inicioTrimestre.toISOString().split('T')[0];
            fechaFin = hoy.toISOString().split('T')[0];
            break;
        case 'año':
            const inicioAño = new Date(hoy.getFullYear(), 0, 1);
            fechaInicio = inicioAño.toISOString().split('T')[0];
            fechaFin = hoy.toISOString().split('T')[0];
            break;
        case 'personalizado':
            fechaInicio = document.getElementById('fechaInicio').value;
            fechaFin = document.getElementById('fechaFin').value;
            break;
    }
    
    cargarDatosReportes(fechaInicio, fechaFin);
}

function cargarDatosReportes(fechaInicio, fechaFin) {
    // Simular carga de datos (en producción, hacer llamadas AJAX)
    setTimeout(() => {
        // Actualizar tarjetas de resumen
        document.getElementById('totalVentas').textContent = '$1,250.00';
        document.getElementById('totalCompras').textContent = '$850.00';
        document.getElementById('gananciaNeta').textContent = '$400.00';
        document.getElementById('totalPerdidas').textContent = '$25.00';
        
        // Cargar datos del tab actual
        cargarDatosTab(currentTab);
    }, 500);
}

function cargarDatosTab(tabName) {
    switch(tabName) {
        case 'ventas':
            cargarDatosVentas();
            break;
        case 'compras':
            cargarDatosCompras();
            break;
        case 'inventario':
            cargarDatosInventario();
            break;
        case 'creditos':
            cargarDatosCreditos();
            break;
        case 'perdidas':
            cargarDatosPerdidas();
            break;
    }
}

function cargarDatosVentas() {
    // Crear gráfico de ventas
    const ctx = document.getElementById('ventasChart').getContext('2d');
    
    if (charts.ventas) {
        charts.ventas.destroy();
    }
    
    charts.ventas = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
            datasets: [{
                label: 'Ventas Diarias',
                data: [120, 190, 300, 500, 200, 300, 450],
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Ventas por Día'
                }
            }
        }
    });
    
    // Actualizar tabla de top productos
    const tbody = document.getElementById('topProductosVendidos');
    tbody.innerHTML = `
        <tr>
            <td>Producto A</td>
            <td>25</td>
            <td>$125.00</td>
            <td>10%</td>
        </tr>
        <tr>
            <td>Producto B</td>
            <td>20</td>
            <td>$100.00</td>
            <td>8%</td>
        </tr>
    `;
}

function cargarDatosCompras() {
    // Similar implementación para compras
    console.log('Cargando datos de compras...');
}

function cargarDatosInventario() {
    // Similar implementación para inventario
    console.log('Cargando datos de inventario...');
}

function cargarDatosCreditos() {
    // Similar implementación para créditos
    console.log('Cargando datos de créditos...');
}

function cargarDatosPerdidas() {
    // Similar implementación para pérdidas
    console.log('Cargando datos de pérdidas...');
}

function exportarReporte() {
    // Implementar exportación de reportes
    alert('Función de exportación en desarrollo');
}
</script>

<style>
/* Estilos específicos para reportes */
.summary-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.summary-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.card-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.card-content h3 {
    margin: 0;
    font-size: 1.8rem;
    font-weight: 700;
    color: #2d3748;
}

.card-content p {
    margin: 0.25rem 0 0 0;
    color: #718096;
    font-size: 0.9rem;
}

.reports-tabs {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
}

.tab-buttons {
    display: flex;
    border-bottom: 1px solid #e2e8f0;
    overflow-x: auto;
}

.tab-button {
    flex: 1;
    padding: 1rem 1.5rem;
    border: none;
    background: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 500;
    color: #718096;
    transition: all 0.2s;
    white-space: nowrap;
}

.tab-button:hover {
    background: #f7fafc;
    color: #4a5568;
}

.tab-button.active {
    background: #667eea;
    color: white;
}

.tab-content {
    display: none;
    padding: 2rem;
}

.tab-content.active {
    display: block;
}

.chart-container {
    height: 400px;
    margin-bottom: 2rem;
}

.table-container h3 {
    margin-bottom: 1rem;
    color: #2d3748;
    font-size: 1.2rem;
}

.filters-section {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 1.5rem;
    flex-wrap: wrap;
}

.filter-group {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.filter-label {
    font-weight: 500;
    color: #4a5568;
    white-space: nowrap;
}

@media (max-width: 768px) {
    .filters-section {
        flex-direction: column;
        align-items: stretch;
    }
    
    .filter-group {
        justify-content: space-between;
    }
    
    .tab-buttons {
        flex-direction: column;
    }
    
    .tab-button {
        justify-content: center;
    }
}
</style>
@endsection




