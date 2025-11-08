@extends('layouts.admin-modern')

@section('title', 'Gestión de Horarios')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-clock me-2 text-primary"></i>Gestión de Horarios
            </h2>
            <p class="text-muted mb-0">Administra los horarios de trabajo del personal</p>
        </div>
        <div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createScheduleModal">
                <i class="fas fa-plus me-2"></i>Nuevo Horario
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $schedules->total() }}</h3>
                            <p class="mb-0">Total Horarios</p>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-clock fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $schedules->where('employees_count', '>', 0)->count() }}</h3>
                            <p class="mb-0">En Uso</p>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-user-check fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $schedules->where('employees_count', 0)->count() }}</h3>
                            <p class="mb-0">Disponibles</p>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-clock fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">8h</h3>
                            <p class="mb-0">Horario Promedio</p>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-chart-line fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedules Table -->
    <div class="card">
        <div class="card-header bg-transparent border-0 pt-4">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-list me-2"></i>Lista de Horarios
                </h5>
                <div class="d-flex gap-2">
                    <div class="input-group" style="width: 300px;">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control" id="searchSchedules" 
                               placeholder="Buscar horarios...">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($schedules->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">#</th>
                                <th class="border-0">Horario</th>
                                <th class="border-0">Duración</th>
                                <th class="border-0">Empleados Asignados</th>
                                <th class="border-0">Estado</th>
                                <th class="border-0 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($schedules as $schedule)
                                <tr>
                                    <td class="fw-bold">#{{ $schedule->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="schedule-icon me-3">
                                                <i class="fas fa-clock text-primary"></i>
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
                                        @if($schedule->employees_count > 0)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                <i class="fas fa-users me-1"></i>{{ $schedule->employees_count }} empleados
                                            </span>
                                        @else
                                            <span class="text-muted">
                                                <i class="fas fa-user-slash me-1"></i>Sin asignar
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($schedule->employees_count > 0)
                                            <span class="badge bg-success">En Uso</span>
                                        @else
                                            <span class="badge bg-secondary">Disponible</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-primary btn-sm" 
                                                    onclick="verHorario({{ $schedule->id }})"
                                                    data-bs-toggle="tooltip" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm" 
                                                    onclick="editarHorario({{ $schedule->id }})"
                                                    data-bs-toggle="tooltip" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            @if($schedule->employees_count == 0)
                                                <button type="button" class="btn btn-outline-danger btn-sm" 
                                                        onclick="eliminarHorario({{ $schedule->id }})"
                                                        data-bs-toggle="tooltip" title="Eliminar">
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
                <div class="text-center py-5">
                    <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay horarios registrados</h5>
                    <p class="text-muted mb-4">Comienza creando tu primer horario de trabajo</p>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createScheduleModal">
                        <i class="fas fa-plus me-2"></i>Crear Primer Horario
                    </button>
                </div>
            @endif
        </div>
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
@endsection

@push('scripts')
<script>
    // Setup modal accessibility
    setupModalAccessibility();

    // Form handling
    document.getElementById('createScheduleForm').addEventListener('submit', function(e) {
        e.preventDefault();
        crearHorario();
    });

    document.getElementById('editScheduleForm').addEventListener('submit', function(e) {
        e.preventDefault();
        actualizarHorario();
    });

    // Search functionality
    document.getElementById('searchSchedules').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });

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

    function editarHorario(id) {
        fetch(`/admin/schedules/${id}/edit`)
            .then(response => response.text())
            .then(data => {
                // For now, let's get schedule data via AJAX
                fetch(`/admin/schedules/${id}`)
                    .then(response => response.json())
                    .then(schedule => {
                        document.getElementById('edit_time_in').value = schedule.time_in.substring(0, 5);
                        document.getElementById('edit_time_out').value = schedule.time_out.substring(0, 5);
                        document.getElementById('editScheduleForm').action = `/admin/schedules/${id}`;
                        
                        new bootstrap.Modal(document.getElementById('editScheduleModal')).show();
                    });
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Error al cargar el horario');
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

    function verHorario(id) {
        fetch(`/admin/schedules/${id}`)
            .then(response => response.json())
            .then(schedule => {
                const content = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold">Información General</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>ID:</strong></td>
                                    <td>#${schedule.id}</td>
                                </tr>
                                <tr>
                                    <td><strong>Hora de Entrada:</strong></td>
                                    <td>${schedule.time_in}</td>
                                </tr>
                                <tr>
                                    <td><strong>Hora de Salida:</strong></td>
                                    <td>${schedule.time_out}</td>
                                </tr>
                                <tr>
                                    <td><strong>Duración:</strong></td>
                                    <td>${calculateDuration(schedule.time_in, schedule.time_out)}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold">Empleados Asignados</h6>
                            ${schedule.employees && schedule.employees.length > 0 ? 
                                '<ul class="list-unstyled">' + 
                                schedule.employees.map(emp => `<li><i class="fas fa-user me-2"></i>${emp.firstname} ${emp.lastname}</li>`).join('') + 
                                '</ul>' : 
                                '<p class="text-muted">No hay empleados asignados</p>'
                            }
                        </div>
                    </div>
                `;
                
                document.getElementById('viewScheduleContent').innerHTML = content;
                new bootstrap.Modal(document.getElementById('viewScheduleModal')).show();
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Error al cargar el horario');
            });
    }

    function calculateDuration(timeIn, timeOut) {
        const start = new Date(`2000-01-01 ${timeIn}`);
        const end = new Date(`2000-01-01 ${timeOut}`);
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
</script>
@endpush