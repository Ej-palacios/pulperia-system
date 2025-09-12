// ventas-pos.jsx - Componente React para el punto de venta
import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';

const VentasPOS = () => {
    const [productos, setProductos] = useState([]);
    const [carrito, setCarrito] = useState([]);
    const [categorias, setCategorias] = useState([]);
    const [categoriaActiva, setCategoriaActiva] = useState('todos');
    const [terminoBusqueda, setTerminoBusqueda] = useState('');
    const [tipoVenta, setTipoVenta] = useState('contado');
    const [clienteSeleccionado, setClienteSeleccionado] = useState(null);
    const [clientes, setClientes] = useState([]);
    const [configuracion, setConfiguracion] = useState({ impuesto: 15 });

    // Cargar datos iniciales
    useEffect(() => {
        cargarDatosIniciales();
    }, []);

    const cargarDatosIniciales = async () => {
        try {
            const [
                productosResponse,
                categoriasResponse,
                clientesResponse,
                configResponse
            ] = await Promise.all([
                fetch('/api/productos/activos'),
                fetch('/api/categorias'),
                fetch('/api/clientes'),
                fetch('/api/configuracion')
            ]);

            const productosData = await productosResponse.json();
            const categoriasData = await categoriasResponse.json();
            const clientesData = await clientesResponse.json();
            const configData = await configResponse.json();

            setProductos(productosData);
            setCategorias(['todos', ...categoriasData.map(c => c.nombre)]);
            setClientes(clientesData);
            setConfiguracion(configData);
        } catch (error) {
            console.error('Error cargando datos:', error);
        }
    };

    // Filtrar productos
    const productosFiltrados = productos.filter(producto => {
        const coincideCategoria = categoriaActiva === 'todos' || producto.categoria === categoriaActiva;
        const coincideBusqueda = producto.nombre.toLowerCase().includes(terminoBusqueda.toLowerCase()) || 
                                producto.codigo_barras.includes(terminoBusqueda);
        return coincideCategoria && coincideBusqueda;
    });

    // Manejar carrito
    const agregarAlCarrito = (producto) => {
        const existeEnCarrito = carrito.find(item => item.id === producto.id);
        
        if (existeEnCarrito) {
            setCarrito(carrito.map(item => 
                item.id === producto.id 
                    ? { ...item, cantidad: item.cantidad + 1 }
                    : item
            ));
        } else {
            setCarrito([...carrito, { 
                ...producto, 
                cantidad: 1,
                subtotal: producto.precio_venta
            }]);
        }
    };

    const actualizarCantidad = (id, nuevaCantidad) => {
        if (nuevaCantidad <= 0) {
            setCarrito(carrito.filter(item => item.id !== id));
        } else {
            setCarrito(carrito.map(item => 
                item.id === id 
                    ? { 
                        ...item, 
                        cantidad: nuevaCantidad,
                        subtotal: item.precio_venta * nuevaCantidad
                    } 
                    : item
            ));
        }
    };

    const eliminarDelCarrito = (id) => {
        setCarrito(carrito.filter(item => item.id !== id));
    };

    // Cálculos
    const subtotal = carrito.reduce((sum, item) => sum + item.subtotal, 0);
    const impuestos = PulperiaUtils.calculateTax(subtotal, configuracion.impuesto / 100);
    const total = subtotal + impuestos;

    // Finalizar venta
    const finalizarVenta = async () => {
        if (carrito.length === 0) {
            alert('El carrito está vacío');
            return;
        }

        if (tipoVenta === 'credito' && !clienteSeleccionado) {
            alert('Debe seleccionar un cliente para venta a crédito');
            return;
        }

        try {
            const response = await fetch('/api/ventas', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    productos: carrito,
                    tipo_pago: tipoVenta,
                    cliente_id: clienteSeleccionado?.id,
                    subtotal,
                    impuestos,
                    total
                })
            });

            const data = await response.json();

            if (data.success) {
                alert('Venta procesada correctamente');
                setCarrito([]);
                setClienteSeleccionado(null);
            } else {
                alert('Error al procesar la venta: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al procesar la venta');
        }
    };

    return (
        <div className="pos-container">
            {/* Lista de productos */}
            <div className="product-list">
                <div className="search-box">
                    <i className="fas fa-search"></i>
                    <input 
                        type="text" 
                        className="form-control" 
                        placeholder="Buscar producto..."
                        value={terminoBusqueda}
                        onChange={(e) => setTerminoBusqueda(e.target.value)}
                    />
                </div>

                <div className="category-filter">
                    {categorias.map(categoria => (
                        <button 
                            key={categoria}
                            className={`btn btn-sm ${categoriaActiva === categoria ? 'btn-primary' : 'btn-outline-primary'}`}
                            onClick={() => setCategoriaActiva(categoria)}
                        >
                            {categoria}
                        </button>
                    ))}
                </div>

                <div className="row">
                    {productosFiltrados.map(producto => (
                        <div key={producto.id} className="col-md-3 mb-3">
                            <div className="card product-card" onClick={() => agregarAlCarrito(producto)}>
                                <div className="card-body">
                                    <h6 className="card-title">{producto.nombre}</h6>
                                    <p className="text-muted small">Código: {producto.codigo_barras}</p>
                                    <div className="d-flex justify-content-between">
                                        <span>{formatCurrency(producto.precio_venta)}</span>
                                        <span className="badge bg-secondary">Stock: {producto.stock}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            </div>

            {/* Carrito de compras */}
            <div className="cart-container">
                <h4>Venta Actual</h4>
                
                <div className="cart-items">
                    {carrito.map(item => (
                        <div key={item.id} className="cart-item">
                            <div className="d-flex justify-content-between">
                                <div>
                                    <h6>{item.nombre}</h6>
                                    <p className="text-muted">
                                        {formatCurrency(item.precio_venta)} x {item.cantidad}
                                    </p>
                                </div>
                                <div>
                                    <strong>{formatCurrency(item.subtotal)}</strong>
                                    <button 
                                        className="btn btn-sm btn-link text-danger"
                                        onClick={() => eliminarDelCarrito(item.id)}
                                    >
                                        <i className="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div className="quantity-controls">
                                <button className="btn btn-sm btn-outline-secondary"
                                    onClick={() => actualizarCantidad(item.id, item.cantidad - 1)}>
                                    -
                                </button>
                                <input 
                                    type="number" 
                                    className="form-control form-control-sm"
                                    value={item.cantidad}
                                    onChange={(e) => actualizarCantidad(item.id, parseInt(e.target.value))}
                                />
                                <button className="btn btn-sm btn-outline-secondary"
                                    onClick={() => actualizarCantidad(item.id, item.cantidad + 1)}>
                                    +
                                </button>
                            </div>
                        </div>
                    ))}
                </div>

                <div className="cart-summary">
                    <div className="d-flex justify-content-between">
                        <span>Subtotal:</span>
                        <span>{formatCurrency(subtotal)}</span>
                    </div>
                    <div className="d-flex justify-content-between">
                        <span>Impuestos ({configuracion.impuesto}%):</span>
                        <span>{formatCurrency(impuestos)}</span>
                    </div>
                    <div className="d-flex justify-content-between fw-bold">
                        <span>Total:</span>
                        <span>{formatCurrency(total)}</span>
                    </div>

                    <div className="mb-3">
                        <label className="form-label">Tipo de venta</label>
                        <div>
                            <div className="form-check form-check-inline">
                                <input 
                                    className="form-check-input" 
                                    type="radio" 
                                    checked={tipoVenta === 'contado'}
                                    onChange={() => setTipoVenta('contado')}
                                />
                                <label className="form-check-label">Contado</label>
                            </div>
                            <div className="form-check form-check-inline">
                                <input 
                                    className="form-check-input" 
                                    type="radio" 
                                    checked={tipoVenta === 'credito'}
                                    onChange={() => setTipoVenta('credito')}
                                />
                                <label className="form-check-label">Crédito</label>
                            </div>
                        </div>
                    </div>

                    {tipoVenta === 'credito' && (
                        <div className="mb-3">
                            <label className="form-label">Cliente</label>
                            <select 
                                className="form-select"
                                value={clienteSeleccionado?.id || ''}
                                onChange={(e) => {
                                    const cliente = clientes.find(c => c.id === parseInt(e.target.value));
                                    setClienteSeleccionado(cliente);
                                }}
                            >
                                <option value="">Seleccionar cliente...</option>
                                {clientes.map(cliente => (
                                    <option key={cliente.id} value={cliente.id}>
                                        {cliente.nombre} {cliente.saldo > 0 ? `(Saldo: ${formatCurrency(cliente.saldo)})` : ''}
                                    </option>
                                ))}
                            </select>
                        </div>
                    )}

                    <div className="d-grid gap-2">
                        <button className="btn btn-success btn-lg" onClick={finalizarVenta}>
                            <i className="fas fa-check-circle me-2"></i> Finalizar Venta
                        </button>
                        <button className="btn btn-outline-secondary" onClick={() => setCarrito([])}>
                            <i className="fas fa-times me-2"></i> Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
};

// Renderizar el componente
if (document.getElementById('pos-react')) {
    const root = createRoot(document.getElementById('pos-react'));
    root.render(<VentasPOS />);
}

export default VentasPOS;