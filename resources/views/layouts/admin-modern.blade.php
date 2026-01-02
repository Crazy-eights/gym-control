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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" type="text/css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">

    <!-- Bootstrap 5 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">

    <!-- Modern Theme CSS -->
    <link href="{{ asset('css/modern-theme.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sidebar-modern.css') }}" rel="stylesheet">
    <link href="{{ asset('css/header-modern.css') }}" rel="stylesheet">

    <!-- Aplicar estado del sidebar ANTES de que el body sea visible -->
    <script>
        // Este script se ejecuta INMEDIATAMENTE antes de que el DOM esté listo
        (function() {
            const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (sidebarCollapsed) {
                // Agregar clase al HTML para aplicarlo inmediatamente
                document.documentElement.classList.add('sidebar-collapsed-initial');
            }
        })();
    </script>

    <style>
        /* Estilos críticos para aplicar ANTES de que cargue JavaScript */
        html.sidebar-collapsed-initial .sidebar-modern {
            width: 70px;
        }
        html.sidebar-collapsed-initial .main-content {
            margin-left: 70px;
        }
        html.sidebar-collapsed-initial .header-modern {
            left: 70px;
        }
        /* Solo ocultar el TEXTO, NO el logo/icono */
        html.sidebar-collapsed-initial .sidebar-logo-text {
            opacity: 0;
            width: 0;
            pointer-events: none;
        }
        html.sidebar-collapsed-initial .sidebar-text,
        html.sidebar-collapsed-initial .sidebar-badge,
        html.sidebar-collapsed-initial .sidebar-section-title {
            opacity: 0;
            width: 0;
            pointer-events: none;
        }
        html.sidebar-collapsed-initial .sidebar-link {
            justify-content: center;
            padding: 1rem;
        }
        html.sidebar-collapsed-initial .sidebar-logo {
            padding: 2rem 0.5rem;
            justify-content: center;
        }
        /* Asegurar que el logo/icono siempre sea visible */
        html.sidebar-collapsed-initial .sidebar-logo > div:first-child,
        html.sidebar-collapsed-initial .sidebar-icon {
            opacity: 1 !important;
            display: flex !important;
        }
    </style>

    @stack('styles')
</head>

<body class="modern-layout">
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Modern Sidebar -->
    <nav class="sidebar-modern" id="sidebar">
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

                <!-- Posiciones -->
                <div class="sidebar-item">
                    <a href="{{ route('admin.positions.index') }}" class="sidebar-link {{ request()->routeIs('admin.positions.*') ? 'active' : '' }}">
                        <div class="sidebar-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <span class="sidebar-text">Posiciones</span>
                        <div class="sidebar-tooltip">Gestión de Posiciones</div>
                    </a>
                </div>

                <!-- Horarios de Personal -->
                <div class="sidebar-item">
                    <a href="{{ route('admin.schedules.index') }}" class="sidebar-link {{ request()->routeIs('admin.schedules.*') ? 'active' : '' }}">
                        <div class="sidebar-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <span class="sidebar-text">Horarios Personal</span>
                        <div class="sidebar-tooltip">Horarios de Trabajo del Personal</div>
                    </a>
                </div>

                <!-- Instructores -->
                <div class="sidebar-item">
                    <a href="{{ route('admin.instructors.index') }}" class="sidebar-link {{ request()->routeIs('admin.instructors.*') ? 'active' : '' }}">
                        <div class="sidebar-icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <span class="sidebar-text">Instructores</span>
                        <div class="sidebar-tooltip">Gestión de Instructores</div>
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
                <div class="sidebar-item" style="opacity: 0.5;">
                    <a href="#" onclick="alert('Configuración de email temporalmente deshabilitada')" class="sidebar-link">
                        <div class="sidebar-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <span class="sidebar-text">Email</span>
                        <div class="sidebar-tooltip">Configuración de Email (Deshabilitado)</div>
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
        <!-- Logo Section -->
        <div class="header-logo">
            <div class="header-logo-icon">GC</div>
            <span class="header-logo-text">Gym Control</span>
        </div>

        <!-- Mobile Toggle -->
        <button class="header-mobile-toggle" id="mobileSidebarToggle">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Search Widget -->
        <div class="header-search">
            <i class="fas fa-search header-search-icon"></i>
            <input type="text" class="header-search-input" placeholder="Buscar socios, clases...">
        </div>

        <!-- Right Section -->
        <div class="header-right">
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
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

        /* El header ya no se ajusta - siempre es full-width */
        .header-modern {
            left: 0 !important;
            width: 100% !important;
        }

        /* Cuando el sidebar está colapsado */
        body.sidebar-collapsed .main-content {
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
