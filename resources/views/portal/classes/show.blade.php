@extends('layouts.socios')

@section('title', $class->name)

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('portal.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('portal.classes.index') }}">Clases</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $class->name }}</li>
        </ol>
    </nav>

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

    <!-- Class Details Header -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h1 class="h3 mb-0 text-gray-800">{{ $class->name }}</h1>
                    <span class="badge badge-{{ $class->active ? 'success' : 'secondary' }} badge-lg">
                        {{ $class->active ? 'Activa' : 'Inactiva' }}
                    </span>
                </div>
                <div class="card-body">
                    <p class="lead">{{ $class->description }}</p>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">Detalles de la Clase</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-user text-primary mr-2"></i><strong>Instructor:</strong> {{ $class->instructor }}</li>
                                <li><i class="fas fa-clock text-primary mr-2"></i><strong>Duración:</strong> {{ $class->duration }} minutos</li>
                                <li><i class="fas fa-users text-primary mr-2"></i><strong>Capacidad máxima:</strong> {{ $class->max_capacity }} personas</li>
                                <li><i class="fas fa-calendar text-primary mr-2"></i><strong>Creada:</strong> {{ $class->created_at->format('d/m/Y') }}</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary">Estadísticas</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-calendar-check text-success mr-2"></i><strong>Sesiones totales:</strong> {{ $class->schedules->count() }}</li>
                                <li><i class="fas fa-calendar-plus text-info mr-2"></i><strong>Próximas sesiones:</strong> {{ $upcomingSchedules->count() }}</li>
                                <li><i class="fas fa-users text-warning mr-2"></i><strong>Reservas totales:</strong> {{ $totalBookings }}</li>
                                <li><i class="fas fa-star text-primary mr-2"></i><strong>Popularidad:</strong> {{ number_format($averageOccupancy, 1) }}% promedio</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Acciones Rápidas</h6>
                </div>
                <div class="card-body text-center">
                    <a href="{{ route('portal.classes.index') }}" class="btn btn-secondary btn-block mb-2">
                        <i class="fas fa-arrow-left"></i> Volver a Clases
                    </a>
                    <a href="{{ route('portal.class-bookings.my-bookings') }}" class="btn btn-primary btn-block">
                        <i class="fas fa-calendar-check"></i> Mis Reservas
                    </a>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Resumen Rápido</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="mb-3">
                            <h4 class="text-primary">{{ $upcomingSchedules->count() }}</h4>
                            <small class="text-muted">Próximas sesiones</small>
                        </div>
                        
                        @if($nextSchedule)
                            <div class="alert alert-info">
                                <small class="font-weight-bold">Próxima sesión:</small><br>
                                <small>{{ \Carbon\Carbon::parse($nextSchedule->date)->format('d/m/Y') }}</small><br>
                                <small>{{ \Carbon\Carbon::parse($nextSchedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($nextSchedule->end_time)->format('H:i') }}</small>
                                
                                @php
                                    $currentBookings = $nextSchedule->bookings->where('status', 'confirmed')->count();
                                    $spotsLeft = $class->max_capacity - $currentBookings;
                                    $userHasBooking = $nextSchedule->bookings->where('member_id', auth()->id())->where('status', 'confirmed')->count() > 0;
                                @endphp
                                
                                <div class="mt-2">
                                    @if($userHasBooking)
                                        <span class="badge badge-success">Ya tienes reserva</span>
                                    @elseif($spotsLeft > 0 && \Carbon\Carbon::parse($nextSchedule->date . ' ' . $nextSchedule->start_time) > now())
                                        <button class="btn btn-sm btn-primary book-class-btn" 
                                                data-schedule-id="{{ $nextSchedule->id }}"
                                                data-class-name="{{ $class->name }}"
                                                data-date="{{ \Carbon\Carbon::parse($nextSchedule->date)->format('d/m/Y') }}"
                                                data-time="{{ \Carbon\Carbon::parse($nextSchedule->start_time)->format('H:i') }}">
                                            Reservar Ahora
                                        </button>
                                        <br><small class="text-success">{{ $spotsLeft }} cupos disponibles</small>
                                    @elseif($spotsLeft <= 0)
                                        <span class="badge badge-danger">Sesión llena</span>
                                    @else
                                        <span class="badge badge-secondary">Sesión expirada</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Schedules -->
    @if($upcomingSchedules->count() > 0)
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Próximas Sesiones</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Hora de Inicio</th>
                                <th>Hora de Fin</th>
                                <th>Reservas</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($upcomingSchedules as $schedule)
                                @php
                                    $currentBookings = $schedule->bookings->where('status', 'confirmed')->count();
                                    $spotsLeft = $class->max_capacity - $currentBookings;
                                    $userHasBooking = $schedule->bookings->where('member_id', auth()->id())->where('status', 'confirmed')->count() > 0;
                                    $isExpired = \Carbon\Carbon::parse($schedule->date . ' ' . $schedule->start_time) < now();
                                @endphp
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($schedule->date)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</td>
                                    <td>
                                        <span class="badge badge-info">{{ $currentBookings }}/{{ $class->max_capacity }}</span>
                                        @if($spotsLeft > 0)
                                            <small class="text-success d-block">{{ $spotsLeft }} cupos libres</small>
                                        @else
                                            <small class="text-danger d-block">Sin cupos</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($userHasBooking)
                                            <span class="badge badge-success">Reservado</span>
                                        @elseif($isExpired)
                                            <span class="badge badge-secondary">Expirado</span>
                                        @elseif($spotsLeft <= 0)
                                            <span class="badge badge-danger">Lleno</span>
                                        @else
                                            <span class="badge badge-primary">Disponible</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($userHasBooking)
                                            <button class="btn btn-sm btn-outline-danger cancel-booking-btn"
                                                    data-booking-id="{{ $schedule->bookings->where('member_id', auth()->id())->where('status', 'confirmed')->first()->id ?? '' }}">
                                                <i class="fas fa-times"></i> Cancelar
                                            </button>
                                        @elseif(!$isExpired && $spotsLeft > 0)
                                            <button class="btn btn-sm btn-primary book-class-btn" 
                                                    data-schedule-id="{{ $schedule->id }}"
                                                    data-class-name="{{ $class->name }}"
                                                    data-date="{{ \Carbon\Carbon::parse($schedule->date)->format('d/m/Y') }}"
                                                    data-time="{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}">
                                                <i class="fas fa-plus"></i> Reservar
                                            </button>
                                        @else
                                            <span class="text-muted">No disponible</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="card shadow">
            <div class="card-body text-center py-5">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No hay sesiones programadas</h4>
                <p class="text-muted">Esta clase no tiene sesiones programadas próximamente.</p>
                <a href="{{ route('portal.classes.index') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Ver otras clases
                </a>
            </div>
        </div>
    @endif
</div>

<!-- Modal de Confirmación de Reserva -->
<div class="modal fade" id="bookingModal" tabindex="-1" role="dialog" aria-labelledby="bookingModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookingModalLabel">Confirmar Reserva</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que quieres reservar esta clase?</p>
                <div id="booking-details" class="alert alert-info">
                    <!-- Los detalles se llenarán vía JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmBookingBtn">Confirmar Reserva</button>
            </div>
        </div>
    </div>
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
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    Esta acción no se puede deshacer.
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
    let selectedScheduleId = null;
    let selectedBookingId = null;

    // Handle booking button clicks
    document.querySelectorAll('.book-class-btn').forEach(button => {
        button.addEventListener('click', function() {
            selectedScheduleId = this.dataset.scheduleId;
            const className = this.dataset.className;
            const date = this.dataset.date;
            const time = this.dataset.time;

            document.getElementById('booking-details').innerHTML = `
                <strong>Clase:</strong> ${className}<br>
                <strong>Fecha:</strong> ${date}<br>
                <strong>Hora:</strong> ${time}
            `;

            $('#bookingModal').modal('show');
        });
    });

    // Handle booking confirmation
    document.getElementById('confirmBookingBtn').addEventListener('click', function() {
        if (selectedScheduleId) {
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

    // Handle cancel booking button clicks
    document.querySelectorAll('.cancel-booking-btn').forEach(button => {
        button.addEventListener('click', function() {
            selectedBookingId = this.dataset.bookingId;
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