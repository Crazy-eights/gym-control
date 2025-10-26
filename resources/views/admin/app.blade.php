<!DOCTYPE html>
<html>
<head>
  	<meta charset="utf-8">
  	<meta http-equiv="X-UA-Compatible" content="IE=edge">
  	<title>Admin | @yield('title', 'Dashboard')</title>
  	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  	<link rel="stylesheet" href="{{ asset('bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
  	<link rel="stylesheet" href="{{ asset('bower_components/font-awesome/css/font-awesome.min.css') }}">
  	<link rel="stylesheet" href="{{ asset('dist/css/AdminLTE.min.css') }}">
  	<link rel="stylesheet" href="{{ asset('dist/css/skins/skin-blue.min.css') }}">
  	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

    @stack('styles')
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

    <header class="main-header">
        <a href="#" class="logo">
            <span class="logo-mini"><b>G</b>C</span>
            <span class="logo-lg"><b>Gym</b>Control</span>
        </a>
        <nav class="navbar navbar-static-top">
            <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <span class="hidden-xs">{{ auth('admin')->user()->firstname }} {{ auth('admin')->user()->lastname }}</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="user-footer">
                                <div class="pull-right">
                                    <form method="POST" action="{{ route('admin.logout') }}">
                                        @csrf
                                        <button type="submit" class="btn btn-default btn-flat">Cerrar Sesión</button>
                                    </form>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <aside class="main-sidebar">
        <section class="sidebar">
            <ul class="sidebar-menu" data-widget="tree">
                <li class="header">NAVEGACIÓN PRINCIPAL</li>
                
                <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
                
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-users"></i>
                        <span>Empleados</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="{{ route('admin.employees.index') }}"><i class="fa fa-circle-o"></i> Lista de Empleados</a></li>
                        <li><a href="#"><i class="fa fa-circle-o"></i> Asistencia</a></li>
                    </ul>
                </li>
                </ul>
        </section>
    </aside>

    <div class="content-wrapper">
        
        @yield('content')

    </div>

    <footer class="main-footer">
        <div class="pull-right hidden-xs">
            <b>Versión</b> 1.0.0 (Laravel)
        </div>
        <strong>Copyright &copy; 2025 <a href="#">Tu Compañía</a>.</strong> Todos los derechos reservados.
    </footer>

</div>
<script src="{{ asset('bower_components/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
@stack('scripts')
</body>
</html>