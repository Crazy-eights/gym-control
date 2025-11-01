@extends('layouts.admin-modern')

@section('title', 'Detalles de ' . $class->name)
@section('page-title', 'Detalles de la Clase')

@section('content')
<div class="animate-fade-in-up">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="text-success mb-1">
                        <i class="fas fa-dumbbell me-2"></i>{{ $class->name }}
                        @if(!$class->active)
                            <span class="badge bg-secondary ms-2">Inactiva</span>
                        @endif
                    </h2>
                    <p class="text-muted mb-0">Información completa de la clase</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.classes.index') }}" class="btn btn-secondary btn-modern">
                        <i class="fas fa-arrow-left me-2"></i>Volver
                    </a>
                    <a href="{{ route('admin.classes.edit', $class->id) }}" class="btn btn-warning btn-modern">
                        <i class="fas fa-edit me-2"></i>Editar
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Información de la Clase -->
                <div class="col-lg-8">
                    <div class="modern-card mb-4">
                        <div class="modern-card-header">
                            <h6 class="mb-0 fw-bold text-success">
                                <i class="fas fa-info-circle me-2"></i>Información de la Clase
                            </h6>
                        </div>
                        <div class="modern-card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-success fw-semibold">
                                        <i class="fas fa-user-tie me-2"></i>Instructor
                                    </h6>
                                    <p class="mb-3 text-muted">{{ $class->instructor_name }}</p>

                                    <h6 class="text-success fw-semibold">
                                        <i class="fas fa-clock me-2"></i>Duración
                                    </h6>
                                    <p class="mb-3 text-muted">{{ $class->duration_minutes }} minutos</p>

                                    <h6 class="text-success fw-semibold">
                                        <i class="fas fa-signal me-2"></i>Nivel de Dificultad
                                    </h6>
                                    <p class="mb-3">
                                        @switch($class->difficulty_level)
                                            @case('principiante')
                                                <span class="badge badge-success badge-pill">
                                                    <i class="fas fa-star"></i> Principiante
                                                </span>
                                                @break
                                            @case('intermedio')
                                                <span class="badge badge-warning badge-pill">
                                                    <i class="fas fa-star"></i><i class="fas fa-star"></i> Intermedio
                                                </span>
                                                @break
                                            @case('avanzado')
                                                <span class="badge badge-danger badge-pill">
                                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i> Avanzado
                                                </span>
                                                @break
                                        @endswitch
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-primary">
                                        <i class="fas fa-users"></i> Capacidad Máxima
                                    </h6>
                                    <p class="mb-3">{{ $class->max_participants }} participantes</p>

                                    <h6 class="text-primary">
                                        <i class="fas fa-dollar-sign"></i> Precio
                                    </h6>
                                    <p class="mb-3">
                                        <strong class="text-success h5">${{ number_format($class->price, 2) }}</strong>
                                    </p>

                                    <h6 class="text-primary">
                                        <i class="fas fa-calendar-check"></i> Estado
                                    </h6>
                                    <p class="mb-3">
                                        @if($class->active)
                                            <span class="badge badge-success badge-pill">
                                                <i class="fas fa-check"></i> Activa
                                            </span>
                                        @else
                                            <span class="badge badge-secondary badge-pill">
                                                <i class="fas fa-pause"></i> Inactiva
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            @if($class->description)
                                <hr>
                                <h6 class="text-primary">
                                    <i class="fas fa-align-left"></i> Descripción
                                </h6>
                                <p class="text-gray-800">{{ $class->description }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Horarios de la Clase -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-calendar-alt"></i> Horarios de la Clase
                            </h6>
                            <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addScheduleModal">
                                <i class="fas fa-plus"></i> Agregar Horario
                            </button>
                        </div>
                        <div class="card-body">
                            @if($class->schedules->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Día</th>
                                                <th>Hora de Inicio</th>
                                                <th>Hora de Fin</th>
                                                <th>Tipo</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($class->schedules as $schedule)
                                                <tr>
                                                    <td>
                                                        <strong>{{ ucfirst($schedule->day_of_week) }}</strong>
                                                    </td>
                                                    <td>
                                                        <i class="fas fa-clock text-info"></i>
                                                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}
                                                    </td>
                                                    <td>
                                                        <i class="fas fa-clock text-warning"></i>
                                                        {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                                    </td>
                                                    <td>
                                                        @if($schedule->is_recurring)
                                                            <span class="badge badge-info">
                                                                <i class="fas fa-repeat"></i> Recurrente
                                                            </span>
                                                        @else
                                                            <span class="badge badge-secondary">
                                                                <i class="fas fa-calendar-day"></i> Único
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($schedule->active)
                                                            <span class="badge badge-success">Activo</span>
                                                        @else
                                                            <span class="badge badge-secondary">Inactivo</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <button class="btn btn-outline-primary" 
                                                                    onclick="editSchedule({{ $schedule->id }})">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button class="btn btn-outline-danger" 
                                                                    onclick="deleteSchedule({{ $schedule->id }})">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-calendar-times fa-3x text-gray-300 mb-3"></i>
                                    <h6 class="text-gray-600">No hay horarios configurados</h6>
                                    <p class="text-muted">Agrega horarios para que los miembros puedan reservar esta clase.</p>
                                    <button class="btn btn-primary" data-toggle="modal" data-target="#addScheduleModal">
                                        <i class="fas fa-plus"></i> Agregar Primer Horario
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Estadísticas y Métricas -->
                <div class="col-lg-4">
                    <!-- Estadísticas Rápidas -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-left-primary shadow h-100 py-2 mb-3">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total Reservas
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $stats['total_bookings'] }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="card border-left-success shadow h-100 py-2 mb-3">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Ingresos del Mes
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                ${{ number_format($stats['revenue_this_month'], 2) }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="card border-left-info shadow h-100 py-2 mb-3">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Sesiones Programadas
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $stats['upcoming_sessions'] }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información Adicional -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-info"></i> Información Adicional
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <small class="text-muted">Creada el:</small><br>
                                <strong>{{ $class->created_at->format('d/m/Y H:i') }}</strong>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted">Última modificación:</small><br>
                                <strong>{{ $class->updated_at->format('d/m/Y H:i') }}</strong>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted">ID de la clase:</small><br>
                                <code>{{ $class->id }}</code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Agregar Horario -->
<div class="modal fade" id="addScheduleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus"></i> Agregar Horario
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="scheduleForm" method="POST" action="{{ route('admin.schedules.store') }}">
                @csrf
                <input type="hidden" name="gym_class_id" value="{{ $class->id }}">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="day_of_week">Día de la Semana</label>
                        <select class="form-control" name="day_of_week" required>
                            <option value="">Seleccionar día...</option>
                            <option value="lunes">Lunes</option>
                            <option value="martes">Martes</option>
                            <option value="miercoles">Miércoles</option>
                            <option value="jueves">Jueves</option>
                            <option value="viernes">Viernes</option>
                            <option value="sabado">Sábado</option>
                            <option value="domingo">Domingo</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="start_time">Hora de Inicio</label>
                                <input type="time" class="form-control" name="start_time" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="end_time">Hora de Fin</label>
                                <input type="time" class="form-control" name="end_time">
                                <small class="text-muted">Se calculará automáticamente si se deja vacío</small>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_recurring" name="is_recurring" checked>
                            <label class="custom-control-label" for="is_recurring">
                                Horario Recurrente (semanal)
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Horario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function editSchedule(scheduleId) {
    // Aquí puedes implementar la edición de horarios
    alert('Función de edición en desarrollo para horario ID: ' + scheduleId);
}

function deleteSchedule(scheduleId) {
    if (confirm('¿Estás seguro de que deseas eliminar este horario?')) {
        // Implementar eliminación de horario
        alert('Función de eliminación en desarrollo para horario ID: ' + scheduleId);
    }
}
</script>
@endpush