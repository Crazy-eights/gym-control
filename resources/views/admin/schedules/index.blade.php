@extends('layouts.admin-modern')

@section('title', 'Horarios de Personal')
@section('page-title', 'Horarios de Personal')

@section('content')
<div class="animate-fade-in-up px-3 pt-3">
    <!-- Header con acciones -->
    <div class="d-flex justify-content-between align-items-center mb-4 px-2">
        <div>
            <h2 class="text-success mb-1">
                <i class="fas fa-calendar-alt me-2"></i>Horarios de Personal
            </h2>
            <p class="text-muted mb-0">Administra los horarios de trabajo del personal</p>
        </div>
        <button type="button" class="btn btn-success btn-modern" data-bs-toggle="modal" data-bs-target="#createScheduleModal">
            <i class="fas fa-plus me-2"></i>Nuevo Horario
        </button>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="dashboard-grid mb-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number">{{ $schedules->total() }}</div>
                    <div class="stat-label">Total Horarios</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-clock" style="color: var(--primary-color); font-size: 2rem;"></i>
                </div>
            </div>
            <div class="mt-3">
                <small class="text-success">
                    <i class="fas fa-clock"></i> Configurados
                </small>
            </div>
        </div>

        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number">{{ $schedules->where('employees_count', '>', 0)->count() }}</div>
                    <div class="stat-label">En Uso</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-user-check" style="color: var(--success); font-size: 2rem;"></i>
                </div>
            </div>
            <div class="mt-3">
                <small class="text-success">
                    <i class="fas fa-arrow-up"></i> Con empleados asignados
                </small>
            </div>
        </div>

        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number">{{ $schedules->where('employees_count', 0)->count() }}</div>
                    <div class="stat-label">Disponibles</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-calendar-plus" style="color: var(--warning); font-size: 2rem;"></i>
                </div>
            </div>
            <div class="mt-3">
                <small class="text-warning">
                    <i class="fas fa-clock"></i> Sin asignar
                </small>
            </div>
        </div>

        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number">8h</div>
                    <div class="stat-label">Horario Promedio</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-chart-line" style="color: var(--info); font-size: 2rem;"></i>
                </div>
            </div>
            <div class="mt-3">
                <small class="text-info">
                    <i class="fas fa-chart-bar"></i> Duración típica
                </small>
            </div>
        </div>
    </div>

    <!-- Lista de horarios -->
    <div class="content-section">
        <h5 class="section-title">
            <i class="fas fa-list me-2"></i>Lista de Horarios
        </h5>
        
        @if($schedules->count() > 0)
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Horario</th>
                                <th>Duración</th>
                                <th>Instructores Asignados</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($schedules as $schedule)
                                <tr>
                                    <td><strong>#{{ $schedule->id }}</strong></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="schedule-icon me-3">
                                                <i class="fas fa-clock text-success"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">
                                                    {{ \Carbon\Carbon::parse($schedule->time_in)->format('H:i') }} -
                                                    {{ \Carbon\Carbon::parse($schedule->time_out)->format('H:i') }}
                                                </div>
                                                <small class="text-muted">
                                                    Entrada: {{ \Carbon\Carbon::parse($schedule->time_in)->format('H:i') }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $timeIn = \Carbon\Carbon::createFromFormat('H:i:s', $schedule->time_in);
                                            $timeOut = \Carbon\Carbon::createFromFormat('H:i:s', $schedule->time_out);
                                            $duration = $timeOut->diff($timeIn);
                                        @endphp
                                        <span class="badge bg-info-subtle text-info border border-info-subtle">
                                            {{ $duration->h }}h {{ $duration->i }}m
                                        </span>
                                    </td>
                                    <td>
                                        @if($schedule->employees->count() > 0)
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($schedule->employees as $employee)
                                                    <span class="badge bg-info-subtle text-info border border-info-subtle">
                                                        <i class="fas fa-user-tie me-1"></i>
                                                        {{ $employee->firstname }} {{ $employee->lastname }}
                                                        @if($employee->position)
                                                            <small>({{ $employee->position->description }})</small>
                                                        @endif
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted">
                                                <i class="fas fa-user-slash me-1"></i>Sin instructores asignados
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($schedule->employees->count() > 0)
                                            <span class="badge badge-modern badge-success">
                                                <i class="fas fa-check-circle me-1"></i>En Uso
                                            </span>
                                        @else
                                            <span class="badge badge-modern badge-secondary">
                                                <i class="fas fa-clock me-1"></i>Disponible
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button type="button" class="btn btn-sm btn-outline-success"
                                                    onclick="verHorario({{ $schedule->id }})"
                                                    title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-warning"
                                                    onclick="editarHorario({{ $schedule->id }})"
                                                    title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            @if($schedule->employees_count == 0)
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                        onclick="eliminarHorario({{ $schedule->id }})"
                                                        title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Mostrando {{ $schedules->firstItem() }} a {{ $schedules->lastItem() }} de {{ $schedules->total() }} horarios
                    </div>
                    {{ $schedules->links() }}
                </div>
            @else
                <div class="empty-state text-center py-5">
                    <div class="empty-icon mb-3">
                        <i class="fas fa-clock fa-3x text-muted"></i>
                    </div>
                    <h5 class="mb-2">No hay horarios registrados</h5>
                    <p class="text-muted mb-3">Comienza creando tu primer horario de trabajo.</p>
                    <button type="button" class="btn btn-success btn-modern" data-bs-toggle="modal" data-bs-target="#createScheduleModal">
                        <i class="fas fa-plus me-2"></i>Crear Horario
                    </button>
                </div>
            @endif
    </div>
</div>

<!-- Create Schedule Modal -->
<div class="modal fade" id="createScheduleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Nuevo Horario
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createScheduleForm" action="{{ route('admin.schedules.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="time_in" class="form-label">Hora de Entrada</label>
                            <input type="time" class="form-control" id="time_in" name="time_in" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="time_out" class="form-label">Hora de Salida</label>
                            <input type="time" class="form-control" id="time_out" name="time_out" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Información:</strong> La hora de salida debe ser posterior a la hora de entrada.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Guardar Horario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Schedule Modal -->
<div class="modal fade" id="editScheduleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Editar Horario
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editScheduleForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_time_in" class="form-label">Hora de Entrada</label>
                            <input type="time" class="form-control" id="edit_time_in" name="time_in" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_time_out" class="form-label">Hora de Salida</label>
                            <input type="time" class="form-control" id="edit_time_out" name="time_out" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Atención:</strong> Los cambios afectarán a todos los empleados asignados a este horario.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Actualizar Horario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Schedule Modal -->
<div class="modal fade" id="viewScheduleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-eye me-2"></i>Detalles del Horario
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewScheduleContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    console.log('Script iniciando...');

    // Declarar funciones globales inmediatamente para onclick
    var verHorario, editarHorario, eliminarHorario;

    // Funciones principales
    window.verHorario = function(id) {
        console.log('verHorario llamado con ID:', id);
        
        fetch(`/admin/schedules/${id}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la petición');
            }
            return response.json();
        })
        .then(data => {
            console.log('Datos recibidos:', data);
            if (data.success) {
                // Crear el contenido del modal
                const content = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold">Información General</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>ID:</strong></td>
                                    <td>#${data.id}</td>
                                </tr>
                                <tr>
                                    <td><strong>Hora de Entrada:</strong></td>
                                    <td>${data.time_in}</td>
                                </tr>
                                <tr>
                                    <td><strong>Hora de Salida:</strong></td>
                                    <td>${data.time_out}</td>
                                </tr>
                                <tr>
                                    <td><strong>Duración:</strong></td>
                                    <td>${calculateDuration(data.time_in, data.time_out)}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold">Instructores Asignados</h6>
                            ${data.employees && data.employees.length > 0 ?
                                '<div class="list-group">' +
                                data.employees.map(emp => \`
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-user-tie me-2 text-primary"></i>
                                            <strong>\${emp.firstname} \${emp.lastname}</strong>
                                            \${emp.position ? '<br><small class="text-muted">Puesto: ' + emp.position + '</small>' : ''}
                                        </div>
                                        <span class="badge bg-primary">\${emp.employee_id}</span>
                                    </div>
                                \`).join('') +
                                '</div>' :
                                '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>No hay instructores asignados</div>'
                            }
                        </div>
                    </div>
                `;

                // Insertar el contenido en el modal
                const contentElement = document.getElementById('viewScheduleContent');
                if (contentElement) {
                    contentElement.innerHTML = content;
                    // Mostrar el modal
                    new bootstrap.Modal(document.getElementById('viewScheduleModal')).show();
                } else {
                    alert('Error: No se encontró el contenedor del modal');
                }
            } else {
                alert('Error: ' + (data.message || 'Error al cargar el horario'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error: ' + error.message);
        });
    };

    window.editarHorario = function(id) {
        console.log('editarHorario llamado con ID:', id);
        
        fetch(`/admin/schedules/${id}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la petición');
            }
            return response.json();
        })
        .then(data => {
            console.log('Datos para editar:', data);
            if (data.success) {
                // Llenar los campos del formulario de editar
                const timeInField = document.getElementById('edit_time_in');
                const timeOutField = document.getElementById('edit_time_out');
                const form = document.getElementById('editScheduleForm');
                
                if (timeInField && timeOutField && form) {
                    timeInField.value = data.time_in.substring(0, 5);
                    timeOutField.value = data.time_out.substring(0, 5);
                    form.action = `/admin/schedules/${id}`;

                    // Mostrar el modal
                    new bootstrap.Modal(document.getElementById('editScheduleModal')).show();
                } else {
                    alert('Error: No se encontraron los campos del formulario de editar');
                }
            } else {
                alert('Error: ' + (data.message || 'Error al cargar el horario para editar'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error: ' + error.message);
        });
    };

    window.eliminarHorario = function(id) {
        if (confirm('¿Estás seguro de eliminar este horario?')) {
            console.log('eliminarHorario llamado con ID:', id);
            
            fetch(`/admin/schedules/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Horario eliminado correctamente');
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Error al eliminar el horario'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error: ' + error.message);
            });
        }
    };

    // Asignar funciones globales inmediatamente
    verHorario = window.verHorario;
    editarHorario = window.editarHorario;
    eliminarHorario = window.eliminarHorario;

    // Función para crear horario
    function crearHorario() {
        const form = document.getElementById('createScheduleForm');
        const formData = new FormData(form);

        fetch(form.action, {
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
                alert('Horario creado correctamente');
                form.reset();
                bootstrap.Modal.getInstance(document.getElementById('createScheduleModal')).hide();
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Error al crear el horario'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error: ' + error.message);
        });
    }

    // Función para actualizar horario
    function actualizarHorario() {
        const form = document.getElementById('editScheduleForm');
        const formData = new FormData(form);

        fetch(form.action, {
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
                alert('Horario actualizado correctamente');
                bootstrap.Modal.getInstance(document.getElementById('editScheduleModal')).hide();
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Error al actualizar el horario'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error: ' + error.message);
        });
    }

    console.log('Funciones definidas correctamente');

    // Función auxiliar para calcular duración
    function calculateDuration(timeIn, timeOut) {
        const start = new Date('1970-01-01T' + timeIn + 'Z');
        const end = new Date('1970-01-01T' + timeOut + 'Z');
        const diff = end.getTime() - start.getTime();
        const hours = Math.floor(diff / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        return `${hours}h ${minutes}m`;
    }

    /* Comentado temporalmente - Form handling
    document.getElementById('createScheduleForm').addEventListener('submit', function(e) {
        e.preventDefault();
        crearHorario();
    });

    document.getElementById('editScheduleForm').addEventListener('submit', function(e) {
        e.preventDefault();
        actualizarHorario();
    });
    */

    // Form handling - Restaurado
    const createForm = document.getElementById('createScheduleForm');
    const editForm = document.getElementById('editScheduleForm');
    
    if (createForm) {
        createForm.addEventListener('submit', function(e) {
            e.preventDefault();
            crearHorario();
        });
    }
    
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            actualizarHorario();
        });
    }

    // Search functionality
    document.getElementById('searchSchedules').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });

    /* Comentado temporalmente - Funciones complejas que pueden tener errores
    // Functions
    function crearHorario() {
        const form = document.getElementById('createScheduleForm');
        const formData = new FormData(form);

        fetch(form.action, {
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
                showAlert('success', data.message);
                document.getElementById('createScheduleModal').querySelector('.btn-close').click();
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert('error', data.message || 'Error al crear el horario');

                if (data.errors) {
                    Object.keys(data.errors).forEach(key => {
                        const input = form.querySelector(`[name="${key}"]`);
                        if (input) {
                            input.classList.add('is-invalid');
                            const feedback = input.nextElementSibling;
                            if (feedback && feedback.classList.contains('invalid-feedback')) {
                                feedback.textContent = data.errors[key][0];
                            }
                        }
                    });
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'Error de conexión');
        });
    }



    function actualizarHorario() {
        const form = document.getElementById('editScheduleForm');
        const formData = new FormData(form);

        fetch(form.action, {
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
                showAlert('success', data.message);
                document.getElementById('editScheduleModal').querySelector('.btn-close').click();
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert('error', data.message || 'Error al actualizar el horario');

                if (data.errors) {
                    Object.keys(data.errors).forEach(key => {
                        const input = form.querySelector(`[name="${key}"]`);
                        if (input) {
                            input.classList.add('is-invalid');
                            const feedback = input.nextElementSibling;
                            if (feedback && feedback.classList.contains('invalid-feedback')) {
                                feedback.textContent = data.errors[key][0];
                            }
                        }
                    });
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'Error de conexión');
        });
    }

    function eliminarHorario(id) {
        if (confirm('¿Estás seguro de eliminar este horario? Esta acción no se puede deshacer.')) {
            fetch(`/admin/schedules/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert('error', data.message || 'Error al eliminar el horario');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Error de conexión');
            });
        }
    }

    // Función auxiliar para calcular duración
    function calculateDuration(timeIn, timeOut) {
        const start = new Date('1970-01-01T' + timeIn + 'Z');
        const end = new Date('1970-01-01T' + timeOut + 'Z');
        const diff = end - start;
        const hours = Math.floor(diff / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        return `${hours}h ${minutes}m`;
    }

    function showAlert(type, message) {
        // Create alert element
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

    // Clear validation on input change
    document.querySelectorAll('input').forEach(input => {
        input.addEventListener('input', function() {
            this.classList.remove('is-invalid');
            const feedback = this.nextElementSibling;
            if (feedback && feedback.classList.contains('invalid-feedback')) {
                feedback.textContent = '';
            }
        });
    });
    
    console.log('Script completado sin errores');
</script>

@endsection