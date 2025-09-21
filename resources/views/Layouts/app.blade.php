<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Pulpería'))</title>
    
    <!-- Custom Fluent CSS -->
    <link href="{{ asset('css/fluent-app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/fluent-toasts.css') }}" rel="stylesheet">
    <link href="{{ asset('css/fluent-header.css') }}" rel="stylesheet">
    
    <!-- Font Awesome (local fallback) -->
    <link rel="stylesheet" href="{{ asset('css/all.min.css') }}">
    
    <!-- Local fonts -->
    <style>
        @font-face {
            font-family: 'Segoe UI';
            src: local('Segoe UI');
            font-weight: 400;
        }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
        }
    </style>
    
    @yield('styles')
</head>
<body class="fluent-body">
    <!-- Sidebar -->
    <div class="sidebar-container" id="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <i class="fas fa-store"></i>
                <span class="logo-text">{{ config('app.name', 'Pulpería') }}</span>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        
        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">Principal</div>
                <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
                @can('ventas.pos')
                <a href="{{ route('ventas.pos') }}" class="nav-item {{ request()->routeIs('ventas.pos') ? 'active' : '' }}">
                    <i class="fas fa-cash-register"></i>
                    <span class="nav-text">Punto de Venta</span>
                </a>
                @endcan
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Gestión</div>
                @can('clientes.view')
                <a href="{{ route('clientes.index') }}" class="nav-item {{ request()->routeIs('clientes.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span class="nav-text">Clientes</span>
                </a>
                @endcan
                
                @can('proveedores.view')
                <a href="{{ route('proveedores.index') }}" class="nav-item {{ request()->routeIs('proveedores.*') ? 'active' : '' }}">
                    <i class="fas fa-truck"></i>
                    <span class="nav-text">Proveedores</span>
                </a>
                @endcan
                
                @can('productos.view')
                <a href="{{ route('productos.index') }}" class="nav-item {{ request()->routeIs('productos.*') ? 'active' : '' }}">
                    <i class="fas fa-boxes"></i>
                    <span class="nav-text">Productos</span>
                </a>
                @endcan
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Operaciones</div>
                @can('ventas.view')
                <a href="{{ route('ventas.index') }}" class="nav-item {{ request()->routeIs('ventas.index') ? 'active' : '' }}">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="nav-text">Ventas</span>
                </a>
                @endcan
                
                @can('compras.view')
                <a href="{{ route('compras.index') }}" class="nav-item {{ request()->routeIs('compras.*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-basket"></i>
                    <span class="nav-text">Compras</span>
                </a>
                @endcan
                
                @can('inventario.view')
                <a href="{{ route('productos.index') }}" class="nav-item {{ request()->routeIs('productos.*') ? 'active' : '' }}">
                    <i class="fas fa-warehouse"></i>
                    <span class="nav-text">Inventario</span>
                </a>
                @endcan
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Reportes</div>
                @can('reportes.view')
                <a href="{{ route('reportes.ventas') }}" class="nav-item {{ request()->routeIs('reportes.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i>
                    <span class="nav-text">Reportes</span>
                </a>
                @endcan
                
                @can('finanzas.view')
                <a href="{{ route('reportes.financieros') }}" class="nav-item {{ request()->routeIs('reportes.financieros') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span class="nav-text">Finanzas</span>
                </a>
                @endcan
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Sistema</div>
                @can('usuarios.view')
                <a href="{{ route('usuarios.index') }}" class="nav-item {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                    <i class="fas fa-user-cog"></i>
                    <span class="nav-text">Usuarios</span>
                </a>
                @endcan
                
                @can('configuracion.view')
                <a href="{{ route('configuracion.edit') }}" class="nav-item {{ request()->routeIs('configuracion.*') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i>
                    <span class="nav-text">Configuración</span>
                </a>
                @endcan
            </div>
        </nav>
        
        <div class="sidebar-footer">
            <div class="user-info">
                @auth
                <div class="user-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="user-details">
                    <div class="user-name">{{ Auth::user()->name }}</div>
                    <div class="user-role">{{ Auth::user()->getRoleNames()->first() ?? 'Usuario' }}</div>
                </div>
                @endauth
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Header Component -->
        @include('components.header')

        <!-- Page Content -->
        <main class="page-content">
            <!-- Page Header -->
            <div class="page-header">
                <div class="page-title-section">
                    <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
                    <p class="page-subtitle">@yield('page-subtitle', 'Bienvenido al sistema de gestión')</p>
                </div>
                <div class="page-actions">
                    @yield('header-buttons')
                </div>
            </div>

            <!-- Alerts -->
            @if(session('success'))
                <x-fluent-alert type="success" :message="session('success')" />
            @endif

            @if(session('error'))
                <x-fluent-alert type="error" :message="session('error')" />
            @endif

            @if(session('warning'))
                <x-fluent-alert type="warning" :message="session('warning')" />
            @endif

            @if(session('info'))
                <x-fluent-alert type="info" :message="session('info')" />
            @endif

            @if($errors->any())
                <x-fluent-alert type="error">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-fluent-alert>
            @endif

            <!-- Main Content Area -->
            <div class="content-area">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner">
            <div class="spinner"></div>
            <div class="loading-text">Cargando...</div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@fluentui/web-components@2.0.0/dist/web-components.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/fluent-app.js') }}"></script>
    <script src="{{ asset('js/fluent-header.js') }}"></script>
    
    @yield('scripts')
</body>
</html>