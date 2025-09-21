@extends('layouts.app', ['title' => 'Proveedores'])

@section('page-title')
    Gestión de Proveedores
@endsection

@section('page-subtitle')
    Administra tus proveedores y contactos comerciales
@endsection

@section('header-buttons')
<x-fluent-button 
    variant="primary" 
    size="medium"
    href="{{ route('proveedores.create') }}"
    icon="plus"
    iconPosition="left"
>
    Nuevo Proveedor
</x-fluent-button>
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('css/views.css') }}">
@endsection

@section('content')
<div class="suppliers-container">
    <!-- Filtros y búsqueda -->
    <div class="filters-section">
        <div class="search-container">
            <x-fluent-input 
                type="search" 
                placeholder="Buscar proveedores..."
                icon="search"
                id="searchInput"
            />
        </div>
        
        <div class="filter-controls">
            <select class="fluent-select" id="statusFilter">
                <option value="">Todos los estados</option>
                <option value="activo">Activos</option>
                <option value="inactivo">Inactivos</option>
            </select>
        </div>
    </div>

    <!-- Tabla de proveedores -->
    <div class="table-container">
        <x-fluent-table>
            <thead>
                <tr>
                    <th>Proveedor</th>
                    <th>Contacto</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Dirección</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="suppliersTableBody">
                @forelse(($proveedores ?? collect()) as $proveedor)
                <tr>
                    <td>
                        <div class="supplier-info">
                            <div class="supplier-name">{{ $proveedor->nombre }}</div>
                            <div class="supplier-code">{{ $proveedor->codigo ?? 'Sin código' }}</div>
                        </div>
                    </td>
                    <td>{{ $proveedor->contacto ?? 'N/A' }}</td>
                    <td>{{ $proveedor->telefono ?? 'N/A' }}</td>
                    <td>{{ $proveedor->email ?? 'N/A' }}</td>
                    <td>{{ Str::limit($proveedor->direccion ?? 'N/A', 30) }}</td>
                    <td>
                        <span class="status-badge status-{{ $proveedor->activo ? 'active' : 'inactive' }}">
                            {{ $proveedor->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <x-fluent-button 
                                variant="ghost" 
                                size="small"
                                href="{{ route('proveedores.show', $proveedor) }}"
                                icon="eye"
                            />
                            <x-fluent-button 
                                variant="ghost" 
                                size="small"
                                href="{{ route('proveedores.edit', $proveedor) }}"
                                icon="edit"
                            />
                            <x-fluent-button 
                                variant="ghost" 
                                size="small"
                                href="{{ route('compras.index', ['proveedor' => $proveedor->id]) }}"
                                icon="shopping-basket"
                            />
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">
                        <div class="empty-state">
                            <i class="fas fa-truck" style="font-size: 3rem; color: var(--fluent-gray-60); margin-bottom: 1rem;"></i>
                            <h3>No hay proveedores</h3>
                            <p>Comienza agregando tu primer proveedor</p>
                            <x-fluent-button 
                                variant="primary" 
                                size="medium"
                                href="{{ route('proveedores.create') }}"
                                icon="plus"
                                iconPosition="left"
                            >
                                Agregar Proveedor
                            </x-fluent-button>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </x-fluent-table>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/views.js') }}"></script>
@endsection