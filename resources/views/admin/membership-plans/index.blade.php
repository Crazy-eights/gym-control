@extends('layouts.admin-modern')
@section('title', 'Gestión de Planes de Membresía')
@section('page-title', 'Planes de Membresía')

@section('content')
<div class="animate-fade-in-up">
    <!-- Header con acciones -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-success mb-1">
                <i class="fas fa-id-card me-2"></i>Planes de Membresía
            </h2>
            <p class="text-muted mb-0">Gestiona los planes de membresía del gimnasio</p>
        </div>
        <button type="button" class="btn btn-success btn-modern" data-bs-toggle="modal" data-bs-target="#createPlanModal">
            <i class="fas fa-plus me-2"></i>Nuevo Plan
        </button>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="dashboard-grid mb-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number">{{ $stats['total_planes'] ?? 0 }}</div>
                    <div class="stat-label">Total Planes</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-id-card" style="color: var(--primary-color); font-size: 2rem;"></i>
                </div>
            </div>
            <div class="mt-3">
                <small class="text-success">
                    <i class="fas fa-layer-group"></i> Planes disponibles
                </small>
            </div>
        </div>

        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number">${{ number_format($stats['precio_promedio'] ?? 0, 0) }}</div>
                    <div class="stat-label">Precio Promedio</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-dollar-sign" style="color: var(--success); font-size: 2rem;"></i>
                </div>
            </div>
            <div class="mt-3">
                <small class="text-success">
                    <i class="fas fa-chart-line"></i> Promedio de precios
                </small>
            </div>
        </div>

        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number">{{ $stats['total_miembros_activos'] ?? 0 }}</div>
                    <div class="stat-label">Miembros Activos</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-users" style="color: var(--primary-color); font-size: 2rem;"></i>
                </div>
            </div>
            <div class="mt-3">
                <small class="text-success">
                    <i class="fas fa-user-check"></i> Con plan activo
                </small>
            </div>
        </div>

        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number" style="font-size: 1.5rem;">{{ Str::limit($stats['plan_mas_popular']->plan_name ?? 'N/A', 12) }}</div>
                    <div class="stat-label">Plan Más Popular</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-star" style="color: var(--warning); font-size: 2rem;"></i>
                </div>
            </div>
            <div class="mt-3">
                <small class="text-warning">
                    <i class="fas fa-crown"></i> Más elegido
                </small>
            </div>
        </div>
    </div>

    <!-- Filtros de búsqueda -->
    <div class="content-section mb-4">
        <h5 class="section-title">
            <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
        </h5>
        
        <form id="searchForm" method="GET" action="{{ route('admin.membership-plans.index') }}">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="search" class="form-label">Buscar Plan</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Nombre del plan...">
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="price_min" class="form-label">Precio Mínimo</label>
                        <input type="number" class="form-control" id="price_min" name="price_min" value="{{ request('price_min') }}" placeholder="0.00" step="0.01">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="price_max" class="form-label">Precio Máximo</label>
                        <input type="number" class="form-control" id="price_max" name="price_max" value="{{ request('price_max') }}" placeholder="999.99" step="0.01">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success btn-modern flex-fill">
                                <i class="fas fa-search me-2"></i>Buscar
                            </button>
                            <a href="{{ route('admin.membership-plans.index') }}" class="btn btn-outline-success btn-modern flex-fill">
                                <i class="fas fa-times me-2"></i>Limpiar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
    </div>

    <!-- Lista de planes -->
    <div class="content-section">
        <h5 class="section-title">
            <i class="fas fa-list me-2"></i>Planes de Membresía
        </h5>
        
        @if($planes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>Nombre del Plan</th>
                                <th>Descripción</th>
                                <th>Precio</th>
                                <th>Duración</th>
                                <th>Miembros</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($planes as $plan)
                            <tr>
                                <td>
                                    <strong>{{ $plan->plan_name }}</strong>
                                </td>
                                <td>
                                    <span class="text-muted">
                                        {{ Str::limit($plan->description, 60) }}
                                    </span>
                                </td>
                                <td style="min-width: 80px;">
                                    <span class="badge badge-modern badge-success">
                                        ${{ number_format($plan->price, 2) }}
                                    </span>
                                </td>
                                <td style="min-width: 120px;">
                                    @if($plan->duration_days <= 7)
                                        <span class="badge badge-modern badge-primary">{{ $plan->duration_days }} día{{ $plan->duration_days > 1 ? 's' : '' }}</span>
                                    @elseif($plan->duration_days <= 31)
                                        @php $weeks = round($plan->duration_days/7, 1); @endphp
                                        <span class="badge badge-modern badge-primary">{{ $weeks }} semana{{ $weeks > 1 ? 's' : '' }}</span>
                                    @elseif($plan->duration_days <= 93)
                                        @php $months = round($plan->duration_days/30, 1); @endphp
                                        <span class="badge badge-modern badge-warning">{{ $months }} mes{{ $months > 1 ? 'es' : '' }}</span>
                                    @else
                                        @php $years = round($plan->duration_days/365, 1); @endphp
                                        <span class="badge badge-modern badge-secondary">{{ $years }} año{{ $years > 1 ? 's' : '' }}</span>
                                    @endif
                                    <br><small class="text-muted">{{ $plan->duration_days }} días</small>
                                </td>
                                <td style="min-width: 80px;">
                                    <span class="badge badge-modern badge-secondary">{{ $plan->members->count() }}</span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.membership-plans.show', $plan) }}" class="btn btn-sm btn-outline-success" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-warning btn-edit-plan" 
                                                data-plan-id="{{ $plan->id }}"
                                                data-plan-name="{{ $plan->plan_name }}"
                                                data-plan-price="{{ $plan->price }}"
                                                data-plan-duration="{{ $plan->duration_days }}"
                                                data-plan-description="{{ $plan->description }}"
                                                title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" 
                                                onclick="duplicatePlan('{{ $plan->id }}')" 
                                                title="Duplicar">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                        @if($plan->members->count() == 0)
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-plan" 
                                                    data-plan-id="{{ $plan->id }}"
                                                    data-plan-name="{{ $plan->plan_name }}"
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

                <!-- Paginación -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Mostrando {{ $planes->firstItem() }} a {{ $planes->lastItem() }} de {{ $planes->total() }} resultados
                    </div>
                    <div>
                        {{ $planes->links() }}
                    </div>
                </div>
            @else
                <div class="empty-state text-center py-5">
                    <div class="empty-icon mb-3">
                        <i class="fas fa-id-card fa-3x text-muted"></i>
                    </div>
                    <h5 class="mb-2">No se encontraron planes de membresía</h5>
                    <p class="text-muted mb-3">
                        @if(request()->hasAny(['search', 'price_min', 'price_max', 'duration_filter']))
                            No hay planes que coincidan con los filtros aplicados.
                            <a href="{{ route('admin.membership-plans.index') }}" class="text-success">Limpiar filtros</a>
                        @else
                            Comienza creando tu primer plan de membresía.
                        @endif
                    </p>
                    @if(!request()->hasAny(['search', 'price_min', 'price_max', 'duration_filter']))
                        <button type="button" class="btn btn-success btn-modern" data-bs-toggle="modal" data-bs-target="#createPlanModal">
                            <i class="fas fa-plus me-2"></i>Crear Primer Plan
                        </button>
                    @endif
                </div>
            @endif
    </div>
</div>

<!-- Modal Crear Plan -->
<div class="modal fade" id="createPlanModal" tabindex="-1" aria-labelledby="createPlanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-gradient-success text-white py-2">
                <h6 class="modal-title fw-bold mb-0" id="createPlanModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Crear Nuevo Plan de Membresía
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form action="{{ route('admin.membership-plans.store') }}" method="POST" id="createPlanForm">
                @csrf
                <div class="modal-body p-3">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="create_plan_name" class="form-label fw-semibold">Nombre del Plan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="create_plan_name" name="plan_name" placeholder="Ej: Membresía Premium Mensual" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="create_price" class="form-label fw-semibold">Precio <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="create_price" name="price" placeholder="0.00" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="create_duration_days" class="form-label fw-semibold">Duración (días) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control form-control-sm" id="create_duration_days" name="duration_days" placeholder="30" min="1" required>
                            <small class="form-text text-muted">
                                <span id="create-duration-helper">Equivale a: <span class="fw-bold">-</span></span>
                            </small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-0">
                            <label for="create_description" class="form-label fw-semibold">Descripción <span class="text-danger">*</span></label>
                            <textarea class="form-control form-control-sm" id="create_description" name="description" rows="3" placeholder="Describe los beneficios y características de este plan..." required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i>Guardar Plan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Plan -->
<div class="modal fade" id="editPlanModal" tabindex="-1" aria-labelledby="editPlanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-gradient-warning text-white py-2">
                <h6 class="modal-title fw-bold mb-0" id="editPlanModalLabel">
                    <i class="fas fa-edit me-2"></i>Editar Plan de Membresía
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form method="POST" id="editPlanForm">
                @csrf
                @method('PUT')
                <div class="modal-body p-3">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="edit_plan_name" class="form-label fw-semibold">Nombre del Plan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="edit_plan_name" name="plan_name" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_price" class="form-label fw-semibold">Precio <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="edit_price" name="price" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_duration_days" class="form-label fw-semibold">Duración (días) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control form-control-sm" id="edit_duration_days" name="duration_days" min="1" required>
                            <small class="form-text text-muted">
                                <span id="edit-duration-helper">Equivale a: <span class="fw-bold">-</span></span>
                            </small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-0">
                            <label for="edit_description" class="form-label fw-semibold">Descripción <span class="text-danger">*</span></label>
                            <textarea class="form-control form-control-sm" id="edit_description" name="description" rows="3" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save me-1"></i>Actualizar Plan
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
                <p class="text-center mb-2">¿Estás seguro de que deseas eliminar el plan <strong id="deletePlanName" class="text-danger"></strong>?</p>
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
                        <i class="fas fa-trash me-1"></i> Eliminar Plan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación para duplicar -->
<div class="modal fade" id="duplicateModal" tabindex="-1" aria-labelledby="duplicateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-gradient-info text-white py-2">
                <h6 class="modal-title fw-bold mb-0" id="duplicateModalLabel">
                    <i class="fas fa-copy me-2"></i>
                    Confirmar Duplicación
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-3">
                <div class="text-center mb-3">
                    <i class="fas fa-copy fa-3x text-info mb-3"></i>
                </div>
                <p class="text-center mb-2">¿Deseas duplicar este plan de membresía?</p>
                <p class="text-center text-muted small">Se creará una copia exacta del plan que podrás modificar después.</p>
            </div>
            <div class="modal-footer bg-light border-0 py-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancelar
                </button>
                <form id="duplicateForm" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-copy me-1"></i> Duplicar Plan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form on filter change
    const filters = document.querySelectorAll('#duration_filter');
    filters.forEach(filter => {
        filter.addEventListener('change', function() {
            this.form.submit();
        });
    });

    // Función para calcular la duración equivalente
    function calculateDurationEquivalent(days) {
        if (!days || days <= 0) return '-';
        if (days === 1) return '1 día';
        if (days <= 7) return `${days} días`;
        if (days <= 31) {
            const weeks = Math.round(days / 7 * 10) / 10;
            return weeks === 1 ? '1 semana' : `${weeks} semanas`;
        }
        if (days <= 93) {
            const months = Math.round(days / 30 * 10) / 10;
            return months === 1 ? '1 mes' : `${months} meses`;
        }
        const years = Math.round(days / 365 * 10) / 10;
        return years === 1 ? '1 año' : `${years} años`;
    }

    // Event listeners para crear plan
    const createDurationInput = document.getElementById('create_duration_days');
    const createDurationHelper = document.getElementById('create-duration-helper');
    
    if (createDurationInput && createDurationHelper) {
        createDurationInput.addEventListener('input', function() {
            const equivalent = calculateDurationEquivalent(parseInt(this.value));
            createDurationHelper.innerHTML = 'Equivale a: <span class="fw-bold">' + equivalent + '</span>';
        });
    }

    // Event listeners para editar plan
    const editDurationInput = document.getElementById('edit_duration_days');
    const editDurationHelper = document.getElementById('edit-duration-helper');
    
    if (editDurationInput && editDurationHelper) {
        editDurationInput.addEventListener('input', function() {
            const equivalent = calculateDurationEquivalent(parseInt(this.value));
            editDurationHelper.innerHTML = 'Equivale a: <span class="fw-bold">' + equivalent + '</span>';
        });
    }

    // Elementos del DOM
    const editModal = document.getElementById('editPlanModal');
    const deleteModal = document.getElementById('deleteModal');
    const duplicateModal = document.getElementById('duplicateModal');
    const editForm = document.getElementById('editPlanForm');
    const deleteForm = document.getElementById('deleteForm');
    const duplicateForm = document.getElementById('duplicateForm');

    // Botones de editar
    document.querySelectorAll('.btn-edit-plan').forEach(btn => {
        btn.addEventListener('click', function() {
            const planId = this.getAttribute('data-plan-id');
            const planName = this.getAttribute('data-plan-name');
            const planPrice = this.getAttribute('data-plan-price');
            const planDuration = this.getAttribute('data-plan-duration');
            const planDescription = this.getAttribute('data-plan-description');
            
            openEditModal(planId, planName, planPrice, planDuration, planDescription);
        });
    });

    // Botones de eliminar
    document.querySelectorAll('.btn-delete-plan').forEach(btn => {
        btn.addEventListener('click', function() {
            const planId = this.getAttribute('data-plan-id');
            const planName = this.getAttribute('data-plan-name');
            confirmDelete(planId, planName);
        });
    });

    // Función para abrir modal de edición
    function openEditModal(id, planName, price, durationDays, description) {
        document.getElementById('edit_plan_name').value = planName;
        document.getElementById('edit_price').value = price;
        document.getElementById('edit_duration_days').value = durationDays;
        document.getElementById('edit_description').value = description;

        // Actualizar helper de duración
        const equivalent = calculateDurationEquivalent(parseInt(durationDays));
        document.getElementById('edit-duration-helper').innerHTML = 'Equivale a: <span class="fw-bold">' + equivalent + '</span>';

        // Configurar action del formulario
        editForm.action = `/admin/membership-plans/${id}`;

        // Mostrar modal
        const modalInstance = new bootstrap.Modal(editModal);
        modalInstance.show();
    }

    // Función para confirmar eliminación
    function confirmDelete(planId, planName) {
        document.getElementById('deletePlanName').textContent = planName;
        deleteForm.action = `/admin/membership-plans/${planId}`;
        
        const modalInstance = new bootstrap.Modal(deleteModal);
        modalInstance.show();
    }

    // Función para confirmar duplicación
    window.duplicatePlan = function(planId) {
        duplicateForm.action = `/admin/membership-plans/${planId}/duplicate`;
        
        const modalInstance = new bootstrap.Modal(duplicateModal);
        modalInstance.show();
    }

    // Feedback visual en envío de formularios
    editForm.addEventListener('submit', function() {
        const submitBtn = this.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Actualizando...';
            submitBtn.disabled = true;
        }
    });

    deleteForm.addEventListener('submit', function() {
        const submitBtn = this.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Eliminando...';
            submitBtn.disabled = true;
        }
    });

    duplicateForm.addEventListener('submit', function() {
        const submitBtn = this.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Duplicando...';
            submitBtn.disabled = true;
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.badge {
    font-size: 0.75em;
    padding: 0.375rem 0.75rem;
}
.table td {
    vertical-align: middle;
}
.table .badge {
    white-space: nowrap;
}
</style>
@endpush