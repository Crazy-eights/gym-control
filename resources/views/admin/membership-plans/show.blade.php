@extends('layouts.admin')

@section('title', 'Detalles del Plan: ' . $membershipPlan->plan_name)

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">{{ $membershipPlan->plan_name }}</h1>
            <p class="mb-0 text-muted">Detalles completos del plan de membresía</p>
        </div>
        <div>
            <a href="{{ route('admin.membership-plans.edit', $membershipPlan) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit"></i> Editar Plan
            </a>
            <a href="{{ route('admin.membership-plans.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver al listado
            </a>
        </div>
    </div>

    <!-- Información del Plan -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Información General</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">{{ $membershipPlan->plan_name }}</h5>
                            <div class="mb-3">
                                <h6 class="text-muted">Precio</h6>
                                <h3 class="text-success">${{ number_format($membershipPlan->price, 2) }}</h3>
                            </div>
                            <div class="mb-3">
                                <h6 class="text-muted">Duración</h6>
                                <p>
                                    <span class="badge badge-primary font-size-14">{{ $membershipPlan->duration_days }} días</span>
                                    @if($membershipPlan->duration_days <= 7)
                                        <small class="text-muted">({{ $membershipPlan->duration_days }} día(s))</small>
                                    @elseif($membershipPlan->duration_days <= 31)
                                        <small class="text-muted">({{ round($membershipPlan->duration_days/7, 1) }} semana(s))</small>
                                    @elseif($membershipPlan->duration_days <= 93)
                                        <small class="text-muted">({{ round($membershipPlan->duration_days/30, 1) }} mes(es))</small>
                                    @else
                                        <small class="text-muted">({{ round($membershipPlan->duration_days/365, 1) }} año(s))</small>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Descripción</h6>
                            <p class="text-justify">{{ $membershipPlan->description }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Estadísticas del Plan -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Estadísticas</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <h4 class="text-primary">{{ $estadisticas['total_miembros'] }}</h4>
                        <p class="text-muted mb-0">Total de Miembros</p>
                    </div>
                    
                    <div class="text-center mb-3">
                        <h4 class="text-success">{{ $estadisticas['miembros_activos'] }}</h4>
                        <p class="text-muted mb-0">Miembros Activos</p>
                    </div>

                    <div class="text-center mb-3">
                        <h4 class="text-warning">${{ number_format($estadisticas['ingresos_totales'], 2) }}</h4>
                        <p class="text-muted mb-0">Ingresos Generados</p>
                    </div>

                    @if($estadisticas['total_miembros'] > 0)
                    <div class="progress mb-2">
                        @php
                            $porcentajeActivos = ($estadisticas['miembros_activos'] / $estadisticas['total_miembros']) * 100;
                        @endphp
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $porcentajeActivos }}%">
                            {{ round($porcentajeActivos, 1) }}%
                        </div>
                    </div>
                    <p class="text-muted small text-center">Porcentaje de miembros activos</p>
                    @endif
                </div>
            </div>

            <!-- Acciones Rápidas -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-secondary">Acciones</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.membership-plans.edit', $membershipPlan) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar Plan
                        </a>
                        
                        <form action="{{ route('admin.membership-plans.duplicate', $membershipPlan) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-info w-100" onclick="return confirm('¿Duplicar este plan?')">
                                <i class="fas fa-copy"></i> Duplicar Plan
                            </button>
                        </form>

                        @if($estadisticas['total_miembros'] == 0)
                        <form action="{{ route('admin.membership-plans.destroy', $membershipPlan) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100" onclick="return confirm('¿Estás seguro de eliminar este plan?')">
                                <i class="fas fa-trash"></i> Eliminar Plan
                            </button>
                        </form>
                        @else
                        <button class="btn btn-danger w-100" disabled title="No se puede eliminar: tiene miembros asociados">
                            <i class="fas fa-trash"></i> No se puede eliminar
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Miembros -->
    @if($miembros->count() > 0)
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Miembros con este Plan ({{ $miembros->total() }})</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID Miembro</th>
                            <th>Nombre Completo</th>
                            <th>Contacto</th>
                            <th>Inicio Suscripción</th>
                            <th>Fin Suscripción</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($miembros as $miembro)
                        <tr>
                            <td>
                                <strong>{{ $miembro->member_id }}</strong>
                            </td>
                            <td>
                                {{ $miembro->full_name }}
                            </td>
                            <td>
                                <small class="text-muted">{{ $miembro->contact_info }}</small>
                            </td>
                            <td>
                                @if($miembro->subscription_start_date)
                                    {{ \Carbon\Carbon::parse($miembro->subscription_start_date)->format('d/m/Y') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($miembro->subscription_end_date)
                                    {{ \Carbon\Carbon::parse($miembro->subscription_end_date)->format('d/m/Y') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($miembro->subscription_end_date && \Carbon\Carbon::parse($miembro->subscription_end_date)->isFuture())
                                    <span class="badge badge-success">Activo</span>
                                @elseif($miembro->subscription_end_date && \Carbon\Carbon::parse($miembro->subscription_end_date)->isPast())
                                    <span class="badge badge-danger">Vencido</span>
                                @else
                                    <span class="badge badge-secondary">Sin fecha</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.socios.show', $miembro) }}" 
                                   class="btn btn-sm btn-info" title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.socios.edit', $miembro) }}" 
                                   class="btn btn-sm btn-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($miembros->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <p class="text-muted mb-0">
                        Mostrando {{ $miembros->firstItem() }} a {{ $miembros->lastItem() }} 
                        de {{ $miembros->total() }} miembros
                    </p>
                </div>
                <div>
                    {{ $miembros->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
    @else
    <div class="card shadow">
        <div class="card-body text-center py-5">
            <i class="fas fa-users fa-3x text-gray-300 mb-3"></i>
            <h5 class="text-muted">Sin Miembros Asignados</h5>
            <p class="text-muted">Este plan aún no tiene miembros asociados.</p>
            <a href="{{ route('admin.socios.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Agregar Primer Miembro
            </a>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Confirmar acciones destructivas
    const deleteButtons = document.querySelectorAll('button[onclick*="confirm"]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm(this.getAttribute('onclick').match(/confirm\('(.*)'\)/)[1])) {
                e.preventDefault();
            }
        });
    });
});
</script>
@endsection