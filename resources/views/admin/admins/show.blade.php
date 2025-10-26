@extends('layouts.admin')

@section('title', 'Detalles del Administrador')

@section('content')
    <div class="content-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">Detalles del Administrador</h1>
                <p class="text-muted mb-0">Información completa del administrador</p>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.admins.index') }}">Administradores</a></li>
                    <li class="breadcrumb-item active">Detalles</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <!-- Tarjeta de perfil -->
            <div class="box text-center">
                <div class="box-body">
                    <div class="profile-photo mb-3">
                        @if($admin->photo && Storage::disk('public')->exists($admin->photo))
                            <img src="{{ Storage::url($admin->photo) }}" 
                                 alt="{{ $admin->firstname }}" 
                                 class="rounded-circle"
                                 style="width: 120px; height: 120px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white mx-auto"
                                 style="width: 120px; height: 120px; font-size: 48px;">
                                {{ strtoupper(substr($admin->firstname, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    
                    <h4 class="mb-1">{{ $admin->firstname }} {{ $admin->lastname }}</h4>
                    <p class="text-muted mb-3">{{ $admin->username }}</p>
                    
                    @if($admin->id === auth('admin')->id())
                        <span class="badge bg-success mb-3">Tu cuenta</span>
                    @endif
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.admins.edit', $admin) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        @if($admin->id !== auth('admin')->id())
                            <button type="button" 
                                    class="btn btn-danger" 
                                    onclick="confirmDelete({{ $admin->id }}, '{{ $admin->username }}')">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <!-- Información detallada -->
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Información Personal</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="form-label fw-bold">Nombre:</label>
                                <p class="form-control-plaintext">{{ $admin->firstname }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="form-label fw-bold">Apellido:</label>
                                <p class="form-control-plaintext">{{ $admin->lastname }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="form-label fw-bold">Usuario:</label>
                                <p class="form-control-plaintext">{{ $admin->username }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="form-label fw-bold">Email:</label>
                                <p class="form-control-plaintext">
                                    <a href="mailto:{{ $admin->email }}">{{ $admin->email }}</a>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="form-label fw-bold">Fecha de Creación:</label>
                                <p class="form-control-plaintext">
                                    {{ \Carbon\Carbon::parse($admin->created_on)->format('d/m/Y') }}
                                    <small class="text-muted">
                                        ({{ \Carbon\Carbon::parse($admin->created_on)->diffForHumans() }})
                                    </small>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="form-label fw-bold">ID:</label>
                                <p class="form-control-plaintext">#{{ $admin->id }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Estadísticas (opcional para futuras funcionalidades) -->
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Estadísticas</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="stat-card text-center">
                                <div class="stat-icon text-primary mb-2">
                                    <i class="fas fa-sign-in-alt fa-2x"></i>
                                </div>
                                <h5>Último acceso</h5>
                                <p class="text-muted">Sin registros</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card text-center">
                                <div class="stat-icon text-success mb-2">
                                    <i class="fas fa-calendar fa-2x"></i>
                                </div>
                                <h5>Días activo</h5>
                                <p class="text-muted">
                                    {{ \Carbon\Carbon::parse($admin->created_on)->diffInDays(now()) }} días
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card text-center">
                                <div class="stat-icon text-info mb-2">
                                    <i class="fas fa-user-shield fa-2x"></i>
                                </div>
                                <h5>Rol</h5>
                                <p class="text-muted">Administrador</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 text-center">
            <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver a la lista
            </a>
        </div>
    </div>

    <!-- Modal de confirmación de eliminación -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar al administrador <strong id="adminName"></strong>?</p>
                    <p class="text-danger"><i class="fas fa-exclamation-triangle"></i> Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
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
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endpush