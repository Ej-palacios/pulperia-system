@extends('Layouts.app')

@section('title', 'Productos Dañados')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="header-text">
                <h1>Productos Dañados</h1>
                <p>Gestión de productos dañados y pérdidas</p>
            </div>
        </div>
        <div class="header-actions">
            <a href="{{ route('productos-danados.create') }}" class="fluent-button primary">
                <i class="fas fa-plus"></i>
                Registrar Daño
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filters-section">
        <div class="filter-group">
            <input type="text" class="fluent-input" id="searchInput" placeholder="Buscar por producto o motivo...">
        </div>
        
        <div class="filter-group">
            <select class="fluent-select" id="productFilter">
                <option value="">Todos los productos</option>
                @foreach(($productos ?? collect()) as $producto)
                    <option value="{{ $producto->id }}">{{ $producto->nombre }}</option>
                @endforeach
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

    <!-- Resumen de pérdidas -->
    <div class="summary-cards">
        <div class="summary-card">
            <div class="card-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="card-content">
                <h3 id="totalPerdidas">$0.00</h3>
                <p>Total Pérdidas</p>
            </div>
        </div>
        
        <div class="summary-card">
            <div class="card-icon">
                <i class="fas fa-boxes"></i>
            </div>
            <div class="card-content">
                <h3 id="totalProductos">0</h3>
                <p>Productos Afectados</p>
            </div>
        </div>
        
        <div class="summary-card">
            <div class="card-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="card-content">
                <h3 id="promedioPerdida">$0.00</h3>
                <p>Promedio por Daño</p>
            </div>
        </div>
    </div>

    <!-- Tabla de productos dañados -->
    <div class="table-container">
        <div class="fluent-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Motivo</th>
                        <th>Costo Pérdida</th>
                        <th>Registrado por</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="damagedProductsTableBody">
                    @forelse(($productosDanados ?? collect()) as $productoDanado)
                    <tr>
                        <td>#{{ $productoDanado->id }}</td>
                        <td>
                            <div class="product-info">
                                <div class="product-name">{{ $productoDanado->producto->nombre ?? 'N/A' }}</div>
                                <div class="product-code">{{ $productoDanado->producto->codigo_barras ?? 'Sin código' }}</div>
                            </div>
                        </td>
                        <td>
                            <span class="quantity-badge">{{ $productoDanado->cantidad }}</span>
                        </td>
                        <td>
                            <div class="reason-text">{{ $productoDanado->motivo }}</div>
                        </td>
                        <td>
                            <span class="amount-badge loss">${{ number_format($productoDanado->costo_perdida, 2) }}</span>
                        </td>
                        <td>
                            <div class="user-info">
                                <div class="user-name">{{ $productoDanado->user->name ?? 'N/A' }}</div>
                            </div>
                        </td>
                        <td>
                            <div class="date-info">
                                <div class="date">{{ \App\Helpers\PulperiaHelper::formatDate($productoDanado->created_at) }}</div>
                                <div class="time">{{ $productoDanado->created_at->format('H:i') }}</div>
                            </div>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="action-btn view" onclick="verDetalle({{ $productoDanado->id }})" title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="action-btn delete" onclick="eliminarRegistro({{ $productoDanado->id }})" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">
                            <div class="empty-state">
                                <i class="fas fa-exclamation-triangle"></i>
                                <h3>No hay productos dañados registrados</h3>
                                <p>Los productos dañados aparecerán aquí cuando se registren</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación -->
    @if(isset($productosDanados) && $productosDanados->hasPages())
    <div class="pagination-container">
        {{ $productosDanados->links() }}
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
            <p>¿Estás seguro de que deseas eliminar este registro de producto dañado?</p>
            <p class="warning-text">Esta acción revertirá el stock del producto.</p>
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
let productoAEliminar = null;

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
    document.getElementById('productFilter').addEventListener('change', aplicarFiltros);
    document.getElementById('dateFilter').addEventListener('change', aplicarFiltros);
}

function aplicarFiltros() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const productFilter = document.getElementById('productFilter').value;
    const dateFilter = document.getElementById('dateFilter').value;
    
    const rows = document.querySelectorAll('#damagedProductsTableBody tr');
    
    rows.forEach(row => {
        if (row.querySelector('.empty-state')) return;
        
        const productName = row.querySelector('.product-name')?.textContent.toLowerCase() || '';
        const reason = row.querySelector('.reason-text')?.textContent.toLowerCase() || '';
        const productId = row.querySelector('td:nth-child(2)')?.dataset.productId || '';
        const date = row.querySelector('.date')?.textContent || '';
        
        let show = true;
        
        // Filtro de búsqueda
        if (searchTerm && !productName.includes(searchTerm) && !reason.includes(searchTerm)) {
            show = false;
        }
        
        // Filtro de producto
        if (productFilter && productId !== productFilter) {
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
    const fechaRegistro = new Date(fecha);
    
    switch(filtro) {
        case 'hoy':
            return fechaRegistro.toDateString() === hoy.toDateString();
        case 'semana':
            const inicioSemana = new Date(hoy);
            inicioSemana.setDate(hoy.getDate() - hoy.getDay());
            return fechaRegistro >= inicioSemana;
        case 'mes':
            return fechaRegistro.getMonth() === hoy.getMonth() && 
                   fechaRegistro.getFullYear() === hoy.getFullYear();
        default:
            return true;
    }
}

function calcularResumen() {
    const visibleRows = document.querySelectorAll('#damagedProductsTableBody tr:not([style*="display: none"])');
    let totalPerdidas = 0;
    let totalProductos = 0;
    
    visibleRows.forEach(row => {
        if (row.querySelector('.empty-state')) return;
        
        const cantidad = parseFloat(row.querySelector('.quantity-badge')?.textContent || 0);
        const costo = parseFloat(row.querySelector('.amount-badge')?.textContent.replace('$', '').replace(',', '') || 0);
        
        if (cantidad > 0) {
            totalProductos++;
            totalPerdidas += costo;
        }
    });
    
    const promedio = totalProductos > 0 ? totalPerdidas / totalProductos : 0;
    
    document.getElementById('totalPerdidas').textContent = '$' + totalPerdidas.toFixed(2);
    document.getElementById('totalProductos').textContent = totalProductos;
    document.getElementById('promedioPerdida').textContent = '$' + promedio.toFixed(2);
}

function verDetalle(id) {
    // Implementar vista de detalles
    window.location.href = `/productos-danados/${id}`;
}

function eliminarRegistro(id) {
    productoAEliminar = id;
    document.getElementById('confirmModal').style.display = 'flex';
}

function cerrarModal() {
    document.getElementById('confirmModal').style.display = 'none';
    productoAEliminar = null;
}

function confirmarEliminacion() {
    if (productoAEliminar) {
        // Crear formulario para eliminar
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/productos-danados/${productoAEliminar}`;
        
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
/* Estilos específicos para productos dañados */
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

.product-info {
    display: flex;
    flex-direction: column;
}

.product-name {
    font-weight: 600;
    color: #2d3748;
}

.product-code {
    font-size: 0.8rem;
    color: #718096;
}

.quantity-badge {
    background: #fed7d7;
    color: #c53030;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.8rem;
}

.amount-badge.loss {
    background: #fed7d7;
    color: #c53030;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.8rem;
}

.reason-text {
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
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
    
    .reason-text {
        max-width: 150px;
    }
}
</style>
@endsection
