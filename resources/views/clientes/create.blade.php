@extends('layouts.app', ['title' => 'Crear Cliente'])

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('clientes.index') }}">Clientes</a></li>
    <li class="breadcrumb-item active">Crear Cliente</li>
@endsection

@section('page-title')
    Crear Nuevo Cliente
@endsection

@section('content')
<form action="{{ route('clientes.store') }}" method="POST">
    @csrf
    
    <div class="row">
        <div class="col-md-6">
            <x-input label="Nombre" name="nombre" required />
        </div>
        <div class="col-md-6">
            <x-input label="Cédula" name="cedula" />
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <x-input label="Teléfono" name="telefono" required />
        </div>
        <div class="col-md-6">
            <x-input label="Email" name="email" type="email" />
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Dirección</label>
        <textarea name="direccion" class="form-control" rows="3">{{ old('direccion') }}</textarea>
    </div>

    <div class="d-flex justify-content-end gap-2">
        <x-button type="button" color="secondary" :href="route('clientes.index')">Cancelar</x-button>
        <x-button type="submit" color="primary">Crear Cliente</x-button>
    </div>
</form>
@endsection