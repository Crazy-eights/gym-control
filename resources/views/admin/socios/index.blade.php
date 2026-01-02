@extends('layouts.admin-modern')
@section('title', 'Gestión de Socios')
@section('page-title', 'Gestión de Socios')

@section('content')
<!-- Toast Container -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;">
</div>

<div class="animate-fade-in-up">
    <!-- Header con acciones -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-success mb-1">
                <i class="fas fa-users me-2"></i>Gestión de Socios
            </h2>
            <p class="text-muted mb-0">Administra todos los miembros de tu gimnasio</p>
        </div>
        <button type="button" class="btn btn-success btn-modern" data-bs-toggle="modal" data-bs-target="#createSocioModal">
            <i class="fas fa-plus me-2"></i>Nuevo Socio
        </button>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="dashboard-grid mb-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number">{{ $stats['total'] }}</div>
                    <div class="stat-label">Total Socios</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-users" style="color: var(--primary-color); font-size: 2rem;"></i>
                </div>
            </div>
            <div class="mt-3">
                <small class="text-success">
                    <i class="fas fa-users"></i> Registrados
                </small>
            </div>
        </div>

        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number">{{ $stats['activos'] }}</div>
                    <div class="stat-label">Socios Activos</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-check-circle" style="color: var(--success); font-size: 2rem;"></i>
                </div>
            </div>
            <div class="mt-3">
                <small class="text-success">
                    <i class="fas fa-arrow-up"></i> Con membresía vigente
                </small>
            </div>
        </div>

        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number">{{ $stats['proximos_vencimiento'] }}</div>
                    <div class="stat-label">Próximos a Vencer</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-exclamation-triangle" style="color: var(--warning); font-size: 2rem;"></i>
                </div>
            </div>
            <div class="mt-3">
                <small class="text-warning">
                    <i class="fas fa-clock"></i> Próximos 7 días
                </small>
            </div>
        </div>

        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number">{{ $stats['vencidos'] }}</div>
                    <div class="stat-label">Membresías Vencidas</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-times-circle" style="color: var(--danger); font-size: 2rem;"></i>
                </div>
            </div>
            <div class="mt-3">
                <small class="text-danger">
                    <i class="fas fa-exclamation"></i> Requieren renovación
                </small>
            </div>
        </div>
    </div>

    <!-- Filtros de búsqueda -->
    <div class="content-section mb-4">
        <h5 class="section-title">
            <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
        </h5>
        
        <form id="searchForm">
            <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="search" class="form-label">Buscar Socio</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search text-muted" id="searchIcon"></i>
                                <i class="fas fa-spinner fa-spin text-muted d-none" id="searchSpinner"></i>
                            </span>
                            <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Nombre, ID, email..." autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="status" class="form-label">Estado</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Todos</option>
                            <option value="activo" {{ request('status') == 'activo' ? 'selected' : '' }}>Activo</option>
                            <option value="vencido" {{ request('status') == 'vencido' ? 'selected' : '' }}>Vencido</option>
                            <option value="proximo_vencimiento" {{ request('status') == 'proximo_vencimiento' ? 'selected' : '' }}>Próximo a vencer</option>
                            <option value="sin_plan" {{ request('status') == 'sin_plan' ? 'selected' : '' }}>Sin plan</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="gender" class="form-label">Género</label>
                        <select class="form-select" id="gender" name="gender">
                            <option value="">Todos</option>
                            <option value="M" {{ request('gender') == 'M' ? 'selected' : '' }}>Masculino</option>
                            <option value="F" {{ request('gender') == 'F' ? 'selected' : '' }}>Femenino</option>
                            <option value="Otro" {{ request('gender') == 'Otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="plan_id" class="form-label">Plan</label>
                        <select class="form-select" id="plan_id" name="plan_id">
                            <option value="">Todos</option>
                            @foreach($planes as $plan)
                                <option value="{{ $plan->id }}" {{ request('plan_id') == $plan->id ? 'selected' : '' }}>
                                    {{ $plan->plan_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success btn-modern flex-fill">
                                <i class="fas fa-search me-2"></i>Buscar
                            </button>
                            <a href="{{ route('admin.socios.index') }}" class="btn btn-outline-success btn-modern flex-fill">
                                <i class="fas fa-times me-2"></i>Limpiar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
    </div>

    <!-- Lista de socios -->
    <div class="content-section">
        <h5 class="section-title">
            <i class="fas fa-list me-2"></i>Lista de Socios
        </h5>
        
        <!-- Contenedor de resultados dinámicos -->
        <div id="sociosResults">
            @include('admin.socios.partials.table', ['socios' => $socios])
        </div>
    </div>
</div>

<!-- Modales del sistema -->

<!-- Modal para editar socio -->
<div class="modal fade" id="editSocioModal" tabindex="-1" aria-labelledby="editSocioModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-gradient-success text-white py-2">
                <h6 class="modal-title fw-bold mb-0" id="editSocioModalLabel">
                    <i class="fas fa-user-edit me-2"></i>Editar Socio
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="editSocioForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body p-3">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit_member_id" class="form-label fw-semibold">ID del Socio</label>
                            <input type="text" class="form-control" id="edit_member_id" name="member_id" readonly style="background-color: #f8f9fa;">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_firstname" class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_firstname" name="firstname" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_lastname" class="form-label fw-semibold">Apellido <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_lastname" name="lastname" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_email" class="form-label fw-semibold">Email</label>
                            <input type="email" class="form-control" id="edit_email" name="email">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_contact_info" class="form-label fw-semibold">Teléfono</label>
                            <input type="tel" class="form-control" id="edit_contact_info" name="contact_info">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_gender" class="form-label fw-semibold">Género</label>
                            <select class="form-select" id="edit_gender" name="gender">
                                <option value="">Seleccionar género</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_birthdate" class="form-label fw-semibold">Fecha de Nacimiento</label>
                            <input type="date" class="form-control" id="edit_birthdate" name="birthdate">
                        </div>
                        <div class="col-md-12">
                            <label for="edit_address" class="form-label fw-semibold">Dirección</label>
                            <textarea class="form-control" id="edit_address" name="address" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_plan_id" class="form-label fw-semibold">Plan de Membresía</label>
                            <select class="form-select" id="edit_plan_id" name="plan_id">
                                <option value="">Seleccionar plan</option>
                                @foreach($planes as $plan)
                                    <option value="{{ $plan->id }}">{{ $plan->plan_name }} - ${{ number_format($plan->price, 2) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_status" class="form-label fw-semibold">Estado</label>
                            <select class="form-select" id="edit_status" name="status" disabled>
                                <option value="activo">Activo</option>
                                <option value="vencido">Vencido</option>
                                <option value="sin_plan">Sin Plan</option>
                                <option value="proximo_vencimiento">Próximo a Vencer</option>
                            </select>
                            <small class="text-muted">El estado se calcula automáticamente según las fechas de membresía</small>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_subscription_start_date" class="form-label fw-semibold">Fecha Inicio Membresía</label>
                            <input type="date" class="form-control" id="edit_subscription_start_date" name="subscription_start_date">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_subscription_end_date" class="form-label fw-semibold">Fecha Fin Membresía</label>
                            <input type="date" class="form-control" id="edit_subscription_end_date" name="subscription_end_date">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3">
                    <button type="button" class="btn btn-light btn-modern" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-success btn-modern">
                        <i class="fas fa-save me-2"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para crear nuevo socio -->
<div class="modal fade" id="createSocioModal" tabindex="-1" aria-labelledby="createSocioModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-gradient-success text-white py-2">
                <h6 class="modal-title fw-bold mb-0" id="createSocioModalLabel">
                    <i class="fas fa-user-plus me-2"></i>Nuevo Socio
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="createSocioForm" method="POST" enctype="multipart/form-data" action="{{ route('admin.socios.store') }}">
                @csrf
                <div class="modal-body p-3">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="create_member_id" class="form-label fw-semibold">ID del Socio</label>
                            <input type="text" class="form-control" id="create_member_id" name="member_id" value="{{ 'SOC-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT) }}" readonly style="background-color: #f8f9fa;">
                            <small class="text-muted">Se genera automáticamente</small>
                        </div>
                        <div class="col-md-6">
                            <label for="create_firstname" class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="create_firstname" name="firstname" required>
                        </div>
                        <div class="col-md-6">
                            <label for="create_lastname" class="form-label fw-semibold">Apellido <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="create_lastname" name="lastname" required>
                        </div>
                        <div class="col-md-6">
                            <label for="create_email" class="form-label fw-semibold">Email</label>
                            <input type="email" class="form-control" id="create_email" name="email">
                        </div>
                        <div class="col-md-6">
                            <label for="create_contact_info" class="form-label fw-semibold">Teléfono <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="create_contact_info" name="contact_info" required>
                        </div>
                        <div class="col-md-6">
                            <label for="create_gender" class="form-label fw-semibold">Género <span class="text-danger">*</span></label>
                            <select class="form-select" id="create_gender" name="gender" required>
                                <option value="">Seleccionar género</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="create_birthdate" class="form-label fw-semibold">Fecha de Nacimiento</label>
                            <input type="date" class="form-control" id="create_birthdate" name="birthdate">
                        </div>
                        <div class="col-md-12">
                            <label for="create_address" class="form-label fw-semibold">Dirección</label>
                            <textarea class="form-control" id="create_address" name="address" rows="2" placeholder="Dirección completa del socio"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="create_plan_id" class="form-label fw-semibold">Plan de Membresía</label>
                            <select class="form-select" id="create_plan_id" name="plan_id">
                                <option value="">Seleccionar plan</option>
                                @foreach($planes as $plan)
                                    <option value="{{ $plan->id }}">{{ $plan->plan_name }} - ${{ number_format($plan->price, 2) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="create_subscription_start_date" class="form-label fw-semibold">Fecha Inicio Membresía</label>
                            <input type="date" class="form-control" id="create_subscription_start_date" name="subscription_start_date" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="create_password" class="form-label fw-semibold">Contraseña <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="create_password" name="password" required minlength="6">
                            <small class="text-muted">Mínimo 6 caracteres para acceso al portal</small>
                        </div>
                        <div class="col-md-6">
                            <label for="create_password_confirmation" class="form-label fw-semibold">Confirmar Contraseña <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="create_password_confirmation" name="password_confirmation" required minlength="6">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3">
                    <button type="button" class="btn btn-light btn-modern" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-success btn-modern">
                        <i class="fas fa-save me-2"></i>Crear Socio
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de confirmación para eliminar -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center py-3">
                    <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                    <h5>¿Estás seguro de eliminar este socio?</h5>
                    <p class="text-danger"><strong>Esta acción no se puede deshacer.</strong></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Eliminar
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
    let searchTimeout;
    
    // Elementos del DOM
    const searchForm = document.getElementById('searchForm');
    const searchInput = document.getElementById('search');
    const searchIcon = document.getElementById('searchIcon');
    const searchSpinner = document.getElementById('searchSpinner');
    const sociosResults = document.getElementById('sociosResults');
    
    // Filtros
    const statusFilter = document.getElementById('status');
    const genderFilter = document.getElementById('gender');
    const planFilter = document.getElementById('plan_id');
    
    // Elementos de modales
    const editModal = document.getElementById('editSocioModal');
    const deleteModal = document.getElementById('deleteModal');
    const editForm = document.getElementById('editSocioForm');
    const deleteForm = document.getElementById('deleteForm');
    
    // Verificar que los elementos existen
    console.log('Elementos encontrados en socios:', {
        searchForm: !!searchForm,
        searchInput: !!searchInput,
        sociosResults: !!sociosResults,
        statusFilter: !!statusFilter,
        genderFilter: !!genderFilter,
        planFilter: !!planFilter
    });
    
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
        
        // Remover el toast del DOM después de que se oculte
        toastElement.addEventListener('hidden.bs.toast', function() {
            this.remove();
        });
    }
    
    // Prevenir envío tradicional del formulario
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Submit prevenido, ejecutando búsqueda AJAX');
            performSearch();
        });
    }
    
    // Función para realizar búsqueda AJAX
    function performSearch() {
        const params = new URLSearchParams();
        
        // Agregar parámetros de búsqueda
        const search = searchInput?.value?.trim() || '';
        const status = statusFilter?.value || '';
        const gender = genderFilter?.value || '';
        const plan_id = planFilter?.value || '';
        
        if (search) params.append('search', search);
        if (status) params.append('status', status);
        if (gender) params.append('gender', gender);
        if (plan_id) params.append('plan_id', plan_id);
        params.append('ajax', '1');
        
        // Mostrar spinner en el icono
        if (searchIcon && searchSpinner) {
            searchIcon.classList.add('d-none');
            searchSpinner.classList.remove('d-none');
        }
        
        // Mostrar skeleton loading en la tabla
        if (sociosResults) {
            sociosResults.innerHTML = `
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>Foto</th>
                                <th>ID Socio</th>
                                <th>Nombre Completo</th>
                                <th>Email</th>
                                <th>Contacto</th>
                                <th>Plan</th>
                                <th>Estado</th>
                                <th>Vencimiento</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <i class="fas fa-spinner fa-spin text-success me-3"></i>
                                        <span class="text-muted">Buscando socios...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            `;
        }
        
        // Realizar petición AJAX
        fetch(`{{ route('admin.socios.index') }}?${params.toString()}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.text();
        })
        .then(html => {
            if (sociosResults) {
                sociosResults.innerHTML = html;
                initializeEventListeners();
            }
            
            // Ocultar spinner
            if (searchIcon && searchSpinner) {
                searchIcon.classList.remove('d-none');
                searchSpinner.classList.add('d-none');
            }
        })
        .catch(error => {
            console.error('Error en la búsqueda:', error);
            
            // Ocultar spinner
            if (searchIcon && searchSpinner) {
                searchIcon.classList.remove('d-none');
                searchSpinner.classList.add('d-none');
            }
            
            // Mostrar mensaje de error
            if (sociosResults) {
                sociosResults.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Error al realizar la búsqueda. Por favor, intenta nuevamente.
                    </div>
                `;
            }
        });
    }
    
    // Event listeners de búsqueda
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            console.log('Input detectado en socios:', this.value);
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performSearch, 300);
        });
    }
    
    // Auto-submit para filtros
    [statusFilter, genderFilter, planFilter].forEach(filter => {
        if (filter) {
            filter.addEventListener('change', function() {
                console.log('Filtro cambiado:', filter.id, '=', filter.value);
                performSearch();
            });
        }
    });
    
    // Función para inicializar event listeners de elementos dinámicos
    function initializeEventListeners() {
        // Botones de editar
        document.querySelectorAll('.btn-edit-socio').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const socioId = this.getAttribute('data-socio-id');
                if (socioId) {
                    abrirModalEditar(socioId);
                }
            });
        });

        // Botones de eliminar
        document.querySelectorAll('.btn-delete-socio').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const socioId = this.getAttribute('data-socio-id');
                if (socioId && deleteForm) {
                    deleteForm.action = `{{ route('admin.socios.index') }}/${socioId}`;
                }
            });
        });
    }
    
    // Función para abrir modal de edición
    function abrirModalEditar(socioId) {
        if (!editForm) return;
        
        // Mostrar loading en el modal
        const modalLabel = document.getElementById('editSocioModalLabel');
        if (modalLabel) {
            modalLabel.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Cargando datos...';
        }

        // Hacer petición AJAX para obtener los datos del socio
        fetch(`/admin/socios/${socioId}/edit`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.socio) {
                const socio = data.socio;
                
                // Configurar acción del formulario
                editForm.action = `/admin/socios/${socioId}`;
                
                // Llenar campos del formulario
                const fields = {
                    'edit_member_id': socio.member_id || '',
                    'edit_firstname': socio.firstname || '',
                    'edit_lastname': socio.lastname || '',
                    'edit_email': socio.email || '',
                    'edit_contact_info': socio.contact_info || '',
                    'edit_gender': socio.gender || '',
                    'edit_birthdate': socio.birthdate ? socio.birthdate.split('T')[0] : '',
                    'edit_address': socio.address || '',
                    'edit_plan_id': socio.plan_id || '',
                    'edit_status': socio.status || 'activo',
                    'edit_subscription_start_date': socio.subscription_start_date ? socio.subscription_start_date.split('T')[0] : '',
                    'edit_subscription_end_date': socio.subscription_end_date ? socio.subscription_end_date.split('T')[0] : ''
                };
                
                Object.entries(fields).forEach(([fieldId, value]) => {
                    const field = document.getElementById(fieldId);
                    if (field) {
                        field.value = value;
                    }
                });
                
                // Actualizar título del modal
                if (modalLabel) {
                    modalLabel.innerHTML = `<i class="fas fa-user-edit me-2"></i>Editar Socio: ${socio.firstname} ${socio.lastname}`;
                }
                
            } else {
                throw new Error('Error al cargar los datos del socio');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            
            // Restaurar título del modal
            if (modalLabel) {
                modalLabel.innerHTML = '<i class="fas fa-user-edit me-2"></i>Error al cargar datos';
            }
            
            // Mostrar error con toast
            showToast('Error al cargar los datos del socio: ' + error.message, 'error');
        });
    }
    
    // Inicializar event listeners al cargar la página
    initializeEventListeners();
    
    // Feedback visual en formularios y manejo de envío con AJAX
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevenir envío normal del formulario
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            
            // Limpiar errores previos
            document.querySelectorAll('.text-danger').forEach(error => error.remove());
            document.querySelectorAll('.is-invalid').forEach(input => input.classList.remove('is-invalid'));
            
            // Mostrar estado de carga
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Actualizando...';
                submitBtn.disabled = true;
            }
            
            // Realizar petición AJAX
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            })
            .then(response => {
                if (response.redirected) {
                    // Si hay redirección, ir a esa página
                    window.location.href = response.url;
                    return;
                }
                return response.json();
            })
            .then(data => {
                if (data && data.success) {
                    // Cerrar modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editSocioModal'));
                    modal.hide();
                    
                    // Mostrar mensaje de éxito con toast
                    showToast('Socio actualizado exitosamente', 'success');
                    
                    // Recargar después de un breve delay para que se vea el toast
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else if (data && data.errors) {
                    // Mostrar errores de validación
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
                    showToast(data?.message || 'Error al actualizar el socio', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error al procesar la solicitud', 'error');
            })
            .finally(() => {
                // Restaurar botón
                if (submitBtn) {
                    submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Guardar Cambios';
                    submitBtn.disabled = false;
                }
            });
        });
    }

    if (deleteForm) {
        deleteForm.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Eliminando...';
                submitBtn.disabled = true;
            }
        });
    }

    // Manejar el formulario de creación de socio
    const createForm = document.getElementById('createSocioForm');
    const createModal = document.getElementById('createSocioModal');
    
    // Generar nuevo ID de socio cuando se abra el modal
    if (createModal) {
        createModal.addEventListener('show.bs.modal', function() {
            const memberIdField = document.getElementById('create_member_id');
            if (memberIdField) {
                const year = new Date().getFullYear();
                const randomNum = Math.floor(Math.random() * 9999) + 1;
                const newId = `SOC-${year}-${randomNum.toString().padStart(4, '0')}`;
                memberIdField.value = newId;
            }
        });
    }
    
    if (createForm) {
        createForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevenir envío normal del formulario
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            
            // Limpiar errores previos
            document.querySelectorAll('.text-danger').forEach(error => error.remove());
            document.querySelectorAll('.is-invalid').forEach(input => input.classList.remove('is-invalid'));
            
            // Validar que las contraseñas coincidan
            const password = document.getElementById('create_password').value;
            const confirmPassword = document.getElementById('create_password_confirmation').value;
            
            if (password !== confirmPassword) {
                document.getElementById('create_password_confirmation').classList.add('is-invalid');
                const errorDiv = document.createElement('div');
                errorDiv.className = 'text-danger small';
                errorDiv.textContent = 'Las contraseñas no coinciden';
                document.getElementById('create_password_confirmation').parentNode.appendChild(errorDiv);
                return;
            }
            
            // Mostrar estado de carga
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Creando socio...';
                submitBtn.disabled = true;
            }
            
            // Realizar petición AJAX
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            })
            .then(response => {
                if (response.redirected) {
                    // Si hay redirección, ir a esa página
                    window.location.href = response.url;
                    return;
                }
                return response.json();
            })
            .then(data => {
                if (data && data.success) {
                    // Cerrar modal
                    const modal = bootstrap.Modal.getInstance(createModal);
                    modal.hide();
                    
                    // Limpiar formulario
                    createForm.reset();
                    
                    // Mostrar mensaje de éxito con toast
                    showToast('Socio creado exitosamente', 'success');
                    
                    // Recargar después de un breve delay para que se vea el toast
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else if (data && data.errors) {
                    // Mostrar errores de validación
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
                    showToast(data?.message || 'Error al crear el socio', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error al procesar la solicitud', 'error');
            })
            .finally(() => {
                // Restaurar botón
                if (submitBtn) {
                    submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Crear Socio';
                    submitBtn.disabled = false;
                }
            });
        });
    }
});
</script>

<style>
/* Estilos personalizados para toast notifications */
.toast {
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    border-radius: 0.5rem;
    backdrop-filter: blur(10px);
}

.toast .toast-body {
    font-weight: 500;
    padding: 0.75rem 1rem;
}

.toast .btn-close {
    filter: none;
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

/* Animación suave para los toast */
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

/* Container de toast responsive */
.toast-container {
    max-width: 350px;
}

@media (max-width: 576px) {
    .toast-container {
        left: 0.5rem !important;
        right: 0.5rem !important;
        max-width: none;
    }
}
</style>
@endpush