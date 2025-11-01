@extends('layouts.admin-modern')

@section('title', 'Detalles del Plan: ' . $membershipPlan->plan_name)
@section('page-title', 'Detalles del Plan de Membresía')

@section('content')
<div class="animate-fade-in-up">
    <!-- Header con acciones -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-success mb-1">
                <i class="fas fa-id-card me-2"></i>{{ $membershipPlan->plan_name }}
            </h2>
            <p class="text-muted mb-0">Detalles completos del plan de membresía</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.membership-plans.edit', $membershipPlan) }}" class="btn btn-warning btn-modern">
                <i class="fas fa-edit me-2"></i>Editar Plan
            </a>
            <a href="{{ route('admin.membership-plans.index') }}" class="btn btn-secondary btn-modern">
                <i class="fas fa-arrow-left me-2"></i>Volver al listado
            </a>
        </div>
    </div>

    <!-- Información del Plan -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="modern-card">
                <div class="modern-card-header">
                    <h6 class="mb-0 fw-bold text-success">
                        <i class="fas fa-info-circle me-2"></i>Información General
                    </h6>
                </div>
                <div class="modern-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="text-success mb-3 fw-bold">{{ $membershipPlan->plan_name }}</h5>
                            <div class="mb-3">
                                <h6 class="text-muted fw-semibold">Precio</h6>
                                <h3 class="text-success fw-bold">${{ number_format($membershipPlan->price, 2) }}</h3>
                            </div>
                            <div class="mb-3">
                                <h6 class="text-muted fw-semibold">Duración</h6>
                                <p>
                                    <span class="badge bg-success fs-6 px-3 py-2">{{ $membershipPlan->duration_days }} días</span>
                                    @if($membershipPlan->duration_days <= 7)
                                        <small class="text-muted ms-2">({{ $membershipPlan->duration_days }} día(s))</small>
                                    @elseif($membershipPlan->duration_days <= 31)
                                        <small class="text-muted ms-2">({{ round($membershipPlan->duration_days/7, 1) }} semana(s))</small>
                                    @elseif($membershipPlan->duration_days <= 93)
                                        <small class="text-muted ms-2">({{ round($membershipPlan->duration_days/30, 1) }} mes(es))</small>
                                    @else
                                        <small class="text-muted ms-2">({{ round($membershipPlan->duration_days/365, 1) }} año(s))</small>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted fw-semibold">Descripción</h6>
                            <p class="text-muted">{{ $membershipPlan->description }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Estadísticas del Plan -->
            <div class="modern-card">
                <div class="modern-card-header">
                    <h6 class="mb-0 fw-bold text-info">
                        <i class="fas fa-chart-bar me-2"></i>Estadísticas
                    </h6>
                </div>
                <div class="modern-card-body">
                    <div class="stat-item text-center mb-3">
                        <div class="stat-number text-primary">{{ $estadisticas['total_miembros'] }}</div>
                        <div class="stat-label text-muted">Total de Miembros</div>
                    </div>
                    
                    <div class="stat-item text-center mb-3">
                        <div class="stat-number text-success">{{ $estadisticas['miembros_activos'] }}</div>
                        <div class="stat-label text-muted">Miembros Activos</div>
                    </div>

                    <div class="stat-item text-center mb-3">
                        <div class="stat-number text-warning">${{ number_format($estadisticas['ingresos_totales'], 2) }}</div>
                        <div class="stat-label text-muted">Ingresos Generados</div>
                    </div>

                    @if($estadisticas['total_miembros'] > 0)
                    <div class="progress mb-2" style="height: 8px;">
                        @php
                            $porcentajeActivos = ($estadisticas['miembros_activos'] / $estadisticas['total_miembros']) * 100;
                        @endphp
                        <div class="progress-bar bg-gradient-success" role="progressbar" style="width: {{ $porcentajeActivos }}%"></div>
                    </div>
                    <p class="text-muted small text-center mb-0">{{ round($porcentajeActivos, 1) }}% de miembros activos</p>
                    @endif
                </div>
            </div>

            <!-- Acciones Rápidas -->
            <div class="modern-card">
                <div class="modern-card-header">
                    <h6 class="mb-0 fw-bold text-secondary">
                        <i class="fas fa-cogs me-2"></i>Acciones
                    </h6>
                </div>
                <div class="modern-card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.membership-plans.edit', $membershipPlan) }}" class="btn btn-warning btn-modern">
                            <i class="fas fa-edit me-2"></i>Editar Plan
                        </a>
                        
                        <form action="{{ route('admin.membership-plans.duplicate', $membershipPlan) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-info btn-modern w-100" onclick="return confirm('¿Duplicar este plan?')">
                                <i class="fas fa-copy me-2"></i>Duplicar Plan
                            </button>
                        </form>

                        @if($estadisticas['total_miembros'] == 0)
                        <form action="{{ route('admin.membership-plans.destroy', $membershipPlan) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-modern w-100" onclick="return confirm('¿Estás seguro de eliminar este plan?')">
                                <i class="fas fa-trash me-2"></i>Eliminar Plan
                            </button>
                        </form>
                        @else
                        <button class="btn btn-danger btn-modern w-100" disabled title="No se puede eliminar: tiene miembros asociados">
                            <i class="fas fa-trash me-2"></i>No se puede eliminar
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Miembros -->
    @if($miembros->count() > 0)
    <div class="modern-card">
        <div class="modern-card-header">
            <h6 class="mb-0 fw-bold text-success">
                <i class="fas fa-users me-2"></i>Miembros con este Plan ({{ $miembros->total() }})
            </h6>
        </div>
        <div class="modern-card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-success">
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
                                <strong class="text-success">{{ $miembro->member_id }}</strong>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $miembro->full_name }}</div>
                            </td>
                            <td>
                                <small class="text-muted">{{ $miembro->contact_info }}</small>
                            </td>
                            <td>
                                @if($miembro->subscription_start_date)
                                    <small>{{ \Carbon\Carbon::parse($miembro->subscription_start_date)->format('d/m/Y') }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($miembro->subscription_end_date)
                                    <small>{{ \Carbon\Carbon::parse($miembro->subscription_end_date)->format('d/m/Y') }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($miembro->subscription_end_date && \Carbon\Carbon::parse($miembro->subscription_end_date)->isFuture())
                                    <span class="badge bg-success">Activo</span>
                                @elseif($miembro->subscription_end_date && \Carbon\Carbon::parse($miembro->subscription_end_date)->isPast())
                                    <span class="badge bg-danger">Vencido</span>
                                @else
                                    <span class="badge bg-secondary">Sin fecha</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.socios.show', $miembro) }}" 
                                       class="btn btn-sm btn-info" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.socios.edit', $miembro) }}" 
                                       class="btn btn-sm btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
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
                    <p class="text-muted mb-0 small">
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
    <div class="modern-card text-center">
        <div class="modern-card-body py-5">
            <i class="fas fa-users fa-4x text-muted mb-3" style="opacity: 0.3;"></i>
            <h5 class="text-muted mb-2">Sin Miembros Asignados</h5>
            <p class="text-muted mb-4">Este plan aún no tiene miembros asociados.</p>
            <a href="{{ route('admin.socios.create') }}" class="btn btn-success btn-modern">
                <i class="fas fa-plus me-2"></i>Agregar Primer Miembro
            </a>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
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
@endpush