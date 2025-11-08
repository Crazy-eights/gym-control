@extends('layouts.portal-modern')

@section('title', 'Dashboard')

@push('styles')
<style>
    /* Estilos para las tarjetas de estadísticas tipo admin */
    .border-left-primary { border-left: 0.25rem solid #4e73df !important; }
    .border-left-success { border-left: 0.25rem solid #1cc88a !important; }
    .border-left-info { border-left: 0.25rem solid #36b9cc !important; }
    .border-left-warning { border-left: 0.25rem solid #f6c23e !important; }
    .border-left-danger { border-left: 0.25rem solid #e74a3b !important; }
    .border-left-dark { border-left: 0.25rem solid #5a5c69 !important; }
    
    .text-primary { color: #4e73df !important; }
    .text-success { color: #1cc88a !important; }
    .text-info { color: #36b9cc !important; }
    .text-warning { color: #f6c23e !important; }
    .text-danger { color: #e74a3b !important; }
    .text-dark { color: #5a5c69 !important; }
    .text-gray-800 { color: #5a5c69 !important; }
    .text-gray-300 { color: #dddfeb !important; }
    
    .font-weight-bold { font-weight: 700 !important; }
    .text-xs { font-size: 0.7rem; }
    .text-uppercase { text-transform: uppercase; }
    .no-gutters { margin-right: 0; margin-left: 0; }
    .no-gutters > .col, .no-gutters > [class*="col-"] { padding-right: 0; padding-left: 0; }
    .h5 { font-size: 1.25rem; }
    .h6 { font-size: 1rem; }
</style>
@endpush

@section('content')

<!-- CSS PARA DASHBOARD - LAYOUT CORRECTO -->
<style>
    /* LAYOUT CORRECTO - CONTENIDO COMPLETAMENTE A LA DERECHA DEL SIDEBAR */
    .main-content {
        margin-left: 300px !important; /* 20px extra de separación */
        width: calc(100% - 300px) !important;
        position: relative !important;
        z-index: 1 !important;
        min-height: 100vh !important;
    }
    
    .content-area {
        padding-top: 90px !important;
        padding-left: 20px !important;
        padding-right: 20px !important;
        padding-bottom: 20px !important;
        position: relative !important;
        z-index: 10 !important;
        margin-left: 0 !important;
    }
    
    .sidebar-modern {
        position: fixed !important;
        left: 0 !important;
        top: 0 !important;
        width: 280px !important;
        height: 100vh !important;
        z-index: 1000 !important;
    }
    
    .header-modern {
        position: fixed !important;
        top: 0 !important;
        left: 300px !important; /* Alineado con el contenido */
        width: calc(100% - 300px) !important;
        height: 70px !important;
        z-index: 900 !important;
        background: white !important;
        border-bottom: 1px solid #e9ecef !important;
    }
    
    .container-fluid {
        opacity: 1 !important;
        visibility: visible !important;
        display: block !important;
        position: relative !important;
        z-index: 20 !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        width: 100% !important;
    }
    
    /* ASEGURAR QUE TODAS LAS TARJETAS ESTÉN COMPLETAMENTE A LA DERECHA */
    .row {
        margin-left: 0 !important;
        margin-right: 0 !important;
        padding-left: 0 !important;
    }
    
    .col, .col-md-3, .col-md-6, .col-xl-3, [class*="col-"] {
        padding-left: 15px !important;
        padding-right: 15px !important;
    }
    
    /* RESPONSIVE PARA MÓVILES */
    @media (max-width: 768px) {
        .main-content {
            margin-left: 0 !important;
            width: 100% !important;
        }
        
        .header-modern {
            left: 0 !important;
            width: 100% !important;
        }
        
        .content-area {
            padding-left: 15px !important;
            padding-right: 15px !important;
        }
    }
    
    /* DEBUG TEMPORAL - BORDES PARA VER EL POSICIONAMIENTO */
    /* .main-content {
        border-left: 2px solid red !important;
    }
    
    .content-area {
        border: 1px solid blue !important;
    } */
</style>

<div class="container-fluid">
    <!-- Header de Bienvenida -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">¡Bienvenido, {{ $socio->firstname }}!</h1>
            <p class="mb-0 text-muted">{{ $socio->full_name }} - Socio #{{ $socio->member_id }}</p>
        </div>
        <div class="text-muted">
            <i class="fas fa-calendar-alt"></i> {{ now()->format('d \d\e F \d\e Y') }}
        </div>
    </div>

    <!-- Status Cards -->
    <div class="row mb-4">
        <!-- Membership Status -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-{{ $estadoMembresia == 'activo' ? 'success' : ($estadoMembresia == 'vencido' ? 'danger' : 'warning') }} shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-{{ $estadoMembresia == 'activo' ? 'success' : ($estadoMembresia == 'vencido' ? 'danger' : 'warning') }} text-uppercase mb-1">
                                Estado de Membresía
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @switch($estadoMembresia)
                                    @case('activo')
                                        ACTIVA
                                        @break
                                    @case('vencido')
                                        VENCIDA
                                        @break
                                    @case('proximo_vencimiento')
                                        POR VENCER
                                        @break
                                    @default
                                        SIN PLAN
                                @endswitch
                            </div>
                            @if($diasRestantes !== null)
                                <div class="text-xs mt-1">
                                    @if($diasRestantes > 0)
                                        {{ $diasRestantes }} días restantes
                                    @elseif($diasRestantes == 0)
                                        Vence hoy
                                    @else
                                        Vencida hace {{ abs($diasRestantes) }} días
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="col-auto">
                            @switch($estadoMembresia)
                                @case('activo')
                                    <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                    @break
                                @case('vencido')
                                    <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                                    @break
                                @case('proximo_vencimiento')
                                    <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                                    @break
                                @default
                                    <i class="fas fa-question-circle fa-2x text-gray-300"></i>
                            @endswitch
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Plan Info -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Plan Actual
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                {{ $socio->membershipPlan->plan_name ?? 'Sin Plan' }}
                            </div>
                            @if($socio->membershipPlan)
                                <div class="text-xs mt-1">
                                    ${{ number_format($socio->membershipPlan->price, 0) }} / {{ $socio->membershipPlan->duration_type }}
                                </div>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-id-card fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Next Payment -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Próximo Pago
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @if($socio->subscription_end_date)
                                    {{ \Carbon\Carbon::parse($socio->subscription_end_date)->addDay()->format('d/m') }}
                                @else
                                    --/--
                                @endif
                            </div>
                            @if($socio->membershipPlan && $socio->subscription_end_date)
                                <div class="text-xs mt-1">
                                    ${{ number_format($socio->membershipPlan->price, 0) }}
                                </div>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-credit-card fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gym Hours -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                Horarios del Gimnasio
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                6:00 - 22:00
                            </div>
                            <div class="text-xs mt-1">
                                Lunes - Domingo
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row">
        <!-- Recent Attendance -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent border-0 pt-4">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-history me-2 text-primary"></i>Mis Asistencias Recientes
                    </h5>
                </div>
                <div class="card-body">
                    @if($asistenciasRecientes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0">Fecha</th>
                                        <th class="border-0">Hora</th>
                                        <th class="border-0">Tipo</th>
                                        <th class="border-0">Duración</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($asistenciasRecientes as $asistencia)
                                        <tr>
                                            <td>
                                                <i class="fas fa-calendar-day me-2 text-muted"></i>
                                                {{ $asistencia->fecha ? $asistencia->fecha->format('d/m/Y') : 'Sin fecha' }}
                                            </td>
                                            <td>
                                                <i class="fas fa-clock me-2 text-muted"></i>
                                                {{ $asistencia->hora ?? '--:--' }}
                                            </td>
                                            <td>
                                                @if($asistencia->tipo == 'entrada')
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                        <i class="fas fa-sign-in-alt me-1"></i>Entrada
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                                        <i class="fas fa-sign-out-alt me-1"></i>Salida
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-muted">
                                                @if($asistencia->tipo == 'salida')
                                                    ~ 2h 15min
                                                @else
                                                    <span class="text-muted">--</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="#" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-chart-line me-2"></i>Ver Historial Completo
                            </a>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">No hay asistencias registradas</h6>
                            <p class="text-muted mb-0">Cuando visites el gimnasio, tus asistencias aparecerán aquí.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions & Info -->
        <div class="col-lg-4 mb-4">
            <!-- Perfil del Socio -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 pt-4">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-user me-2 text-primary"></i>Mi Perfil
                    </h5>
                </div>
                <div class="card-body text-center">
                    @if($socio->photo)
                        <img src="{{ asset('storage/' . $socio->photo) }}" 
                             alt="Foto de {{ $socio->full_name }}" 
                             class="rounded-circle mb-3"
                             style="width: 80px; height: 80px; object-fit: cover; border: 3px solid #e3e6f0;">
                    @else
                        <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center bg-light text-muted"
                             style="width: 80px; height: 80px; font-size: 2rem; border: 3px solid #e3e6f0;">
                            <i class="fas fa-user"></i>
                        </div>
                    @endif
                    <h6 class="font-weight-bold">{{ $socio->full_name }}</h6>
                    <p class="text-muted mb-2">Socio #{{ $socio->member_id }}</p>
                    <small class="text-muted">
                        <i class="fas fa-calendar me-1"></i>Miembro desde {{ $socio->created_at ? $socio->created_at->format('F Y') : 'Fecha no disponible' }}
                    </small>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 pt-4">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-bolt me-2 text-primary"></i>Acciones Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('portal.membresia') }}" class="btn btn-primary">
                            <i class="fas fa-id-card me-2"></i>Ver mi Membresía
                        </a>
                        <a href="{{ route('portal.clases') }}" class="btn btn-outline-primary">
                            <i class="fas fa-calendar-plus me-2"></i>Reservar Clase
                        </a>
                        <a href="{{ route('portal.rutinas') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-list-alt me-2"></i>Ver Rutinas
                        </a>
                        <a href="{{ route('portal.perfil') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-user-edit me-2"></i>Editar Perfil
                        </a>
                    </div>
                </div>
            </div>
                    </a>
                </div>
            </div>
        </div>

            <!-- Próximas Clases -->
            <div class="card shadow-sm">
                <div class="card-header bg-transparent border-0 pt-4">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-calendar-alt me-2 text-primary"></i>Próximas Clases
                    </h5>
                </div>
                <div class="card-body">
                    @if($proximasClases->count() > 0)
                        @foreach($proximasClases as $clase)
                            <div class="d-flex align-items-center mb-3 p-3 bg-light rounded">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary text-white rounded p-2">
                                        <i class="fas fa-dumbbell"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">{{ $clase->nombre }}</h6>
                                    <small class="text-muted d-block">
                                        <i class="fas fa-user me-1"></i>{{ $clase->instructor }}
                                    </small>
                                    <small class="text-muted d-block">
                                        <i class="fas fa-calendar me-1"></i>{{ $clase->fecha ? $clase->fecha->format('d/m') : '--/--' }} 
                                        <i class="fas fa-clock ms-2 me-1"></i>{{ $clase->hora ?? '--:--' }}
                                    </small>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-plus fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">No hay clases reservadas</h6>
                            <p class="text-muted mb-3">Explora nuestras clases disponibles y reserva tu lugar.</p>
                            <a href="{{ route('portal.clases') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-search me-2"></i>Explorar Clases
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
            </div>
        </div>
    </div>
</div>

<!-- Membership Alert -->
@if($estadoMembresia == 'vencido' || $estadoMembresia == 'proximo_vencimiento')
    <div class="row mt-4">
        <div class="col-12">
            <div class="alert {{ $estadoMembresia == 'vencido' ? 'alert-danger' : 'alert-warning' }} alert-dismissible fade show">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>
                    @if($estadoMembresia == 'vencido')
                        ¡Tu membresía ha vencido!
                    @else
                        ¡Tu membresía vence pronto!
                    @endif
                </strong>
                @if($estadoMembresia == 'vencido')
                    Tu membresía venció el {{ $socio->subscription_end_date ? \Carbon\Carbon::parse($socio->subscription_end_date)->format('d/m/Y') : 'fecha no disponible' }}. 
                    Contacta con el gimnasio para renovarla.
                @else
                    Tu membresía vence el {{ $socio->subscription_end_date ? \Carbon\Carbon::parse($socio->subscription_end_date)->format('d/m/Y') : 'fecha no disponible' }} 
                    (en {{ $diasRestantes }} días). No olvides renovarla.
                @endif
                <div class="mt-2">
                    <a href="{{ route('portal.membresia') }}" class="btn btn-sm {{ $estadoMembresia == 'vencido' ? 'btn-light' : 'btn-dark' }}">
                        Ver Detalles de Membresía
                    </a>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate stat cards on load
    const statCards = document.querySelectorAll('.stats-card');
    statCards.forEach((card, index) => {
        setTimeout(() => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>
@endpush