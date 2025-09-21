@extends('layouts.app', ['title' => 'Gestión de Clientes'])

@section('breadcrumb')
    <li class="breadcrumb-item active">Clientes</li>
@endsection

@section('page-title')
    Gestión de Clientes
@endsection

@section('header-buttons')
    <x-button color="success" :href="route('clientes.create')" icon="fas fa-plus">
        Nuevo Cliente
    </x-button>
@endsection

@section('content')
<!-- Search and Filters Section -->
<x-filters-section :action="route('clientes.index')">
    <div class="col-md-4">
        <label for="search" class="form-label">Buscar por ID o Nombre</label>
        <input type="text" 
               class="form-control" 
               id="search" 
               name="search" 
               value="{{ request('search') }}" 
               placeholder="ID o nombre del cliente">
    </div>
    <div class="col-md-3">
        <label for="estado" class="form-label">Estado</label>
        <select class="form-select" id="estado" name="estado">
            <option value="">Todos</option>
            <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activos</option>
            <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivos</option>
        </select>
    </div>
    <div class="col-md-3">
        <label for="saldo" class="form-label">Saldo</label>
        <select class="form-select" id="saldo" name="saldo">
            <option value="">Todos</option>
            <option value="con_deuda" {{ request('saldo') == 'con_deuda' ? 'selected' : '' }}>Con deuda</option>
            <option value="sin_deuda" {{ request('saldo') == 'sin_deuda' ? 'selected' : '' }}>Sin deuda</option>
        </select>
    </div>
    <div class="col-md-2 d-flex align-items-end">
        <button type="submit" class="btn btn-primary me-2">
            <i class="fas fa-search"></i> Buscar
        </button>
        <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-times"></i> Limpiar
        </a>
    </div>
</x-filters-section>

<!-- Results Table -->
<div class="table-responsive">
    <table class="table table-hover">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Cédula</th>
                <th>Teléfono</th>
                <th>Saldo Pendiente</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($clientes as $cliente)
            <tr>
                <td>{{ $cliente->id }}</td>
                <td>{{ $cliente->nombre }}</td>
                <td>{{ $cliente->cedula ?? 'N/A' }}</td>
                <td>{{ $cliente->telefono }}</td>
                <td>
                    <span class="badge bg-{{ $cliente->saldo_calculado > 0 ? 'warning' : 'success' }}">
                        C$ {{ number_format($cliente->saldo_calculado, 2) }}
                    </span>
                </td>
                <td>
                    <span class="badge bg-{{ $cliente->activo ? 'success' : 'danger' }}">
                        {{ $cliente->activo ? 'Activo' : 'Inactivo' }}
                    </span>
                </td>
                <td>
                    <div class="btn-group" role="group">
                        <a href="{{ route('clientes.show', $cliente) }}" 
                           class="btn btn-sm btn-outline-info" 
                           title="Ver detalles">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('clientes.edit', $cliente) }}" 
                           class="btn btn-sm btn-outline-primary" 
                           title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        @if($cliente->saldo_calculado > 0)
                        <a href="{{ route('clientes.creditos', $cliente) }}" 
                           class="btn btn-sm btn-outline-warning" 
                           title="Ver créditos">
                            <i class="fas fa-credit-card"></i>
                        </a>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center text-muted py-4">
                    <i class="fas fa-users fa-2x mb-2"></i>
                    <br>
                    @if(request()->hasAny(['search', 'estado', 'saldo']))
                        No se encontraron clientes con los filtros aplicados
                    @else
                        No hay clientes registrados
                    @endif
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($clientes->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $clientes->appends(request()->query())->links() }}
</div>
@endif
@endsection