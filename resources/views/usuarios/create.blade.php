@extends('layouts.app', ['title' => 'Crear Usuario'])

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('usuarios.index') }}">Usuarios</a></li>
    <li class="breadcrumb-item active">Crear Usuario</li>
@endsection

@section('page-title')
    Crear Nuevo Usuario
@endsection

@section('content')
<form action="{{ route('usuarios.store') }}" method="POST">
    @csrf
    
    <div class="row">
        <div class="col-md-6">
            <x-input label="Nombre" name="name" required />
        </div>
        <div class="col-md-6">
            <x-input label="Email" name="email" type="email" required />
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <x-input label="Contraseña" name="password" type="password" required />
        </div>
        <div class="col-md-6">
            <x-input label="Confirmar Contraseña" name="password_confirmation" type="password" required />
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label">Rol</label>
                <select name="role" class="form-select" required>
                    <option value="">Seleccionar Rol</option>
                    @foreach($roles as $role)
                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label">Estado</label>
                <select name="activo" class="form-select">
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                </select>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2">
        <x-button type="button" color="secondary" :href="route('usuarios.index')">Cancelar</x-button>
        <x-button type="submit" color="primary">Crear Usuario</x-button>
    </div>
</form>
@endsection