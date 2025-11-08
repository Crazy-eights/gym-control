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
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
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
                                                        @php
                                                            $days = [
                                                                0 => 'Domingo',
                                                                1 => 'Lunes', 
                                                                2 => 'Martes',
                                                                3 => 'Miércoles',
                                                                4 => 'Jueves',
                                                                5 => 'Viernes',
                                                                6 => 'Sábado'
                                                            ];
                                                        @endphp
                                                        <strong>{{ $days[$schedule->day_of_week] ?? $schedule->day_of_week }}</strong>
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
                                                            <span class="badge bg-info text-white">
                                                                <i class="fas fa-repeat"></i> Recurrente
                                                            </span>
                                                        @else
                                                            <span class="badge bg-secondary">
                                                                <i class="fas fa-calendar-day"></i> Único
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($schedule->active)
                                                            <span class="badge bg-success">Activo</span>
                                                        @else
                                                            <span class="badge bg-secondary">Inactivo</span>
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
                                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                    <h6 class="text-muted">No hay horarios configurados</h6>
                                    <p class="text-muted">Agrega horarios para que los miembros puedan reservar esta clase.</p>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
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
<div class="modal fade" id="addScheduleModal" tabindex="-1" aria-labelledby="addScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addScheduleModalLabel">
                    <i class="fas fa-plus"></i> Agregar Horario
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="scheduleForm" method="POST" action="{{ route('admin.classes.store-schedule', $class->id) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="day_of_week" class="form-label">Día de la Semana</label>
                        <select class="form-select" name="day_of_week" required>
                            <option value="">Seleccionar día...</option>
                            <option value="1">Lunes</option>
                            <option value="2">Martes</option>
                            <option value="3">Miércoles</option>
                            <option value="4">Jueves</option>
                            <option value="5">Viernes</option>
                            <option value="6">Sábado</option>
                            <option value="0">Domingo</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_time" class="form-label">Hora de Inicio</label>
                                <input type="time" class="form-control" name="start_time" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="end_time" class="form-label">Hora de Fin</label>
                                <input type="time" class="form-control" name="end_time" required>
                                <div class="form-text">La hora de fin debe ser posterior a la de inicio</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Fecha de Inicio</label>
                                <input type="date" class="form-control" name="start_date" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="end_date" class="form-label">Fecha de Fin (opcional)</label>
                                <input type="date" class="form-control" name="end_date">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_recurring" name="is_recurring" checked>
                            <label class="form-check-label" for="is_recurring">
                                Horario Recurrente (semanal)
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="active" name="active" checked>
                            <label class="form-check-label" for="active">
                                Horario Activo
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
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
console.log('Script de show.blade.php cargado'); // Debug

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado en show'); // Debug

    // Manejar envío del formulario de agregar horario
    const scheduleForm = document.getElementById('scheduleForm');
    if (scheduleForm) {
        scheduleForm.addEventListener('submit', function(e) {
            console.log('Enviando formulario de horario'); // Debug
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Mostrar estado de carga
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
            submitBtn.disabled = true;
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('Respuesta del servidor:', data); // Debug
                
                if (data.success) {
                    // Cerrar modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addScheduleModal'));
                    modal.hide();
                    
                    // Mostrar mensaje de éxito
                    showAlert('success', data.message || 'Horario agregado exitosamente');
                    
                    // Recargar página después de un momento
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showAlert('error', data.message || 'Error al agregar el horario');
                }
            })
            .catch(error => {
                console.error('Error:', error); // Debug
                showAlert('error', 'Error de conexión al servidor');
            })
            .finally(() => {
                // Restaurar botón
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }
    
    console.log('Script de show completamente inicializado'); // Debug
});

// Función para editar horario
function editSchedule(scheduleId) {
    console.log('Editando horario:', scheduleId); // Debug
    
    // Aquí puedes implementar un modal de edición o redirigir
    if (confirm('¿Deseas editar este horario? Te redirigiremos a la página de edición de la clase.')) {
        window.location.href = `{{ route('admin.classes.edit', $class->id) }}`;
    }
}

// Función para eliminar horario
function deleteSchedule(scheduleId) {
    console.log('Eliminando horario:', scheduleId); // Debug
    
    if (confirm('¿Estás seguro de que deseas eliminar este horario? Esta acción no se puede deshacer.')) {
        const deleteUrl = `{{ route('admin.classes.destroy-schedule', ['class' => $class->id, 'schedule' => ':scheduleId']) }}`.replace(':scheduleId', scheduleId);
        
        fetch(deleteUrl, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Respuesta de eliminación:', data); // Debug
            
            if (data.success) {
                showAlert('success', data.message || 'Horario eliminado exitosamente');
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showAlert('error', data.message || 'Error al eliminar el horario');
            }
        })
        .catch(error => {
            console.error('Error al eliminar:', error); // Debug
            showAlert('error', 'Error de conexión al servidor');
        });
    }
}

// Función para mostrar alertas
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.parentNode.removeChild(alertDiv);
        }
    }, 5000);
}

// Validación en tiempo real del modal
document.addEventListener('input', function(e) {
    if (e.target.name === 'start_time' || e.target.name === 'end_time') {
        const form = e.target.closest('form');
        const startTime = form.querySelector('input[name="start_time"]');
        const endTime = form.querySelector('input[name="end_time"]');
        
        if (startTime.value && endTime.value) {
            if (startTime.value >= endTime.value) {
                endTime.setCustomValidity('La hora de fin debe ser posterior a la hora de inicio');
                endTime.classList.add('is-invalid');
            } else {
                endTime.setCustomValidity('');
                endTime.classList.remove('is-invalid');
            }
        }
    }
});
</script>
@endpush