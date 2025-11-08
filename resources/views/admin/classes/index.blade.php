@extends('layouts.admin-modern')

@section('title', 'Gestión de Clases')
@section('page-title', 'Gestión de Clases')

@section('content')
<div class="animate-fade-in-up">
    <!-- Header con acciones -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-success mb-1">
                <i class="fas fa-dumbbell me-2"></i>Gestión de Clases
            </h2>
            <p class="text-muted mb-0">Administra las clases y horarios del gimnasio</p>
        </div>
        <button type="button" class="btn btn-success btn-modern" data-bs-toggle="modal" data-bs-target="#createClassModal">
            <i class="fas fa-plus me-2"></i>Nueva Clase
        </button>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="dashboard-grid mb-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number">{{ $classes->count() }}</div>
                    <div class="stat-label">Total Clases</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-dumbbell" style="color: var(--primary-color); font-size: 2rem;"></i>
                </div>
            </div>
            <div class="mt-3">
                <small class="text-success">
                    <i class="fas fa-layer-group"></i> Clases disponibles
                </small>
            </div>
        </div>
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number">{{ $classes->where('active', true)->count() }}</div>
                    <div class="stat-label">Clases Activas</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-play-circle" style="color: var(--success); font-size: 2rem;"></i>
                </div>
            </div>
            <div class="mt-3">
                <small class="text-success">
                    <i class="fas fa-check"></i> En funcionamiento
                </small>
            </div>
        </div>
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number">{{ isset($availableSpots) ? $availableSpots : $classes->sum('max_participants') }}</div>
                    <div class="stat-label">Plazas Disponibles</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-users" style="color: var(--primary-color); font-size: 2rem;"></i>
                </div>
            </div>
            <div class="mt-3">
                <small class="text-info">
                    <i class="fas fa-info-circle"></i> 
                    @if(isset($totalCapacity))
                        {{ $totalCapacity - (isset($availableSpots) ? $availableSpots : 0) }} reservadas
                    @else
                        Capacidad total
                    @endif
                </small>
            </div>
        </div>
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number">${{ number_format($classes->avg('price'), 0) }}</div>
                    <div class="stat-label">Precio Promedio</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-dollar-sign" style="color: var(--success); font-size: 2rem;"></i>
                </div>
            </div>
            <div class="mt-3">
                <small class="text-success">
                    <i class="fas fa-chart-line"></i> Promedio por clase
                </small>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Lista de clases -->
    <div class="card-modern">
        <div class="card-header-modern">
            <h5 class="card-title-modern text-success">
                <i class="fas fa-list me-2"></i>Lista de Clases
            </h5>
        </div>
        <div class="card-body">
            @if($classes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>Clase</th>
                                <th>Instructor</th>
                                <th>Duración</th>
                                <th>Capacidad</th>
                                <th>Precio</th>
                                <th>Dificultad</th>
                                <th>Horarios</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                                    @foreach($classes as $class)
                                        <tr>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <strong>{{ $class->name }}</strong>
                                                    @if($class->description)
                                                        <small class="text-muted">{{ Str::limit($class->description, 50) }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <i class="fas fa-user-tie text-primary"></i>
                                                {{ $class->instructor_name }}
                                            </td>
                                            <td>
                                                <i class="fas fa-clock text-info"></i>
                                                {{ $class->duration_minutes }} min
                                            </td>
                                            <td>
                                                <i class="fas fa-users text-secondary"></i>
                                                {{ $class->max_participants }} personas
                                            </td>
                                            <td>
                                                <strong class="text-success">
                                                    ${{ number_format($class->price, 2) }}
                                                </strong>
                                            </td>
                                            <td>
                                                @switch($class->difficulty_level)
                                                    @case('principiante')
                                                        <span class="badge badge-modern badge-success">
                                                            <i class="fas fa-star"></i> Principiante
                                                        </span>
                                                        @break
                                                    @case('intermedio')
                                                        <span class="badge badge-modern badge-warning">
                                                            <i class="fas fa-star"></i><i class="fas fa-star"></i> Intermedio
                                                        </span>
                                                        @break
                                                    @case('avanzado')
                                                        <span class="badge badge-modern badge-danger">
                                                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i> Avanzado
                                                        </span>
                                                        @break
                                                @endswitch
                                            </td>
                                            <td>
                                                @if($class->schedules->count() > 0)
                                                    <div class="d-flex flex-wrap">
                                                        @foreach($class->schedules->take(3) as $schedule)
                                                            <small class="badge badge-modern badge-secondary me-1 mb-1">
                                                                {{ ucfirst($schedule->day_of_week) }} 
                                                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}
                                                            </small>
                                                        @endforeach
                                                        @if($class->schedules->count() > 3)
                                                            <small class="text-muted">+{{ $class->schedules->count() - 3 }} más</small>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted">Sin horarios</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($class->active)
                                                    <span class="badge badge-modern badge-success">
                                                        <i class="fas fa-check"></i> Activa
                                                    </span>
                                                @else
                                                    <span class="badge badge-modern badge-secondary">
                                                        <i class="fas fa-pause"></i> Inactiva
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="{{ route('admin.classes.show', $class) }}" 
                                                       class="btn btn-sm btn-outline-success" 
                                                       title="Ver detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-warning" 
                                                            title="Editar"
                                                            onclick="editClass('{{ $class->id }}', '{{ $class->name }}', '{{ $class->instructor_name }}', '{{ $class->duration_minutes }}', '{{ $class->max_participants }}', '{{ $class->price }}', '{{ $class->difficulty_level }}', '{{ $class->active }}', '{{ addslashes($class->description ?? '') }}')">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-danger" 
                                                            title="Eliminar"
                                                            onclick="confirmDelete({{ $class->id }}, '{{ $class->name }}')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $classes->links() }}
                        </div>
                    @else
                        <div class="empty-state text-center py-5">
                            <div class="empty-icon mb-3">
                                <i class="fas fa-dumbbell fa-3x text-muted"></i>
                            </div>
                            <h5 class="mb-2">No hay clases registradas</h5>
                            <p class="text-muted mb-3">Comienza creando tu primera clase del gimnasio.</p>
                            <button type="button" class="btn btn-success btn-modern" data-bs-toggle="modal" data-bs-target="#createClassModal">
                                <i class="fas fa-plus me-2"></i>Crear Primera Clase
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

    </div>

    <!-- Modal Crear Clase -->
    <div class="modal fade" id="createClassModal" tabindex="-1" aria-labelledby="createClassModalLabel" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-gradient-success text-white py-2">
                    <h6 class="modal-title fw-bold mb-0" id="createClassModalLabel">
                        <i class="fas fa-plus-circle me-2"></i>Crear Nueva Clase
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form action="{{ route('admin.classes.store') }}" method="POST" id="createClassForm">
                    @csrf
                    <div class="modal-body p-3">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="create_name" class="form-label fw-semibold">Nombre de la Clase <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control form-control-sm" 
                                       id="create_name" 
                                       name="name" 
                                       placeholder="Ej: Yoga Matutino"
                                       required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="create_instructor_name" class="form-label fw-semibold">Instructor <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control form-control-sm" 
                                       id="create_instructor_name" 
                                       name="instructor_name" 
                                       placeholder="Nombre del instructor"
                                       required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="create_duration_minutes" class="form-label fw-semibold">Duración (minutos) <span class="text-danger">*</span></label>
                                <input type="number" 
                                       class="form-control form-control-sm" 
                                       id="create_duration_minutes" 
                                       name="duration_minutes" 
                                       placeholder="60"
                                       min="15"
                                       max="300"
                                       required>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="create_max_participants" class="form-label fw-semibold">Capacidad Máxima <span class="text-danger">*</span></label>
                                <input type="number" 
                                       class="form-control form-control-sm" 
                                       id="create_max_participants" 
                                       name="max_participants" 
                                       placeholder="20"
                                       min="1"
                                       max="100"
                                       required>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="create_price" class="form-label fw-semibold">Precio <span class="text-danger">*</span></label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">$</span>
                                    <input type="number" 
                                           class="form-control" 
                                           id="create_price" 
                                           name="price" 
                                           placeholder="0.00"
                                           step="0.01"
                                           min="0"
                                           required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="create_difficulty_level" class="form-label fw-semibold">Nivel de Dificultad <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm" id="create_difficulty_level" name="difficulty_level" required>
                                    <option value="">Seleccionar nivel</option>
                                    <option value="principiante">Principiante</option>
                                    <option value="intermedio">Intermedio</option>
                                    <option value="avanzado">Avanzado</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="create_active" class="form-label fw-semibold">Estado</label>
                                <select class="form-select form-select-sm" id="create_active" name="active">
                                    <option value="1">Activa</option>
                                    <option value="0">Inactiva</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 mb-0">
                                <label for="create_description" class="form-label fw-semibold">Descripción</label>
                                <textarea class="form-control form-control-sm" 
                                          id="create_description" 
                                          name="description" 
                                          rows="3" 
                                          placeholder="Describe la clase, objetivos y beneficios..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 py-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-1"></i>Guardar Clase
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Clase -->
    <div class="modal fade" id="editClassModal" tabindex="-1" aria-labelledby="editClassModalLabel" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-gradient-warning text-white py-2">
                    <h6 class="modal-title fw-bold mb-0" id="editClassModalLabel">
                        <i class="fas fa-edit me-2"></i>Editar Clase
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form method="POST" id="editClassForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-3">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_name" class="form-label fw-semibold">Nombre de la Clase <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control form-control-sm" 
                                       id="edit_name" 
                                       name="name" 
                                       required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="edit_instructor_name" class="form-label fw-semibold">Instructor <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control form-control-sm" 
                                       id="edit_instructor_name" 
                                       name="instructor_name" 
                                       required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="edit_duration_minutes" class="form-label fw-semibold">Duración (minutos) <span class="text-danger">*</span></label>
                                <input type="number" 
                                       class="form-control form-control-sm" 
                                       id="edit_duration_minutes" 
                                       name="duration_minutes" 
                                       min="15"
                                       max="300"
                                       required>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="edit_max_participants" class="form-label fw-semibold">Capacidad Máxima <span class="text-danger">*</span></label>
                                <input type="number" 
                                       class="form-control form-control-sm" 
                                       id="edit_max_participants" 
                                       name="max_participants" 
                                       min="1"
                                       max="100"
                                       required>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="edit_price" class="form-label fw-semibold">Precio <span class="text-danger">*</span></label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">$</span>
                                    <input type="number" 
                                           class="form-control" 
                                           id="edit_price" 
                                           name="price" 
                                           step="0.01"
                                           min="0"
                                           required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_difficulty_level" class="form-label fw-semibold">Nivel de Dificultad <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm" id="edit_difficulty_level" name="difficulty_level" required>
                                    <option value="principiante">Principiante</option>
                                    <option value="intermedio">Intermedio</option>
                                    <option value="avanzado">Avanzado</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="edit_active" class="form-label fw-semibold">Estado</label>
                                <select class="form-select form-select-sm" id="edit_active" name="active">
                                    <option value="1">Activa</option>
                                    <option value="0">Inactiva</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 mb-0">
                                <label for="edit_description" class="form-label fw-semibold">Descripción</label>
                                <textarea class="form-control form-control-sm" 
                                          id="edit_description" 
                                          name="description" 
                                          rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 py-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save me-1"></i>Actualizar Clase
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<!-- Modal de confirmación para eliminar -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-gradient-danger text-white py-2">
                <h6 class="modal-title fw-bold mb-0" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmar Eliminación
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-3">
                <div class="text-center mb-3">
                    <i class="fas fa-trash-alt fa-3x text-danger mb-3"></i>
                </div>
                <p class="text-center mb-2">¿Estás seguro de que deseas eliminar la clase <strong id="className" class="text-danger"></strong>?</p>
                <p class="text-center text-muted small">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer bg-light border-0 py-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancelar
                </button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i> Eliminar Clase
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@push('scripts')
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

<script>
// Inicializar DataTable
$(document).ready(function() {
    $('#dataTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        "order": [[ 0, "asc" ]],
        "pageLength": 10,
        "responsive": true
    });
});

// Función para confirmar eliminación
function confirmDelete(classId, className) {
    document.getElementById('className').textContent = className;
    document.getElementById('deleteForm').action = '/admin/classes/' + classId;
    
    const modalElement = document.getElementById('deleteModal');
    
    // Configurar accesibilidad
    if (window.setupModalAccessibility) {
        window.setupModalAccessibility(modalElement);
    }
    
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
}

// Función para editar clase
function editClass(id, name, instructorName, durationMinutes, maxParticipants, price, difficultyLevel, active, description) {
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_instructor_name').value = instructorName;
    document.getElementById('edit_duration_minutes').value = durationMinutes;
    document.getElementById('edit_max_participants').value = maxParticipants;
    document.getElementById('edit_price').value = price;
    document.getElementById('edit_difficulty_level').value = difficultyLevel;
    document.getElementById('edit_active').value = active;
    document.getElementById('edit_description').value = description;
    
    // Actualizar action del formulario
    document.getElementById('editClassForm').action = `/admin/classes/${id}`;
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('editClassModal'));
    setupModalAccessibility(modal._element);
    modal.show();
}

// Setup modal accessibility
document.addEventListener('DOMContentLoaded', function() {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('shown.bs.modal', function() {
            if (window.setupModalAccessibility) {
                setupModalAccessibility(this);
            }
        });
    });
});
</script>
@endpush