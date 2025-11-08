@extends('layouts.portal-modern')

@section('title', 'Clases Disponibles')

@push('styles')
<style>
    /* Estilos para las tarjetas de clases */
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
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        transition: all 0.2s ease;
    }

    .schedule-card:hover {
        background: #e9ecef;
        border-color: var(--primary-color, #4CAF50);
    }

    .status-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 12px;
        font-weight: 600;
    }

    .btn-reserve {
        background: linear-gradient(135deg, var(--primary-color, #4CAF50) 0%, var(--success-color, #28a745) 100%);
        border: none;
        color: white;
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .btn-reserve:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
        color: white;
    }

    .empty-state {
        padding: 3rem 2rem;
        text-align: center;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .class-info-item {
            padding: 0.5rem;
        }
        
        .class-info-item .value {
            font-size: 0.8rem;
        }
    }
</style>
@endpush

@section('content')

<!-- FORZAR POSICIONAMIENTO CON JAVASCRIPT -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para forzar el layout correcto
    function forceCorrectLayout() {
        const sidebar = document.querySelector('.sidebar-modern');
        const mainContent = document.querySelector('.main-content');
        const header = document.querySelector('.header-modern');
        const contentArea = document.querySelector('.content-area');
        
        if (sidebar) {
            sidebar.style.setProperty('position', 'fixed', 'important');
            sidebar.style.setProperty('left', '0', 'important');
            sidebar.style.setProperty('top', '0', 'important');
            sidebar.style.setProperty('width', '280px', 'important');
            sidebar.style.setProperty('height', '100vh', 'important');
            sidebar.style.setProperty('z-index', '1000', 'important');
        }
        
        if (mainContent) {
            mainContent.style.setProperty('margin-left', '300px', 'important'); // 20px extra de separación
            mainContent.style.setProperty('width', 'calc(100% - 300px)', 'important');
            mainContent.style.setProperty('min-height', '100vh', 'important');
            mainContent.style.setProperty('position', 'relative', 'important');
        }
        
        if (header) {
            header.style.setProperty('position', 'fixed', 'important');
            header.style.setProperty('top', '0', 'important');
            header.style.setProperty('left', '300px', 'important'); // Alineado con el contenido
            header.style.setProperty('width', 'calc(100% - 300px)', 'important');
            header.style.setProperty('height', '70px', 'important');
            header.style.setProperty('z-index', '900', 'important');
        }
        
        if (contentArea) {
            contentArea.style.setProperty('padding-top', '90px', 'important');
            contentArea.style.setProperty('padding-left', '20px', 'important');
            contentArea.style.setProperty('padding-right', '20px', 'important');
            contentArea.style.setProperty('background', 'red', 'important'); // DEBUG
        }
        
        // Log para debug
        console.log('Layout forzado aplicado');
        console.log('Sidebar width:', sidebar ? sidebar.offsetWidth : 'No encontrado');
        console.log('Main content margin-left:', mainContent ? window.getComputedStyle(mainContent).marginLeft : 'No encontrado');
    }
    
    // Aplicar inmediatamente
    forceCorrectLayout();
    
    // Aplicar cada 100ms durante los primeros 2 segundos por si algo lo sobrescribe
    let attempts = 0;
    const interval = setInterval(() => {
        forceCorrectLayout();
        attempts++;
        if (attempts >= 20) { // 20 intentos = 2 segundos
            clearInterval(interval);
        }
    }, 100);
});
</script>

<!-- CSS ADICIONAL -->
<style>
<!-- CSS PARA CORREGIR EL CONTENEDOR -->
<style>
    /* FORZAR QUE EL CONTENEDOR RESPETE EL ÁREA DISPONIBLE */
    .main-content {
        position: relative !important;
        left: 280px !important; /* Posicionar después del sidebar */
        width: calc(100vw - 280px) !important; /* Ancho exacto del área disponible */
        top: 0 !important;
        z-index: 1 !important;
        min-height: 100vh !important;
        margin: 0 !important; /* Sin márgenes */
        padding: 0 !important; /* Sin padding interno */
    }
    
    .content-area {
        padding-top: 70px !important; /* Solo padding top para el header */
        padding-left: 20px !important;
        padding-right: 20px !important;
        padding-bottom: 20px !important;
        width: 100% !important; /* Usar todo el ancho disponible */
        max-width: 100% !important;
        margin: 0 !important;
        box-sizing: border-box !important;
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
        left: 280px !important;
        width: calc(100vw - 280px) !important; /* Mismo ancho que el contenedor */
        height: 70px !important;
        z-index: 900 !important;
        background: white !important;
        border-bottom: 1px solid #e9ecef !important;
    }
    
    /* CONTENEDOR FLUID - USAR TODO EL ESPACIO DISPONIBLE */
    .container-fluid {
        width: 100% !important;
        max-width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
        box-sizing: border-box !important;
        background: rgba(0, 255, 0, 0.2) !important; /* DEBUG: verde para ver límites */
    }
    
    /* FORZAR LAYOUT CORRECTO DE BOOTSTRAP */
    .row {
        width: 100% !important;
        margin: 0 !important;
        max-width: 100% !important;
    }
    
    .col, .col-md-4, .col-lg-4, .col-xl-4, [class*="col-"] {
        padding-left: 15px !important;
        padding-right: 15px !important;
        box-sizing: border-box !important;
    }
    
    /* DEBUG: BORDES PARA VER EL PROBLEMA */
    .main-content {
        border: 3px solid red !important;
    }
    
    .container-fluid {
        border: 2px solid blue !important;
    }
    
    /* RESPONSIVE */
    @media (max-width: 768px) {
        .main-content {
            left: 0 !important;
            width: 100vw !important;
        }
        
        .header-modern {
            left: 0 !important;
            width: 100vw !important;
        }
    }
    
    /* MENSAJE DEBUG */
    body::before {
        content: "DEBUG: Contenedor corregido - Width calc(100vw - 280px)" !important;
        position: fixed !important;
        top: 100px !important;
        right: 20px !important;
        background: purple !important;
        color: white !important;
        padding: 10px !important;
        z-index: 9999 !important;
        font-weight: bold !important;
        font-size: 12px !important;
    }
</style>
</style>

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
                    <div class="col-md-3 mb-3">
                        <label for="date" class="form-label">Fecha</label>
                        <input type="date" class="form-control" id="date" name="date" 
                               value="{{ request('date') }}">
                    </div>
                    <div class="col-md-3 mb-3">
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
                    <div class="col-md-3 mb-3">
                        <label for="duration" class="form-label">Duración</label>
                        <select class="form-select" id="duration" name="duration">
                            <option value="">Cualquier duración</option>
                            <option value="30" {{ request('duration') == '30' ? 'selected' : '' }}>30 minutos</option>
                            <option value="45" {{ request('duration') == '45' ? 'selected' : '' }}>45 minutos</option>
                            <option value="60" {{ request('duration') == '60' ? 'selected' : '' }}>60 minutos</option>
                            <option value="90" {{ request('duration') == '90' ? 'selected' : '' }}>90 minutos</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search me-1"></i>Buscar
                            </button>
                            <a href="{{ route('portal.classes.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Limpiar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Grid de Clases -->
    <div class="row">
        @forelse($classes as $class)
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="card shadow-sm h-100 class-card">
                    <!-- Header de la clase -->
                    <div class="card-header bg-white border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold text-primary">{{ $class->name }}</h5>
                            <span class="badge status-badge bg-{{ $class->active ? 'success' : 'secondary' }}">
                                {{ $class->active ? 'Activa' : 'Inactiva' }}
                            </span>
                        </div>
                    </div>

                    <!-- Cuerpo de la clase -->
                    <div class="card-body">
                        @if($class->description)
                            <p class="card-text text-muted mb-3">{{ Str::limit($class->description, 80) }}</p>
                        @endif
                        
                        <!-- Información de la clase -->
                        <div class="row mb-3">
                            <div class="col-4">
                                <div class="class-info-item">
                                    <i class="fas fa-user text-primary"></i>
                                    <div class="label">Instructor</div>
                                    <div class="value">{{ Str::limit($class->instructor_name, 15) }}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="class-info-item">
                                    <i class="fas fa-clock text-info"></i>
                                    <div class="label">Duración</div>
                                    <div class="value">{{ $class->duration_minutes }}min</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="class-info-item">
                                    <i class="fas fa-users text-success"></i>
                                    <div class="label">Capacidad</div>
                                    <div class="value">{{ $class->max_participants }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Próximas sesiones -->
                        @if($class->upcomingSchedules && $class->upcomingSchedules->count() > 0)
                            <div class="mb-3">
                                <h6 class="fw-bold text-dark mb-2">
                                    <i class="fas fa-calendar-alt me-1"></i>Próximas Sesiones
                                </h6>
                                @foreach($class->upcomingSchedules->take(3) as $schedule)
                                    <div class="schedule-card">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="flex-grow-1">
                                                <div class="fw-bold text-primary small">
                                                    {{ $schedule->start_date->format('d/m/Y') }}
                                                </div>
                                                <div class="text-muted small">
                                                    {{ $schedule->start_time }} - {{ $schedule->end_time }}
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                @php
                                                    $currentBookings = $schedule->bookings ? $schedule->bookings->where('status', 'confirmed')->count() : 0;
                                                    $spotsLeft = $class->max_participants - $currentBookings;
                                                    $userHasBooking = $schedule->bookings ? $schedule->bookings->where('member_id', auth()->id())->where('status', 'confirmed')->count() > 0 : false;
                                                @endphp
                                                
                                                <div class="small text-{{ $spotsLeft > 0 ? 'success' : 'danger' }} mb-1">
                                                    <i class="fas fa-users me-1"></i>{{ $spotsLeft }} cupos
                                                </div>
                                                
                                                @if($userHasBooking)
                                                    <span class="badge bg-success status-badge">
                                                        <i class="fas fa-check me-1"></i>Reservado
                                                    </span>
                                                @elseif($spotsLeft > 0 && \Carbon\Carbon::parse($schedule->start_date->format('Y-m-d') . ' ' . $schedule->start_time) > now())
                                                    <button class="btn btn-sm btn-reserve book-class-btn" 
                                                            data-schedule-id="{{ $schedule->id }}"
                                                            data-class-name="{{ $class->name }}"
                                                            data-date="{{ $schedule->start_date->format('d/m/Y') }}"
                                                            data-time="{{ $schedule->start_time }}">
                                                        <i class="fas fa-plus me-1"></i>Reservar
                                                    </button>
                                                @elseif($spotsLeft <= 0)
                                                    <span class="badge bg-danger status-badge">
                                                        <i class="fas fa-times me-1"></i>Lleno
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary status-badge">
                                                        <i class="fas fa-clock me-1"></i>Expirado
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                
                                @if($class->upcomingSchedules->count() > 3)
                                    <div class="text-center mt-2">
                                        <a href="{{ route('portal.classes.show', $class) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-calendar-alt me-1"></i>
                                            Ver todas ({{ $class->upcomingSchedules->count() }})
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="fas fa-calendar-times"></i>
                                <h6 class="text-muted">Sin sesiones programadas</h6>
                                <small class="text-muted">Las próximas sesiones aparecerán aquí pronto</small>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Footer con enlace a detalles -->
                    @if($class->upcomingSchedules && $class->upcomingSchedules->count() > 0)
                        <div class="card-footer bg-light text-center border-0">
                            <a href="{{ route('portal.classes.show', $class) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-info-circle me-1"></i>Ver Detalles Completos
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-dumbbell fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No hay clases disponibles</h4>
                        <p class="text-muted">No se encontraron clases que coincidan con los filtros seleccionados.</p>
                        <a href="{{ route('portal.classes.index') }}" class="btn btn-primary">
                            <i class="fas fa-refresh"></i> Ver todas las clases
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($classes->hasPages())
        <div class="d-flex justify-content-center">
            {{ $classes->links() }}
        </div>
    @endif
</div>

<!-- Modal de Confirmación de Reserva -->
<div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookingModalLabel">
                    <i class="fas fa-calendar-plus me-2"></i>Confirmar Reserva
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">¿Estás seguro de que quieres reservar esta clase?</p>
                <div id="booking-details" class="alert alert-info">
                    <!-- Los detalles se llenarán vía JavaScript -->
                </div>
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Podrás cancelar la reserva hasta 2 horas antes de la clase.
                </small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="confirmBookingBtn">
                    <i class="fas fa-check me-2"></i>Confirmar Reserva
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let selectedScheduleId = null;

    // Handle booking button clicks
    document.querySelectorAll('.book-class-btn').forEach(button => {
        button.addEventListener('click', function() {
            selectedScheduleId = this.dataset.scheduleId;
            const className = this.dataset.className;
            const date = this.dataset.date;
            const time = this.dataset.time;

            document.getElementById('booking-details').innerHTML = `
                <div class="row">
                    <div class="col-6">
                        <strong><i class="fas fa-dumbbell me-2"></i>Clase:</strong><br>
                        ${className}
                    </div>
                    <div class="col-3">
                        <strong><i class="fas fa-calendar me-2"></i>Fecha:</strong><br>
                        ${date}
                    </div>
                    <div class="col-3">
                        <strong><i class="fas fa-clock me-2"></i>Hora:</strong><br>
                        ${time}
                    </div>
                </div>
            `;

            const bookingModal = new bootstrap.Modal(document.getElementById('bookingModal'));
            bookingModal.show();
        });
    });

    // Handle booking confirmation
    document.getElementById('confirmBookingBtn').addEventListener('click', function() {
        if (selectedScheduleId) {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("portal.class-bookings.store") }}';
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const scheduleInput = document.createElement('input');
            scheduleInput.type = 'hidden';
            scheduleInput.name = 'schedule_id';
            scheduleInput.value = selectedScheduleId;
            
            form.appendChild(csrfToken);
            form.appendChild(scheduleInput);
            document.body.appendChild(form);
            form.submit();
        }
    });
});
</script>
@endpush