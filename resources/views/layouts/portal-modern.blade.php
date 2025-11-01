<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Portal de Socios - Gym Control">
    <meta name="author" content="Gym Control">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Portal de Socios') - Gym Control</title>

    <!-- Modern Fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" type="text/css">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Modern Theme CSS -->
    <link href="{{ asset('css/modern-theme.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sidebar-modern.css') }}" rel="stylesheet">
    <link href="{{ asset('css/header-modern.css') }}" rel="stylesheet">
    
    <!-- Portal-specific styles -->
    <style>
        /* Portal specific color scheme */
        :root {
            --primary-color: #4CAF50;
            --secondary-color: #00BCD4;
            --accent-color: #20c997;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 70px;
            --header-height: 70px;
            --transition-speed: 0.3s;
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.1);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.15);
            --shadow-lg: 0 8px 25px rgba(0,0,0,0.2);
            --border-radius-sm: 6px;
            --border-radius-md: 12px;
            --border-radius-lg: 18px;
            --spacing-sm: 0.5rem;
            --spacing-md: 1rem;
            --spacing-lg: 1.5rem;
            --spacing-xl: 2rem;
            --bg-dark: #2c3e50;
            --bg-dark-light: #34495e;
            --text-white: #ffffff;
            --bg-secondary: #F8F9FA;
            --bg-tertiary: #E9ECEF;
            --text-primary: #343A40;
            --text-secondary: #6C757D;
        }

        /* Layout adjustments */
        .main-content {
            margin-left: var(--sidebar-width);
            padding-top: var(--header-height);
            min-height: 100vh;
            transition: margin-left var(--transition-speed) ease;
        }

        .sidebar-modern.collapsed + .main-content {
            margin-left: var(--sidebar-collapsed-width);
        }

        /* Welcome section styling */
        .welcome-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 2rem 0;
            margin: -1.5rem -1.5rem 2rem -1.5rem;
            border-radius: 0 0 20px 20px;
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid rgba(255, 255, 255, 0.3);
        }

        .dashboard-card {
            border: none;
            border-radius: 15px;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
        }

        .stats-card {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            padding: 1.5rem;
            text-align: center;
            color: white;
            border-radius: 15px;
            height: 100%;
        }

        .stats-card .icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            opacity: 0.9;
        }

        .stats-card .number {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stats-card .label {
            font-size: 0.9rem;
            opacity: 0.9;
            font-weight: 500;
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
            
            .sidebar-modern {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar-modern.show-mobile {
                transform: translateX(0);
            }
            
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
            }
            
            .sidebar-overlay.show {
                opacity: 1;
                visibility: visible;
            }
        }

        /* Fix dropdown and notification styles */
        .dropdown-menu {
            border: none;
            border-radius: var(--border-radius-md);
            box-shadow: var(--shadow-lg);
            padding: 0.5rem 0;
            margin-top: 0.5rem;
            min-width: 200px;
        }

        .dropdown-item {
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            color: var(--text-primary);
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background: var(--bg-secondary);
            color: var(--primary-color);
        }

        .dropdown-item i {
            width: 16px;
            text-align: center;
            margin-right: 0.5rem;
        }

        .dropdown-divider {
            margin: 0.5rem 0;
            border-color: var(--bg-tertiary);
        }

        /* Notification styles */
        .notification-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            background: var(--danger-color);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            border: 2px solid white;
        }

        .notification-dropdown {
            min-width: 320px;
            max-width: 380px;
        }

        .notification-item {
            padding: 1rem;
            border-bottom: 1px solid var(--bg-tertiary);
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .notification-item:hover {
            background: var(--bg-secondary);
        }

        .notification-icon {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .notification-content {
            flex: 1;
        }

        .notification-title {
            font-weight: 500;
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }

        .notification-time {
            font-size: 0.75rem;
            color: var(--text-secondary);
        }

        /* User dropdown styles */
        .user-dropdown {
            min-width: 250px;
        }

        .user-dropdown .dropdown-header {
            padding: 1.5rem;
            background: linear-gradient(135deg, rgba(76, 175, 80, 0.1) 0%, rgba(0, 188, 212, 0.1) 100%);
            border-bottom: 1px solid var(--bg-tertiary);
            text-align: center;
        }

        .user-dropdown .user-info .user-name {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .user-dropdown .user-info .user-email {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }
    </style>
    
    @stack('styles')
</head>

<body class="modern-layout">
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- Modern Sidebar -->
    <nav class="sidebar-modern" id="sidebar">
        <!-- Logo Section -->
        <div class="sidebar-logo">
            <div style="width: 40px; height: 40px; background: rgba(255, 255, 255, 0.2); border-radius: var(--border-radius-md); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.2rem;">
                GC
            </div>
            <span class="sidebar-logo-text">Portal Socios</span>
        </div>

        <!-- User Info -->
        <div class="sidebar-user">
            @if(auth()->user()->photo)
                <img src="{{ asset('storage/' . auth()->user()->photo) }}" 
                     alt="Foto de {{ auth()->user()->firstname }}" 
                     class="sidebar-user-avatar">
            @else
                <div class="sidebar-user-avatar">
                    <i class="fas fa-user"></i>
                </div>
            @endif
            <div class="sidebar-user-info">
                <div class="sidebar-user-name">{{ auth()->user()->firstname }}</div>
                <div class="sidebar-user-role">Socio #{{ auth()->user()->member_id }}</div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="sidebar-nav">
            <!-- Principal Section -->
            <div class="sidebar-section">
                <div class="sidebar-section-title">Principal</div>
                <div class="sidebar-item">
                    <a href="{{ route('portal.dashboard') }}" class="sidebar-link {{ request()->routeIs('portal.dashboard') ? 'active' : '' }}">
                        <div class="sidebar-icon">
                            <i class="fas fa-tachometer-alt"></i>
                        </div>
                        <span class="sidebar-text">Dashboard</span>
                        <div class="sidebar-tooltip">Dashboard</div>
                    </a>
                </div>

                <div class="sidebar-item">
                    <a href="{{ route('portal.membresia') }}" class="sidebar-link {{ request()->routeIs('portal.membresia') ? 'active' : '' }}">
                        <div class="sidebar-icon">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <span class="sidebar-text">Mi Membresía</span>
                        <div class="sidebar-tooltip">Información de Membresía</div>
                    </a>
                </div>
            </div>

            <!-- Actividades Section -->
            <div class="sidebar-section">
                <div class="sidebar-section-title">Actividades</div>
                
                <div class="sidebar-item">
                    <a href="{{ route('portal.clases') }}" class="sidebar-link {{ request()->routeIs('portal.clases*') ? 'active' : '' }}">
                        <div class="sidebar-icon">
                            <i class="fas fa-dumbbell"></i>
                        </div>
                        <span class="sidebar-text">Clases</span>
                        <div class="sidebar-tooltip">Clases Disponibles</div>
                    </a>
                </div>

                <div class="sidebar-item">
                    <a href="{{ route('portal.classes.bookings') }}" class="sidebar-link {{ request()->routeIs('portal.classes.bookings') ? 'active' : '' }}">
                        <div class="sidebar-icon">
                            <i class="fas fa-bookmark"></i>
                        </div>
                        <span class="sidebar-text">Mis Reservas</span>
                        <div class="sidebar-tooltip">Mis Reservas de Clases</div>
                    </a>
                </div>

                <div class="sidebar-item">
                    <a href="{{ route('portal.rutinas') }}" class="sidebar-link {{ request()->routeIs('portal.rutinas') ? 'active' : '' }}">
                        <div class="sidebar-icon">
                            <i class="fas fa-list-alt"></i>
                        </div>
                        <span class="sidebar-text">Rutinas</span>
                        <div class="sidebar-tooltip">Mis Rutinas</div>
                    </a>
                </div>
            </div>

            <!-- Cuenta Section -->
            <div class="sidebar-section">
                <div class="sidebar-section-title">Mi Cuenta</div>
                
                <div class="sidebar-item">
                    <a href="{{ route('portal.perfil') }}" class="sidebar-link {{ request()->routeIs('portal.perfil') ? 'active' : '' }}">
                        <div class="sidebar-icon">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <span class="sidebar-text">Perfil</span>
                        <div class="sidebar-tooltip">Editar Perfil</div>
                    </a>
                </div>

                <div class="sidebar-item">
                    <a href="{{ route('portal.configuracion') }}" class="sidebar-link {{ request()->routeIs('portal.configuracion') ? 'active' : '' }}">
                        <div class="sidebar-icon">
                            <i class="fas fa-cog"></i>
                        </div>
                        <span class="sidebar-text">Configuración</span>
                        <div class="sidebar-tooltip">Configuración de Cuenta</div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Logout -->
        <div class="sidebar-footer">
            <a href="#" onclick="document.getElementById('logout-form').submit(); return false;" class="sidebar-link">
                <div class="sidebar-icon">
                    <i class="fas fa-sign-out-alt"></i>
                </div>
                <span class="sidebar-text">Cerrar Sesión</span>
                <div class="sidebar-tooltip">Cerrar Sesión</div>
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Modern Header -->
        <header class="header-modern">
            <div class="header-left">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="breadcrumb-section">
                    <h4 class="page-title mb-0">@yield('title', 'Dashboard')</h4>
                    @isset($breadcrumbs)
                        <nav class="breadcrumb-nav">
                            @foreach($breadcrumbs as $breadcrumb)
                                @if($loop->last)
                                    <span class="breadcrumb-current">{{ $breadcrumb['name'] }}</span>
                                @else
                                    <a href="{{ $breadcrumb['url'] }}" class="breadcrumb-link">{{ $breadcrumb['name'] }}</a>
                                    <i class="fas fa-chevron-right breadcrumb-separator"></i>
                                @endif
                            @endforeach
                        </nav>
                    @endisset
                </div>
            </div>
            
            <div class="header-right">
                <!-- Notifications -->
                <div class="header-item dropdown">
                    <button class="header-button" data-bs-toggle="dropdown">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">2</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end notification-dropdown">
                        <div class="dropdown-header">
                            <h6>Notificaciones</h6>
                            <span class="badge bg-primary">2 nuevas</span>
                        </div>
                        <div class="notification-item">
                            <div class="notification-icon">
                                <i class="fas fa-exclamation-triangle text-warning"></i>
                            </div>
                            <div class="notification-content">
                                <div class="notification-title">Membresía por vencer</div>
                                <div class="notification-time">Hace 2 horas</div>
                            </div>
                        </div>
                        <div class="notification-item">
                            <div class="notification-icon">
                                <i class="fas fa-calendar text-info"></i>
                            </div>
                            <div class="notification-content">
                                <div class="notification-title">Nueva clase disponible</div>
                                <div class="notification-time">Hace 1 día</div>
                            </div>
                        </div>
                        <div class="dropdown-footer">
                            <a href="#" class="btn btn-sm btn-primary w-100">Ver todas</a>
                        </div>
                    </div>
                </div>

                <!-- User Menu -->
                <div class="header-item dropdown">
                    <button class="header-user" data-bs-toggle="dropdown">
                        @if(auth()->user()->photo)
                            <img src="{{ asset('storage/' . auth()->user()->photo) }}" 
                                 alt="Avatar" class="user-avatar">
                        @else
                            <div class="user-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                        @endif
                        <span class="user-name">{{ auth()->user()->firstname }}</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end user-dropdown">
                        <div class="dropdown-header">
                            <div class="user-info">
                                <div class="user-name">{{ auth()->user()->full_name }}</div>
                                <div class="user-email">Socio #{{ auth()->user()->member_id }}</div>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('portal.perfil') }}" class="dropdown-item">
                            <i class="fas fa-user me-2"></i>Mi Perfil
                        </a>
                        <a href="{{ route('portal.configuracion') }}" class="dropdown-item">
                            <i class="fas fa-cog me-2"></i>Configuración
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" onclick="document.getElementById('logout-form').submit(); return false;" class="dropdown-item text-danger">
                            <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="content-area">
            @yield('content')
        </main>
    </div>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Modern Theme JS -->
    <script src="{{ asset('js/modern-theme.js') }}"></script>
    
    @stack('scripts')
</body>
</html>