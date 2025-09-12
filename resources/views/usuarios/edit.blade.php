@extends('layouts.app', ['title' => 'Editar Usuario'])

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('usuarios.index') }}">Usuarios</a></li>
    <li class="breadcrumb-item active">Editar Usuario</li>
@endsection

@section('page-title')
    Editar Usuario: {{ $user->name }}
@endsection

@section('content')
<form action="{{ route('usuarios.update', $user) }}" method="POST">
    @csrf @method('PUT')
    
    <div class="row">
        <div class="col-md-6">
            <x-input label="Nombre" name="name" :value="$user->name" required />
        </div>
        <div class="col-md-6">
            <x-input label="Email" name="email" type="email" :value="$user->email" required />
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <x-input label="Contraseña" name="password" type="password" 
                help="Dejar en blanco para mantener la contraseña actual" />
        </div>
        <div class="col-md-6">
            <x-input label="Confirmar Contraseña" name="password_confirmation" type="password" />
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label">Rol</label>
                <select name="role" class="form-select" required>
                    @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label">Estado</label>
                <select name="activo" class="form-select">
                    <option value="1" {{ $user->activo ? 'selected' : '' }}>Activo</option>
                    <option value="0" {{ !$user->activo ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2">
        <x-button type="button" color="secondary" :href="route('usuarios.index')">Cancelar</x-button>
        <x-button type="submit" color="primary">Actualizar Usuario</x-button>
    </div>
</form>
@endsection