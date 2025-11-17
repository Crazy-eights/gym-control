@extends('layouts.admin-modern')

@section('title', 'Gestión de Posiciones')
@section('page-title', 'Gestión de Posiciones')

@section('content')
<!-- Toast Container -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;">
</div>

<div class="animate-fade-in-up px-3 pt-3">
    <!-- Header con acciones -->
    <div class="d-flex justify-content-between align-items-center mb-4 px-2">
        <div>
            <h2 class="text-success mb-1">
                <i class="fas fa-briefcase me-2"></i>Gestión de Puestos
            </h2>
            <p class="text-muted mb-0">Administra los puestos de trabajo del personal</p>
        </div>
        <button type="button" class="btn btn-success btn-modern" data-bs-toggle="modal" data-bs-target="#createPositionModal">
            <i class="fas fa-plus me-2"></i>Nuevo Puesto
        </button>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="dashboard-grid mb-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number">{{ $positions->total() }}</div>
                    <div class="stat-label">Total Puestos</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-briefcase" style="color: var(--primary-color); font-size: 2rem;"></i>
                </div>
            </div>
            <div class="mt-3">
                <small class="text-success">
                    <i class="fas fa-chart-line"></i> Puestos definidos
                </small>
            </div>
        </div>

        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number">{{ $positions->where('employees_count', '>', 0)->count() }}</div>
                    <div class="stat-label">En Uso</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-users" style="color: var(--success); font-size: 2rem;"></i>
                </div>
            </div>
            <div class="mt-3">
                <small class="text-info">
                    <i class="fas fa-user-tie"></i> Con empleados asignados
                </small>
            </div>
        </div>

        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number">{{ $positions->where('employees_count', 0)->count() }}</div>
                    <div class="stat-label">Disponibles</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-plus-square" style="color: var(--warning); font-size: 2rem;"></i>
                </div>
            </div>
            <div class="mt-3">
                <small class="text-warning">
                    <i class="fas fa-clipboard-list"></i> Sin asignar
                </small>
            </div>
        </div>
    </div>

    <!-- Filtros y búsqueda -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="search-box">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="searchPosition" class="form-control search-input" 
                       placeholder="Buscar por descripción de la posición...">
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

    <!-- Tabla de posiciones -->
    <div class="modern-card">
        <div class="card-body p-0">
            <div id="positionsTableContainer">
                @include('admin.positions.partials.table', ['positions' => $positions])
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear posición -->
<div class="modal fade" id="createPositionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-gradient-success text-white py-2">
                <h6 class="modal-title fw-bold mb-0">
                    <i class="fas fa-plus-circle me-2"></i>Nueva Posición
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="createPositionForm">
                @csrf
                <div class="modal-body p-3">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="create_description" class="form-label fw-semibold">Descripción <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="create_description" name="description" required>
                            <small class="text-muted">Ej: Instructor de Fitness, Recepcionista, Gerente</small>
                        </div>
                        {{-- Campo de tarifa oculto temporalmente
                        <div class="col-12">
                            <label for="create_rate" class="form-label fw-semibold">Tarifa por Hora <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="create_rate" name="rate" step="0.01" min="0" required>
                            </div>
                            <small class="text-muted">Tarifa base por hora para esta posición</small>
                        </div>
                        --}}
                        {{-- Campo oculto para enviar valor por defecto --}}
                        <input type="hidden" name="rate" value="0.00">
                    </div>
                </div>
                <div class="modal-footer border-0 p-3">
                    <button type="button" class="btn btn-light btn-modern" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-success btn-modern">
                        <i class="fas fa-save me-2"></i>Guardar Posición
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para editar posición -->
<div class="modal fade" id="editPositionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-gradient-success text-white py-2">
                <h6 class="modal-title fw-bold mb-0">
                    <i class="fas fa-edit me-2"></i>Editar Posición
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editPositionForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_position_id">
                <div class="modal-body p-3">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="edit_description" class="form-label fw-semibold">Descripción <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_description" name="description" required>
                            <small class="text-muted">Ej: Instructor de Fitness, Recepcionista, Gerente</small>
                        </div>
                        {{-- Campo de tarifa oculto temporalmente
                        <div class="col-12">
                            <label for="edit_rate" class="form-label fw-semibold">Tarifa por Hora <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="edit_rate" name="rate" step="0.01" min="0" required>
                            </div>
                            <small class="text-muted">Tarifa base por hora para esta posición</small>
                        </div>
                        --}}
                        {{-- Campo oculto para mantener valor actual --}}
                        <input type="hidden" id="edit_rate_hidden" name="rate">
                    </div>
                </div>
                <div class="modal-footer border-0 p-3">
                    <button type="button" class="btn btn-light btn-modern" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-success btn-modern">
                        <i class="fas fa-save me-2"></i>Actualizar Posición
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
    
    // Función para mostrar toast notifications
    function showToast(message, type = 'success') {
        const toastContainer = document.querySelector('.toast-container');
        const toastId = 'toast-' + Date.now();
        
        const toastColor = {
            'success': 'text-bg-success',
            'error': 'text-bg-danger',
            'warning': 'text-bg-warning',
            'info': 'text-bg-info'
        };
        
        const toastIcon = {
            'success': 'fas fa-check-circle',
            'error': 'fas fa-times-circle',
            'warning': 'fas fa-exclamation-triangle',
            'info': 'fas fa-info-circle'
        };
        
        const toastHtml = `
            <div id="${toastId}" class="toast ${toastColor[type]}" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center">
                        <i class="${toastIcon[type]} me-2"></i>
                        <span>${message}</span>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        
        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, {
            autohide: true,
            delay: type === 'error' ? 5000 : 3000
        });
        
        toast.show();
        
        toastElement.addEventListener('hidden.bs.toast', function() {
            this.remove();
        });
    }
    
    // Búsqueda con debounce
    document.getElementById('searchPosition').addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        const searchValue = e.target.value;
        
        searchTimeout = setTimeout(() => {
            searchPositions(searchValue);
        }, 300);
    });

    function searchPositions(search = '') {
        const spinner = document.getElementById('searchSpinner');
        const container = document.getElementById('positionsTableContainer');
        
        // Mostrar spinner
        spinner.classList.remove('d-none');
        
        fetch(`{{ route('admin.positions.index') }}?search=${encodeURIComponent(search)}`, {
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
            showToast('Error al cargar los datos', 'error');
        })
        .finally(() => {
            spinner.classList.add('d-none');
        });
    }

    // Limpiar búsqueda
    window.clearSearch = function() {
        document.getElementById('searchPosition').value = '';
        searchPositions();
    };

    // Formulario de crear posición
    document.getElementById('createPositionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        
        // Limpiar errores previos
        document.querySelectorAll('.text-danger').forEach(error => error.remove());
        document.querySelectorAll('.is-invalid').forEach(input => input.classList.remove('is-invalid'));
        
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Creando...';
            submitBtn.disabled = true;
        }
        
        fetch('{{ route('admin.positions.store') }}', {
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
                const modal = bootstrap.Modal.getInstance(document.getElementById('createPositionModal'));
                modal.hide();
                this.reset();
                showToast('Posición creada exitosamente', 'success');
                setTimeout(() => searchPositions(), 1000);
            } else if (data.errors) {
                Object.keys(data.errors).forEach(field => {
                    const input = document.getElementById(`create_${field}`);
                    if (input) {
                        input.classList.add('is-invalid');
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'text-danger small';
                        errorDiv.textContent = data.errors[field][0];
                        input.parentNode.appendChild(errorDiv);
                    }
                });
            } else {
                showToast(data.message || 'Error al crear la posición', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error al procesar la solicitud', 'error');
        })
        .finally(() => {
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Guardar Posición';
                submitBtn.disabled = false;
            }
        });
    });

    // Formulario de editar posición
    document.getElementById('editPositionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const positionId = document.getElementById('edit_position_id').value;
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        
        // Limpiar errores previos
        document.querySelectorAll('.text-danger').forEach(error => error.remove());
        document.querySelectorAll('.is-invalid').forEach(input => input.classList.remove('is-invalid'));
        
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Actualizando...';
            submitBtn.disabled = true;
        }
        
        fetch(`{{ url('admin/positions') }}/${positionId}`, {
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
                const modal = bootstrap.Modal.getInstance(document.getElementById('editPositionModal'));
                modal.hide();
                showToast('Posición actualizada exitosamente', 'success');
                setTimeout(() => searchPositions(), 1000);
            } else if (data.errors) {
                Object.keys(data.errors).forEach(field => {
                    const input = document.getElementById(`edit_${field}`);
                    if (input) {
                        input.classList.add('is-invalid');
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'text-danger small';
                        errorDiv.textContent = data.errors[field][0];
                        input.parentNode.appendChild(errorDiv);
                    }
                });
            } else {
                showToast(data.message || 'Error al actualizar la posición', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error al procesar la solicitud', 'error');
        })
        .finally(() => {
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Actualizar Posición';
                submitBtn.disabled = false;
            }
        });
    });

    function bindTableEvents() {
        // Botones de editar
        document.querySelectorAll('.edit-position').forEach(button => {
            button.addEventListener('click', function() {
                const positionId = this.dataset.id;
                loadPositionData(positionId);
            });
        });

        // Botones de eliminar
        document.querySelectorAll('.delete-position').forEach(button => {
            button.addEventListener('click', function() {
                const positionId = this.dataset.id;
                const positionName = this.dataset.name;
                deletePosition(positionId, positionName);
            });
        });
    }

    function loadPositionData(positionId) {
        fetch(`{{ url('admin/positions') }}/${positionId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            const position = data.position;
            
            document.getElementById('edit_position_id').value = position.id;
            document.getElementById('edit_description').value = position.description || '';
            document.getElementById('edit_rate_hidden').value = position.rate || '0.00';
            
            const modal = new bootstrap.Modal(document.getElementById('editPositionModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error al cargar datos:', error);
            showToast('Error al cargar los datos de la posición', 'error');
        });
    }

    function deletePosition(positionId, positionName) {
        if (confirm(`¿Está seguro de que desea eliminar la posición "${positionName}"?`)) {
            fetch(`{{ url('admin/positions') }}/${positionId}`, {
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
                    showToast(data.message, 'success');
                    searchPositions();
                } else {
                    showToast(data.message || 'Error al eliminar la posición', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error al procesar la solicitud', 'error');
            });
        }
    }

    // Bind initial events
    bindTableEvents();
});
</script>

<style>
/* Estilos personalizados para toast notifications */
.toast {
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    border-radius: 0.5rem;
}

.toast .toast-body {
    font-weight: 500;
    padding: 0.75rem 1rem;
}

.text-bg-success {
    background: linear-gradient(135deg, #28a745, #20c997) !important;
}

.text-bg-danger {
    background: linear-gradient(135deg, #dc3545, #e74c3c) !important;
}

.text-bg-warning {
    background: linear-gradient(135deg, #ffc107, #fd7e14) !important;
}

.text-bg-info {
    background: linear-gradient(135deg, #17a2b8, #6f42c1) !important;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.toast.show {
    animation: slideInRight 0.3s ease-out;
}
</style>
@endpush
@endsection