@extends('layouts.app', ['title' => 'Créditos'])

@section('page-title')
    Gestión de Créditos
@endsection

@section('page-subtitle')
    Administra los créditos pendientes de cobro
@endsection

@section('content')
<div class="credits-container">
    <!-- Filtros y búsqueda -->
    <div class="filters-section">
        <div class="search-container">
            <x-fluent-input 
                type="search" 
                placeholder="Buscar créditos..."
                icon="search"
                id="searchInput"
            />
        </div>
        
        <div class="filter-controls">
            <select class="fluent-select" id="statusFilter">
                <option value="">Todos los estados</option>
                <option value="pendiente">Pendiente</option>
                <option value="parcialmente_pagado">Parcialmente Pagado</option>
                <option value="pagado">Pagado</option>
                <option value="vencido">Vencido</option>
            </select>
            
            <select class="fluent-select" id="clientFilter">
                <option value="">Todos los clientes</option>
                @foreach(($clientes ?? collect()) as $cliente)
                    <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Resumen de créditos -->
    <div class="credits-summary">
        <div class="summary-card">
            <div class="summary-icon">
                <i class="fas fa-credit-card"></i>
            </div>
            <div class="summary-content">
                <div class="summary-value" id="totalCredits">{{ $resumen['total_creditos'] ?? 0 }}</div>
                <div class="summary-label">Total Créditos</div>
            </div>
        </div>
        
        <div class="summary-card">
            <div class="summary-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="summary-content">
                <div class="summary-value" id="totalAmount">{{ \App\Helpers\PulperiaHelper::formatCurrency($resumen['monto_total'] ?? 0) }}</div>
                <div class="summary-label">Monto Total</div>
            </div>
        </div>
        
        <div class="summary-card">
            <div class="summary-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="summary-content">
                <div class="summary-value" id="overdueCredits">{{ $resumen['creditos_vencidos'] ?? 0 }}</div>
                <div class="summary-label">Vencidos</div>
            </div>
        </div>
        
        <div class="summary-card">
            <div class="summary-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="summary-content">
                <div class="summary-value" id="pendingCredits">{{ $resumen['creditos_pendientes'] ?? 0 }}</div>
                <div class="summary-label">Pendientes</div>
            </div>
        </div>
    </div>

    <!-- Tabla de créditos -->
    <div class="table-container">
        <x-fluent-table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Fecha Límite</th>
                    <th>Monto Original</th>
                    <th>Saldo Pendiente</th>
                    <th>Estado</th>
                    <th>Días Vencido</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="creditsTableBody">
                @forelse(($creditos ?? collect()) as $credito)
                <tr>
                    <td>#{{ $credito->id }}</td>
                    <td>
                        <div class="client-info">
                            <div class="client-name">{{ $credito->cliente->nombre ?? 'N/A' }}</div>
                            <div class="client-phone">{{ $credito->cliente->telefono ?? '' }}</div>
                        </div>
                    </td>
                    <td>{{ \App\Helpers\PulperiaHelper::formatDate($credito->fecha_limite) }}</td>
                    <td>{{ \App\Helpers\PulperiaHelper::formatCurrency($credito->monto_original) }}</td>
                    <td>
                        <strong>{{ \App\Helpers\PulperiaHelper::formatCurrency($credito->saldo_pendiente) }}</strong>
                    </td>
                    <td>
                        <span class="status-badge status-{{ $credito->estado }}">
                            {{ ucfirst(str_replace('_', ' ', $credito->estado)) }}
                        </span>
                    </td>
                    <td>
                        @php
                            $diasVencido = now()->diffInDays($credito->fecha_limite, false);
                        @endphp
                        @if($diasVencido > 0)
                            <span class="overdue-days">{{ $diasVencido }} días</span>
                        @else
                            <span class="days-remaining">{{ abs($diasVencido) }} días</span>
                        @endif
                    </td>
                    <td>
                        <div class="action-buttons">
                            <x-fluent-button 
                                variant="ghost" 
                                size="small"
                                href="{{ route('creditos.show', $credito) }}"
                                icon="eye"
                            />
                            <x-fluent-button 
                                variant="ghost" 
                                size="small"
                                href="{{ route('abonos.create', $credito) }}"
                                icon="plus"
                            />
                            <x-fluent-button 
                                variant="ghost" 
                                size="small"
                                href="{{ route('creditos.edit', $credito) }}"
                                icon="edit"
                            />
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">
                        <div class="empty-state">
                            <i class="fas fa-credit-card" style="font-size: 3rem; color: var(--fluent-gray-60); margin-bottom: 1rem;"></i>
                            <h3>No hay créditos</h3>
                            <p>Los créditos aparecerán aquí cuando se realicen ventas a crédito</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </x-fluent-table>
    </div>
</div>

@section('styles')
<link rel="stylesheet" href="{{ asset('css/views.css') }}">
@endsection

@section('scripts')
<script src="{{ asset('js/views.js') }}"></script>
@endsection
