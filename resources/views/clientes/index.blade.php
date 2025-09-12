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
<x-table :headers="['Nombre', 'Cédula', 'Teléfono', 'Saldo Pendiente', 'Acciones']">
    @foreach($clientes as $cliente)
    <tr>
        <td>{{ $cliente->nombre }}</td>
        <td>{{ $cliente->cedula ?? 'N/A' }}</td>
        <td>{{ $cliente->telefono }}</td>
        <td>
            <span class="badge bg-{{ $cliente->saldo > 0 ? 'warning' : 'success' }}">
                C$ {{ number_format($cliente->saldo, 2) }}
            </span>
        </td>
        <td>
            <x-button color="outline-info" size="sm" :href="route('clientes.show', $cliente)" icon="fas fa-eye">
                Ver
            </x-button>
            <x-button color="outline-primary" size="sm" :href="route('clientes.edit', $cliente)" icon="fas fa-edit">
                Editar
            </x-button>
        </td>
    </tr>
    @endforeach
</x-table>
@endsection