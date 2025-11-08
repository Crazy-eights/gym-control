@extends('layouts.portal-modern')

@section('title', 'Mis Reservas')

@section('content')

<!-- CSS PARA MIS RESERVAS - LAYOUT CORRECTO -->
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
    
    .col, .col-md-4, .col-lg-4, .col-xl-4, [class*="col-"] {
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
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Mis Reservas de Clases</h1>
        <div class="d-flex">
            <a href="{{ route('portal.classes.index') }}" class="btn btn-primary">
                <i class="fas fa-plus fa-sm text-white-50"></i> Nueva Reserva
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Filter Tabs -->
    <ul class="nav nav-tabs mb-4" id="bookingTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ request('status', 'upcoming') == 'upcoming' ? 'active' : '' }}" 
               href="{{ route('portal.class-bookings.my-bookings', ['status' => 'upcoming']) }}">
                <i class="fas fa-calendar-plus"></i> Próximas ({{ $upcomingCount }})
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ request('status') == 'completed' ? 'active' : '' }}" 
               href="{{ route('portal.class-bookings.my-bookings', ['status' => 'completed']) }}">
                <i class="fas fa-calendar-check"></i> Completadas ({{ $completedCount }})
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ request('status') == 'cancelled' ? 'active' : '' }}" 
               href="{{ route('portal.class-bookings.my-bookings', ['status' => 'cancelled']) }}">
                <i class="fas fa-calendar-times"></i> Canceladas ({{ $cancelledCount }})
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ request('status') == 'all' ? 'active' : '' }}" 
               href="{{ route('portal.class-bookings.my-bookings', ['status' => 'all']) }}">
                <i class="fas fa-calendar"></i> Todas ({{ $totalCount }})
            </a>
        </li>
    </ul>

    <!-- Bookings List -->
    @if($bookings->count() > 0)
        <div class="row">
            @foreach($bookings as $booking)
                @php
                    $schedule = $booking->classSchedule;
                    $class = $schedule->gymClass;
                    $isUpcoming = \Carbon\Carbon::parse($schedule->start_date . ' ' . $schedule->start_time) > now();
                    $isPast = \Carbon\Carbon::parse($schedule->start_date . ' ' . $schedule->end_time) < now();
                    $canCancel = $isUpcoming && $booking->status === 'confirmed' && 
                                \Carbon\Carbon::parse($schedule->start_date . ' ' . $schedule->start_time)->subHours(2) > now();
                @endphp
                
                <div class="col-lg-6 col-xl-4 mb-4">
                    <div class="card shadow h-100 {{ $booking->status === 'cancelled' ? 'border-danger' : ($isPast ? 'border-success' : 'border-primary') }}">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0 text-primary">{{ $class->name }}</h6>
                            <span class="badge badge-{{ 
                                $booking->status === 'confirmed' ? ($isPast ? 'success' : 'primary') : 
                                ($booking->status === 'cancelled' ? 'danger' : 'secondary') 
                            }}">
                                @if($booking->status === 'confirmed')
                                    {{ $isPast ? 'Completada' : 'Confirmada' }}
                                @elseif($booking->status === 'cancelled')
                                    Cancelada
                                @else
                                    {{ ucfirst($booking->status) }}
                                @endif
                            </span>
                        </div>
                        <div class="card-body">
                            <!-- Class Details -->
                            <div class="mb-3">
                                <h6 class="text-dark">{{ $class->name }}</h6>
                                <p class="text-muted small mb-1">{{ Str::limit($class->description, 100) }}</p>
                            </div>

                            <!-- Schedule Details -->
                            <div class="border-left-primary pl-3 mb-3">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <small class="text-muted d-block">
                                            <i class="fas fa-calendar text-primary"></i> 
                                            <strong>Fecha:</strong>
                                        </small>
                                        <small>{{ \Carbon\Carbon::parse($schedule->start_date)->format('d/m/Y') }}</small>
                                    </div>
                                    <div class="col-sm-6">
                                        <small class="text-muted d-block">
                                            <i class="fas fa-clock text-primary"></i> 
                                            <strong>Horario:</strong>
                                        </small>
                                        <small>{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</small>
                                    </div>
                                </div>
                                
                                <div class="row mt-2">
                                    <div class="col-sm-6">
                                        <small class="text-muted d-block">
                                            <i class="fas fa-user text-primary"></i> 
                                            <strong>Instructor:</strong>
                                        </small>
                                        <small>{{ $class->instructor }}</small>
                                    </div>
                                    <div class="col-sm-6">
                                        <small class="text-muted d-block">
                                            <i class="fas fa-hourglass-half text-primary"></i> 
                                            <strong>Duración:</strong>
                                        </small>
                                        <small>{{ $class->duration }} minutos</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Booking Details -->
                            <div class="booking-details">
                                <small class="text-muted d-block">
                                    <i class="fas fa-ticket-alt text-primary"></i> 
                                    <strong>Reserva #{{ $booking->id }}</strong>
                                </small>
                                <small class="text-muted d-block">
                                    <i class="fas fa-calendar-plus text-primary"></i> 
                                    Reservado el {{ $booking->created_at->format('d/m/Y H:i') }}
                                </small>
                                
                                @if($booking->status === 'cancelled' && $booking->cancelled_at)
                                    <small class="text-danger d-block">
                                        <i class="fas fa-times text-danger"></i> 
                                        Cancelado el {{ \Carbon\Carbon::parse($booking->cancelled_at)->format('d/m/Y H:i') }}
                                    </small>
                                @endif
                            </div>

                            <!-- Time Status -->
                            @if($isUpcoming && $booking->status === 'confirmed')
                                @php
                                    $timeUntilClass = \Carbon\Carbon::parse($schedule->start_date . ' ' . $schedule->start_time)->diffForHumans();
                                @endphp
                                <div class="alert alert-info mt-3 mb-0">
                                    <small>
                                        <i class="fas fa-info-circle"></i>
                                        La clase es {{ $timeUntilClass }}
                                    </small>
                                </div>
                            @endif
                        </div>
                        
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="{{ route('portal.classes.show', $class) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-info-circle"></i> Ver Clase
                                    </a>
                                </div>
                                
                                <div>
                                    @if($canCancel)
                                        <button class="btn btn-sm btn-outline-danger cancel-booking-btn" 
                                                data-booking-id="{{ $booking->id }}"
                                                data-class-name="{{ $class->name }}"
                                                data-date="{{ \Carbon\Carbon::parse($schedule->start_date)->format('d/m/Y') }}"
                                                data-time="{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}">
                                            <i class="fas fa-times"></i> Cancelar
                                        </button>
                                    @elseif($booking->status === 'confirmed' && !$canCancel && $isUpcoming)
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle"></i>
                                            No se puede cancelar<br>
                                            (menos de 2h restantes)
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($bookings->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $bookings->appends(request()->query())->links() }}
            </div>
        @endif
    @else
        <div class="card shadow">
            <div class="card-body text-center py-5">
                @if(request('status') === 'upcoming' || !request('status'))
                    <i class="fas fa-calendar-plus fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No tienes reservas próximas</h4>
                    <p class="text-muted">¡Es hora de reservar tu próxima clase de entrenamiento!</p>
                    <a href="{{ route('portal.classes.index') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Reservar una Clase
                    </a>
                @elseif(request('status') === 'completed')
                    <i class="fas fa-calendar-check fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No hay clases completadas</h4>
                    <p class="text-muted">Tus clases completadas aparecerán aquí después de asistir.</p>
                @elseif(request('status') === 'cancelled')
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No hay clases canceladas</h4>
                    <p class="text-muted">¡Excelente! No has cancelado ninguna clase.</p>
                @else
                    <i class="fas fa-calendar fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No tienes reservas</h4>
                    <p class="text-muted">Aún no has reservado ninguna clase.</p>
                    <a href="{{ route('portal.classes.index') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Hacer tu Primera Reserva
                    </a>
                @endif
            </div>
        </div>
    @endif
</div>

<!-- Modal de Confirmación de Cancelación -->
<div class="modal fade" id="cancelModal" tabindex="-1" role="dialog" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelModalLabel">Cancelar Reserva</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que quieres cancelar esta reserva?</p>
                <div id="cancel-details" class="alert alert-info">
                    <!-- Los detalles se llenarán vía JavaScript -->
                </div>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Importante:</strong> Esta acción no se puede deshacer y se liberará el cupo para otros miembros.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No, mantener reserva</button>
                <button type="button" class="btn btn-danger" id="confirmCancelBtn">Sí, cancelar reserva</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let selectedBookingId = null;

    // Handle cancel booking button clicks
    document.querySelectorAll('.cancel-booking-btn').forEach(button => {
        button.addEventListener('click', function() {
            selectedBookingId = this.dataset.bookingId;
            const className = this.dataset.className;
            const date = this.dataset.date;
            const time = this.dataset.time;

            document.getElementById('cancel-details').innerHTML = `
                <strong>Clase:</strong> ${className}<br>
                <strong>Fecha:</strong> ${date}<br>
                <strong>Hora:</strong> ${time}
            `;

            $('#cancelModal').modal('show');
        });
    });

    // Handle booking cancellation confirmation
    document.getElementById('confirmCancelBtn').addEventListener('click', function() {
        if (selectedBookingId) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ url('portal/class-bookings') }}/${selectedBookingId}/cancel`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PATCH';
            
            form.appendChild(csrfToken);
            form.appendChild(methodInput);
            document.body.appendChild(form);
            form.submit();
        }
    });
});
</script>
@endsection