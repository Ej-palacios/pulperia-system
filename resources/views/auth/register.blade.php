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
                    <x-fluent-alert type="error">{{ session('error') }}</x-fluent-alert>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <x-fluent-input 
                        label="Nombre" 
                        name="name" 
                        type="text" 
                        required 
                        autofocus
                        icon="user"
                        :error="$errors->first('name')"
                        value="{{ old('name') }}"
                    />

                    <x-fluent-input 
                        label="Apellido" 
                        name="apellido" 
                        type="text" 
                        required 
                        icon="user"
                        :error="$errors->first('apellido')"
                        value="{{ old('apellido') }}"
                    />

                    <x-fluent-input 
                        label="Nombre de Usuario" 
                        name="username" 
                        type="text" 
                        required 
                        placeholder="elije_un_nombre_unico"
                        icon="user-shield"
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

                    <x-fluent-input 
                        label="Confirmar Contraseña" 
                        name="password_confirmation" 
                        type="password" 
                        required 
                        icon="lock"
                    />

                    <x-fluent-select
                        label="Rol"
                        name="role"
                        required
                        :error="$errors->first('role')"
                    >
                        <option value="">Selecciona un rol</option>
                        <option value="usuario" {{ old('role') == 'usuario' ? 'selected' : '' }}>Usuario</option>
                        @if(!$adminExists)
                            <option value="administrador" {{ old('role') == 'administrador' ? 'selected' : '' }}>Administrador</option>
                            <option value="dueño" {{ old('role') == 'dueño' ? 'selected' : '' }}>Dueño</option>
                        @endif
                    </x-fluent-select>

                    <div class="d-grid">
                        <x-fluent-button type="submit" variant="success" size="large">
                            Registrar Usuario
                        </x-fluent-button>
                    </div>
                </form>

                <div class="text-center mt-3">
                    <p class="mb-0">¿Ya tienes una cuenta?</p>
                    <x-fluent-button href="{{ route('login') }}" variant="link">
                        Iniciar Sesión
                    </x-fluent-button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
