@extends('layouts.admin')

@section('title', 'Gestión de Socios')

@section('content')
<div class="container-fluid">
    <!-- Header con estadísticas -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-3">
                    <i class="fas fa-users me-2"></i>
                    Gestión de Socios
                </h1>
                <a href="{{ route('admin.socios.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    Nuevo Socio
                </a>
            </div>
        </div>
    </div>

    <!-- Tarjetas de estadísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Socios
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['total'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Socios Activos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['activos'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Próximos a Vencer
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['proximos_vencimiento'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Membresías Vencidas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['vencidos'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros y búsqueda -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtros de Búsqueda</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.socios.index') }}">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="search" class="form-label">Buscar por nombre o ID</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Nombre, apellido o ID...">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="status" class="form-label">Estado</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Todos los estados</option>
                            <option value="activo" {{ request('status') == 'activo' ? 'selected' : '' }}>Activos</option>
                            <option value="vencido" {{ request('status') == 'vencido' ? 'selected' : '' }}>Vencidos</option>
                            <option value="proximo_vencimiento" {{ request('status') == 'proximo_vencimiento' ? 'selected' : '' }}>Próximo a vencer</option>
                            <option value="sin_plan" {{ request('status') == 'sin_plan' ? 'selected' : '' }}>Sin plan</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="plan_id" class="form-label">Plan de Membresía</label>
                        <select class="form-select" id="plan_id" name="plan_id">
                            <option value="">Todos los planes</option>
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
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>Buscar
                            </button>
                            <a href="{{ route('admin.socios.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Limpiar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de socios -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Lista de Socios</h6>
        </div>
        <div class="card-body">
            @if($socios->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Foto</th>
                                <th>ID Socio</th>
                                <th>Nombre Completo</th>
                                <th>Contacto</th>
                                <th>Plan</th>
                                <th>Estado</th>
                                <th>Vencimiento</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($socios as $socio)
                                <tr>
                                    <td class="text-center">
                                        @if($socio->photo)
                                            <img src="{{ asset('storage/' . $socio->photo) }}" 
                                                 alt="Foto de {{ $socio->full_name }}" 
                                                 class="rounded-circle" 
                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center" 
                                                 style="width: 40px; height: 40px;">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $socio->member_id }}</td>
                                    <td>
                                        <strong>{{ $socio->full_name }}</strong><br>
                                        <small class="text-muted">{{ $socio->gender }}</small>
                                    </td>
                                    <td>{{ $socio->contact_info }}</td>
                                    <td>
                                        @if($socio->membershipPlan)
                                            <span class="badge bg-info">{{ $socio->membershipPlan->plan_name }}</span>
                                        @else
                                            <span class="badge bg-secondary">Sin plan</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $status = $socio->status;
                                        @endphp
                                        @switch($status)
                                            @case('activo')
                                                <span class="badge bg-success">Activo</span>
                                                @break
                                            @case('vencido')
                                                <span class="badge bg-danger">Vencido</span>
                                                @break
                                            @case('proximo_vencimiento')
                                                <span class="badge bg-warning">Próximo a vencer</span>
                                                @break
                                            @case('sin_plan')
                                                <span class="badge bg-secondary">Sin plan</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        @if($socio->subscription_end_date)
                                            {{ $socio->subscription_end_date->format('d/m/Y') }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.socios.show', $socio) }}" 
                                               class="btn btn-sm btn-info" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.socios.edit', $socio) }}" 
                                               class="btn btn-sm btn-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="confirmarEliminacion({{ $socio->id }})" 
                                                    title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Mostrando {{ $socios->firstItem() }} a {{ $socios->lastItem() }} de {{ $socios->total() }} socios
                    </div>
                    <div>
                        {{ $socios->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-users fa-3x text-gray-300 mb-3"></i>
                    <h5>No hay socios registrados</h5>
                    <p class="text-muted">Comienza registrando tu primer socio.</p>
                    <a href="{{ route('admin.socios.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Registrar Socio
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de confirmación para eliminar -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar este socio?</p>
                <p class="text-danger"><strong>Esta acción no se puede deshacer.</strong></p>
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
function confirmarEliminacion(socioId) {
    const form = document.getElementById('deleteForm');
    form.action = `/admin/socios/${socioId}`;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endpush