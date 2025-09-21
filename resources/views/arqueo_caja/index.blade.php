@extends('Layouts.app')

@section('title', 'Arqueo de Caja')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-icon">
                <i class="fas fa-calculator"></i>
            </div>
            <div class="header-text">
                <h1>Arqueo de Caja</h1>
                <p>Control y verificación de efectivo en caja</p>
            </div>
        </div>
        <div class="header-actions">
            <a href="{{ route('arqueo-caja.create') }}" class="fluent-button primary">
                <i class="fas fa-plus"></i>
                Nuevo Arqueo
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filters-section">
        <div class="filter-group">
            <input type="text" class="fluent-input" id="searchInput" placeholder="Buscar por observaciones...">
        </div>
        
        <div class="filter-group">
            <select class="fluent-select" id="statusFilter">
                <option value="">Todos los estados</option>
                <option value="correcto">Correcto</option>
                <option value="sobrante">Sobrante</option>
                <option value="faltante">Faltante</option>
            </select>
        </div>
        
        <div class="filter-group">
            <select class="fluent-select" id="dateFilter">
                <option value="">Todas las fechas</option>
                <option value="hoy">Hoy</option>
                <option value="semana">Esta semana</option>
                <option value="mes">Este mes</option>
            </select>
        </div>
        
        <button class="fluent-button" onclick="aplicarFiltros()">
            <i class="fas fa-filter"></i>
            Filtrar
        </button>
    </div>

    <!-- Resumen de arqueos -->
    <div class="summary-cards">
        <div class="summary-card">
            <div class="card-icon">
                <i class="fas fa-calculator"></i>
            </div>
            <div class="card-content">
                <h3 id="totalArqueos">0</h3>
                <p>Total Arqueos</p>
            </div>
        </div>
        
        <div class="summary-card">
            <div class="card-icon correct">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="card-content">
                <h3 id="arqueosCorrectos">0</h3>
                <p>Arqueos Correctos</p>
            </div>
        </div>
        
        <div class="summary-card">
            <div class="card-icon surplus">
                <i class="fas fa-arrow-up"></i>
            </div>
            <div class="card-content">
                <h3 id="totalSobrante">$0.00</h3>
                <p>Total Sobrante</p>
            </div>
        </div>
        
        <div class="summary-card">
            <div class="card-icon deficit">
                <i class="fas fa-arrow-down"></i>
            </div>
            <div class="card-content">
                <h3 id="totalFaltante">$0.00</h3>
                <p>Total Faltante</p>
            </div>
        </div>
    </div>

    <!-- Tabla de arqueos -->
    <div class="table-container">
        <div class="fluent-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Efectivo Inicial</th>
                        <th>Efectivo Final</th>
                        <th>Ventas Contado</th>
                        <th>Diferencia</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="arqueosTableBody">
                    @forelse(($arqueos ?? collect()) as $arqueo)
                    <tr>
                        <td>#{{ $arqueo->id }}</td>
                        <td>
                            <div class="date-info">
                                <div class="date">{{ \App\Helpers\PulperiaHelper::formatDate($arqueo->created_at) }}</div>
                                <div class="time">{{ $arqueo->created_at->format('H:i') }}</div>
                            </div>
                        </td>
                        <td>
                            <div class="user-info">
                                <div class="user-name">{{ $arqueo->user->name ?? 'N/A' }}</div>
                            </div>
                        </td>
                        <td>
                            <span class="amount-badge">${{ number_format($arqueo->efectivo_inicial, 2) }}</span>
                        </td>
                        <td>
                            <span class="amount-badge">${{ number_format($arqueo->efectivo_final, 2) }}</span>
                        </td>
                        <td>
                            <span class="amount-badge">${{ number_format($arqueo->ventas_contado, 2) }}</span>
                        </td>
                        <td>
                            @if($arqueo->diferencia == 0)
                                <span class="status-badge correct">${{ number_format($arqueo->diferencia, 2) }}</span>
                            @elseif($arqueo->diferencia > 0)
                                <span class="status-badge surplus">+${{ number_format($arqueo->diferencia, 2) }}</span>
                            @else
                                <span class="status-badge deficit">${{ number_format($arqueo->diferencia, 2) }}</span>
                            @endif
                        </td>
                        <td>
                            @if($arqueo->diferencia == 0)
                                <span class="status-badge correct">
                                    <i class="fas fa-check-circle"></i>
                                    Correcto
                                </span>
                            @elseif($arqueo->diferencia > 0)
                                <span class="status-badge surplus">
                                    <i class="fas fa-arrow-up"></i>
                                    Sobrante
                                </span>
                            @else
                                <span class="status-badge deficit">
                                    <i class="fas fa-arrow-down"></i>
                                    Faltante
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="action-btn view" onclick="verDetalle({{ $arqueo->id }})" title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="action-btn print" onclick="imprimirReporte({{ $arqueo->id }})" title="Imprimir">
                                    <i class="fas fa-print"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center">
                            <div class="empty-state">
                                <i class="fas fa-calculator"></i>
                                <h3>No hay arqueos registrados</h3>
                                <p>Los arqueos de caja aparecerán aquí cuando se registren</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación -->
    @if(isset($arqueos) && $arqueos->hasPages())
    <div class="pagination-container">
        {{ $arqueos->links() }}
    </div>
    @endif
</div>

<!-- Scripts -->
<script src="{{ asset('js/views.js') }}"></script>
<script>
// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    inicializarFiltros();
    calcularResumen();
});

function inicializarFiltros() {
    // Búsqueda en tiempo real
    document.getElementById('searchInput').addEventListener('input', function() {
        aplicarFiltros();
    });
    
    // Filtros de selección
    document.getElementById('statusFilter').addEventListener('change', aplicarFiltros);
    document.getElementById('dateFilter').addEventListener('change', aplicarFiltros);
}

function aplicarFiltros() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const dateFilter = document.getElementById('dateFilter').value;
    
    const rows = document.querySelectorAll('#arqueosTableBody tr');
    
    rows.forEach(row => {
        if (row.querySelector('.empty-state')) return;
        
        const observaciones = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
        const status = row.querySelector('.status-badge')?.textContent.toLowerCase() || '';
        const date = row.querySelector('.date')?.textContent || '';
        
        let show = true;
        
        // Filtro de búsqueda
        if (searchTerm && !observaciones.includes(searchTerm)) {
            show = false;
        }
        
        // Filtro de estado
        if (statusFilter && !status.includes(statusFilter)) {
            show = false;
        }
        
        // Filtro de fecha
        if (dateFilter && !filtrarPorFecha(date, dateFilter)) {
            show = false;
        }
        
        row.style.display = show ? '' : 'none';
    });
    
    calcularResumen();
}

function filtrarPorFecha(fecha, filtro) {
    const hoy = new Date();
    const fechaArqueo = new Date(fecha);
    
    switch(filtro) {
        case 'hoy':
            return fechaArqueo.toDateString() === hoy.toDateString();
        case 'semana':
            const inicioSemana = new Date(hoy);
            inicioSemana.setDate(hoy.getDate() - hoy.getDay());
            return fechaArqueo >= inicioSemana;
        case 'mes':
            return fechaArqueo.getMonth() === hoy.getMonth() && 
                   fechaArqueo.getFullYear() === hoy.getFullYear();
        default:
            return true;
    }
}

function calcularResumen() {
    const visibleRows = document.querySelectorAll('#arqueosTableBody tr:not([style*="display: none"])');
    let totalArqueos = 0;
    let arqueosCorrectos = 0;
    let totalSobrante = 0;
    let totalFaltante = 0;
    
    visibleRows.forEach(row => {
        if (row.querySelector('.empty-state')) return;
        
        totalArqueos++;
        
        const statusBadge = row.querySelector('.status-badge');
        if (statusBadge) {
            if (statusBadge.classList.contains('correct')) {
                arqueosCorrectos++;
            } else if (statusBadge.classList.contains('surplus')) {
                const diferencia = parseFloat(statusBadge.textContent.replace('+$', '').replace(',', ''));
                totalSobrante += diferencia;
            } else if (statusBadge.classList.contains('deficit')) {
                const diferencia = parseFloat(statusBadge.textContent.replace('$', '').replace(',', ''));
                totalFaltante += Math.abs(diferencia);
            }
        }
    });
    
    document.getElementById('totalArqueos').textContent = totalArqueos;
    document.getElementById('arqueosCorrectos').textContent = arqueosCorrectos;
    document.getElementById('totalSobrante').textContent = '$' + totalSobrante.toFixed(2);
    document.getElementById('totalFaltante').textContent = '$' + totalFaltante.toFixed(2);
}

function verDetalle(id) {
    // Implementar vista de detalles
    window.location.href = `/arqueo-caja/${id}`;
}

function imprimirReporte(id) {
    // Implementar impresión de reporte
    window.open(`/arqueo-caja/${id}/reporte`, '_blank');
}
</script>

<style>
/* Estilos específicos para arqueo de caja */
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

.card-icon.correct {
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
}

.card-icon.surplus {
    background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
}

.card-icon.deficit {
    background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
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

.amount-badge {
    background: #e6fffa;
    color: #38b2ac;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.8rem;
}

.status-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.8rem;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}

.status-badge.correct {
    background: #c6f6d5;
    color: #22543d;
}

.status-badge.surplus {
    background: #fed7aa;
    color: #7c2d12;
}

.status-badge.deficit {
    background: #fed7d7;
    color: #742a2a;
}

.user-info {
    display: flex;
    flex-direction: column;
}

.user-name {
    font-weight: 500;
    color: #2d3748;
}

.date-info {
    display: flex;
    flex-direction: column;
}

.date {
    font-weight: 500;
    color: #2d3748;
}

.time {
    font-size: 0.8rem;
    color: #718096;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.action-btn {
    width: 32px;
    height: 32px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.action-btn.view {
    background: #e6fffa;
    color: #38b2ac;
}

.action-btn.view:hover {
    background: #b2f5ea;
}

.action-btn.print {
    background: #e6f3ff;
    color: #3182ce;
}

.action-btn.print:hover {
    background: #bee3f8;
}

.empty-state {
    text-align: center;
    padding: 3rem;
    color: #718096;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    color: #cbd5e0;
}

.empty-state h3 {
    margin: 0 0 0.5rem 0;
    color: #4a5568;
}

.empty-state p {
    margin: 0;
}

@media (max-width: 768px) {
    .summary-cards {
        grid-template-columns: 1fr;
    }
    
    .action-buttons {
        flex-direction: column;
    }
}
</style>
@endsection









