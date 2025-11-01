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

    <!-- Custom fonts for this template-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- SB Admin 2 CSS (Adaptado para Bootstrap 5) -->
    <style>
        :root {
            --primary: #4e73df;
            --secondary: #858796;
            --success: #1cc88a;
            --info: #36b9cc;
            --warning: #f6c23e;
            --danger: #e74a3b;
            --light: #f8f9fc;
            --dark: #5a5c69;
        }

        body {
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
            background-color: #f8f9fc;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            min-height: 100vh;
            background: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
            background-size: cover;
            z-index: 1000;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1rem 0.75rem 1.5rem !important;
        }

        .sidebar .nav-link i {
            margin-right: 0.75rem;
        }

        .sidebar .sidebar-brand {
            padding: 1rem 1.5rem !important;
        }

        /* Estilos para las gráficas del dashboard */
        .chart-area {
            position: relative;
            height: 10rem;
            width: 100%;
        }

        .chart-pie {
            position: relative;
            height: 15rem;
            width: 100%;
        }

        @media (min-width: 768px) {
            .chart-area {
                height: 20rem;
            }
        }

        .border-left-primary {
            border-left: 0.25rem solid var(--primary) !important;
        }

        .border-left-success {
            border-left: 0.25rem solid var(--success) !important;
        }

        .border-left-info {
            border-left: 0.25rem solid var(--info) !important;
        }

        .border-left-warning {
            border-left: 0.25rem solid var(--warning) !important;
        }

        .border-left-danger {
            border-left: 0.25rem solid var(--danger) !important;
            padding: 1rem;
            transition: all 0.3s;
        }

        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.2);
        }

        .sidebar .nav-link i {
            margin-right: 0.75rem;
            width: 1rem;
        }

        .sidebar-brand {
            padding: 1.5rem 1rem;
            text-decoration: none;
            color: #fff;
            display: block;
        }

        .sidebar-brand:hover {
            color: #fff;
            text-decoration: none;
        }

        .sidebar-brand-icon {
            font-size: 2rem;
        }

        .sidebar-brand-text {
            font-size: 1.2rem;
            font-weight: 700;
        }

        .topbar {
            background-color: #fff;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .border-left-primary {
            border-left: 0.25rem solid var(--primary) !important;
        }

        .border-left-success {
            border-left: 0.25rem solid var(--success) !important;
        }

        .border-left-warning {
            border-left: 0.25rem solid var(--warning) !important;
        }

        .border-left-danger {
            border-left: 0.25rem solid var(--danger) !important;
        }

        .card {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            border: none;
        }

        .text-gray-300 {
            color: #dddfeb !important;
        }

        .text-gray-800 {
            color: #5a5c69 !important;
        }

        .text-xs {
            font-size: 0.7rem;
        }

        .font-weight-bold {
            font-weight: 700 !important;
        }

        .text-uppercase {
            text-transform: uppercase !important;
        }

        .page-link {
            color: var(--primary);
        }

        .page-link:hover {
            color: #224abe;
        }

        .page-item.active .page-link {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background-color: #224abe;
            border-color: #224abe;
        }

        .alert {
            border-radius: 0.35rem;
        }

        .table th {
            border-top: none;
            font-weight: 700;
            color: var(--dark);
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: -100%;
                z-index: 1000;
                transition: left 0.3s;
                width: 250px;
            }
            
            .sidebar.show {
                left: 0;
            }
            
            .content-wrapper {
                margin-left: 0 !important;
            }
        }

        .content-wrapper {
            margin-left: 250px;
            min-height: 100vh;
        }

        #content-wrapper {
            margin-left: 0;
        }
    </style>

    <!-- CSS Dinámico Personalizado -->
    @if(file_exists(public_path('storage/css/dynamic-theme.css')))
        <link href="{{ asset('storage/css/dynamic-theme.css') }}" rel="stylesheet" type="text/css">
    @endif

    @stack('styles')
</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar">
            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('admin.dashboard') }}">
                <div class="sidebar-brand-icon">
                    <i class="fas fa-dumbbell"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Gym Control</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading px-3 py-2">
                <small class="text-uppercase" style="color: rgba(255,255,255,0.6);">Gestión</small>
            </div>

            <!-- Nav Item - Socios -->
            <li class="nav-item {{ request()->routeIs('admin.socios.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.socios.index') }}">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Socios</span>
                </a>
            </li>

            <!-- Nav Item - Planes de Membresía -->
            <li class="nav-item {{ request()->routeIs('admin.membership-plans.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.membership-plans.index') }}">
                    <i class="fas fa-fw fa-calendar-check"></i>
                    <span>Planes de Membresía</span>
                </a>
            </li>

            <!-- Nav Item - Clases -->
            <li class="nav-item {{ request()->routeIs('admin.classes.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.classes.index') }}">
                    <i class="fas fa-fw fa-dumbbell"></i>
                    <span>Clases</span>
                </a>
            </li>

            <!-- Nav Item - Asistencias -->
            <li class="nav-item">
                <a class="nav-link" href="#" onclick="alert('Próximamente: Control de Asistencias')">
                    <i class="fas fa-fw fa-clock"></i>
                    <span>Asistencias</span>
                </a>
            </li>

            <!-- Nav Item - Pagos -->
            <li class="nav-item">
                <a class="nav-link" href="#" onclick="alert('Próximamente: Gestión de Pagos')">
                    <i class="fas fa-fw fa-dollar-sign"></i>
                    <span>Pagos</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading px-3 py-2">
                <small class="text-uppercase" style="color: rgba(255,255,255,0.6);">Configuración</small>
            </div>

            <!-- Nav Item - Administradores -->
            <li class="nav-item {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.admins.index') }}">
                    <i class="fas fa-fw fa-user-shield"></i>
                    <span>Administradores</span>
                </a>
            </li>

            <!-- Nav Item - Configuración de Email -->
            <li class="nav-item {{ request()->routeIs('admin.mail.config.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.mail.config.index') }}">
                    <i class="fas fa-fw fa-envelope"></i>
                    <span>Configuración Email</span>
                </a>
            </li>

            <!-- Nav Item - Configuración General -->
            <li class="nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.settings.index') }}">
                    <i class="fas fa-fw fa-cogs"></i>
                    <span>Configuración General</span>
                </a>
            </li>

            <!-- Nav Item - Configuración Visual -->
            <li class="nav-item {{ request()->routeIs('admin.visual.config.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.visual.config.index') }}">
                    <i class="fas fa-fw fa-palette"></i>
                    <span>Configuración Visual</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="btn btn-link rounded-circle text-white" id="sidebarToggle" title="Colapsar/Expandir Sidebar">
                    <i class="fas fa-angle-left"></i>
                </button>
            </div>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div class="content-wrapper d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light topbar mb-4 static-top shadow">
                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Nav Item - Alerts -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                               data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <!-- Counter - Alerts -->
                                <span class="badge badge-danger badge-counter">3+</span>
                            </a>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                               data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    {{ auth('admin')->user()->name ?? 'Administrador' }}
                                </span>
                                <i class="fas fa-user-circle fa-lg text-gray-400"></i>
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in">
                                <a class="dropdown-item" href="#" onclick="alert('Próximamente: Perfil de Usuario')">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Perfil
                                </a>
                                <a class="dropdown-item" href="{{ route('admin.settings.index') }}">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Configuración
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Cerrar Sesión
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <!-- Mensajes de Flash -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            {{ session('warning') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <strong>¡Se encontraron errores!</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @yield('content')
                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>&copy; {{ date('Y') }} Gym Control. Todos los derechos reservados.</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->
        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top" style="display: none; position: fixed; bottom: 20px; right: 20px; background: var(--primary); color: white; padding: 10px; border-radius: 50%;">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">¿Listo para salir?</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Selecciona "Cerrar Sesión" a continuación si estás listo para terminar tu sesión actual.
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancelar</button>
                    <form method="POST" action="{{ route('admin.logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-primary">Cerrar Sesión</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom scripts -->
    <script>
        // Sidebar toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarToggleTop = document.getElementById('sidebarToggleTop');
            const sidebar = document.getElementById('accordionSidebar');
            const contentWrapper = document.getElementById('content-wrapper');

            // Añadir clases CSS para el comportamiento del sidebar colapsado
            const style = document.createElement('style');
            style.textContent = `
                /* Estilos para sidebar colapsado */
                .sidebar-collapsed {
                    width: 80px !important;
                    transition: width 0.3s ease;
                }
                
                .sidebar-collapsed .nav-item .nav-link span {
                    display: none !important;
                }
                
                .sidebar-collapsed .nav-item .nav-link {
                    text-align: center !important;
                    padding: 0.75rem 0 !important;
                }
                
                .sidebar-collapsed .nav-item .nav-link i {
                    margin-right: 0 !important;
                    font-size: 1.2rem !important;
                }
                
                .sidebar-collapsed .sidebar-brand {
                    padding: 0 !important;
                    justify-content: center !important;
                }
                
                .sidebar-collapsed .sidebar-brand-text {
                    display: none !important;
                }
                
                .sidebar-collapsed .sidebar-divider {
                    margin: 0.5rem 0.5rem !important;
                }
                
                .sidebar-collapsed #sidebarToggle i {
                    transform: rotate(180deg);
                }
                
                /* Efecto hover para mostrar texto cuando está colapsado */
                .sidebar-collapsed .nav-item {
                    position: relative;
                }
                
                .sidebar-collapsed .nav-item:hover::after {
                    content: attr(data-title);
                    position: absolute;
                    left: 100%;
                    top: 50%;
                    transform: translateY(-50%);
                    background: #333;
                    color: white;
                    padding: 0.5rem 1rem;
                    border-radius: 0.375rem;
                    white-space: nowrap;
                    z-index: 1000;
                    margin-left: 0.5rem;
                    font-size: 0.875rem;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                }
                
                /* Transición suave para el contenido */
                .content-wrapper {
                    transition: margin-left 0.3s ease;
                }
                
                /* Estilos para mobile */
                @media (max-width: 768px) {
                    .sidebar-collapsed {
                        display: none !important;
                    }
                    
                    .content-wrapper {
                        margin-left: 0 !important;
                    }
                }
            `;
            document.head.appendChild(style);

            // Función para toggle del sidebar
            function toggleSidebar() {
                if (window.innerWidth > 768) {
                    sidebar.classList.toggle('sidebar-collapsed');
                    
                    if (sidebar.classList.contains('sidebar-collapsed')) {
                        contentWrapper.style.marginLeft = '80px';
                        // Añadir atributos data-title para tooltips
                        const navItems = sidebar.querySelectorAll('.nav-item');
                        navItems.forEach(item => {
                            const link = item.querySelector('.nav-link');
                            const span = link.querySelector('span');
                            if (span) {
                                item.setAttribute('data-title', span.textContent.trim());
                            }
                        });
                    } else {
                        contentWrapper.style.marginLeft = '250px';
                    }
                }
            }

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', toggleSidebar);
            }

            if (sidebarToggleTop) {
                sidebarToggleTop.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        sidebar.classList.toggle('show');
                    } else {
                        toggleSidebar();
                    }
                });
            }

            // Ajustar en resize de ventana
            window.addEventListener('resize', function() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.remove('sidebar-collapsed');
                    contentWrapper.style.marginLeft = '0';
                } else if (!sidebar.classList.contains('sidebar-collapsed')) {
                    contentWrapper.style.marginLeft = '250px';
                }
            });

            // Scroll to top functionality
            const scrollToTop = document.querySelector('.scroll-to-top');
            if (scrollToTop) {
                window.addEventListener('scroll', function() {
                    if (window.pageYOffset > 100) {
                        scrollToTop.style.display = 'block';
                    } else {
                        scrollToTop.style.display = 'none';
                    }
                });

                scrollToTop.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.scrollTo({top: 0, behavior: 'smooth'});
                });
            }

            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });
    </script>

    @stack('scripts')
</body>
</html>