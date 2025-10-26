<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - Gym Control</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --sidebar-bg: #2c3e50;
            --sidebar-text: #ecf0f1;
        }
        
        body {
            background-color: #f8f9fa;
        }
        
        .sidebar {
            background: linear-gradient(180deg, var(--sidebar-bg) 0%, #34495e 100%);
            min-height: 100vh;
            color: var(--sidebar-text);
        }
        
        .sidebar .nav-link {
            color: var(--sidebar-text);
            border-radius: 0.375rem;
            margin: 0.125rem 0;
        }
        
        .sidebar .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
            color: white;
        }
        
        .sidebar .nav-link.active {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
        }
        
        .navbar-brand {
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .content-header {
            background: white;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 0.375rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .box {
            background: white;
            border-radius: 0.375rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
        }
        
        .box-header {
            padding: 1rem;
            border-bottom: 1px solid #dee2e6;
        }
        
        .box-body {
            padding: 1rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h4 class="text-white">
                            <i class="fas fa-dumbbell"></i> Gym Control
                        </h4>
                        <small class="text-muted">Panel Admin</small>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                               href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}" 
                               href="{{ route('admin.admins.index') }}">
                                <i class="fas fa-user-shield"></i> Administradores
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-users"></i> Empleados
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-user-friends"></i> Miembros
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-calendar-check"></i> Asistencias
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-credit-card"></i> Membresías
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" 
                               href="{{ route('admin.settings.index') }}">
                                <i class="fas fa-cogs"></i> Configuración
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.mail.config.*') ? 'active' : '' }}" 
                               href="{{ route('admin.mail.config.index') }}">
                                <i class="fas fa-envelope"></i> Config. Email
                            </a>
                        </li>
                    </ul>
                    
                    <hr class="my-3">
                    
                    <div class="text-center">
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-light btn-sm">
                                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                            </button>
                        </form>
                    </div>
                </div>
            </nav>
            
            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <!-- Top navbar -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <nav class="navbar navbar-expand-lg navbar-light">
                        <div class="container-fluid">
                            <span class="navbar-brand mb-0 h1">
                                <i class="fas fa-dumbbell"></i> Gym Control
                            </span>
                            
                            <div class="navbar-nav ms-auto">
                                <div class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-user-circle"></i> 
                                        {{ Auth::guard('admin')->user()->firstname ?? 'Admin' }}
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-user"></i> Perfil</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form method="POST" action="{{ route('admin.logout') }}">
                                                @csrf
                                                <button type="submit" class="dropdown-item">
                                                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </nav>
                </div>
                
                @yield('content')
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>