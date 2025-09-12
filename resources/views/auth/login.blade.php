@extends('layouts.app', ['title' => 'Iniciar Sesión'])

@section('content')
<div class="row justify-content-center">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h4 class="text-center">Iniciar Sesión</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <x-input 
                        label="Correo Electrónico" 
                        name="email" 
                        type="email" 
                        required 
                    />

                    <x-input 
                        label="Contraseña" 
                        name="password" 
                        type="password" 
                        required 
                    />

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Recordarme</label>
                    </div>

                    <div class="d-grid">
                        <x-button type="submit" color="primary">
                            <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection