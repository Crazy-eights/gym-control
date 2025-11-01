@extends('layouts.admin-modern')

@section('title', 'Detalles del Administrador')
@section('page-title', 'Detalles del Administrador')

@section('content')
<div class="animate-fade-in-up">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-success mb-1">
                <i class="fas fa-user-shield me-2"></i>{{ $admin->firstname }} {{ $admin->lastname }}
            </h2>
            <p class="text-muted mb-0">Información completa del administrador</p>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-success"><i class="fa fa-dashboard"></i> Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.admins.index') }}" class="text-success">Administradores</a></li>
                <li class="breadcrumb-item active">Detalles</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-4">
            <!-- Tarjeta de perfil -->
            <div class="modern-card text-center">
                <div class="modern-card-body">
                    <div class="profile-photo mb-3">
                        @if($admin->photo && Storage::disk('public')->exists($admin->photo))
                            <img src="{{ Storage::url($admin->photo) }}" 
                                 alt="{{ $admin->firstname }}" 
                                 class="rounded-circle shadow"
                                 style="width: 120px; height: 120px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-gradient-success d-flex align-items-center justify-content-center text-white mx-auto shadow"
                                 style="width: 120px; height: 120px; font-size: 48px;">
                                {{ strtoupper(substr($admin->firstname, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    
                    <h4 class="mb-1 fw-bold text-success">{{ $admin->firstname }} {{ $admin->lastname }}</h4>
                    <p class="text-muted mb-3">{{ $admin->username }}</p>
                    
                    @if($admin->id === auth('admin')->id())
                        <span class="badge bg-success mb-3">Tu cuenta</span>
                    @endif
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.admins.edit', $admin) }}" class="btn btn-warning btn-modern">
                            <i class="fas fa-edit me-2"></i>Editar
                        </a>
                        @if($admin->id !== auth('admin')->id())
                            <button type="button" 
                                    class="btn btn-danger btn-modern" 
                                    onclick="confirmDelete({{ $admin->id }}, '{{ $admin->username }}')">
                                <i class="fas fa-trash me-2"></i>Eliminar
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <!-- Información detallada -->
            <div class="modern-card">
                <div class="modern-card-header">
                    <h6 class="mb-0 fw-bold text-success">
                        <i class="fas fa-user me-2"></i>Información Personal
                    </h6>
                </div>
                <div class="modern-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="form-label fw-semibold text-success">Nombre:</label>
                                <p class="text-muted mb-0">{{ $admin->firstname }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="form-label fw-semibold text-success">Apellido:</label>
                                <p class="text-muted mb-0">{{ $admin->lastname }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="form-label fw-semibold text-success">Usuario:</label>
                                <p class="text-muted mb-0">{{ $admin->username }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="form-label fw-semibold text-success">Email:</label>
                                <p class="text-muted mb-0">
                                    <a href="mailto:{{ $admin->email }}" class="text-success">{{ $admin->email }}</a>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="form-label fw-semibold text-success">Fecha de Creación:</label>
                                <p class="text-muted mb-0">
                                    {{ \Carbon\Carbon::parse($admin->created_on)->format('d/m/Y') }}
                                    <small class="text-muted d-block">
                                        ({{ \Carbon\Carbon::parse($admin->created_on)->diffForHumans() }})
                                    </small>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="form-label fw-semibold text-success">ID:</label>
                                <p class="text-muted mb-0">#{{ $admin->id }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Estadísticas -->
            <div class="modern-card">
                <div class="modern-card-header">
                    <h6 class="mb-0 fw-bold text-info">
                        <i class="fas fa-chart-bar me-2"></i>Estadísticas
                    </h6>
                </div>
                <div class="modern-card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="stat-item text-center">
                                <div class="stat-icon text-primary mb-2">
                                    <i class="fas fa-sign-in-alt fa-2x"></i>
                                </div>
                                <div class="stat-label fw-semibold">Último acceso</div>
                                <div class="stat-value text-muted">Sin registros</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-item text-center">
                                <div class="stat-icon text-success mb-2">
                                    <i class="fas fa-calendar fa-2x"></i>
                                </div>
                                <div class="stat-label fw-semibold">Días activo</div>
                                <div class="stat-value text-muted">
                                    {{ \Carbon\Carbon::parse($admin->created_on)->diffInDays(now()) }} días
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-item text-center">
                                <div class="stat-icon text-info mb-2">
                                    <i class="fas fa-user-shield fa-2x"></i>
                                </div>
                                <div class="stat-label fw-semibold">Rol</div>
                                <div class="stat-value text-muted">Administrador</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 text-center">
            <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary btn-modern">
                <i class="fas fa-arrow-left me-2"></i>Volver a la lista
            </a>
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
</script>
@endpush