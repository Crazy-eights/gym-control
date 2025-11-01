@extends('layouts.socios')

@section('title', 'Clases Disponibles')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Clases Disponibles</h1>
        <div class="d-flex">
            <a href="{{ route('portal.class-bookings.my-bookings') }}" class="btn btn-primary mr-2">
                <i class="fas fa-calendar-check fa-sm text-white-50"></i> Mis Reservas
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

    <!-- Filter Options -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtros</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('portal.classes.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date">Fecha</label>
                            <input type="date" class="form-control" id="date" name="date" 
                                   value="{{ request('date') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="instructor">Instructor</label>
                            <select class="form-control" id="instructor" name="instructor">
                                <option value="">Todos los instructores</option>
                                @foreach($instructors as $instructor)
                                    <option value="{{ $instructor }}" 
                                            {{ request('instructor') == $instructor ? 'selected' : '' }}>
                                        {{ $instructor }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="duration">Duración</label>
                            <select class="form-control" id="duration" name="duration">
                                <option value="">Cualquier duración</option>
                                <option value="30" {{ request('duration') == '30' ? 'selected' : '' }}>30 minutos</option>
                                <option value="45" {{ request('duration') == '45' ? 'selected' : '' }}>45 minutos</option>
                                <option value="60" {{ request('duration') == '60' ? 'selected' : '' }}>60 minutos</option>
                                <option value="90" {{ request('duration') == '90' ? 'selected' : '' }}>90 minutos</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                                <a href="{{ route('portal.classes.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Limpiar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Classes Grid -->
    <div class="row">
        @forelse($classes as $class)
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 text-primary">{{ $class->name }}</h5>
                        <span class="badge badge-{{ $class->is_active ? 'success' : 'secondary' }}">
                            {{ $class->is_active ? 'Activa' : 'Inactiva' }}
                        </span>
                    </div>
                    <div class="card-body">
                        <p class="card-text">{{ $class->description }}</p>
                        
                        <div class="class-details mb-3">
                            <small class="text-muted d-block">
                                <i class="fas fa-user text-primary"></i> 
                                <strong>Instructor:</strong> {{ $class->instructor }}
                            </small>
                            <small class="text-muted d-block">
                                <i class="fas fa-clock text-primary"></i> 
                                <strong>Duración:</strong> {{ $class->duration }} minutos
                            </small>
                            <small class="text-muted d-block">
                                <i class="fas fa-users text-primary"></i> 
                                <strong>Capacidad máxima:</strong> {{ $class->max_capacity }} personas
                            </small>
                        </div>

                        <!-- Upcoming schedules for this class -->
                        @if($class->upcomingSchedules->count() > 0)
                            <h6 class="text-primary mb-2">Próximas sesiones:</h6>
                            @foreach($class->upcomingSchedules->take(3) as $schedule)
                                <div class="schedule-item border-left-primary pl-2 mb-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="font-weight-bold">
                                                {{ \Carbon\Carbon::parse($schedule->date)->format('d/m/Y') }}
                                            </small>
                                            <small class="text-muted d-block">
                                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - 
                                                {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                            </small>
                                        </div>
                                        <div class="text-right">
                                            @php
                                                $currentBookings = $schedule->bookings->where('status', 'confirmed')->count();
                                                $spotsLeft = $class->max_capacity - $currentBookings;
                                                $userHasBooking = $schedule->bookings->where('member_id', auth()->id())->where('status', 'confirmed')->count() > 0;
                                            @endphp
                                            
                                            <small class="d-block {{ $spotsLeft > 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $spotsLeft }} cupos
                                            </small>
                                            
                                            @if($userHasBooking)
                                                <span class="badge badge-success">Reservado</span>
                                            @elseif($spotsLeft > 0 && \Carbon\Carbon::parse($schedule->date . ' ' . $schedule->start_time) > now())
                                                <button class="btn btn-sm btn-primary book-class-btn" 
                                                        data-schedule-id="{{ $schedule->id }}"
                                                        data-class-name="{{ $class->name }}"
                                                        data-date="{{ \Carbon\Carbon::parse($schedule->date)->format('d/m/Y') }}"
                                                        data-time="{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}">
                                                    Reservar
                                                </button>
                                            @elseif($spotsLeft <= 0)
                                                <span class="badge badge-danger">Lleno</span>
                                            @else
                                                <span class="badge badge-secondary">Expirado</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            
                            @if($class->upcomingSchedules->count() > 3)
                                <small class="text-muted">
                                    <a href="{{ route('portal.classes.show', $class) }}" class="text-primary">
                                        Ver todas las {{ $class->upcomingSchedules->count() }} sesiones
                                    </a>
                                </small>
                            @endif
                        @else
                            <div class="text-center text-muted py-3">
                                <i class="fas fa-calendar-times fa-2x mb-2"></i>
                                <p class="mb-0">No hay sesiones programadas próximamente</p>
                            </div>
                        @endif
                    </div>
                    
                    @if($class->upcomingSchedules->count() > 0)
                        <div class="card-footer text-center">
                            <a href="{{ route('portal.classes.show', $class) }}" class="btn btn-outline-primary">
                                <i class="fas fa-info-circle"></i> Ver Detalles
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
@endsection