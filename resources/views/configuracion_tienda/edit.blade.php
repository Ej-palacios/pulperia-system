@extends('layouts.app', ['title' => 'Configuración de Tienda'])

@section('breadcrumb')
    <li class="breadcrumb-item active">Configuración</li>
@endsection

@section('page-title')
    Configuración de Tienda
@endsection

@section('content')
<form action="{{ route('configuracion.update') }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')
    
    <div class="row">
        <div class="col-md-6">
            <x-input label="Nombre de la Tienda" name="nombre" :value="$configuracion->nombre" required />
        </div>
        <div class="col-md-6">
            <x-input label="Impuesto (%)" name="impuesto" type="number" step="0.01" 
                :value="$configuracion->impuesto" required />
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <x-input label="Moneda" name="moneda" :value="$configuracion->moneda" required />
        </div>
        <div class="col-md-6">
            <x-input label="Teléfono" name="telefono" :value="$configuracion->telefono" />
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Dirección</label>
        <textarea name="direccion" class="form-control" rows="3">{{ old('direccion', $configuracion->direccion) }}</textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Mensaje en Ticket</label>
        <textarea name="mensaje_ticket" class="form-control" rows="2">{{ old('mensaje_ticket', $configuracion->mensaje_ticket) }}</textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Logo de la Tienda</label>
        <input type="file" name="logo" class="form-control" accept="image/*">
        @if($configuracion->logo)
        <div class="mt-2">
            <img src="{{ asset('storage/' . $configuracion->logo) }}" alt="Logo" style="max-height: 100px;">
        </div>
        @endif
    </div>

    <div class="d-flex justify-content-end">
        <x-button type="submit" color="primary">Guardar Configuración</x-button>
    </div>
</form>
@endsection