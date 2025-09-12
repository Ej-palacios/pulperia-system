@extends('layouts.app', ['title' => 'Gestión de Usuarios'])

@section('breadcrumb')
    <li class="breadcrumb-item active">Usuarios</li>
@endsection

@section('page-title')
    Gestión de Usuarios
@endsection

@section('header-buttons')
    <x-button color="success" :href="route('usuarios.create')" icon="fas fa-plus">
        Nuevo Usuario
    </x-button>
@endsection

@section('content')
<x-table :headers="['Nombre', 'Email', 'Rol', 'Estado', 'Acciones']">
    @foreach($users as $user)
    <tr>
        <td>{{ $user->name }}</td>
        <td>{{ $user->email }}</td>
        <td>
            <span class="badge bg-primary">{{ $user->getRoleNames()->first() }}</span>
        </td>
        <td>
            <span class="badge bg-{{ $user->activo ? 'success' : 'danger' }}">
                {{ $user->activo ? 'Activo' : 'Inactivo' }}
            </span>
        </td>
        <td>
            <x-button color="outline-primary" size="sm" :href="route('usuarios.edit', $user)" icon="fas fa-edit">
                Editar
            </x-button>
            
            @if($user->id !== auth()->id())
            <x-button color="outline-danger" size="sm" icon="fas fa-trash"
                data-bs-toggle="modal" data-bs-target="#deleteModal{{ $user->id }}">
                Eliminar
            </x-button>

            <x-modal id="deleteModal{{ $user->id }}" title="Confirmar Eliminación">
                <p>¿Está seguro de eliminar al usuario {{ $user->name }}?</p>
                <form action="{{ route('usuarios.destroy', $user) }}" method="POST">
                    @csrf @method('DELETE')
                    <div class="d-flex justify-content-end gap-2">
                        <x-button type="button" color="secondary" data-bs-dismiss="modal">Cancelar</x-button>
                        <x-button type="submit" color="danger">Eliminar</x-button>
                    </div>
                </form>
            </x-modal>
            @endif
        </td>
    </tr>
    @endforeach
</x-table>
@endsection