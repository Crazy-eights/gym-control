@extends('layouts.admin-modern')

@section('title', 'Editar ' . $class->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-edit"></i> Editar Clase: {{ $class->name }}
                </h1>
                <div class="btn-group">
                    <a href="{{ route('admin.classes.show', $class->id) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                    <a href="{{ route('admin.classes.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-list"></i> Todas las Clases
                    </a>
                </div>
            </div>

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h6><i class="fas fa-exclamation-circle"></i> Errores de validación:</h6>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <form action="{{ route('admin.classes.update', $class->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <!-- Información Básica -->
                    <div class="col-lg-8">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-info-circle"></i> Información de la Clase
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name" class="form-label">
                                                <i class="fas fa-dumbbell text-primary"></i> Nombre de la Clase *
                                            </label>
                                            <input type="text" 
                                                   class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" 
                                                   name="name" 
                                                   value="{{ old('name', $class->name) }}" 
                                                   placeholder="Ej: Yoga Matutino, CrossFit Avanzado"
                                                   required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="instructor_name" class="form-label">
                                                <i class="fas fa-user-tie text-primary"></i> Instructor *
                                            </label>
                                            <input type="text" 
                                                   class="form-control @error('instructor_name') is-invalid @enderror" 
                                                   id="instructor_name" 
                                                   name="instructor_name" 
                                                   value="{{ old('instructor_name', $class->instructor_name) }}" 
                                                   placeholder="Nombre del instructor"
                                                   required>
                                            @error('instructor_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="description" class="form-label">
                                        <i class="fas fa-align-left text-primary"></i> Descripción
                                    </label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" 
                                              name="description" 
                                              rows="3" 
                                              placeholder="Describe en qué consiste la clase, beneficios, requisitos, etc.">{{ old('description', $class->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="duration_minutes" class="form-label">
                                                <i class="fas fa-clock text-primary"></i> Duración (min) *
                                            </label>
                                            <input type="number" 
                                                   class="form-control @error('duration_minutes') is-invalid @enderror" 
                                                   id="duration_minutes" 
                                                   name="duration_minutes" 
                                                   value="{{ old('duration_minutes', $class->duration_minutes) }}" 
                                                   min="15" 
                                                   max="240" 
                                                   required>
                                            @error('duration_minutes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="max_participants" class="form-label">
                                                <i class="fas fa-users text-primary"></i> Capacidad máx. *
                                            </label>
                                            <input type="number" 
                                                   class="form-control @error('max_participants') is-invalid @enderror" 
                                                   id="max_participants" 
                                                   name="max_participants" 
                                                   value="{{ old('max_participants', $class->max_participants) }}" 
                                                   min="1" 
                                                   max="100" 
                                                   required>
                                            @error('max_participants')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="price" class="form-label">
                                                <i class="fas fa-dollar-sign text-primary"></i> Precio *
                                            </label>
                                            <input type="number" 
                                                   class="form-control @error('price') is-invalid @enderror" 
                                                   id="price" 
                                                   name="price" 
                                                   value="{{ old('price', $class->price) }}" 
                                                   step="0.01" 
                                                   min="0" 
                                                   required>
                                            @error('price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="difficulty_level" class="form-label">
                                                <i class="fas fa-signal text-primary"></i> Dificultad *
                                            </label>
                                            <select class="form-control @error('difficulty_level') is-invalid @enderror" 
                                                    id="difficulty_level" 
                                                    name="difficulty_level" 
                                                    required>
                                                <option value="">Seleccionar...</option>
                                                <option value="principiante" {{ old('difficulty_level', $class->difficulty_level) == 'principiante' ? 'selected' : '' }}>
                                                    🟢 Principiante
                                                </option>
                                                <option value="intermedio" {{ old('difficulty_level', $class->difficulty_level) == 'intermedio' ? 'selected' : '' }}>
                                                    🟡 Intermedio
                                                </option>
                                                <option value="avanzado" {{ old('difficulty_level', $class->difficulty_level) == 'avanzado' ? 'selected' : '' }}>
                                                    🔴 Avanzado
                                                </option>
                                            </select>
                                            @error('difficulty_level')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Configuración y Estado -->
                    <div class="col-lg-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-cog"></i> Configuración
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="active" 
                                               name="active" 
                                               {{ old('active', $class->active) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="active">
                                            <i class="fas fa-toggle-on text-success"></i> Clase Activa
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Las clases inactivas no estarán disponibles para reservas.
                                    </small>
                                </div>

                                <hr>

                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Atención:</strong> Los cambios en capacidad o precio afectarán las reservas futuras.
                                </div>
                            </div>
                        </div>

                        <!-- Horarios de la Clase -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-calendar-alt"></i> Horarios de la Clase
                                </h6>
                            </div>
                            <div class="card-body">
                                <div id="schedules-container">
                                    @forelse($class->schedules as $index => $schedule)
                                        <div class="schedule-item border rounded p-3 mb-3">
                                            <div class="row">
                                                <div class="col-12 mb-2">
                                                    <label class="form-label">Día de la Semana</label>
                                                    <select name="schedules[{{ $index }}][day_of_week]" class="form-control form-control-sm">
                                                        <option value="0" {{ $schedule->day_of_week == 0 ? 'selected' : '' }}>Domingo</option>
                                                        <option value="1" {{ $schedule->day_of_week == 1 ? 'selected' : '' }}>Lunes</option>
                                                        <option value="2" {{ $schedule->day_of_week == 2 ? 'selected' : '' }}>Martes</option>
                                                        <option value="3" {{ $schedule->day_of_week == 3 ? 'selected' : '' }}>Miércoles</option>
                                                        <option value="4" {{ $schedule->day_of_week == 4 ? 'selected' : '' }}>Jueves</option>
                                                        <option value="5" {{ $schedule->day_of_week == 5 ? 'selected' : '' }}>Viernes</option>
                                                        <option value="6" {{ $schedule->day_of_week == 6 ? 'selected' : '' }}>Sábado</option>
                                                    </select>
                                                </div>
                                                <div class="col-6 mb-2">
                                                    <label class="form-label">Hora Inicio</label>
                                                    <input type="time" 
                                                           name="schedules[{{ $index }}][start_time]" 
                                                           class="form-control form-control-sm"
                                                           value="{{ $schedule->start_time ? \Carbon\Carbon::parse($schedule->start_time)->format('H:i') : '' }}">
                                                </div>
                                                <div class="col-6 mb-2">
                                                    <label class="form-label">Hora Fin</label>
                                                    <input type="time" 
                                                           name="schedules[{{ $index }}][end_time]" 
                                                           class="form-control form-control-sm"
                                                           value="{{ $schedule->end_time ? \Carbon\Carbon::parse($schedule->end_time)->format('H:i') : '' }}">
                                                </div>
                                                <div class="col-6 mb-2">
                                                    <label class="form-label">Fecha Inicio</label>
                                                    <input type="date" 
                                                           name="schedules[{{ $index }}][start_date]" 
                                                           class="form-control form-control-sm"
                                                           value="{{ $schedule->start_date ? $schedule->start_date->format('Y-m-d') : '' }}">
                                                </div>
                                                <div class="col-6 mb-2">
                                                    <label class="form-label">Fecha Fin</label>
                                                    <input type="date" 
                                                           name="schedules[{{ $index }}][end_date]" 
                                                           class="form-control form-control-sm"
                                                           value="{{ $schedule->end_date ? $schedule->end_date->format('Y-m-d') : '' }}">
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-check form-check-inline">
                                                        <input type="checkbox" 
                                                               name="schedules[{{ $index }}][is_recurring]" 
                                                               class="form-check-input"
                                                               {{ $schedule->is_recurring ? 'checked' : '' }}>
                                                        <label class="form-check-label">Recurrente</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input type="checkbox" 
                                                               name="schedules[{{ $index }}][active]" 
                                                               class="form-check-input"
                                                               {{ $schedule->active ? 'checked' : '' }}>
                                                        <label class="form-check-label">Activo</label>
                                                    </div>
                                                    <button type="button" class="btn btn-sm btn-outline-danger float-right remove-schedule">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-3">
                                            <i class="fas fa-calendar-times fa-2x text-muted mb-2"></i>
                                            <p class="text-muted">No hay horarios configurados</p>
                                        </div>
                                    @endforelse
                                </div>
                                
                                <button type="button" id="add-schedule" class="btn btn-outline-primary btn-sm w-100">
                                    <i class="fas fa-plus"></i> Agregar Horario
                                </button>
                            </div>
                        </div>

                        <!-- Estadísticas Actuales -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-chart-bar"></i> Estadísticas Actuales
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <small class="text-muted">Total de Reservas:</small><br>
                                    <strong class="h6">{{ $class->bookings()->count() }}</strong>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted">Horarios Configurados:</small><br>
                                    <strong class="h6">{{ $class->schedules()->count() }}</strong>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted">Última Modificación:</small><br>
                                    <strong class="h6">{{ $class->updated_at->format('d/m/Y H:i') }}</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Vista Previa -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-eye"></i> Vista Previa
                                </h6>
                            </div>
                            <div class="card-body">
                                <div id="preview-card" class="card border-left-primary">
                                    <div class="card-body py-2">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                    <span id="preview-name">{{ $class->name }}</span>
                                                </div>
                                                <div class="h6 mb-0 font-weight-bold text-gray-800">
                                                    <i class="fas fa-user-tie"></i> <span id="preview-instructor">{{ $class->instructor_name }}</span>
                                                </div>
                                                <div class="text-xs text-gray-600 mt-1">
                                                    <i class="fas fa-clock"></i> <span id="preview-duration">{{ $class->duration_minutes }}</span> min | 
                                                    <i class="fas fa-users"></i> <span id="preview-capacity">{{ $class->max_participants }}</span> personas | 
                                                    <strong>$<span id="preview-price">{{ number_format($class->price, 2) }}</span></strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow">
                            <div class="card-body py-3">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <a href="{{ route('admin.classes.show', $class) }}" class="btn btn-secondary">
                                            <i class="fas fa-times"></i> Cancelar
                                        </a>
                                        <a href="{{ route('admin.classes.index') }}" class="btn btn-outline-secondary ml-2">
                                            <i class="fas fa-list"></i> Ver Todas
                                        </a>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Guardar Cambios
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
console.log('Script de edición cargado'); // Debug

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado'); // Debug
    
    let scheduleIndex = {{ $class->schedules->count() }};
    console.log('Schedule index inicial:', scheduleIndex); // Debug
    
    // Actualizar vista previa en tiempo real
    function updatePreview() {
        const nameEl = document.getElementById('preview-name');
        const instructorEl = document.getElementById('preview-instructor');
        const durationEl = document.getElementById('preview-duration');
        const capacityEl = document.getElementById('preview-capacity');
        const priceEl = document.getElementById('preview-price');
        
        if (nameEl) nameEl.textContent = document.getElementById('name').value || 'Nombre de la Clase';
        if (instructorEl) instructorEl.textContent = document.getElementById('instructor_name').value || 'Instructor';
        if (durationEl) durationEl.textContent = document.getElementById('duration_minutes').value || '60';
        if (capacityEl) capacityEl.textContent = document.getElementById('max_participants').value || '20';
        if (priceEl) priceEl.textContent = parseFloat(document.getElementById('price').value || 0).toFixed(2);
    }

    // Eventos para actualizar la vista previa
    const previewInputs = ['name', 'instructor_name', 'duration_minutes', 'max_participants', 'price'];
    previewInputs.forEach(inputId => {
        const input = document.getElementById(inputId);
        if (input) {
            input.addEventListener('input', updatePreview);
        }
    });
    
    // Actualizar al cargar la página
    updatePreview();

    // Agregar nuevo horario - CON DEBUG
    const addScheduleBtn = document.getElementById('add-schedule');
    console.log('Botón agregar horario:', addScheduleBtn); // Debug
    
    if (addScheduleBtn) {
        console.log('Botón encontrado, agregando evento'); // Debug
        addScheduleBtn.addEventListener('click', function(e) {
            console.log('Click en agregar horario detectado'); // Debug
            e.preventDefault();
            
            const currentDate = new Date().toISOString().split('T')[0];
            
            const scheduleHtml = `
                <div class="schedule-item border rounded p-3 mb-3" style="background-color: #f8f9fa;">
                    <div class="row">
                        <div class="col-12 mb-2">
                            <label class="form-label"><strong>Día de la Semana</strong></label>
                            <select name="schedules[${scheduleIndex}][day_of_week]" class="form-control form-control-sm" required>
                                <option value="">Seleccionar día</option>
                                <option value="0">Domingo</option>
                                <option value="1">Lunes</option>
                                <option value="2">Martes</option>
                                <option value="3">Miércoles</option>
                                <option value="4">Jueves</option>
                                <option value="5">Viernes</option>
                                <option value="6">Sábado</option>
                            </select>
                        </div>
                        <div class="col-6 mb-2">
                            <label class="form-label"><strong>Hora Inicio</strong></label>
                            <input type="time" 
                                   name="schedules[${scheduleIndex}][start_time]" 
                                   class="form-control form-control-sm"
                                   required>
                        </div>
                        <div class="col-6 mb-2">
                            <label class="form-label"><strong>Hora Fin</strong></label>
                            <input type="time" 
                                   name="schedules[${scheduleIndex}][end_time]" 
                                   class="form-control form-control-sm"
                                   required>
                        </div>
                        <div class="col-6 mb-2">
                            <label class="form-label">Fecha Inicio</label>
                            <input type="date" 
                                   name="schedules[${scheduleIndex}][start_date]" 
                                   class="form-control form-control-sm"
                                   value="${currentDate}">
                        </div>
                        <div class="col-6 mb-2">
                            <label class="form-label">Fecha Fin (opcional)</label>
                            <input type="date" 
                                   name="schedules[${scheduleIndex}][end_date]" 
                                   class="form-control form-control-sm">
                        </div>
                        <div class="col-12">
                            <div class="form-check form-check-inline">
                                <input type="checkbox" 
                                       name="schedules[${scheduleIndex}][is_recurring]" 
                                       class="form-check-input"
                                       id="recurring_${scheduleIndex}">
                                <label class="form-check-label" for="recurring_${scheduleIndex}">Recurrente</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="checkbox" 
                                       name="schedules[${scheduleIndex}][active]" 
                                       class="form-check-input"
                                       id="active_${scheduleIndex}"
                                       checked>
                                <label class="form-check-label" for="active_${scheduleIndex}">Activo</label>
                            </div>
                            <button type="button" class="btn btn-sm btn-danger float-end remove-schedule">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            const container = document.getElementById('schedules-container');
            console.log('Container:', container); // Debug
            
            if (container) {
                container.insertAdjacentHTML('beforeend', scheduleHtml);
                scheduleIndex++;
                console.log('Horario agregado, nuevo índice:', scheduleIndex); // Debug
            } else {
                console.error('No se encontró el contenedor schedules-container'); // Debug
            }
        });
    } else {
        console.error('No se encontró el botón add-schedule'); // Debug
    }

    // Eliminar horario (usando delegación de eventos) - CON DEBUG
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-schedule')) {
            console.log('Click en eliminar horario detectado'); // Debug
            if (confirm('¿Está seguro de eliminar este horario?')) {
                const scheduleItem = e.target.closest('.schedule-item');
                if (scheduleItem) {
                    scheduleItem.remove();
                    console.log('Horario eliminado'); // Debug
                }
            }
        }
    });

    // Validación de horarios - CON DEBUG
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('Enviando formulario, validando horarios'); // Debug
            
            let valid = true;
            let errorMessage = '';
            
            const scheduleItems = document.querySelectorAll('.schedule-item');
            console.log('Horarios a validar:', scheduleItems.length); // Debug
            
            scheduleItems.forEach((item, index) => {
                const daySelect = item.querySelector('select[name*="[day_of_week]"]');
                const startTime = item.querySelector('input[name*="[start_time]"]');
                const endTime = item.querySelector('input[name*="[end_time]"]');
                
                if (daySelect && daySelect.value === '') {
                    errorMessage = `Horario ${index + 1}: Debe seleccionar un día de la semana.`;
                    valid = false;
                    return;
                }
                
                if (startTime && endTime && (startTime.value === '' || endTime.value === '')) {
                    errorMessage = `Horario ${index + 1}: Debe completar la hora de inicio y fin.`;
                    valid = false;
                    return;
                }
                
                if (startTime && endTime && startTime.value >= endTime.value) {
                    errorMessage = `Horario ${index + 1}: La hora de fin debe ser posterior a la hora de inicio.`;
                    valid = false;
                    return;
                }
            });
            
            if (!valid) {
                console.log('Validación falló:', errorMessage); // Debug
                alert(errorMessage);
                e.preventDefault();
            } else {
                console.log('Validación exitosa'); // Debug
            }
        });
    } else {
        console.error('No se encontró el formulario'); // Debug
    }
    
    console.log('Script de edición completamente inicializado'); // Debug
});
</script>
@endpush