@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<!-- Header de Bienvenida -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Panel de Administración</h1>
        <p class="mb-0 text-muted">Gestiona tu gimnasio de manera eficiente</p>
    </div>
    <div class="text-muted">
        <i class="fas fa-calendar-alt"></i> {{ now()->format('d \d\e F \d\e Y') }}
    </div>
</div>

<!-- Cards de Estadísticas -->
<div class="row">
    <!-- Total Socios Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Socios
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ \App\Models\Member::count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Asistencias Hoy Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Asistencias Hoy
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            @php
                                // Asistencias reales de hoy usando la tabla member_attendance
                                $asistenciasHoy = \App\Models\MemberAttendance::whereDate('attendance_date', today())->count();
                            @endphp
                            {{ $asistenciasHoy }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Membresías Vencidas Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Membresías Vencidas
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ \App\Models\Member::expired()->count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Planes Disponibles Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Planes Disponibles
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ \App\Models\MembershipPlan::count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Clases Activas Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Clases Activas
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ \App\Models\GymClass::where('active', true)->count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dumbbell fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Segunda fila de estadísticas -->
<div class="row">
    <!-- Reservas de Hoy Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Reservas de Hoy
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ \App\Models\ClassBooking::whereDate('booking_date', today())->where('status', 'confirmed')->count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ingresos del Mes por Clases Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Ingresos Clases (Mes)
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            ${{ number_format(\Illuminate\Support\Facades\DB::table('class_bookings')
                                ->join('class_schedules', 'class_bookings.class_schedule_id', '=', 'class_schedules.id')
                                ->join('gym_classes', 'class_schedules.gym_class_id', '=', 'gym_classes.id')
                                ->where('class_bookings.booking_date', '>=', now()->startOfMonth())
                                ->where('class_bookings.status', '!=', 'cancelled')
                                ->sum('gym_classes.price'), 2) }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Clases Más Populares Card -->
    <div class="col-xl-6 col-md-12 mb-4">
        <div class="card border-left-dark shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                            Clase Más Popular
                        </div>
                        @php
                            $popularClass = \App\Models\GymClass::withCount(['bookings' => function($query) {
                                $query->where('booking_date', '>=', now()->subMonth())
                                      ->where('status', '!=', 'cancelled');
                            }])->orderBy('bookings_count', 'desc')->first();
                        @endphp
                        @if($popularClass)
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                {{ $popularClass->name }}
                                <small class="text-muted">({{ $popularClass->bookings_count }} reservas)</small>
                            </div>
                        @else
                            <div class="h6 mb-0 font-weight-bold text-gray-800">Sin datos</div>
                        @endif
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-trophy fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gráficas -->
<div class="row">
    <!-- Gráfica de Asistencias Semanales -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Asistencias de la Semana</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                        aria-labelledby="dropdownMenuLink">
                        <div class="dropdown-header">Opciones:</div>
                        <a class="dropdown-item" href="#">Ver reporte completo</a>
                        <a class="dropdown-item" href="#">Exportar datos</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="asistenciasChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfica de Socios por Plan -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Socios por Plan</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="sociosPlanChart"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    @php
                        $planesConSocios = \App\Models\MembershipPlan::withCount('members')
                            ->having('members_count', '>', 0)
                            ->orderBy('members_count', 'desc')
                            ->take(5)
                            ->get();
                    @endphp
                    @foreach($planesConSocios as $plan)
                    <span class="mr-2">
                        <i class="fas fa-circle" style="color: {{ ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'][$loop->index % 5] }}"></i>
                        {{ $plan->plan_name }} ({{ $plan->members_count }})
                    </span><br>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Control de Asistencias -->
<div class="row">
    <!-- Asistencias Recientes -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Asistencias Recientes</h6>
            </div>
            <div class="card-body">
                @php
                    // Asistencias reales recientes de hoy
                    $asistenciasRecientes = \App\Models\MemberAttendance::with('member')
                        ->whereDate('attendance_date', today())
                        ->orderBy('attendance_date', 'desc')
                        ->take(5)
                        ->get();
                @endphp
                
                @if($asistenciasRecientes->count() > 0)
                    @foreach($asistenciasRecientes as $asistencia)
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <div class="rounded-circle bg-success d-flex align-items-center justify-content-center text-white"
                                     style="width: 40px; height: 40px;">
                                    <i class="fas fa-check"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0">{{ $asistencia->member->full_name ?? 'Socio no encontrado' }}</h6>
                                <small class="text-muted">
                                    Registrado - {{ $asistencia->attendance_date->format('H:i') }}
                                </small>
                            </div>
                            <div>
                                <span class="badge bg-success">
                                    Presente
                                </span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-clock fa-3x mb-3"></i>
                        <p>No hay asistencias registradas hoy</p>
                    </div>
                @endif
                
                <div class="text-center mt-3">
                    <button class="btn btn-primary btn-sm" disabled>
                        <i class="fas fa-clock"></i> Módulo de Asistencias (Próximamente)
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas y Notificaciones -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Alertas y Notificaciones</h6>
            </div>
            <div class="card-body">
                @php
                    $membresiasProximas = \App\Models\Member::whereNotNull('subscription_end_date')
                        ->whereBetween('subscription_end_date', [now(), now()->addDays(7)])
                        ->take(5)
                        ->get();
                @endphp
                
                @if($membresiasProximas->count() > 0)
                    <h6 class="text-warning mb-3">
                        <i class="fas fa-exclamation-triangle"></i> Membresías que vencen esta semana
                    </h6>
                    @foreach($membresiasProximas as $socio)
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <div class="rounded-circle bg-warning d-flex align-items-center justify-content-center text-white"
                                     style="width: 40px; height: 40px;">
                                    <i class="fas fa-calendar-times"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0">{{ $socio->full_name }}</h6>
                                <small class="text-muted">
                                    Vence: {{ \Carbon\Carbon::parse($socio->subscription_end_date)->format('d/m/Y') }}
                                </small>
                            </div>
                            <div>
                                <span class="badge bg-warning">
                                    {{ \Carbon\Carbon::parse($socio->subscription_end_date)->diffInDays(now()) }} días
                                </span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                        <p>No hay membresías próximas a vencer</p>
                    </div>
                @endif
                
                <div class="text-center mt-3">
                    <a href="{{ route('admin.socios.index') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-users"></i> Ver Todos los Socios
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfica de Asistencias Semanales - Datos reales
    const ctxAsistencias = document.getElementById('asistenciasChart').getContext('2d');
    
    // Obtener datos reales de asistencias de los últimos 7 días
    @php
        $diasSemana = [];
        $asistenciasDatos = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $fecha = now()->subDays($i);
            $diasSemana[] = $fecha->locale('es')->dayName;
            $asistenciasDatos[] = \App\Models\MemberAttendance::whereDate('attendance_date', $fecha)->count();
        }
    @endphp
    
    const asistenciasChart = new Chart(ctxAsistencias, {
        type: 'line',
        data: {
            labels: {!! json_encode($diasSemana) !!},
            datasets: [{
                label: 'Asistencias',
                data: {!! json_encode($asistenciasDatos) !!},
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(200, 200, 200, 0.3)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Gráfica de Socios por Plan
    const ctxSocios = document.getElementById('sociosPlanChart').getContext('2d');
    const sociosPlanChart = new Chart(ctxSocios, {
        type: 'doughnut',
        data: {
            labels: [
                @foreach($planesConSocios as $plan)
                    '{{ $plan->plan_name }}',
                @endforeach
            ],
            datasets: [{
                data: [
                    @foreach($planesConSocios as $plan)
                        {{ $plan->members_count }},
                    @endforeach
                ],
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#f4b619', '#e02d1b'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            cutout: '70%'
        }
    });
});
</script>
@endpush