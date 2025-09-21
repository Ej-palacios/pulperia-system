@extends('Layouts.app')

@section('title', 'Gastos')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-icon">
                <i class="fas fa-receipt"></i>
            </div>
            <div class="header-text">
                <h1>Gastos</h1>
                <p>Gestión de gastos operativos</p>
            </div>
        </div>
        <div class="header-actions">
            <a href="{{ route('gastos.create') }}" class="fluent-button primary">
                <i class="fas fa-plus"></i>
                Nuevo Gasto
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filters-section">
        <div class="filter-group">
            <input type="text" class="fluent-input" id="searchInput" placeholder="Buscar por descripción...">
        </div>
        
        <div class="filter-group">
            <select class="fluent-select" id="categoryFilter">
                <option value="">Todas las categorías</option>
                <option value="alquiler">Alquiler</option>
                <option value="luz">Luz</option>
                <option value="agua">Agua</option>
                <option value="transporte">Transporte</option>
                <option value="mantenimiento">Mantenimiento</option>
                <option value="otros">Otros</option>
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

    <!-- Resumen de gastos -->
    <div class="summary-cards">
        <div class="summary-card">
            <div class="card-icon">
                <i class="fas fa-receipt"></i>
            </div>
            <div class="card-content">
                <h3 id="totalGastos">$0.00</h3>
                <p>Total Gastos</p>
            </div>
        </div>
        
        <div class="summary-card">
            <div class="card-icon">
                <i class="fas fa-list"></i>
            </div>
            <div class="card-content">
                <h3 id="totalRegistros">0</h3>
                <p>Total Registros</p>
            </div>
        </div>
        
        <div class="summary-card">
            <div class="card-icon">
                <i class="fas fa-chart-pie"></i>
            </div>
            <div class="card-content">
                <h3 id="promedioGasto">$0.00</h3>
                <p>Promedio por Gasto</p>
            </div>
        </div>
        
        <div class="summary-card">
            <div class="card-icon">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="card-content">
                <h3 id="gastosHoy">$0.00</h3>
                <p>Gastos de Hoy</p>
            </div>
        </div>
    </div>

    <!-- Tabla de gastos -->
    <div class="table-container">
        <div class="fluent-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Descripción</th>
                        <th>Categoría</th>
                        <th>Monto</th>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Comprobante</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="gastosTableBody">
                    @forelse(($gastos ?? collect()) as $gasto)
                    <tr>
                        <td>#{{ $gasto->id }}</td>
                        <td>
                            <div class="description-info">
                                <div class="description-text">{{ $gasto->descripcion }}</div>
                                @if($gasto->observaciones)
                                    <div class="observations-text">{{ $gasto->observaciones }}</div>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="category-badge category-{{ $gasto->categoria }}">
                                {{ $gasto->nombre_categoria }}
                            </span>
                        </td>
                        <td>
                            <span class="amount-badge">${{ number_format($gasto->monto, 2) }}</span>
                        </td>
                        <td>
                            <div class="date-info">
                                <div class="date">{{ \App\Helpers\PulperiaHelper::formatDate($gasto->fecha) }}</div>
                            </div>
                        </td>
                        <td>
                            <div class="user-info">
                                <div class="user-name">{{ $gasto->user->name ?? 'N/A' }}</div>
                            </div>
                        </td>
                        <td>
                            @if($gasto->tieneComprobante())
                                <span class="status-badge has-document">
                                    <i class="fas fa-file-alt"></i>
                                    Sí
                                </span>
                            @else
                                <span class="status-badge no-document">
                                    <i class="fas fa-times"></i>
                                    No
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="action-btn view" onclick="verDetalle({{ $gasto->id }})" title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="action-btn edit" onclick="editarGasto({{ $gasto->id }})" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="action-btn delete" onclick="eliminarGasto({{ $gasto->id }})" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">
                            <div class="empty-state">
                                <i class="fas fa-receipt"></i>
                                <h3>No hay gastos registrados</h3>
                                <p>Los gastos aparecerán aquí cuando se registren</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación -->
    @if(isset($gastos) && $gastos->hasPages())
    <div class="pagination-container">
        {{ $gastos->links() }}
    </div>
    @endif
</div>

<!-- Modal de confirmación -->
<div class="modal" id="confirmModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Confirmar Eliminación</h3>
            <button class="modal-close" onclick="cerrarModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p>¿Estás seguro de que deseas eliminar este gasto?</p>
            <p class="warning-text">Esta acción no se puede deshacer.</p>
        </div>
        <div class="modal-footer">
            <button class="fluent-button" onclick="cerrarModal()">Cancelar</button>
            <button class="fluent-button danger" onclick="confirmarEliminacion()">Eliminar</button>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="{{ asset('js/views.js') }}"></script>
<script>
let gastoAEliminar = null;

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
    document.getElementById('categoryFilter').addEventListener('change', aplicarFiltros);
    document.getElementById('dateFilter').addEventListener('change', aplicarFiltros);
}

function aplicarFiltros() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const categoryFilter = document.getElementById('categoryFilter').value;
    const dateFilter = document.getElementById('dateFilter').value;
    
    const rows = document.querySelectorAll('#gastosTableBody tr');
    
    rows.forEach(row => {
        if (row.querySelector('.empty-state')) return;
        
        const description = row.querySelector('.description-text')?.textContent.toLowerCase() || '';
        const category = row.querySelector('.category-badge')?.textContent.toLowerCase() || '';
        const date = row.querySelector('.date')?.textContent || '';
        
        let show = true;
        
        // Filtro de búsqueda
        if (searchTerm && !description.includes(searchTerm)) {
            show = false;
        }
        
        // Filtro de categoría
        if (categoryFilter && !category.includes(categoryFilter)) {
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
    const fechaGasto = new Date(fecha);
    
    switch(filtro) {
        case 'hoy':
            return fechaGasto.toDateString() === hoy.toDateString();
        case 'semana':
            const inicioSemana = new Date(hoy);
            inicioSemana.setDate(hoy.getDate() - hoy.getDay());
            return fechaGasto >= inicioSemana;
        case 'mes':
            return fechaGasto.getMonth() === hoy.getMonth() && 
                   fechaGasto.getFullYear() === hoy.getFullYear();
        default:
            return true;
    }
}

function calcularResumen() {
    const visibleRows = document.querySelectorAll('#gastosTableBody tr:not([style*="display: none"])');
    let totalGastos = 0;
    let totalRegistros = 0;
    let gastosHoy = 0;
    const hoy = new Date();
    
    visibleRows.forEach(row => {
        if (row.querySelector('.empty-state')) return;
        
        totalRegistros++;
        
        const monto = parseFloat(row.querySelector('.amount-badge')?.textContent.replace('$', '').replace(',', '') || 0);
        totalGastos += monto;
        
        const fecha = row.querySelector('.date')?.textContent;
        if (fecha && new Date(fecha).toDateString() === hoy.toDateString()) {
            gastosHoy += monto;
        }
    });
    
    const promedio = totalRegistros > 0 ? totalGastos / totalRegistros : 0;
    
    document.getElementById('totalGastos').textContent = '$' + totalGastos.toFixed(2);
    document.getElementById('totalRegistros').textContent = totalRegistros;
    document.getElementById('promedioGasto').textContent = '$' + promedio.toFixed(2);
    document.getElementById('gastosHoy').textContent = '$' + gastosHoy.toFixed(2);
}

function verDetalle(id) {
    // Implementar vista de detalles
    window.location.href = `/gastos/${id}`;
}

function editarGasto(id) {
    // Implementar edición
    window.location.href = `/gastos/${id}/edit`;
}

function eliminarGasto(id) {
    gastoAEliminar = id;
    document.getElementById('confirmModal').style.display = 'flex';
}

function cerrarModal() {
    document.getElementById('confirmModal').style.display = 'none';
    gastoAEliminar = null;
}

function confirmarEliminacion() {
    if (gastoAEliminar) {
        // Crear formulario para eliminar
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/gastos/${gastoAEliminar}`;
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        const tokenField = document.createElement('input');
        tokenField.type = 'hidden';
        tokenField.name = '_token';
        tokenField.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        form.appendChild(methodField);
        form.appendChild(tokenField);
        document.body.appendChild(form);
        form.submit();
    }
}

// Cerrar modal al hacer clic fuera
document.getElementById('confirmModal').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModal();
    }
});
</script>

<style>
/* Estilos específicos para gastos */
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

.description-info {
    display: flex;
    flex-direction: column;
}

.description-text {
    font-weight: 500;
    color: #2d3748;
}

.observations-text {
    font-size: 0.8rem;
    color: #718096;
    margin-top: 0.25rem;
}

.category-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.8rem;
    text-transform: capitalize;
}

.category-alquiler { background: #fed7d7; color: #742a2a; }
.category-luz { background: #fef5e7; color: #7c2d12; }
.category-agua { background: #e6fffa; color: #22543d; }
.category-transporte { background: #e6f3ff; color: #2c5282; }
.category-mantenimiento { background: #f0fff4; color: #22543d; }
.category-otros { background: #f7fafc; color: #4a5568; }

.amount-badge {
    background: #fed7d7;
    color: #c53030;
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

.status-badge.has-document {
    background: #c6f6d5;
    color: #22543d;
}

.status-badge.no-document {
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

.action-btn.edit {
    background: #e6f3ff;
    color: #3182ce;
}

.action-btn.edit:hover {
    background: #bee3f8;
}

.action-btn.delete {
    background: #fed7d7;
    color: #c53030;
}

.action-btn.delete:hover {
    background: #feb2b2;
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

.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background: white;
    border-radius: 12px;
    max-width: 500px;
    width: 90%;
    box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
}

.modal-header {
    padding: 1.5rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    color: #2d3748;
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: #718096;
}

.modal-body {
    padding: 1.5rem;
}

.warning-text {
    color: #e53e3e;
    font-weight: 500;
    margin-top: 0.5rem;
}

.modal-footer {
    padding: 1.5rem;
    border-top: 1px solid #e2e8f0;
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
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









