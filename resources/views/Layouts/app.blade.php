<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name', 'Pulpería'))</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @yield('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="fas fa-store me-2"></i>{{ config('app.name', 'Pulpería') }}
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <i class="fas fa-home me-1"></i> Dashboard
                        </a>
                    </li>
                    @can('ventas')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('ventas.pos') }}">
                            <i class="fas fa-cash-register me-1"></i> POS
                        </a>
                    </li>
                    @endcan
                </ul>
                
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 bg-light sidebar py-3">
                <div class="list-group">
                    @can('clientes')
                    <a href="{{ route('clientes.index') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-users me-2"></i>Clientes
                    </a>
                    @endcan
                    
                    @can('proveedores')
                    <a href="{{ route('proveedores.index') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-truck me-2"></i>Proveedores
                    </a>
                    @endcan
                    
                    @can('productos')
                    <a href="{{ route('productos.index') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-boxes me-2"></i>Productos
                    </a>
                    @endcan
                    
                    @can('ventas')
                    <a href="{{ route('ventas.index') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-shopping-cart me-2"></i>Ventas
                    </a>
                    @endcan
                    
                    @can('compras')
                    <a href="{{ route('compras.index') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-shopping-basket me-2"></i>Compras
                    </a>
                    @endcan
                    
                    @can('reportes')
                    <a href="{{ route('reportes.ventas') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-chart-bar me-2"></i>Reportes
                    </a>
                    @endcan
                    
                    @can('configuracion')
                    <a href="{{ route('configuracion.edit') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-cog me-2"></i>Configuración
                    </a>
                    @endcan
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 py-3">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        @yield('breadcrumb')
                    </ol>
                </nav>

                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>@yield('page-title')</h2>
                    <div>
                        @yield('header-buttons')
                    </div>
                </div>

                <!-- Alerts -->
                @if(session('success'))
                    <x-alert type="success" :message="session('success')" />
                @endif

                @if(session('error'))
                    <x-alert type="danger" :message="session('error')" />
                @endif

                @if($errors->any())
                    <x-alert type="danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-alert>
                @endif

                <!-- Content -->
                <div class="card">
                    <div class="card-body">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @yield('scripts')
</body>
</html>