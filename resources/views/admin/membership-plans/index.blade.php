@extends('layouts.admin')

@section('title', 'Gestión de Planes de Membresía')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Planes de Membresía</h1>
            <p class="mb-0 text-muted">Gestiona los planes de membresía del gimnasio</p>
        </div>
        <a href="{{ route('admin.membership-plans.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Plan
        </a>
    </div>

    <!-- Estadísticas Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Planes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_planes'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-2x text-gray-300"></i>
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
                                Precio Promedio</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($stats['precio_promedio'], 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Plan Más Popular</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['plan_mas_popular']->plan_name ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
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
                                Miembros Activos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_miembros_activos'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros y Búsqueda -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtros de Búsqueda</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.membership-plans.index') }}">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="search">Buscar</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Nombre o descripción...">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="price_min">Precio Mín.</label>
                        <input type="number" class="form-control" id="price_min" name="price_min" 
                               value="{{ request('price_min') }}" placeholder="0.00" step="0.01">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="price_max">Precio Máx.</label>
                        <input type="number" class="form-control" id="price_max" name="price_max" 
                               value="{{ request('price_max') }}" placeholder="999.99" step="0.01">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="duration_filter">Duración</label>
                        <select class="form-control" id="duration_filter" name="duration_filter">
                            <option value="">Todas las duraciones</option>
                            <option value="semanal" {{ request('duration_filter') == 'semanal' ? 'selected' : '' }}>
                                Semanal (≤7 días)
                            </option>
                            <option value="mensual" {{ request('duration_filter') == 'mensual' ? 'selected' : '' }}>
                                Mensual (8-31 días)
                            </option>
                            <option value="trimestral" {{ request('duration_filter') == 'trimestral' ? 'selected' : '' }}>
                                Trimestral (32-93 días)
                            </option>
                            <option value="anual" {{ request('duration_filter') == 'anual' ? 'selected' : '' }}>
                                Anual (≥365 días)
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                        <a href="{{ route('admin.membership-plans.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Planes -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Lista de Planes de Membresía</h6>
        </div>
        <div class="card-body">
            @if($planes->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
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
                                <span class="badge bg-success fs-6">
                                    ${{ number_format($plan->price, 2) }}
                                </span>
                            </td>
                            <td style="min-width: 120px;">
                                @if($plan->duration_days <= 7)
                                    <span class="badge bg-info">{{ $plan->duration_days }} día{{ $plan->duration_days > 1 ? 's' : '' }}</span>
                                @elseif($plan->duration_days <= 31)
                                    @php $weeks = round($plan->duration_days/7, 1); @endphp
                                    <span class="badge bg-primary">{{ $weeks }} semana{{ $weeks > 1 ? 's' : '' }}</span>
                                @elseif($plan->duration_days <= 93)
                                    @php $months = round($plan->duration_days/30, 1); @endphp
                                    <span class="badge bg-warning text-dark">{{ $months }} mes{{ $months > 1 ? 'es' : '' }}</span>
                                @else
                                    @php $years = round($plan->duration_days/365, 1); @endphp
                                    <span class="badge bg-secondary">{{ $years }} año{{ $years > 1 ? 's' : '' }}</span>
                                @endif
                                <br><small class="text-muted">{{ $plan->duration_days }} días</small>
                            </td>
                            <td style="min-width: 80px;">
                                <span class="badge bg-light text-dark">{{ $plan->members->count() }}</span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.membership-plans.show', $plan) }}" 
                                       class="btn btn-sm btn-info" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.membership-plans.edit', $plan) }}" 
                                       class="btn btn-sm btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.membership-plans.duplicate', $plan) }}" 
                                          method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-secondary" 
                                                title="Duplicar" onclick="return confirm('¿Duplicar este plan?')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </form>
                                    @if($plan->members->count() == 0)
                                    <form action="{{ route('admin.membership-plans.destroy', $plan) }}" 
                                          method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                title="Eliminar" onclick="return confirm('¿Estás seguro de eliminar este plan?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
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
                    <p class="text-muted mb-0">
                        Mostrando {{ $planes->firstItem() }} a {{ $planes->lastItem() }} 
                        de {{ $planes->total() }} resultados
                    </p>
                </div>
                <div>
                    {{ $planes->links() }}
                </div>
            </div>
            @else
            <div class="text-center py-4">
                <i class="fas fa-list fa-3x text-gray-300 mb-3"></i>
                <h5 class="text-muted">No se encontraron planes de membresía</h5>
                <p class="text-muted">
                    @if(request()->hasAny(['search', 'price_min', 'price_max', 'duration_filter']))
                        No hay planes que coincidan con los filtros aplicados.
                        <a href="{{ route('admin.membership-plans.index') }}">Limpiar filtros</a>
                    @else
                        Comienza creando tu primer plan de membresía.
                    @endif
                </p>
                @if(!request()->hasAny(['search', 'price_min', 'price_max', 'duration_filter']))
                <a href="{{ route('admin.membership-plans.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Crear Primer Plan
                </a>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form on filter change
    const filters = document.querySelectorAll('#duration_filter');
    filters.forEach(filter => {
        filter.addEventListener('change', function() {
            this.form.submit();
        });
    });
});
</script>
@endsection

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