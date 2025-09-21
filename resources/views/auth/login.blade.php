@extends('layouts.app', ['title' => 'Iniciar Sesión'])

@section('content')
<div class="row justify-content-center">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h4 class="text-center">Iniciar Sesión</h4>
            </div>
            <div class="card-body">

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning">{{ session('warning') }}</div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <x-input 
                        label="Nombre de Usuario (Username)" 
                        name="username" 
                        type="text" 
                        required 
                        autofocus
                    />

                    <!-- El rol se determina automáticamente por el sistema -->

                    <x-input 
                        label="Contraseña" 
                        name="password" 
                        type="password" 
                        required 
                    />

                    <div class="d-grid">
                        <x-button type="submit" color="primary">
                            <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                        </x-button>
                    </div>
                </form>

                <div class="text-center mt-3">
                    <p class="mb-0">¿No tienes cuenta?</p>
                    <a href="{{ route('register') }}" class="btn btn-link">
                        <i class="fas fa-user-plus me-1"></i>Registrar Usuario
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection