@extends('layouts.admin')

@section('title', 'Administradores')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Administradores</h1>
            <p class="mb-0 text-muted">Gestión de usuarios administradores del sistema</p>
        </div>
        <a href="{{ route('admin.admins.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Administrador
        </a>
    </div>
                
                <div class="box-body">
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

    <!-- Tabla de Administradores -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Lista de Administradores del Sistema</h6>
        </div>
        <div class="card-body">
            @if($admins->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
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
                                    <span class="badge bg-success ms-1">Tú</span>
                                @endif
                            </td>
                            <td>{{ $admin->firstname }} {{ $admin->lastname }}</td>
                            <td>{{ $admin->email }}</td>
                            <td>{{ \Carbon\Carbon::parse($admin->created_on)->format('d/m/Y') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.admins.show', $admin) }}" 
                                       class="btn btn-sm btn-info" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.admins.edit', $admin) }}" 
                                       class="btn btn-sm btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($admin->id !== auth('admin')->id())
                                    <button type="button" 
                                            class="btn btn-sm btn-danger" 
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
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <p class="text-muted mb-0">
                        Mostrando {{ $admins->firstItem() }} a {{ $admins->lastItem() }} 
                        de {{ $admins->total() }} administradores
                    </p>
                </div>
                <div>
                    {{ $admins->links() }}
                </div>
            </div>
            @endif
            @else
            <div class="text-center py-4">
                <i class="fas fa-user-shield fa-3x text-gray-300 mb-3"></i>
                <h5 class="text-muted">No hay administradores registrados</h5>
                <p class="text-muted">Comienza agregando el primer administrador al sistema.</p>
                <a href="{{ route('admin.admins.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Crear Primer Administrador
                </a>
            </div>
            @endif
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