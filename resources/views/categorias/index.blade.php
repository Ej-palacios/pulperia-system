@extends('layouts.app', ['title' => 'Categorías'])

@section('page-title')
    Gestión de Categorías
@endsection

@section('page-subtitle')
    Organiza tus productos por categorías
@endsection

@section('header-buttons')
<x-fluent-button 
    variant="primary" 
    size="medium"
    href="{{ route('categorias.create') }}"
    icon="plus"
    iconPosition="left"
>
    Nueva Categoría
</x-fluent-button>
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('css/views.css') }}">
@endsection

@section('content')
<div class="categories-container">
    <!-- Filtros y búsqueda -->
    <div class="filters-section">
        <div class="search-container">
            <x-fluent-input 
                type="search" 
                placeholder="Buscar categorías..."
                icon="search"
                id="searchInput"
            />
        </div>
    </div>

    <!-- Grid de categorías -->
    <div class="categories-grid" id="categoriesGrid">
        @forelse(($categorias ?? collect()) as $categoria)
        <div class="category-card">
            <div class="category-header">
                <div class="category-icon">
                    <i class="fas fa-{{ $categoria->icono ?? 'tag' }}"></i>
                </div>
                <div class="category-actions">
                    <x-fluent-button 
                        variant="ghost" 
                        size="small"
                        href="{{ route('categorias.edit', $categoria) }}"
                        icon="edit"
                    />
                    <x-fluent-button 
                        variant="ghost" 
                        size="small"
                        href="{{ route('categorias.show', $categoria) }}"
                        icon="eye"
                    />
                </div>
            </div>
            
            <div class="category-content">
                <h3 class="category-name">{{ $categoria->nombre }}</h3>
                <p class="category-description">{{ $categoria->descripcion ?? 'Sin descripción' }}</p>
                
                <div class="category-stats">
                    <div class="stat-item">
                        <span class="stat-label">Productos:</span>
                        <span class="stat-value">{{ $categoria->productos_count ?? 0 }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Estado:</span>
                        <span class="status-badge status-{{ $categoria->activa ? 'active' : 'inactive' }}">
                            {{ $categoria->activa ? 'Activa' : 'Inactiva' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="empty-state">
            <i class="fas fa-tags" style="font-size: 4rem; color: var(--fluent-gray-60); margin-bottom: 1rem;"></i>
            <h3>No hay categorías</h3>
            <p>Crea categorías para organizar mejor tus productos</p>
            <x-fluent-button 
                variant="primary" 
                size="medium"
                href="{{ route('categorias.create') }}"
                icon="plus"
                iconPosition="left"
            >
                Crear Primera Categoría
            </x-fluent-button>
        </div>
        @endforelse
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/views.js') }}"></script>
@endsection