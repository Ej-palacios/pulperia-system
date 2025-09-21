@extends('layouts.app', ['title' => 'Productos'])

@section('page-title')
    Gestión de Productos
@endsection

@section('page-subtitle')
    Administra el inventario de productos
@endsection

@section('header-buttons')
<x-fluent-button 
    variant="primary" 
    size="medium"
    href="{{ route('productos.create') }}"
    icon="plus"
    iconPosition="left"
>
    Nuevo Producto
</x-fluent-button>
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('css/views.css') }}">
@endsection

@section('content')
<div class="products-container">
    <!-- Filtros y búsqueda -->
    <div class="filters-section">
        <div class="search-container">
            <x-fluent-input 
                type="search" 
                placeholder="Buscar productos..."
                icon="search"
                id="searchInput"
            />
        </div>
        
        <div class="filter-controls">
            <select class="fluent-select" id="categoryFilter">
                <option value="">Todas las categorías</option>
                @foreach(($categorias ?? collect()) as $categoria)
                    <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                @endforeach
            </select>
            
            <select class="fluent-select" id="stockFilter">
                <option value="">Todos los stocks</option>
                <option value="bajo">Stock bajo</option>
                <option value="agotado">Agotado</option>
                <option value="disponible">Disponible</option>
            </select>
        </div>
    </div>

    <!-- Grid de tarjetas de productos -->
    <div class="row g-3">
        @forelse(($productos ?? collect()) as $producto)
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <x-ui.product-card 
                    :nombre="$producto->nombre"
                    :precio="$producto->precio_venta"
                    :categoria="$producto->categoria->nombre ?? 'Sin categoría'"
                    :imagen="$producto->imagen_url ?? $producto->imagen"
                    :href="route('productos.show', $producto)"
                    :badge="$producto->tieneStockBajo() ? 'Stock bajo' : null"
                />
            </div>
        @empty
            <div class="col-12">
                <div class="empty-state text-center py-5">
                    <i class="fas fa-box" style="font-size: 3rem; color: var(--fluent-gray-60); margin-bottom: 1rem;"></i>
                    <h3>No hay productos</h3>
                    <p>Comienza agregando tu primer producto al inventario</p>
                    <x-fluent-button 
                        variant="primary" 
                        size="medium"
                        href="{{ route('productos.create') }}"
                        icon="plus"
                        iconPosition="left"
                    >
                        Agregar Producto
                    </x-fluent-button>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/views.js') }}"></script>
@endsection