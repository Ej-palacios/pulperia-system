<header class="fluent-header">
    <div class="header-container">
        <div class="header-left">
            <button class="sidebar-toggle-mobile" id="sidebarToggleMobile">
                <i class="fas fa-bars"></i>
            </button>
            <div class="breadcrumb-container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">
                                <i class="fas fa-home"></i>
                            </a>
                        </li>
                        {{ $breadcrumb ?? '' }}
                    </ol>
                </nav>
            </div>
        </div>
        
        <div class="header-right">
            <div class="header-actions">
                <!-- Notifications -->
                <div class="notification-dropdown">
                    <button class="notification-btn" id="notificationDropdownBtn" aria-label="Notificaciones">
                        <i class="fas fa-bell"></i>
                        @if(isset($notificationCount) && $notificationCount > 0)
                            <span class="notification-badge">{{ $notificationCount }}</span>
                        @endif
                    </button>
                    <div class="notification-menu" id="notificationMenu">
                        <div class="notification-header">
                            <h6>Notificaciones</h6>
                            <button class="btn-link" id="markAllReadBtn">Marcar todas como leídas</button>
                        </div>
                        <div class="notification-list">
                            @if(isset($notifications) && count($notifications) > 0)
                                @foreach($notifications as $notification)
                                <div class="notification-item {{ $notification->read ? 'read' : '' }}">
                                    <div class="notification-icon">
                                        @switch($notification->type)
                                            @case('warning')
                                                <i class="fas fa-exclamation-triangle text-warning"></i>
                                                @break
                                            @case('success')
                                                <i class="fas fa-check-circle text-success"></i>
                                                @break
                                            @case('error')
                                                <i class="fas fa-times-circle text-danger"></i>
                                                @break
                                            @default
                                                <i class="fas fa-info-circle text-info"></i>
                                        @endswitch
                                    </div>
                                    <div class="notification-content">
                                        <div class="notification-title">{{ $notification->title }}</div>
                                        <div class="notification-text">{{ $notification->message }}</div>
                                        <div class="notification-time">{{ $notification->created_at->diffForHumans() }}</div>
                                    </div>
                                    <button class="notification-action" aria-label="Marcar como leída">
                                        <i class="fas fa-circle"></i>
                                    </button>
                                </div>
                                @endforeach
                            @else
                                <div class="notification-empty">
                                    <i class="fas fa-bell-slash"></i>
                                    <p>No tienes notificaciones</p>
                                </div>
                            @endif
                        </div>
                        @if(isset($notifications) && count($notifications) > 0)
                            <div class="notification-footer">
                                <a href="#" class="view-all-link">Ver todas las notificaciones</a>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- User Menu -->
                <div class="user-dropdown">
                    <button class="user-btn" id="userDropdownBtn" aria-label="Menú de usuario">
                        <div class="user-avatar-small">
                            @if(Auth::check() && Auth::user()->profile_photo_path)
                                <img src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}" alt="{{ Auth::user()->name }}">
                            @else
                                <i class="fas fa-user"></i>
                            @endif
                        </div>
                        <span class="user-name-small">{{ Auth::check() ? Auth::user()->name : 'Usuario' }}</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="user-menu" id="userMenu">
                        <div class="user-menu-header">
                            <div class="user-avatar-large">
                                @if(Auth::check() && Auth::user()->profile_photo_path)
                                    <img src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}" alt="{{ Auth::user()->name }}">
                                @else
                                    <i class="fas fa-user-circle"></i>
                                @endif
                            </div>
                            <div class="user-info">
                                <div class="user-name">{{ Auth::check() ? Auth::user()->name : 'Usuario' }}</div>
                                <div class="user-email">{{ Auth::check() ? (Auth::user()->email ?? Auth::user()->username) : 'usuario' }}</div>
                                <div class="user-role">{{ Auth::check() ? (Auth::user()->getRoleNames()->first() ?? 'Usuario') : 'Usuario' }}</div>
                            </div>
                        </div>
                        <div class="user-menu-divider"></div>
                        <a href="{{ route('profile.show') }}" class="user-menu-item">
                            <i class="fas fa-user"></i>
                            <span>Mi Perfil</span>
                        </a>
                        @can('configuracion.view')
                        <a href="{{ route('configuracion.edit') }}" class="user-menu-item">
                            <i class="fas fa-cog"></i>
                            <span>Configuración</span>
                        </a>
                        @endcan
                        <div class="user-menu-divider"></div>
                        <form method="POST" action="{{ route('logout') }}" class="user-menu-form">
                            @csrf
                            <button type="submit" class="user-menu-item logout-btn">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Cerrar Sesión</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>