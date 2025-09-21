@extends('layouts.app', ['title' => 'Registrar Usuario'])

@section('content')
<div class="row justify-content-center">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h4 class="text-center">Registrar Usuario</h4>
            </div>
            <div class="card-body">

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <x-input 
                        label="Nombre" 
                        name="name" 
                        type="text" 
                        required 
                        autofocus
                    />

                    <x-input 
                        label="Apellido" 
                        name="apellido" 
                        type="text" 
                        required 
                    />

                    <x-input 
                        label="Nombre de Usuario (Username)" 
                        name="username" 
                        type="text" 
                        required 
                        placeholder="elijer_un_nombre_unico"
                    />

                    <x-input 
                        label="Contraseña" 
                        name="password" 
                        type="password" 
                        required 
                    />

                    <x-input 
                        label="Confirmar Contraseña" 
                        name="password_confirmation" 
                        type="password" 
                        required 
                    />

                    <div class="mb-3">
                        <label for="role" class="form-label">Rol</label>
                        <select id="role" name="role" class="form-select" required>
                            <option value="">Selecciona un rol</option>
                            <option value="usuario" {{ old('role') == 'usuario' ? 'selected' : '' }}>Usuario</option>
                            @if(!$adminExists)
                                <option value="administrador" {{ old('role') == 'administrador' ? 'selected' : '' }}>Administrador</option>
                                <option value="dueño" {{ old('role') == 'dueño' ? 'selected' : '' }}>Dueño</option>
                            @endif
                        </select>

                        @if($adminExists)
                            <div class="form-text text-muted">Ya existe un administrador. Solo puedes registrarte como Usuario.</div>
                        @else
                            <div class="form-text text-warning">Solo puede haber un Administrador en el sistema.</div>
                        @endif
                    </div>

                    <div class="d-grid">
                        <x-button type="submit" color="success">
                            <i class="fas fa-user-plus me-2"></i>Registrar Usuario
                        </x-button>
                    </div>
                </form>

                <div class="text-center mt-3">
                    <p class="mb-0">¿Ya tienes una cuenta?</p>
                    <a href="{{ route('login') }}" class="btn btn-link">
                        <i class="fas fa-sign-in-alt me-1"></i>Iniciar Sesión
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection