@extends('layouts.portal')

@section('title', 'Dashboard')

@section('content')
<!-- Welcome Section -->
<div class="welcome-section text-center">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-3 mb-3 mb-md-0">
                @if($socio->photo)
                    <img src="{{ asset('storage/' . $socio->photo) }}" 
                         alt="Foto de {{ $socio->full_name }}" 
                         class="profile-avatar mx-auto d-block">
                @else
                    <div class="profile-avatar mx-auto d-flex align-items-center justify-content-center bg-white text-success"
                         style="font-size: 2rem;">
                        <i class="fas fa-user"></i>
                    </div>
                @endif
            </div>
            <div class="col-md-9">
                <h1 class="h2 mb-2">¡Bienvenido, {{ $socio->firstname }}!</h1>
                <p class="lead mb-1">{{ $socio->full_name }}</p>
                <p class="mb-0">
                    <i class="fas fa-id-badge me-2"></i>Socio #{{ $socio->member_id }}
                    <span class="mx-3">|</span>
                    <i class="fas fa-calendar me-2"></i>Miembro desde {{ $socio->created_at->format('F Y') }}
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Status Cards -->
<div class="row mb-4">
    <!-- Membership Status -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card dashboard-card h-100">
            <div class="card-body stats-card">
                <div class="icon">
                    @switch($estadoMembresia)
                        @case('activo')
                            <i class="fas fa-check-circle text-success"></i>
                            @break
                        @case('vencido')
                            <i class="fas fa-times-circle text-danger"></i>
                            @break
                        @case('proximo_vencimiento')
                            <i class="fas fa-exclamation-triangle text-warning"></i>
                            @break
                        @default
                            <i class="fas fa-question-circle text-secondary"></i>
                    @endswitch
                </div>
                <div class="number">
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
                <div class="label">Estado de Membresía</div>
                @if($diasRestantes !== null)
                    <small class="text-muted mt-2 d-block">
                        @if($diasRestantes > 0)
                            {{ $diasRestantes }} días restantes
                        @elseif($diasRestantes == 0)
                            Vence hoy
                        @else
                            Vencida hace {{ abs($diasRestantes) }} días
                        @endif
                    </small>
                @endif
            </div>
        </div>
    </div>

    <!-- Plan Info -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card dashboard-card h-100">
            <div class="card-body stats-card">
                <div class="icon">
                    <i class="fas fa-id-card"></i>
                </div>
                <div class="number" style="font-size: 1.5rem;">
                    {{ $socio->membershipPlan->plan_name ?? 'Sin Plan' }}
                </div>
                <div class="label">Plan Actual</div>
                @if($socio->membershipPlan)
                    <small class="text-muted mt-2 d-block">
                        ${{ number_format($socio->membershipPlan->price, 0) }} / {{ $socio->membershipPlan->duration_type }}
                    </small>
                @endif
            </div>
        </div>
    </div>

    <!-- Next Payment -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card dashboard-card h-100">
            <div class="card-body stats-card">
                <div class="icon">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div class="number" style="font-size: 1.5rem;">
                    @if($socio->subscription_end_date)
                        {{ \Carbon\Carbon::parse($socio->subscription_end_date)->addDay()->format('d/m') }}
                    @else
                        --/--
                    @endif
                </div>
                <div class="label">Próximo Pago</div>
                @if($socio->membershipPlan && $socio->subscription_end_date)
                    <small class="text-muted mt-2 d-block">
                        ${{ number_format($socio->membershipPlan->price, 0) }}
                    </small>
                @endif
            </div>
        </div>
    </div>

    <!-- Gym Hours -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card dashboard-card h-100">
            <div class="card-body stats-card">
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="number" style="font-size: 1.2rem;">
                    6:00 - 22:00
                </div>
                <div class="label">Horarios</div>
                <small class="text-muted mt-2 d-block">
                    Lun - Dom
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Row -->
<div class="row">
    <!-- Recent Attendance -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>Mis Asistencias Recientes
                </h5>
            </div>
            <div class="card-body">
                @if($asistenciasRecientes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Tipo</th>
                                    <th>Duración</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($asistenciasRecientes as $asistencia)
                                    <tr>
                                        <td>
                                            <i class="fas fa-calendar-day me-2 text-muted"></i>
                                            {{ $asistencia->fecha->format('d/m/Y') }}
                                        </td>
                                        <td>
                                            <i class="fas fa-clock me-2 text-muted"></i>
                                            {{ $asistencia->hora }}
                                        </td>
                                        <td>
                                            @if($asistencia->tipo == 'entrada')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-sign-in-alt me-1"></i>Entrada
                                                </span>
                                            @else
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-sign-out-alt me-1"></i>Salida
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-muted">
                                            @if($asistencia->tipo == 'salida')
                                                ~ 2h 15min
                                            @else
                                                --
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No hay asistencias registradas</h5>
                        <p class="text-muted">Tus visitas al gimnasio aparecerán aquí.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions & Upcoming Classes -->
    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>Acciones Rápidas
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('portal.membresia') }}" class="btn btn-primary">
                        <i class="fas fa-id-card me-2"></i>Ver mi Membresía
                    </a>
                    <a href="{{ route('portal.clases') }}" class="btn btn-outline-primary">
                        <i class="fas fa-calendar-alt me-2"></i>Reservar Clase
                    </a>
                    <a href="{{ route('portal.rutinas') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list-alt me-2"></i>Ver Rutinas
                    </a>
                    <a href="{{ route('portal.perfil') }}" class="btn btn-outline-primary">
                        <i class="fas fa-user me-2"></i>Editar Perfil
                    </a>
                </div>
            </div>
        </div>

        <!-- Upcoming Classes -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-check me-2"></i>Próximas Clases
                </h5>
            </div>
            <div class="card-body">
                @if($proximasClases->count() > 0)
                    @foreach($proximasClases as $clase)
                        <div class="d-flex align-items-center mb-3 {{ !$loop->last ? 'border-bottom pb-3' : '' }}">
                            <div class="flex-shrink-0 me-3">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white"
                                     style="width: 45px; height: 45px;">
                                    <i class="fas fa-dumbbell"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $clase->nombre }}</h6>
                                <small class="text-muted d-block">
                                    <i class="fas fa-user me-1"></i>{{ $clase->instructor }}
                                </small>
                                <small class="text-muted d-block">
                                    <i class="fas fa-calendar me-1"></i>{{ $clase->fecha->format('d/m') }} 
                                    <i class="fas fa-clock ms-2 me-1"></i>{{ $clase->hora }}
                                </small>
                            </div>
                            <div class="flex-shrink-0">
                                <button class="btn btn-sm btn-outline-primary" 
                                        onclick="alert('Próximamente: Sistema de reservas')">
                                    Reservar
                                </button>
                            </div>
                        </div>
                    @endforeach
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('portal.clases') }}" class="btn btn-sm btn-primary">
                            Ver Todas las Clases
                        </a>
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-calendar fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">No hay clases programadas</p>
                    </div>
                @endif
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
                    Tu membresía venció el {{ \Carbon\Carbon::parse($socio->subscription_end_date)->format('d/m/Y') }}. 
                    Contacta con el gimnasio para renovarla.
                @else
                    Tu membresía vence el {{ \Carbon\Carbon::parse($socio->subscription_end_date)->format('d/m/Y') }} 
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