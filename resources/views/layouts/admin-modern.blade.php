<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Panel de Administración - Gym Control">
    <meta name="author" content="Gym Control">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Panel de Administración') - Gym Control</title>

    <!-- Modern Fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" type="text/css">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Modern Theme CSS -->
    <link href="{{ asset('css/modern-theme.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sidebar-modern.css') }}" rel="stylesheet">
    <link href="{{ asset('css/header-modern.css') }}" rel="stylesheet">
    
    @stack('styles')
</head>

<body class="modern-layout">
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- Modern Sidebar -->
    <nav class="sidebar-modern" id="sidebar">
        <!-- Logo Section -->
        <div class="sidebar-logo">
            <div style="width: 40px; height: 40px; background: var(--primary-color); border-radius: var(--border-radius-md); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.2rem;">
                GC
            </div>
            <span class="sidebar-logo-text">Gym Control</span>
        </div>

        <!-- Navigation -->
        <div class="sidebar-nav">
            <!-- Dashboard Section -->
            <div class="sidebar-section">
                <div class="sidebar-section-title">Principal</div>
                <div class="sidebar-item">
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <div class="sidebar-icon">
                            <i class="fas fa-tachometer-alt"></i>
                        </div>
                        <span class="sidebar-text">Dashboard</span>
                        <div class="sidebar-tooltip">Dashboard</div>
                    </a>
                </div>
            </div>

            <!-- Gestión Section -->
            <div class="sidebar-section">
                <div class="sidebar-section-title">Gestión</div>
                
                <!-- Socios -->
                <div class="sidebar-item">
                    <a href="{{ route('admin.socios.index') }}" class="sidebar-link {{ request()->routeIs('admin.socios.*') ? 'active' : '' }}">
                        <div class="sidebar-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <span class="sidebar-text">Socios</span>
                        @php
                            try {
                                $memberCount = \App\Models\Member::count();
                            } catch (Exception $e) {
                                $memberCount = 0;
                            }
                        @endphp
                        @if($memberCount > 0)
                            <span class="sidebar-badge">{{ $memberCount }}</span>
                        @endif
                        <div class="sidebar-tooltip">Gestión de Socios</div>
                    </a>
                </div>

                <!-- Planes de Membresía -->
                <div class="sidebar-item">
                    <a href="{{ route('admin.membership-plans.index') }}" class="sidebar-link {{ request()->routeIs('admin.membership-plans.*') ? 'active' : '' }}">
                        <div class="sidebar-icon">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <span class="sidebar-text">Planes</span>
                        <div class="sidebar-tooltip">Planes de Membresía</div>
                    </a>
                </div>

                <!-- Clases -->
                <div class="sidebar-item">
                    <a href="{{ route('admin.classes.index') }}" class="sidebar-link {{ request()->routeIs('admin.classes.*') ? 'active' : '' }}">
                        <div class="sidebar-icon">
                            <i class="fas fa-dumbbell"></i>
                        </div>
                        <span class="sidebar-text">Clases</span>
                        <div class="sidebar-tooltip">Gestión de Clases</div>
                    </a>
                </div>

                <!-- Horarios -->
                <div class="sidebar-item">
                    <a href="{{ route('admin.schedules.index') }}" class="sidebar-link {{ request()->routeIs('admin.schedules.*') ? 'active' : '' }}">
                        <div class="sidebar-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <span class="sidebar-text">Horarios</span>
                        <div class="sidebar-tooltip">Horarios de Clases</div>
                    </a>
                </div>
            </div>

            <!-- Administración Section -->
            <div class="sidebar-section">
                <div class="sidebar-section-title">Administración</div>
                
                <!-- Administradores -->
                <div class="sidebar-item">
                    <a href="{{ route('admin.admins.index') }}" class="sidebar-link {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}">
                        <div class="sidebar-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <span class="sidebar-text">Administradores</span>
                        <div class="sidebar-tooltip">Gestión de Administradores</div>
                    </a>
                </div>
            </div>

            <!-- Configuración Section -->
            <div class="sidebar-section">
                <div class="sidebar-section-title">Sistema</div>
                
                <!-- Configuración General -->
                <div class="sidebar-item">
                    <a href="{{ route('admin.settings.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                        <div class="sidebar-icon">
                            <i class="fas fa-cog"></i>
                        </div>
                        <span class="sidebar-text">Configuración</span>
                        <div class="sidebar-tooltip">Configuración General</div>
                    </a>
                </div>

                <!-- Configuración Visual -->
                <div class="sidebar-item">
                    <a href="{{ route('admin.visual.config.index') }}" class="sidebar-link {{ request()->routeIs('admin.visual.config.*') ? 'active' : '' }}">
                        <div class="sidebar-icon">
                            <i class="fas fa-palette"></i>
                        </div>
                        <span class="sidebar-text">Diseño</span>
                        <div class="sidebar-tooltip">Configuración Visual</div>
                    </a>
                </div>

                <!-- Configuración de Email -->
                <div class="sidebar-item">
                    <a href="{{ route('admin.mail.config.index') }}" class="sidebar-link {{ request()->routeIs('admin.mail.config.*') ? 'active' : '' }}">
                        <div class="sidebar-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <span class="sidebar-text">Email</span>
                        <div class="sidebar-tooltip">Configuración de Email</div>
                    </a>
                </div>
            </div>
        </div>

        <!-- User Section -->
        <div class="sidebar-user">
            <div class="sidebar-user-info">
                <div class="sidebar-user-avatar">
                    {{ auth('admin')->check() ? strtoupper(substr(auth('admin')->user()->firstname ?? 'A', 0, 1)) : 'A' }}
                </div>
                <div class="sidebar-user-details">
                    <div class="sidebar-user-name">
                        {{ auth('admin')->check() ? (auth('admin')->user()->firstname ?? 'Admin') : 'Admin' }} 
                        {{ auth('admin')->check() ? (auth('admin')->user()->lastname ?? '') : '' }}
                    </div>
                    <div class="sidebar-user-role">Administrador</div>
                </div>
            </div>
        </div>

        <!-- Collapse Toggle -->
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-chevron-left"></i>
        </button>
    </nav>

    <!-- Modern Header -->
    <header class="header-modern" id="header">
        <div class="header-left">
            <button class="header-mobile-toggle" id="mobileSidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h1 class="header-title">@yield('page-title', 'Dashboard')</h1>
            @if(isset($breadcrumbs))
            <nav class="header-breadcrumb">
                @foreach($breadcrumbs as $index => $breadcrumb)
                    @if($index > 0)
                        <span class="header-breadcrumb-separator">/</span>
                    @endif
                    @if($loop->last)
                        <span class="header-breadcrumb-current">{{ $breadcrumb['title'] }}</span>
                    @else
                        <a href="{{ $breadcrumb['url'] }}" class="header-breadcrumb-link">{{ $breadcrumb['title'] }}</a>
                    @endif
                @endforeach
            </nav>
            @endif
        </div>

        <div class="header-right">
            <!-- Search Widget -->
            <div class="header-search">
                <i class="fas fa-search header-search-icon"></i>
                <input type="text" class="header-search-input" placeholder="Buscar socios, clases...">
            </div>

            <!-- Date Widget -->
            <div class="header-date">
                <div class="header-date-day">{{ date('d') }}</div>
                <div class="header-date-full">{{ date('M Y') }}</div>
            </div>

            <!-- Notifications Widget -->
            <div class="header-notifications">
                <button class="header-notification-btn">
                    <i class="fas fa-bell"></i>
                    <span class="header-notification-badge">3</span>
                </button>
            </div>

            <!-- User Widget -->
            <div class="header-user" id="headerUser">
                <div class="header-user-avatar">
                    {{ auth('admin')->check() ? strtoupper(substr(auth('admin')->user()->firstname ?? 'A', 0, 1)) : 'A' }}
                </div>
                <div class="header-user-info">
                    <div class="header-user-name">{{ auth('admin')->check() ? (auth('admin')->user()->firstname ?? 'Admin') : 'Admin' }}</div>
                    <div class="header-user-role">Administrador</div>
                </div>
                <i class="fas fa-chevron-down header-user-dropdown"></i>

                <!-- Dropdown Menu -->
                <div class="header-user-dropdown-menu">
                    <a href="#" class="header-dropdown-item">
                        <i class="fas fa-user header-dropdown-icon"></i>
                        Mi Perfil
                    </a>
                    <a href="{{ route('admin.settings.index') }}" class="header-dropdown-item">
                        <i class="fas fa-cog header-dropdown-icon"></i>
                        Configuración
                    </a>
                    <a href="{{ route('logout') }}" class="header-dropdown-item" 
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt header-dropdown-icon"></i>
                        Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="main-content" id="mainContent">
        <div class="content-wrapper">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show animate-fade-in-up" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show animate-fade-in-up" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show animate-fade-in-up" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/modern-admin.js') }}"></script>
    
    @stack('scripts')

    <style>
        /* Layout adjustments */
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--header-height);
            transition: margin-left var(--transition-speed) ease;
            min-height: calc(100vh - var(--header-height));
        }
        
        .sidebar-modern.collapsed ~ * .main-content {
            margin-left: var(--sidebar-collapsed-width);
        }
        
        .content-wrapper {
            padding: var(--spacing-xl);
            background: var(--bg-secondary);
            min-height: 100%;
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</body>
</html>