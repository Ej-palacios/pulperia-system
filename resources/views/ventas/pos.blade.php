@extends('layouts.app', ['title' => 'Punto de Venta'])

@section('page-title')
    Punto de Venta (POS)
@endsection

@section('page-subtitle')
    Sistema de ventas rápido y eficiente
@endsection

@section('styles')
<style>
.pos-container {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: var(--fluent-space-lg);
    height: calc(100vh - 200px);
    min-height: 600px;
}

.product-section {
    background: var(--fluent-white);
    border-radius: var(--fluent-radius-lg);
    box-shadow: var(--fluent-shadow-sm);
    padding: var(--fluent-space-lg);
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.cart-section {
    background: var(--fluent-white);
    border-radius: var(--fluent-radius-lg);
    box-shadow: var(--fluent-shadow-sm);
    padding: var(--fluent-space-lg);
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.search-container {
    margin-bottom: var(--fluent-space-lg);
    position: relative;
}

.search-input {
    width: 100%;
    padding: var(--fluent-space-md) var(--fluent-space-lg);
    padding-left: var(--fluent-space-xl);
    border: 1px solid var(--fluent-gray-40);
    border-radius: var(--fluent-radius-md);
    font-size: var(--fluent-font-size-base);
    transition: all var(--fluent-transition-fast);
}

.search-input:focus {
    outline: none;
    border-color: var(--fluent-primary);
    box-shadow: 0 0 0 2px rgba(0, 120, 212, 0.2);
}

.search-icon {
    position: absolute;
    left: var(--fluent-space-md);
    top: 50%;
    transform: translateY(-50%);
    color: var(--fluent-gray-70);
    font-size: var(--fluent-font-size-sm);
}

.category-filters {
    display: flex;
    gap: var(--fluent-space-sm);
    margin-bottom: var(--fluent-space-lg);
    flex-wrap: wrap;
}

.category-btn {
    padding: var(--fluent-space-sm) var(--fluent-space-md);
    border: 1px solid var(--fluent-gray-40);
    background: var(--fluent-white);
    border-radius: var(--fluent-radius-md);
    cursor: pointer;
    transition: all var(--fluent-transition-fast);
    font-size: var(--fluent-font-size-sm);
}

.category-btn:hover {
    background: var(--fluent-gray-20);
    border-color: var(--fluent-gray-50);
}

.category-btn.active {
    background: var(--fluent-primary);
    color: var(--fluent-white);
    border-color: var(--fluent-primary);
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: var(--fluent-space-md);
    overflow-y: auto;
    flex: 1;
    padding-right: var(--fluent-space-sm);
}

.product-card {
    border: 1px solid var(--fluent-gray-30);
    border-radius: var(--fluent-radius-md);
    padding: var(--fluent-space-md);
    cursor: pointer;
    transition: all var(--fluent-transition-fast);
    background: var(--fluent-white);
}

.product-card:hover {
    border-color: var(--fluent-primary);
    box-shadow: var(--fluent-shadow-md);
    transform: translateY(-2px);
}

.product-name {
    font-weight: 600;
    color: var(--fluent-gray-120);
    margin-bottom: var(--fluent-space-xs);
    font-size: var(--fluent-font-size-sm);
}

.product-code {
    color: var(--fluent-gray-70);
    font-size: var(--fluent-font-size-xs);
    margin-bottom: var(--fluent-space-sm);
}

.product-price {
    font-weight: 600;
    color: var(--fluent-primary);
    font-size: var(--fluent-font-size-base);
}

.product-stock {
    background: var(--fluent-gray-20);
    color: var(--fluent-gray-80);
    padding: 2px var(--fluent-space-xs);
    border-radius: var(--fluent-radius-sm);
    font-size: var(--fluent-font-size-xs);
    display: inline-block;
    margin-top: var(--fluent-space-xs);
}

.cart-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: var(--fluent-space-lg);
    padding-bottom: var(--fluent-space-md);
    border-bottom: 1px solid var(--fluent-gray-30);
}

.cart-title {
    font-size: var(--fluent-font-size-lg);
    font-weight: 600;
    color: var(--fluent-gray-120);
    margin: 0;
}

.cart-items {
    flex: 1;
    overflow-y: auto;
    margin-bottom: var(--fluent-space-lg);
}

.cart-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--fluent-space-md);
    border: 1px solid var(--fluent-gray-30);
    border-radius: var(--fluent-radius-md);
    margin-bottom: var(--fluent-space-sm);
    background: var(--fluent-gray-10);
}

.cart-item-info {
    flex: 1;
}

.cart-item-name {
    font-weight: 600;
    color: var(--fluent-gray-120);
    margin-bottom: var(--fluent-space-xs);
    font-size: var(--fluent-font-size-sm);
}

.cart-item-price {
    color: var(--fluent-gray-70);
    font-size: var(--fluent-font-size-xs);
}

.cart-item-total {
    font-weight: 600;
    color: var(--fluent-primary);
    margin-right: var(--fluent-space-sm);
}

.cart-item-actions {
    display: flex;
    align-items: center;
    gap: var(--fluent-space-xs);
}

.quantity-controls {
    display: flex;
    align-items: center;
    gap: var(--fluent-space-xs);
}

.qty-btn {
    width: 32px;
    height: 32px;
    border: 1px solid var(--fluent-gray-40);
    background: var(--fluent-white);
    border-radius: var(--fluent-radius-sm);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: var(--fluent-font-size-sm);
    transition: all var(--fluent-transition-fast);
}

.qty-btn:hover {
    background: var(--fluent-gray-20);
    border-color: var(--fluent-gray-50);
}

.qty-input {
    width: 50px;
    height: 32px;
    text-align: center;
    border: 1px solid var(--fluent-gray-40);
    border-radius: var(--fluent-radius-sm);
    font-size: var(--fluent-font-size-sm);
}

.remove-btn {
    color: var(--fluent-error);
    background: none;
    border: none;
    cursor: pointer;
    padding: var(--fluent-space-xs);
    border-radius: var(--fluent-radius-sm);
    transition: all var(--fluent-transition-fast);
}

.remove-btn:hover {
    background: var(--fluent-error-light);
}

.cart-summary {
    border-top: 1px solid var(--fluent-gray-30);
    padding-top: var(--fluent-space-lg);
}

.summary-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: var(--fluent-space-sm);
    font-size: var(--fluent-font-size-sm);
}

.summary-total {
    font-weight: 600;
    font-size: var(--fluent-font-size-base);
    color: var(--fluent-gray-120);
    border-top: 1px solid var(--fluent-gray-40);
    padding-top: var(--fluent-space-sm);
    margin-top: var(--fluent-space-sm);
}

.sale-type-section {
    margin-bottom: var(--fluent-space-lg);
}

.sale-type-label {
    font-weight: 600;
    color: var(--fluent-gray-120);
    margin-bottom: var(--fluent-space-sm);
    font-size: var(--fluent-font-size-sm);
}

.sale-type-options {
    display: flex;
    gap: var(--fluent-space-md);
}

.sale-type-option {
    display: flex;
    align-items: center;
    gap: var(--fluent-space-xs);
}

.sale-type-option input[type="radio"] {
    margin: 0;
}

.client-section {
    margin-bottom: var(--fluent-space-lg);
}

.client-select {
    width: 100%;
    padding: var(--fluent-space-sm) var(--fluent-space-md);
    border: 1px solid var(--fluent-gray-40);
    border-radius: var(--fluent-radius-md);
    font-size: var(--fluent-font-size-sm);
}

.cart-actions {
    display: flex;
    flex-direction: column;
    gap: var(--fluent-space-sm);
}

.btn-finalize {
    background: var(--fluent-success);
    color: var(--fluent-white);
    border: none;
    padding: var(--fluent-space-md) var(--fluent-space-lg);
    border-radius: var(--fluent-radius-md);
    font-weight: 600;
    cursor: pointer;
    transition: all var(--fluent-transition-fast);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--fluent-space-sm);
}

.btn-finalize:hover {
    background: var(--fluent-success-hover);
    transform: translateY(-1px);
    box-shadow: var(--fluent-shadow-md);
}

.btn-cancel {
    background: var(--fluent-gray-20);
    color: var(--fluent-gray-80);
    border: 1px solid var(--fluent-gray-40);
    padding: var(--fluent-space-sm) var(--fluent-space-lg);
    border-radius: var(--fluent-radius-md);
    cursor: pointer;
    transition: all var(--fluent-transition-fast);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--fluent-space-sm);
}

.btn-cancel:hover {
    background: var(--fluent-gray-30);
    border-color: var(--fluent-gray-50);
}

.empty-cart {
    text-align: center;
    color: var(--fluent-gray-70);
    padding: var(--fluent-space-xl);
    font-style: italic;
}

/* Responsive */
@media (max-width: 1024px) {
    .pos-container {
        grid-template-columns: 1fr;
        height: auto;
        min-height: auto;
    }
    
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        max-height: 400px;
    }
}

@media (max-width: 768px) {
    .pos-container {
        gap: var(--fluent-space-md);
    }
    
    .product-section,
    .cart-section {
        padding: var(--fluent-space-md);
    }
    
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: var(--fluent-space-sm);
    }
    
    .category-filters {
        gap: var(--fluent-space-xs);
    }
    
    .category-btn {
        padding: var(--fluent-space-xs) var(--fluent-space-sm);
        font-size: var(--fluent-font-size-xs);
    }
}
</style>
@endsection

@section('content')
<div class="pos-container">
    <!-- Sección de Productos -->
    <div class="product-section">
        <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" class="search-input" id="searchInput" placeholder="Buscar producto por nombre o código...">
        </div>
        
        <div class="category-filters" id="categoryFilters">
            <button class="category-btn active" data-category="todos">Todos</button>
        </div>
        
        <div class="products-grid" id="productsGrid">
            <!-- Los productos se cargarán aquí dinámicamente -->
        </div>
    </div>
    
    <!-- Sección del Carrito -->
    <div class="cart-section">
        <div class="cart-header">
            <h3 class="cart-title">Venta Actual</h3>
        </div>
        
        <div class="cart-items" id="cartItems">
            <div class="empty-cart">
                <i class="fas fa-shopping-cart" style="font-size: 2rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                <p>El carrito está vacío</p>
                <p>Selecciona productos para comenzar la venta</p>
            </div>
        </div>
        
        <div class="cart-summary" id="cartSummary" style="display: none;">
            <div class="summary-row">
                <span>Subtotal:</span>
                <span id="subtotal">C$ 0.00</span>
            </div>
            <div class="summary-row">
                <span>Impuestos (15%):</span>
                <span id="taxes">C$ 0.00</span>
            </div>
            <div class="summary-row summary-total">
                <span>Total:</span>
                <span id="total">C$ 0.00</span>
            </div>
            
            <div class="sale-type-section">
                <div class="sale-type-label">Tipo de Venta</div>
                <div class="sale-type-options">
                    <div class="sale-type-option">
                        <input type="radio" id="cashSale" name="saleType" value="contado" checked>
                        <label for="cashSale">Contado</label>
                    </div>
                    <div class="sale-type-option">
                        <input type="radio" id="creditSale" name="saleType" value="credito">
                        <label for="creditSale">Crédito</label>
                    </div>
                </div>
            </div>
            
            <div class="client-section" id="clientSection" style="display: none;">
                <label for="clientSelect" class="sale-type-label">Cliente</label>
                <select class="client-select" id="clientSelect">
                    <option value="">Seleccionar cliente...</option>
                </select>
            </div>
            
            <div class="cart-actions">
                <button class="btn-finalize" id="finalizeBtn">
                    <i class="fas fa-check-circle"></i>
                    Finalizar Venta
                </button>
                <button class="btn-cancel" id="cancelBtn">
                    <i class="fas fa-times"></i>
                    Cancelar Venta
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
class POSSystem {
    constructor() {
        this.productos = [];
        this.categorias = [];
        this.clientes = [];
        this.carrito = [];
        this.categoriaActiva = 'todos';
        this.terminoBusqueda = '';
        this.configuracion = { impuesto: 15 };
        
        this.init();
    }
    
    async init() {
        await this.cargarDatos();
        this.setupEventListeners();
        this.renderizarProductos();
        this.renderizarCategorias();
        this.renderizarClientes();
    }
    
    async cargarDatos() {
        try {
            const [productosResponse, categoriasResponse, clientesResponse] = await Promise.all([
                fetch('/api/productos/activos'),
                fetch('/api/categorias'),
                fetch('/api/clientes')
            ]);
            
            this.productos = await productosResponse.json();
            this.categorias = await categoriasResponse.json();
            this.clientes = await clientesResponse.json();
        } catch (error) {
            console.error('Error cargando datos:', error);
            this.mostrarAlerta('Error al cargar los datos', 'error');
        }
    }
    
    setupEventListeners() {
        // Búsqueda
        document.getElementById('searchInput').addEventListener('input', (e) => {
            this.terminoBusqueda = e.target.value;
            this.renderizarProductos();
        });
        
        // Tipo de venta
        document.querySelectorAll('input[name="saleType"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                const clientSection = document.getElementById('clientSection');
                if (e.target.value === 'credito') {
                    clientSection.style.display = 'block';
                } else {
                    clientSection.style.display = 'none';
                }
            });
        });
        
        // Botones de acción
        document.getElementById('finalizeBtn').addEventListener('click', () => this.finalizarVenta());
        document.getElementById('cancelBtn').addEventListener('click', () => this.cancelarVenta());
    }
    
    renderizarCategorias() {
        const container = document.getElementById('categoryFilters');
        const categorias = ['todos', ...this.categorias.map(c => c.nombre)];
        
        container.innerHTML = categorias.map(categoria => `
            <button class="category-btn ${categoria === this.categoriaActiva ? 'active' : ''}" 
                    data-category="${categoria}">
                ${categoria}
            </button>
        `).join('');
        
        // Event listeners para filtros de categoría
        container.querySelectorAll('.category-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.categoriaActiva = e.target.dataset.category;
                this.renderizarCategorias();
                this.renderizarProductos();
            });
        });
    }
    
    renderizarProductos() {
        const productosFiltrados = this.productos.filter(producto => {
            const coincideCategoria = this.categoriaActiva === 'todos' || 
                                    producto.categoria?.nombre === this.categoriaActiva;
            const coincideBusqueda = producto.nombre.toLowerCase().includes(this.terminoBusqueda.toLowerCase()) || 
                                    producto.codigo_barras?.includes(this.terminoBusqueda);
            return coincideCategoria && coincideBusqueda;
        });
        
        const container = document.getElementById('productsGrid');
        
        if (productosFiltrados.length === 0) {
            container.innerHTML = `
                <div style="grid-column: 1 / -1; text-align: center; padding: 2rem; color: var(--fluent-gray-70);">
                    <i class="fas fa-search" style="font-size: 2rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                    <p>No se encontraron productos</p>
                </div>
            `;
            return;
        }
        
        container.innerHTML = productosFiltrados.map(producto => `
            <div class="product-card" onclick="pos.agregarAlCarrito(${producto.id})">
                <div class="product-name">${producto.nombre}</div>
                <div class="product-code">Código: ${producto.codigo_barras || 'N/A'}</div>
                <div class="product-price">${this.formatearMoneda(producto.precio_venta)}</div>
                <div class="product-stock">Stock: ${producto.stock}</div>
            </div>
        `).join('');
    }
    
    renderizarClientes() {
        const select = document.getElementById('clientSelect');
        select.innerHTML = '<option value="">Seleccionar cliente...</option>' +
            this.clientes.map(cliente => `
                <option value="${cliente.id}">
                    ${cliente.nombre} ${cliente.saldo > 0 ? `(Saldo: ${this.formatearMoneda(cliente.saldo)})` : ''}
                </option>
            `).join('');
    }
    
    agregarAlCarrito(productoId) {
        const producto = this.productos.find(p => p.id === productoId);
        if (!producto) return;
        
        const existeEnCarrito = this.carrito.find(item => item.id === producto.id);
        
        if (existeEnCarrito) {
            existeEnCarrito.cantidad += 1;
            existeEnCarrito.subtotal = existeEnCarrito.precio_venta * existeEnCarrito.cantidad;
        } else {
            this.carrito.push({
                ...producto,
                cantidad: 1,
                subtotal: producto.precio_venta
            });
        }
        
        this.renderizarCarrito();
        this.actualizarResumen();
    }
    
    actualizarCantidad(productoId, nuevaCantidad) {
        if (nuevaCantidad <= 0) {
            this.carrito = this.carrito.filter(item => item.id !== productoId);
        } else {
            const item = this.carrito.find(item => item.id === productoId);
            if (item) {
                item.cantidad = nuevaCantidad;
                item.subtotal = item.precio_venta * nuevaCantidad;
            }
        }
        
        this.renderizarCarrito();
        this.actualizarResumen();
    }
    
    eliminarDelCarrito(productoId) {
        this.carrito = this.carrito.filter(item => item.id !== productoId);
        this.renderizarCarrito();
        this.actualizarResumen();
    }
    
    renderizarCarrito() {
        const container = document.getElementById('cartItems');
        const summary = document.getElementById('cartSummary');
        
        if (this.carrito.length === 0) {
            container.innerHTML = `
                <div class="empty-cart">
                    <i class="fas fa-shopping-cart" style="font-size: 2rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                    <p>El carrito está vacío</p>
                    <p>Selecciona productos para comenzar la venta</p>
                </div>
            `;
            summary.style.display = 'none';
            return;
        }
        
        summary.style.display = 'block';
        
        container.innerHTML = this.carrito.map(item => `
            <div class="cart-item">
                <div class="cart-item-info">
                    <div class="cart-item-name">${item.nombre}</div>
                    <div class="cart-item-price">${this.formatearMoneda(item.precio_venta)} x ${item.cantidad}</div>
                </div>
                <div class="cart-item-actions">
                    <span class="cart-item-total">${this.formatearMoneda(item.subtotal)}</span>
                    <div class="quantity-controls">
                        <button class="qty-btn" onclick="pos.actualizarCantidad(${item.id}, ${item.cantidad - 1})">-</button>
                        <input type="number" class="qty-input" value="${item.cantidad}" 
                               onchange="pos.actualizarCantidad(${item.id}, parseInt(this.value))">
                        <button class="qty-btn" onclick="pos.actualizarCantidad(${item.id}, ${item.cantidad + 1})">+</button>
                    </div>
                    <button class="remove-btn" onclick="pos.eliminarDelCarrito(${item.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `).join('');
    }
    
    actualizarResumen() {
        const subtotal = this.carrito.reduce((sum, item) => sum + item.subtotal, 0);
        const impuestos = subtotal * (this.configuracion.impuesto / 100);
        const total = subtotal + impuestos;
        
        document.getElementById('subtotal').textContent = this.formatearMoneda(subtotal);
        document.getElementById('taxes').textContent = this.formatearMoneda(impuestos);
        document.getElementById('total').textContent = this.formatearMoneda(total);
    }
    
    async finalizarVenta() {
        if (this.carrito.length === 0) {
            this.mostrarAlerta('El carrito está vacío', 'warning');
            return;
        }
        
        const tipoVenta = document.querySelector('input[name="saleType"]:checked').value;
        const clienteId = document.getElementById('clientSelect').value;
        
        if (tipoVenta === 'credito' && !clienteId) {
            this.mostrarAlerta('Debe seleccionar un cliente para venta a crédito', 'warning');
            return;
        }
        
        try {
            const subtotal = this.carrito.reduce((sum, item) => sum + item.subtotal, 0);
            const impuestos = subtotal * (this.configuracion.impuesto / 100);
            const total = subtotal + impuestos;
            
            const response = await fetch('/ventas/procesar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    productos: this.carrito,
                    tipo_pago: tipoVenta,
                    cliente_id: clienteId ? parseInt(clienteId) : null,
                    subtotal,
                    impuestos,
                    total
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.mostrarAlerta('Venta procesada correctamente', 'success');
                this.cancelarVenta();
            } else {
                this.mostrarAlerta('Error al procesar la venta: ' + data.message, 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            this.mostrarAlerta('Error al procesar la venta', 'error');
        }
    }
    
    cancelarVenta() {
        this.carrito = [];
        this.renderizarCarrito();
        this.actualizarResumen();
        document.getElementById('clientSection').style.display = 'none';
        document.getElementById('cashSale').checked = true;
        document.getElementById('clientSelect').value = '';
    }
    
    formatearMoneda(cantidad) {
        return 'C$ ' + parseFloat(cantidad).toFixed(2);
    }
    
    mostrarAlerta(mensaje, tipo) {
        // Crear alerta temporal
        const alerta = document.createElement('div');
        alerta.className = `alert alert-${tipo === 'error' ? 'danger' : tipo} alert-dismissible fade show`;
        alerta.style.position = 'fixed';
        alerta.style.top = '20px';
        alerta.style.right = '20px';
        alerta.style.zIndex = '9999';
        alerta.innerHTML = `
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(alerta);
        
        // Auto-remover después de 5 segundos
        setTimeout(() => {
            if (alerta.parentNode) {
                alerta.parentNode.removeChild(alerta);
            }
        }, 5000);
    }
}

// Inicializar el sistema POS
let pos;
document.addEventListener('DOMContentLoaded', () => {
    pos = new POSSystem();
});
</script>
@endsection

