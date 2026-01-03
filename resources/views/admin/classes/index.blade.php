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

    <!-- Filtros de búsqueda -->
    <div class="content-section mb-4">
        <h5 class="section-title">
            <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
        </h5>
        
        <form id="searchFormClasses">
            @csrf
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="search" class="form-label">Buscar Clase</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search text-muted" id="searchIconClasses"></i>
                                <i class="fas fa-spinner fa-spin text-muted d-none" id="searchSpinnerClasses"></i>
                            </span>
                            <input type="text" class="form-control" id="searchClasses" name="search" value="{{ request('search') }}" placeholder="Nombre, instructor..." autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="active" class="form-label">Estado</label>
                        <select class="form-select" id="active" name="active">
                            <option value="">Todas</option>
                            <option value="1" {{ request('active') == '1' ? 'selected' : '' }}>Activas</option>
                            <option value="0" {{ request('active') == '0' ? 'selected' : '' }}>Inactivas</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="price_min" class="form-label">Precio Mín.</label>
                        <input type="number" class="form-control" id="price_min" name="price_min" value="{{ request('price_min') }}" placeholder="0.00" step="0.01">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="price_max" class="form-label">Precio Máx.</label>
                        <input type="number" class="form-control" id="price_max" name="price_max" value="{{ request('price_max') }}" placeholder="999.99" step="0.01">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success btn-modern flex-fill">
                                <i class="fas fa-search me-2"></i>Buscar
                            </button>
                            <a href="{{ route('admin.classes.index') }}" class="btn btn-outline-success btn-modern flex-fill">
                                <i class="fas fa-times me-2"></i>Limpiar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
    </div>

    <!-- Lista de clases -->
    <div class="content-section">
        <h5 class="section-title">
            <i class="fas fa-list me-2"></i>Lista de Clases
        </h5>
        
        <!-- Contenedor de resultados dinámicos -->
        <div id="classesResults">
            @include('admin.classes.partials.table', ['classes' => $classes])
        </div>
    </div>
</div>

<!-- Modal Crear Clase -->
    <div class="modal fade" id="createClassModal" tabindex="-1" aria-labelledby="createClassModalLabel" aria-hidden="true">
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
                                <label for="create_instructor_id" class="form-label fw-semibold">Instructor <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm"
                                        id="create_instructor_id"
                                        name="instructor_id"
                                        required>
                                    <option value="">Seleccionar instructor...</option>
                                    @foreach($instructors as $instructor)
                                        <option value="{{ $instructor->id }}">
                                            {{ $instructor->firstname }} {{ $instructor->lastname }}
                                            @if($instructor->position)
                                                - {{ $instructor->position->description }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Selecciona un instructor del personal registrado</small>
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

                        <!-- Nota sobre Horarios -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Horarios:</strong> Después de crear la clase, podrás agregar sus horarios desde el botón <strong><i class="fas fa-eye"></i> Ver detalles</strong>
                                </div>
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
    <div class="modal fade" id="editClassModal" tabindex="-1" aria-labelledby="editClassModalLabel" aria-hidden="true">
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
                                <label for="edit_instructor_id" class="form-label fw-semibold">Instructor <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm"
                                        id="edit_instructor_id"
                                        name="instructor_id"
                                        required>
                                    <option value="">Seleccionar instructor...</option>
                                    @foreach($instructors as $instructor)
                                        <option value="{{ $instructor->id }}">
                                            {{ $instructor->firstname }} {{ $instructor->lastname }}
                                            @if($instructor->position)
                                                - {{ $instructor->position->description }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
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

                        <!-- Sección de Horarios -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <hr>
                                <h6 class="fw-bold text-success mb-3">
                                    <i class="fas fa-calendar-alt me-2"></i>Gestionar Horarios
                                </h6>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Para agregar o modificar horarios:</strong>
                                    <ol class="mb-0 mt-2">
                                        <li>Guarda primero los cambios de la clase</li>
                                        <li>Haz clic en el botón <strong><i class="fas fa-eye"></i> Ver detalles</strong> de la clase</li>
                                        <li>Desde allí podrás gestionar todos los horarios</li>
                                    </ol>
                                </div>
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
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
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
// Auto-submit form on filter change y búsqueda AJAX
document.addEventListener('DOMContentLoaded', function() {
    let searchTimeout;
    
    // Elementos del DOM para classes
    const searchFormClasses = document.getElementById('searchFormClasses');
    const searchInputClasses = document.getElementById('searchClasses');
    const searchIconClasses = document.getElementById('searchIconClasses');
    const searchSpinnerClasses = document.getElementById('searchSpinnerClasses');
    const classesResults = document.getElementById('classesResults');
    
    // Filtros
    const activeFilter = document.getElementById('active');
    const priceMinFilter = document.getElementById('price_min');
    const priceMaxFilter = document.getElementById('price_max');
    
    // Verificar que los elementos existen
    console.log('Elementos encontrados:', {
        searchFormClasses: !!searchFormClasses,
        searchInputClasses: !!searchInputClasses,
        searchIconClasses: !!searchIconClasses,
        searchSpinnerClasses: !!searchSpinnerClasses,
        classesResults: !!classesResults,
        activeFilter: !!activeFilter,
        priceMinFilter: !!priceMinFilter,
        priceMaxFilter: !!priceMaxFilter
    });
    
    // Función para realizar búsqueda AJAX
    function performSearchClasses() {
        console.log('Iniciando búsqueda AJAX...');
        
        const params = new URLSearchParams();
        
        // Agregar parámetros de búsqueda
        const search = searchInputClasses.value.trim();
        const active = activeFilter.value;
        const price_min = priceMinFilter.value;
        const price_max = priceMaxFilter.value;
        
        if (search) params.append('search', search);
        if (active) params.append('active', active);
        if (price_min) params.append('price_min', price_min);
        if (price_max) params.append('price_max', price_max);
        params.append('ajax', '1'); // Indicar que es una petición AJAX
        
        console.log('Parámetros de búsqueda:', params.toString());
        
        // Mostrar spinner en el icono
        searchIconClasses.classList.add('d-none');
        searchSpinnerClasses.classList.remove('d-none');
        
        // Mostrar skeleton loading en la tabla
        classesResults.innerHTML = `
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
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class="fas fa-spinner fa-spin text-success me-3"></i>
                                    <span class="text-muted">Buscando clases...</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        `;
        
        // Realizar petición AJAX
        fetch(`{{ route('admin.classes.index') }}?${params.toString()}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            console.log('Respuesta recibida:', response);
            return response.text();
        })
        .then(html => {
            console.log('HTML recibido:', html.substring(0, 200) + '...');
            classesResults.innerHTML = html;
            
            // Ocultar spinner
            searchIconClasses.classList.remove('d-none');
            searchSpinnerClasses.classList.add('d-none');
        })
        .catch(error => {
            console.error('Error en la búsqueda:', error);
            
            // Ocultar spinner
            searchIconClasses.classList.remove('d-none');
            searchSpinnerClasses.classList.add('d-none');
            
            // Mostrar mensaje de error
            classesResults.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Error al realizar la búsqueda. Por favor, intenta nuevamente.
                </div>
            `;
        });
    }
    
    // Prevenir envío tradicional del formulario
    searchFormClasses.addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('Submit prevenido, ejecutando búsqueda AJAX');
        performSearchClasses();
    });
    
    // Búsqueda en tiempo real con debounce
    searchInputClasses.addEventListener('input', function() {
        console.log('Input detectado:', this.value);
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(performSearchClasses, 300); // Esperar 300ms
    });
    
    // Auto-submit para filtros
    [activeFilter, priceMinFilter, priceMaxFilter].forEach(filter => {
        filter.addEventListener('change', function() {
            console.log('Filtro cambiado:', filter.id, '=', filter.value);
            performSearchClasses();
        });
    });
});

// Inicializar DataTable solo si no hay búsqueda AJAX
$(document).ready(function() {
    // Solo inicializar DataTable si no es una búsqueda AJAX
    if (!document.querySelector('#classesResults table[id^="dataTable"]')) {
        $('#dataTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
            },
            "order": [[ 0, "asc" ]],
            "pageLength": 10,
            "responsive": true
        });
    }
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
function editClass(id, name, instructorId, durationMinutes, maxParticipants, price, difficultyLevel, active, description) {
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_instructor_id').value = instructorId || '';
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
