@extends('layouts.admin-modern')

@section('title', 'Administradores')
@section('page-title', 'Administradores')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="animate-fade-in-up">
    <!-- Header con acciones -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-success mb-1">
                <i class="fas fa-user-shield me-2"></i>Administradores
            </h2>
            <p class="text-muted mb-0">Gestión de usuarios administradores del sistema</p>
        </div>
        <button type="button" class="btn btn-success btn-modern" data-bs-toggle="modal" data-bs-target="#createAdminModal">
            <i class="fas fa-plus me-2"></i>Nuevo Administrador
        </button>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="dashboard-grid mb-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number">{{ $admins->count() }}</div>
                    <div class="stat-label">Total Administradores</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-user-shield" style="color: var(--primary-color); font-size: 2rem;"></i>
                </div>
            </div>
            <div class="mt-3">
                <small class="text-success">
                    <i class="fas fa-users-cog"></i> Usuarios del sistema
                </small>
            </div>
        </div>
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number">{{ $admins->where('created_at', '>=', now()->subMonth())->count() }}</div>
                    <div class="stat-label">Nuevos este Mes</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-user-plus" style="color: var(--success); font-size: 2rem;"></i>
                </div>
            </div>
            <div class="mt-3">
                <small class="text-success">
                    <i class="fas fa-calendar-plus"></i> Registrados recientemente
                </small>
            </div>
        </div>
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number">{{ $admins->whereNotNull('last_login_at')->count() }}</div>
                    <div class="stat-label">Con Acceso Reciente</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-sign-in-alt" style="color: var(--primary-color); font-size: 2rem;"></i>
                </div>
            </div>
            <div class="mt-3">
                <small class="text-success">
                    <i class="fas fa-clock"></i> Actividad registrada
                </small>
            </div>
        </div>
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number">{{ $admins->where('is_active', true)->count() }}</div>
                    <div class="stat-label">Administradores Activos</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-user-check" style="color: var(--success); font-size: 2rem;"></i>
                </div>
            </div>
            <div class="mt-3">
                <small class="text-success">
                    <i class="fas fa-check-circle"></i> Cuentas habilitadas
                </small>
            </div>
        </div>
    </div>
                
    </div>
                
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Lista de administradores -->
    <div class="card-modern">
        <div class="card-header-modern">
            <h5 class="card-title-modern text-success">
                <i class="fas fa-list me-2"></i>Lista de Administradores del Sistema
            </h5>
        </div>
        <div class="card-body">
            @if($admins->count() > 0)
            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Usuario</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Fecha Creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($admins as $admin)
                        <tr>
                            <td style="width: 60px;">
                                @if($admin->photo && Storage::disk('public')->exists($admin->photo))
                                    <img src="{{ Storage::url($admin->photo) }}" 
                                         alt="{{ $admin->firstname }}" 
                                         class="rounded-circle"
                                         style="width: 40px; height: 40px; object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white"
                                         style="width: 40px; height: 40px; font-size: 16px;">
                                        {{ strtoupper(substr($admin->firstname, 0, 1)) }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $admin->username }}</strong>
                                @if($admin->id === auth('admin')->id())
                                    <span class="badge badge-modern badge-success ms-1">Tú</span>
                                @endif
                            </td>
                            <td>{{ $admin->firstname }} {{ $admin->lastname }}</td>
                            <td>{{ $admin->email }}</td>
                            <td>{{ \Carbon\Carbon::parse($admin->created_on)->format('d/m/Y') }}</td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.admins.show', $admin) }}" 
                                       class="btn btn-sm btn-outline-success" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-warning" 
                                            title="Editar"
                                            onclick="editAdmin('{{ $admin->id }}', '{{ $admin->firstname }}', '{{ $admin->lastname }}', '{{ $admin->username }}', '{{ $admin->email }}')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @if($admin->id !== auth('admin')->id())
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger" 
                                            title="Eliminar"
                                            onclick="confirmDelete({{ $admin->id }}, '{{ $admin->username }}')">
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
            @if($admins->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Mostrando {{ $admins->firstItem() }} a {{ $admins->lastItem() }} 
                    de {{ $admins->total() }} administradores
                </div>
                <div>
                    {{ $admins->links() }}
                </div>
            </div>
            @endif
            @else
            <div class="empty-state text-center py-5">
                <div class="empty-icon mb-3">
                    <i class="fas fa-user-shield fa-3x text-muted"></i>
                </div>
                <h5 class="mb-2">No hay administradores registrados</h5>
                <p class="text-muted mb-3">Comienza agregando el primer administrador al sistema.</p>
                <button type="button" class="btn btn-success btn-modern" data-bs-toggle="modal" data-bs-target="#createAdminModal">
                    <i class="fas fa-plus me-2"></i>Crear Primer Administrador
                </button>
            </div>
            @endif
        </div>
    </div>

    <!-- Modal Crear Administrador -->
    <div class="modal fade" id="createAdminModal" tabindex="-1" aria-labelledby="createAdminModalLabel" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-gradient-success text-white py-2">
                    <h6 class="modal-title fw-bold mb-0" id="createAdminModalLabel">
                        <i class="fas fa-user-plus me-2"></i>Crear Nuevo Administrador
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form action="{{ route('admin.admins.store') }}" method="POST" id="createAdminForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-3">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="create_firstname" class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control form-control-sm" 
                                       id="create_firstname" 
                                       name="firstname" 
                                       placeholder="Nombre"
                                       required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="create_lastname" class="form-label fw-semibold">Apellido <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control form-control-sm" 
                                       id="create_lastname" 
                                       name="lastname" 
                                       placeholder="Apellido"
                                       required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="create_username" class="form-label fw-semibold">Usuario <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control form-control-sm" 
                                       id="create_username" 
                                       name="username" 
                                       placeholder="usuario123"
                                       required>
                                <small class="form-text text-muted">Solo letras, números y guiones bajos</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="create_email" class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                <input type="email" 
                                       class="form-control form-control-sm" 
                                       id="create_email" 
                                       name="email" 
                                       placeholder="admin@gimnasio.com"
                                       required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="create_password" class="form-label fw-semibold">Contraseña <span class="text-danger">*</span></label>
                                <input type="password" 
                                       class="form-control form-control-sm" 
                                       id="create_password" 
                                       name="password" 
                                       placeholder="Mínimo 8 caracteres"
                                       required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="create_password_confirmation" class="form-label fw-semibold">Confirmar Contraseña <span class="text-danger">*</span></label>
                                <input type="password" 
                                       class="form-control form-control-sm" 
                                       id="create_password_confirmation" 
                                       name="password_confirmation" 
                                       placeholder="Repetir contraseña"
                                       required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 mb-0">
                                <label for="create_photo" class="form-label fw-semibold">Foto de Perfil</label>
                                <input type="file" 
                                       class="form-control form-control-sm" 
                                       id="create_photo" 
                                       name="photo" 
                                       accept="image/*">
                                <small class="form-text text-muted">Opcional. Formatos: JPG, PNG, GIF. Máximo 2MB</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 py-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-1"></i>Crear Administrador
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Administrador -->
    <div class="modal fade" id="editAdminModal" tabindex="-1" aria-labelledby="editAdminModalLabel" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-gradient-warning text-white py-2">
                    <h6 class="modal-title fw-bold mb-0" id="editAdminModalLabel">
                        <i class="fas fa-user-edit me-2"></i>Editar Administrador
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form method="POST" id="editAdminForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-3">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_firstname" class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control form-control-sm" 
                                       id="edit_firstname" 
                                       name="firstname" 
                                       required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="edit_lastname" class="form-label fw-semibold">Apellido <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control form-control-sm" 
                                       id="edit_lastname" 
                                       name="lastname" 
                                       required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_username" class="form-label fw-semibold">Usuario <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control form-control-sm" 
                                       id="edit_username" 
                                       name="username" 
                                       required>
                                <small class="form-text text-muted">Solo letras, números y guiones bajos</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="edit_email" class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                <input type="email" 
                                       class="form-control form-control-sm" 
                                       id="edit_email" 
                                       name="email" 
                                       required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_password" class="form-label fw-semibold">Nueva Contraseña</label>
                                <input type="password" 
                                       class="form-control form-control-sm" 
                                       id="edit_password" 
                                       name="password" 
                                       placeholder="Dejar en blanco para mantener actual">
                                <small class="form-text text-muted">Solo cambiar si deseas actualizar la contraseña</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="edit_password_confirmation" class="form-label fw-semibold">Confirmar Contraseña</label>
                                <input type="password" 
                                       class="form-control form-control-sm" 
                                       id="edit_password_confirmation" 
                                       name="password_confirmation" 
                                       placeholder="Repetir nueva contraseña">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 mb-0">
                                <label for="edit_photo" class="form-label fw-semibold">Actualizar Foto de Perfil</label>
                                <input type="file" 
                                       class="form-control form-control-sm" 
                                       id="edit_photo" 
                                       name="photo" 
                                       accept="image/*">
                                <small class="form-text text-muted">Opcional. Formatos: JPG, PNG, GIF. Máximo 2MB</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 py-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save me-1"></i>Actualizar Administrador
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación de eliminación -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-gradient-danger text-white py-2">
                    <h6 class="modal-title fw-bold mb-0" id="deleteModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Eliminación
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="text-center mb-3">
                        <i class="fas fa-user-times fa-3x text-danger mb-3"></i>
                    </div>
                    <p class="text-center mb-2">¿Estás seguro de que deseas eliminar al administrador <strong id="adminName" class="text-danger"></strong>?</p>
                    <p class="text-center text-muted small">
                        <i class="fas fa-exclamation-triangle me-1"></i>Esta acción no se puede deshacer.
                    </p>
                </div>
                <div class="modal-footer bg-light border-0 py-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
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
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(adminId, adminName) {
    document.getElementById('adminName').textContent = adminName;
    document.getElementById('deleteForm').action = `/admin/admins/${adminId}`;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    setupModalAccessibility(modal._element);
    modal.show();
}

// Función para editar administrador
function editAdmin(id, firstname, lastname, username, email) {
    document.getElementById('edit_firstname').value = firstname;
    document.getElementById('edit_lastname').value = lastname;
    document.getElementById('edit_username').value = username;
    document.getElementById('edit_email').value = email;
    
    // Limpiar campos de contraseña
    document.getElementById('edit_password').value = '';
    document.getElementById('edit_password_confirmation').value = '';
    
    // Actualizar action del formulario
    document.getElementById('editAdminForm').action = `/admin/admins/${id}`;
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('editAdminModal'));
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
    
    // Validación de contraseñas en tiempo real
    const passwordField = document.getElementById('create_password');
    const confirmPasswordField = document.getElementById('create_password_confirmation');
    const editPasswordField = document.getElementById('edit_password');
    const editConfirmPasswordField = document.getElementById('edit_password_confirmation');
    
    function validatePasswords(password, confirm) {
        if (password.value !== confirm.value) {
            confirm.setCustomValidity('Las contraseñas no coinciden');
        } else {
            confirm.setCustomValidity('');
        }
    }
    
    if (passwordField && confirmPasswordField) {
        confirmPasswordField.addEventListener('input', function() {
            validatePasswords(passwordField, confirmPasswordField);
        });
        passwordField.addEventListener('input', function() {
            validatePasswords(passwordField, confirmPasswordField);
        });
    }
    
    if (editPasswordField && editConfirmPasswordField) {
        editConfirmPasswordField.addEventListener('input', function() {
            if (editPasswordField.value || editConfirmPasswordField.value) {
                validatePasswords(editPasswordField, editConfirmPasswordField);
            } else {
                editConfirmPasswordField.setCustomValidity('');
            }
        });
        editPasswordField.addEventListener('input', function() {
            if (editPasswordField.value || editConfirmPasswordField.value) {
                validatePasswords(editPasswordField, editConfirmPasswordField);
            } else {
                editConfirmPasswordField.setCustomValidity('');
            }
        });
    }
});
</script>
@endpush