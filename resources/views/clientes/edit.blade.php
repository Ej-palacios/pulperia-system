@extends('layouts.app', ['title' => 'Editar Cliente'])

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('clientes.index') }}">Clientes</a></li>
    <li class="breadcrumb-item active">Editar Cliente</li>
@endsection

@section('page-title')
    Editar Cliente: {{ $cliente->nombre }}
@endsection

@section('content')
<form action="{{ route('clientes.update', $cliente) }}" method="POST">
    @csrf @method('PUT')
    
    <div class="row">
        <div class="col-md-6">
            <x-input label="Nombre" name="nombre" :value="$cliente->nombre" required />
        </div>
        <div class="col-md-6">
            <x-input label="Cédula" name="cedula" :value="$cliente->cedula" />
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <x-input label="Teléfono" name="telefono" :value="$cliente->telefono" required />
        </div>
        <div class="col-md-6">
            <x-input label="Email" name="email" type="email" :value="$cliente->email" />
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Dirección</label>
        <textarea name="direccion" class="form-control" rows="3">{{ old('direccion', $cliente->direccion) }}</textarea>
    </div>

    <div class="d-flex justify-content-end gap-2">
        <x-button type="button" color="secondary" :href="route('clientes.index')">Cancelar</x-button>
        <x-button type="submit" color="primary">Actualizar Cliente</x-button>
    </div>
</form>
@endsection