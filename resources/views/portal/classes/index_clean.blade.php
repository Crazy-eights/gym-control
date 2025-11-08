@extends('layouts.portal-modern')

@section('title', 'Clases Disponibles')

@push('styles')
<style>
    /* Estilos básicos para las tarjetas de clases */
    .class-card {
        border: none;
        border-radius: 15px;
        transition: all 0.3s ease;
        overflow: hidden;
    }
    
    .class-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }

    .class-info-item {
        text-align: center;
        padding: 1rem;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 10px;
        margin-bottom: 0.5rem;
    }

    .class-info-item i {
        display: block;
        margin-bottom: 0.5rem;
    }

    .class-info-item .label {
        font-size: 0.75rem;
        text-transform: uppercase;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }

    .class-info-item .value {
        font-size: 0.9rem;
        font-weight: 700;
        color: #495057;
    }

    .schedule-card {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 1rem;
        margin: 0.5rem 0;
    }
    
    .btn-reserve {
        background: linear-gradient(135deg, #4CAF50 0%, #00BCD4 100%);
        border: none;
        color: white;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-weight: 600;
    }
    
    .btn-reserve:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header de Página -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">Clases Disponibles</h1>
            <p class="mb-0 text-muted">Reserva tu lugar en nuestras clases de gimnasio</p>
        </div>
        <div>
            <a href="{{ route('portal.class-bookings.my-bookings') }}" class="btn btn-outline-primary">
                <i class="fas fa-calendar-check me-2"></i>Mis Reservas
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Filtros -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-transparent border-0 pt-4">
            <h5 class="mb-0 fw-bold">
                <i class="fas fa-filter me-2 text-primary"></i>Filtros de Búsqueda
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('portal.classes.index') }}">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="instructor" class="form-label">Instructor</label>
                        <select class="form-select" id="instructor" name="instructor">
                            <option value="">Todos los instructores</option>
                            @foreach($instructors as $instructor)
                                <option value="{{ $instructor }}"
                                        {{ request('instructor') == $instructor ? 'selected' : '' }}>
                                    {{ $instructor }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="duration" class="form-label">Duración</label>
                        <select class="form-select" id="duration" name="duration">
                            <option value="">Cualquier duración</option>
                            <option value="30" {{ request('duration') == '30' ? 'selected' : '' }}>30 minutos</option>
                            <option value="45" {{ request('duration') == '45' ? 'selected' : '' }}>45 minutos</option>
                            <option value="60" {{ request('duration') == '60' ? 'selected' : '' }}>60 minutos</option>
                            <option value="90" {{ request('duration') == '90' ? 'selected' : '' }}>90 minutos</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid gap-2 d-md-flex">
                            <button type="submit" class="btn btn-primary me-md-2">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                            <a href="{{ route('portal.classes.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Limpiar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Clases -->
    @if($classes->count() > 0)
        <div class="row">
            @foreach($classes as $class)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card class-card h-100 shadow-sm">
                        <div class="card-header bg-white border-0 pb-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <span class="badge bg-success">Activa</span>
                                <h5 class="card-title text-primary mb-0">{{ $class->name }}</h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="card-text text-muted mb-3">{{ $class->description }}</p>
                            
                            <div class="row g-2 mb-3">
                                <div class="col-4">
                                    <div class="class-info-item">
                                        <i class="fas fa-clock text-primary"></i>
                                        <div class="label">Duración</div>
                                        <div class="value">{{ $class->duration }}min</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="class-info-item">
                                        <i class="fas fa-users text-success"></i>
                                        <div class="label">Capacidad</div>
                                        <div class="value">{{ $class->capacity ?? 15 }}</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="class-info-item">
                                        <i class="fas fa-user text-info"></i>
                                        <div class="label">Instructor</div>
                                        <div class="value">{{ $class->instructor_name ?? 'TBD' }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Próximas Sesiones -->
                            <div class="mb-3">
                                <h6 class="fw-bold mb-2">
                                    <i class="fas fa-calendar text-primary"></i> Próximas Sesiones
                                </h6>
                                @php
                                    $upcomingSessions = $class->schedules()
                                        ->whereDate('start_time', '>=', now())
                                        ->orderBy('start_time')
                                        ->take(2)
                                        ->get();
                                @endphp

                                @if($upcomingSessions->count() > 0)
                                    @foreach($upcomingSessions as $session)
                                        <div class="schedule-card">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong>{{ $session->start_time->format('d/m/Y') }}</strong><br>
                                                    <small>{{ $session->start_time->format('H:i') }} - {{ $session->end_time->format('H:i') }}</small>
                                                </div>
                                                <div class="text-end">
                                                    @php
                                                        $availableSpots = ($class->capacity ?? 15) - $session->bookings()->where('status', 'confirmed')->count();
                                                    @endphp
                                                    <span class="badge bg-info">{{ $availableSpots }} cupos</span>
                                                    @if($session->start_time->isPast())
                                                        <span class="badge bg-secondary">Expirado</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="schedule-card text-center text-muted">
                                        <i class="fas fa-calendar-times fa-2x mb-2"></i>
                                        <p class="mb-0">Sin sesiones programadas</p>
                                        <small>Las próximas sesiones aparecerán aquí pronto</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <a href="{{ route('portal.classes.show', $class) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-eye"></i> Ver Detalles Completos
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Paginación -->
        @if($classes->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $classes->appends(request()->query())->links() }}
            </div>
        @endif
    @else
        <div class="text-center py-5">
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">No se encontraron clases</h4>
            <p class="text-muted mb-4">No hay clases disponibles con los criterios seleccionados.</p>
            <a href="{{ route('portal.classes.index') }}" class="btn btn-primary">
                <i class="fas fa-refresh"></i> Ver Todas las Clases
            </a>
        </div>
    @endif
</div>
@endsection