@extends('layouts.admin-modern')

@section('title', 'Gestión de Instructores')
@section('page-title', 'Gestión de Instructores')

@section('content')
<div class="animate-fade-in-up px-3 pt-3">
    <!-- Header con acciones -->
    <div class="d-flex justify-content-between align-items-center mb-4 px-2">
        <div>
            <h2 class="text-success mb-1">
                <i class="fas fa-chalkboard-teacher me-2"></i>Gestión de Instructores
            </h2>
            <p class="text-muted mb-0">Administra el personal de entrenamiento e instrucción</p>
        </div>
        <button type="button" class="btn btn-success btn-modern" data-bs-toggle="modal" data-bs-target="#createInstructorModal">
            <i class="fas fa-plus me-2"></i>Nuevo Instructor
        </button>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="dashboard-grid mb-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number">{{ $instructors->total() }}</div>
                    <div class="stat-label">Total Instructores</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-chalkboard-teacher" style="color: var(--primary-color); font-size: 2rem;"></i>
                </div>
            </div>
            <div class="mt-3">
                <small class="text-success">
                    <i class="fas fa-user-check"></i> Personal Activo
                </small>
            </div>
        </div>

        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number">{{ $instructors->where('created_at', '>=', now()->startOfMonth())->count() }}</div>
                    <div class="stat-label">Nuevos este Mes</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-user-plus" style="color: var(--success); font-size: 2rem;"></i>
                </div>
            </div>
            <div class="mt-3">
                <small class="text-info">
                    <i class="fas fa-calendar-month"></i> Este periodo
                </small>
            </div>
        </div>

        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number">{{ $instructors->groupBy('position_id')->count() }}</div>
                    <div class="stat-label">Especialidades</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-dumbbell" style="color: var(--warning); font-size: 2rem;"></i>
                </div>
            </div>
            <div class="mt-3">
                <small class="text-warning">
                    <i class="fas fa-tags"></i> Diferentes áreas
                </small>
            </div>
        </div>
    </div>

    <!-- Filtros y búsqueda -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="search-box">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="searchInstructor" class="form-control search-input" 
                       placeholder="Buscar por nombre, apellido o ID de empleado...">
                <div id="searchSpinner" class="search-spinner d-none">
                    <div class="spinner-border spinner-border-sm" role="status"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <button type="button" class="btn btn-outline-secondary" onclick="clearSearch()">
                <i class="fas fa-eraser me-2"></i>Limpiar Filtros
            </button>
        </div>
    </div>

    <!-- Tabla de instructores -->
    <div class="modern-card">
        <div class="card-body p-0">
            <div id="instructorsTableContainer">
                @include('admin.instructors.partials.table', ['instructors' => $instructors])
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear instructor -->
<div class="modal fade" id="createInstructorModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle me-2"></i>Nuevo Instructor
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createInstructorForm">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="create_firstname" class="form-label">Nombre *</label>
                            <input type="text" class="form-control" id="create_firstname" name="firstname" required>
                        </div>
                        <div class="col-md-6">
                            <label for="create_lastname" class="form-label">Apellido *</label>
                            <input type="text" class="form-control" id="create_lastname" name="lastname" required>
                        </div>
                        <div class="col-12">
                            <label for="create_address" class="form-label">Dirección *</label>
                            <textarea class="form-control" id="create_address" name="address" rows="2" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="create_birthdate" class="form-label">Fecha de Nacimiento</label>
                            <input type="date" class="form-control" id="create_birthdate" name="birthdate">
                        </div>
                        <div class="col-md-6">
                            <label for="create_gender" class="form-label">Género *</label>
                            <select class="form-select" id="create_gender" name="gender" required>
                                <option value="">Seleccionar género...</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="create_contact_info" class="form-label">Información de Contacto *</label>
                            <input type="text" class="form-control" id="create_contact_info" name="contact_info" required>
                        </div>
                        <div class="col-md-6">
                            <label for="create_position_id" class="form-label">Posición *</label>
                            <select class="form-select" id="create_position_id" name="position_id" required>
                                <option value="">Seleccionar posición...</option>
                                @foreach($positions ?? [] as $position)
                                    <option value="{{ $position->id }}">{{ $position->description }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="create_schedule_id" class="form-label">Horario *</label>
                            <select class="form-select" id="create_schedule_id" name="schedule_id" required>
                                <option value="">Seleccionar horario...</option>
                                @foreach($schedules ?? [] as $schedule)
                                    <option value="{{ $schedule->id }}">
                                        {{ \Carbon\Carbon::parse($schedule->time_in)->format('H:i') }} - 
                                        {{ \Carbon\Carbon::parse($schedule->time_out)->format('H:i') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="create_photo" class="form-label">Foto</label>
                            <input type="text" class="form-control" id="create_photo" name="photo" placeholder="URL de la foto (opcional)">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-2"></i>Guardar Instructor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para editar instructor -->
<div class="modal fade" id="editInstructorModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Editar Instructor
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editInstructorForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_instructor_id">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit_firstname" class="form-label">Nombre *</label>
                            <input type="text" class="form-control" id="edit_firstname" name="firstname" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_lastname" class="form-label">Apellido *</label>
                            <input type="text" class="form-control" id="edit_lastname" name="lastname" required>
                        </div>
                        <div class="col-12">
                            <label for="edit_address" class="form-label">Dirección *</label>
                            <textarea class="form-control" id="edit_address" name="address" rows="2" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_birthdate" class="form-label">Fecha de Nacimiento</label>
                            <input type="date" class="form-control" id="edit_birthdate" name="birthdate">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_gender" class="form-label">Género *</label>
                            <select class="form-select" id="edit_gender" name="gender" required>
                                <option value="">Seleccionar género...</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_contact_info" class="form-label">Información de Contacto *</label>
                            <input type="text" class="form-control" id="edit_contact_info" name="contact_info" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_position_id" class="form-label">Posición *</label>
                            <select class="form-select" id="edit_position_id" name="position_id" required>
                                <option value="">Seleccionar posición...</option>
                                @foreach($positions ?? [] as $position)
                                    <option value="{{ $position->id }}">{{ $position->description }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_schedule_id" class="form-label">Horario *</label>
                            <select class="form-select" id="edit_schedule_id" name="schedule_id" required>
                                <option value="">Seleccionar horario...</option>
                                @foreach($schedules ?? [] as $schedule)
                                    <option value="{{ $schedule->id }}">
                                        {{ \Carbon\Carbon::parse($schedule->time_in)->format('H:i') }} - 
                                        {{ \Carbon\Carbon::parse($schedule->time_out)->format('H:i') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_photo" class="form-label">Foto</label>
                            <input type="text" class="form-control" id="edit_photo" name="photo" placeholder="URL de la foto (opcional)">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-2"></i>Actualizar Instructor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let searchTimeout;
    
    // Búsqueda con debounce
    document.getElementById('searchInstructor').addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        const searchValue = e.target.value;
        
        searchTimeout = setTimeout(() => {
            searchInstructors(searchValue);
        }, 300);
    });

    function searchInstructors(search = '') {
        const spinner = document.getElementById('searchSpinner');
        const container = document.getElementById('instructorsTableContainer');
        
        // Mostrar spinner
        spinner.classList.remove('d-none');
        
        // Mostrar skeleton loading
        container.innerHTML = `
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nombre Completo</th>
                            <th>Posición</th>
                            <th>Contacto</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${Array(5).fill().map(() => `
                            <tr>
                                <td><div class="skeleton skeleton-text"></div></td>
                                <td><div class="skeleton skeleton-text"></div></td>
                                <td><div class="skeleton skeleton-text"></div></td>
                                <td><div class="skeleton skeleton-text"></div></td>
                                <td><div class="skeleton skeleton-text"></div></td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        `;

        fetch(`{{ route('admin.instructors.index') }}?search=${encodeURIComponent(search)}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            container.innerHTML = data.html;
            bindTableEvents();
        })
        .catch(error => {
            console.error('Error en búsqueda:', error);
            container.innerHTML = '<div class="alert alert-danger">Error al cargar los datos</div>';
        })
        .finally(() => {
            spinner.classList.add('d-none');
        });
    }

    // Limpiar búsqueda
    window.clearSearch = function() {
        document.getElementById('searchInstructor').value = '';
        searchInstructors();
    };

    // Formulario de crear instructor
    document.getElementById('createInstructorForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('{{ route('admin.instructors.store') }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Cerrar modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('createInstructorModal'));
                modal.hide();
                
                // Limpiar formulario
                this.reset();
                
                // Mostrar mensaje
                showAlert('success', data.message);
                
                // Recargar tabla
                searchInstructors();
            } else {
                showAlert('error', 'Error al crear el instructor');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'Error al procesar la solicitud');
        });
    });

    // Formulario de editar instructor
    document.getElementById('editInstructorForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const instructorId = document.getElementById('edit_instructor_id').value;
        const formData = new FormData(this);
        
        fetch(`{{ url('admin/instructors') }}/${instructorId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Cerrar modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('editInstructorModal'));
                modal.hide();
                
                // Mostrar mensaje
                showAlert('success', data.message);
                
                // Recargar tabla
                searchInstructors();
            } else {
                showAlert('error', 'Error al actualizar el instructor');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'Error al procesar la solicitud');
        });
    });

    function bindTableEvents() {
        // Botones de editar
        document.querySelectorAll('.edit-instructor').forEach(button => {
            button.addEventListener('click', function() {
                const instructorId = this.dataset.id;
                loadInstructorData(instructorId);
            });
        });

        // Botones de eliminar
        document.querySelectorAll('.delete-instructor').forEach(button => {
            button.addEventListener('click', function() {
                const instructorId = this.dataset.id;
                const instructorName = this.dataset.name;
                deleteInstructor(instructorId, instructorName);
            });
        });
    }

    function loadInstructorData(instructorId) {
        fetch(`{{ url('admin/instructors') }}/${instructorId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            const instructor = data.instructor;
            
            // Llenar el formulario
            document.getElementById('edit_instructor_id').value = instructor.id;
            document.getElementById('edit_firstname').value = instructor.firstname || '';
            document.getElementById('edit_lastname').value = instructor.lastname || '';
            document.getElementById('edit_address').value = instructor.address || '';
            document.getElementById('edit_birthdate').value = instructor.birthdate || '';
            document.getElementById('edit_gender').value = instructor.gender || '';
            document.getElementById('edit_contact_info').value = instructor.contact_info || '';
            document.getElementById('edit_position_id').value = instructor.position_id || '';
            document.getElementById('edit_schedule_id').value = instructor.schedule_id || '';
            document.getElementById('edit_photo').value = instructor.photo || '';
            
            // Actualizar opciones de posiciones si están disponibles
            if (data.positions) {
                const positionSelect = document.getElementById('edit_position_id');
                positionSelect.innerHTML = '<option value="">Seleccionar posición...</option>';
                data.positions.forEach(position => {
                    const option = document.createElement('option');
                    option.value = position.id;
                    option.textContent = position.description;
                    if (position.id == instructor.position_id) {
                        option.selected = true;
                    }
                    positionSelect.appendChild(option);
                });
            }

            // Actualizar opciones de horarios si están disponibles
            if (data.schedules) {
                const scheduleSelect = document.getElementById('edit_schedule_id');
                scheduleSelect.innerHTML = '<option value="">Seleccionar horario...</option>';
                data.schedules.forEach(schedule => {
                    const option = document.createElement('option');
                    option.value = schedule.id;
                    const timeIn = schedule.time_in.substring(0, 5);
                    const timeOut = schedule.time_out.substring(0, 5);
                    option.textContent = `${timeIn} - ${timeOut}`;
                    if (schedule.id == instructor.schedule_id) {
                        option.selected = true;
                    }
                    scheduleSelect.appendChild(option);
                });
            }
            
            // Mostrar modal
            const modal = new bootstrap.Modal(document.getElementById('editInstructorModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error al cargar datos:', error);
            showAlert('error', 'Error al cargar los datos del instructor');
        });
    }

    function deleteInstructor(instructorId, instructorName) {
        if (confirm(`¿Está seguro de que desea eliminar al instructor ${instructorName}?`)) {
            fetch(`{{ url('admin/instructors') }}/${instructorId}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    searchInstructors();
                } else {
                    showAlert('error', 'Error al eliminar el instructor');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Error al procesar la solicitud');
            });
        }
    }

    function showAlert(type, message) {
        // Implementar sistema de alertas similar al de otros módulos
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        // Insertar al inicio del contenido
        const content = document.querySelector('.animate-fade-in-up');
        content.insertAdjacentHTML('afterbegin', alertHtml);
        
        // Auto-remover después de 5 segundos
        setTimeout(() => {
            const alert = content.querySelector('.alert');
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }

    // Bind initial events
    bindTableEvents();
});
</script>
@endpush
@endsection