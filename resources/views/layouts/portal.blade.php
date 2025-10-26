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

    <!-- Custom fonts for this template-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Portal de Socios CSS -->
    <style>
        :root {
            --primary: #28a745;
            --secondary: #6c757d;
            --success: #20c997;
            --info: #17a2b8;
            --warning: #ffc107;
            --danger: #dc3545;
            --light: #f8f9fa;
            --dark: #343a40;
            --gym-blue: #007bff;
            --gym-green: #28a745;
        }

        body {
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
            background-color: #f8f9fa;
        }

        .navbar-brand {
            font-weight: 800;
            color: var(--gym-green) !important;
        }

        .navbar {
            background: linear-gradient(135deg, var(--gym-green) 0%, var(--gym-blue) 100%) !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            color: #fff !important;
            transform: translateY(-1px);
        }

        .navbar-nav .nav-link.active {
            color: #fff !important;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background: linear-gradient(135deg, var(--gym-green) 0%, var(--gym-blue) 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--gym-green) 0%, var(--gym-blue) 100%);
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .btn-outline-primary {
            border-color: var(--gym-green);
            color: var(--gym-green);
            border-radius: 25px;
            padding: 10px 25px;
            font-weight: 600;
        }

        .btn-outline-primary:hover {
            background-color: var(--gym-green);
            border-color: var(--gym-green);
        }

        .dashboard-card {
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
            border-left: 4px solid var(--gym-green);
        }

        .stats-card {
            text-align: center;
            padding: 2rem;
        }

        .stats-card .icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--gym-green);
        }

        .stats-card .number {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .stats-card .label {
            color: var(--secondary);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.875rem;
        }

        .badge-status-activo {
            background-color: var(--success);
        }

        .badge-status-vencido {
            background-color: var(--danger);
        }

        .badge-status-proximo_vencimiento {
            background-color: var(--warning);
        }

        .badge-status-sin_plan {
            background-color: var(--secondary);
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--gym-green);
        }

        .welcome-section {
            background: linear-gradient(135deg, var(--gym-green) 0%, var(--gym-blue) 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
            border-radius: 15px;
        }

        .footer {
            background-color: var(--dark);
            color: white;
            padding: 2rem 0;
            margin-top: 3rem;
        }

        .nav-tabs .nav-link {
            border-radius: 15px 15px 0 0;
            border: none;
            background-color: transparent;
            color: var(--secondary);
        }

        .nav-tabs .nav-link.active {
            background-color: var(--gym-green);
            color: white;
        }

        .alert {
            border-radius: 15px;
            border: none;
        }

        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--gym-green);
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }

        @media (max-width: 768px) {
            .navbar-nav {
                text-align: center;
            }
            
            .welcome-section {
                padding: 2rem 0;
            }
            
            .stats-card {
                padding: 1.5rem;
                margin-bottom: 1rem;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('portal.dashboard') }}">
                <i class="fas fa-dumbbell me-2"></i>
                <strong>Gym Control</strong>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('portal.dashboard') ? 'active' : '' }}" 
                           href="{{ route('portal.dashboard') }}">
                            <i class="fas fa-home me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('portal.membresia') ? 'active' : '' }}" 
                           href="{{ route('portal.membresia') }}">
                            <i class="fas fa-id-card me-1"></i>Mi Membresía
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('portal.clases') ? 'active' : '' }}" 
                           href="{{ route('portal.clases') }}">
                            <i class="fas fa-calendar-alt me-1"></i>Clases
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('portal.rutinas') ? 'active' : '' }}" 
                           href="{{ route('portal.rutinas') }}">
                            <i class="fas fa-list-alt me-1"></i>Rutinas
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" 
                           data-bs-toggle="dropdown">
                            @if(auth()->user()->photo)
                                <img src="{{ asset('storage/' . auth()->user()->photo) }}" 
                                     alt="Perfil" class="rounded-circle me-1" 
                                     style="width: 30px; height: 30px; object-fit: cover;">
                            @else
                                <i class="fas fa-user-circle me-1"></i>
                            @endif
                            {{ auth()->user()->firstname }}
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('portal.perfil') }}">
                                    <i class="fas fa-user me-2"></i>Mi Perfil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('portal.configuracion') }}">
                                    <i class="fas fa-cog me-2"></i>Configuración
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('login') }}" 
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container my-4">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Por favor corrige los siguientes errores:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-dumbbell me-2"></i>Gym Control</h5>
                    <p class="mb-0">Tu gimnasio, tu salud, tu bienestar.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">
                        &copy; {{ date('Y') }} Gym Control. Todos los derechos reservados.
                    </p>
                    <small class="text-muted">
                        Portal de Socios v1.0
                    </small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Scripts -->
    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            let alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(function(alert) {
                let bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>