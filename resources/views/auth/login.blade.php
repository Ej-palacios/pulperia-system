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
                    <x-fluent-alert type="error">{{ session('error') }}</x-fluent-alert>
                @endif

                @if(session('warning'))
                    <x-fluent-alert type="warning">{{ session('warning') }}</x-fluent-alert>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <x-fluent-input 
                        label="Nombre de Usuario" 
                        name="username" 
                        type="text" 
                        required 
                        autofocus
                        icon="user"
                        :error="$errors->first('username')"
                        value="{{ old('username') }}"
                    />

                    <x-fluent-input 
                        label="Contraseña" 
                        name="password" 
                        type="password" 
                        required 
                        icon="lock"
                        :error="$errors->first('password')"
                    />

                    <div class="d-grid">
                        <x-fluent-button type="submit" variant="primary" size="large">
                            Iniciar Sesión
                        </x-fluent-button>
                    </div>
                </form>

                <div class="text-center mt-3">
                    <p class="mb-0">¿No tienes cuenta?</p>
                    <x-fluent-button href="{{ route('register') }}" variant="link">
                        Registrar Usuario
                    </x-fluent-button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
