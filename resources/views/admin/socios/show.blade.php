@extends('layouts.admin-modern')

@section('title', 'Detalles del Socio')

@section('header-color', 'bg-success')

@section('content')
<div class="animate-fade-in-up">
    <!-- Header con acciones -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-success mb-1">
                <i class="fas fa-user me-2"></i>Detalles del Socio
            </h2>
            <p class="text-muted mb-0">Información completa del miembro</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.socios.edit', $socio) }}" class="btn btn-warning btn-modern">
                <i class="fas fa-edit me-1"></i>Editar
            </a>
            <a href="{{ route('admin.socios.index') }}" class="btn btn-secondary btn-modern">
                <i class="fas fa-arrow-left me-1"></i>Volver
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Información Personal -->
        <div class="col-lg-4">
            <div class="card-modern mb-4">
                <div class="card-modern-header">
                    <h3 class="card-modern-title">
                        <i class="fas fa-id-card text-success me-2"></i>
                        Información Personal
                    </h3>
                </div>
                <div class="card-modern-body text-center">
                    <!-- Foto del Socio -->
                    <div class="mb-3">
                        @if($socio->photo)
                            <img src="{{ asset('storage/' . $socio->photo) }}" 
                                 alt="Foto de {{ $socio->full_name }}" 
                                 class="rounded-circle img-fluid"
                                 style="width: 150px; height: 150px; object-fit: cover; border: 4px solid var(--success-color);">
                        @else
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto border" 
                                 style="width: 150px; height: 150px; border: 4px solid var(--success-color) !important;">
                                <i class="fas fa-user fa-4x text-muted"></i>
                            </div>
                        @endif
                    </div>

                    <h4 class="mb-1 text-success">{{ $socio->full_name }}</h4>
                    <p class="text-muted mb-3">ID: {{ $socio->member_id }}</p>

                    <!-- Estado de la Membresía -->
                    <div class="mb-3">
                        @php
                            $status = $socio->status;
                        @endphp
                        @switch($status)
                            @case('activo')
                                <span class="badge bg-success fs-6 px-3 py-2">
                                    <i class="fas fa-check-circle me-1"></i>Membresía Activa
                                </span>
                                @break
                            @case('vencido')
                                <span class="badge bg-danger fs-6 px-3 py-2">
                                    <i class="fas fa-times-circle me-1"></i>Membresía Vencida
                                </span>
                                @break
                            @case('proximo_vencimiento')
                                <span class="badge bg-warning fs-6 px-3 py-2">
                                    <i class="fas fa-exclamation-triangle me-1"></i>Próximo a Vencer
                                </span>
                                @break
                            @case('sin_plan')
                                <span class="badge bg-secondary fs-6 px-3 py-2">
                                    <i class="fas fa-user-times me-1"></i>Sin Plan
                                </span>
                                @break
                        @endswitch
                    </div>

                    <!-- Datos básicos -->
                    <div class="text-start">
                        <div class="row mb-2">
                            <div class="col-4"><strong>Género:</strong></div>
                            <div class="col-8">
                                @switch($socio->gender)
                                    @case('M')
                                        <i class="fas fa-mars text-success me-1"></i>Masculino
                                        @break
                                    @case('F')
                                        <i class="fas fa-venus text-success me-1"></i>Femenino
                                        @break
                                    @default
                                        <i class="fas fa-genderless text-secondary me-1"></i>{{ $socio->gender }}
                                @endswitch
                            </div>
                        </div>

                        @if($socio->birthdate)
                            <div class="row mb-2">
                                <div class="col-4"><strong>Edad:</strong></div>
                                <div class="col-8">
                                    {{ $socio->birthdate->age }} años
                                    <small class="text-muted">({{ $socio->birthdate->format('d/m/Y') }})</small>
                                </div>
                            </div>
                        @endif

                        <div class="row mb-2">
                            <div class="col-4"><strong>Contacto:</strong></div>
                            <div class="col-8">{{ $socio->contact_info }}</div>
                        </div>

                        @if($socio->address)
                            <div class="row mb-2">
                                <div class="col-4"><strong>Dirección:</strong></div>
                                <div class="col-8">{{ $socio->address }}</div>
                            </div>
                        @endif

                        <div class="row mb-2">
                            <div class="col-4"><strong>Registrado:</strong></div>
                            <div class="col-8">{{ $socio->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información de Membresía -->
        <div class="col-lg-8">
            <div class="card-modern mb-4">
                <div class="card-modern-header d-flex justify-content-between align-items-center">
                    <h3 class="card-modern-title mb-0">
                        <i class="fas fa-id-badge text-success me-2"></i>
                        Información de Membresía
                    </h3>
                    @if($socio->membershipPlan)
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#renovarModal">
                            <i class="fas fa-sync me-1"></i>Renovar Membresía
                        </button>
                    @else
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#asignarModal">
                            <i class="fas fa-plus me-1"></i>Asignar Plan
                        </button>
                    @endif
                </div>
                <div class="card-modern-body">
                    @if($socio->membershipPlan)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border-success bg-light">
                                    <div class="card-body">
                                        <h5 class="card-title text-success">
                                            <i class="fas fa-calendar-check me-2"></i>
                                            {{ $socio->membershipPlan->plan_name }}
                                        </h5>
                                        <p class="card-text">{{ $socio->membershipPlan->description }}</p>
                                        <div class="d-flex justify-content-between">
                                            <span><strong>Precio:</strong> ${{ number_format($socio->membershipPlan->price, 0) }}</span>
                                            <span><strong>Duración:</strong> {{ $socio->membershipPlan->duration_days }} días</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-info">
                                    <div class="card-body">
                                        <h6 class="card-title text-success">Fechas de Membresía</h6>
                                        <div class="mb-2">
                                            <strong>Inicio:</strong>
                                            <span class="text-success">
                                                <i class="fas fa-calendar-alt me-1"></i>
                                                {{ $socio->subscription_start_date ? $socio->subscription_start_date->format('d/m/Y') : 'N/A' }}
                                            </span>
                                        </div>
                                        <div class="mb-2">
                                            <strong>Vencimiento:</strong>
                                            @if($socio->subscription_end_date)
                                                @if($socio->subscription_end_date->isFuture())
                                                    <span class="text-success">
                                                        <i class="fas fa-calendar-check me-1"></i>
                                                        {{ $socio->subscription_end_date->format('d/m/Y') }}
                                                    </span>
                                                @else
                                                    <span class="text-danger">
                                                        <i class="fas fa-calendar-times me-1"></i>
                                                        {{ $socio->subscription_end_date->format('d/m/Y') }} (Vencida)
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </div>
                                        @if($socio->subscription_end_date)
                                            <div class="mb-2">
                                                <strong>Días restantes:</strong>
                                                @php
                                                    $daysRemaining = $socio->subscription_end_date->diffInDays(now(), false);
                                                @endphp
                                                @if($daysRemaining < 0)
                                                    <span class="text-success">{{ abs($daysRemaining) }} días</span>
                                                @elseif($daysRemaining <= 7)
                                                    <span class="text-warning">{{ $daysRemaining }} días (¡Próximo a vencer!)</span>
                                                @else
                                                    <span class="text-danger">Vencida hace {{ $daysRemaining }} días</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-exclamation-circle fa-3x text-warning mb-3"></i>
                            <h5>Sin Plan de Membresía</h5>
                            <p class="text-muted">Este socio no tiene un plan de membresía asignado.</p>
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#asignarModal">
                                <i class="fas fa-plus me-1"></i>Asignar Plan de Membresía
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Historial (placeholder para futuras funcionalidades) -->
            <div class="card-modern">
                <div class="card-modern-header">
                    <h3 class="card-modern-title">
                        <i class="fas fa-history text-success me-2"></i>
                        Historial de Actividad
                    </h3>
                </div>
                <div class="card-modern-body">
                    <div class="text-center py-3">
                        <i class="fas fa-history fa-2x text-muted mb-2"></i>
                        <p class="text-muted">Próximamente: Historial de pagos, asistencias y actividades.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para renovar membresía -->
<div class="modal fade" id="renovarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-sync me-2"></i>Renovar Membresía
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.socios.renovar-membresia', $socio) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="plan_id_renovar" class="form-label">Seleccionar Plan</label>
                        <select class="form-select" id="plan_id_renovar" name="plan_id" required>
                            @foreach(App\Models\MembershipPlan::all() as $plan)
                                <option value="{{ $plan->id }}" {{ $socio->plan_id == $plan->id ? 'selected' : '' }}>
                                    {{ $plan->plan_name }} - ${{ number_format($plan->price, 0) }} ({{ $plan->duration_days }} días)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="subscription_start_date_renovar" class="form-label">Fecha de Inicio</label>
                        <input type="date" class="form-control" id="subscription_start_date_renovar" 
                               name="subscription_start_date" value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                    <div class="alert alert-info d-flex align-items-center">
                        <i class="fas fa-info-circle me-2"></i>
                        <div>La fecha de vencimiento se calculará automáticamente según el plan seleccionado.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-sync me-1"></i>Renovar Membresía
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para asignar plan (si no tiene) -->
<div class="modal fade" id="asignarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Asignar Plan de Membresía
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.socios.renovar-membresia', $socio) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="plan_id_asignar" class="form-label">Seleccionar Plan</label>
                        <select class="form-select" id="plan_id_asignar" name="plan_id" required>
                            <option value="">Elegir plan...</option>
                            @foreach(App\Models\MembershipPlan::all() as $plan)
                                <option value="{{ $plan->id }}">
                                    {{ $plan->plan_name }} - ${{ number_format($plan->price, 0) }} ({{ $plan->duration_days }} días)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="subscription_start_date_asignar" class="form-label">Fecha de Inicio</label>
                        <input type="date" class="form-control" id="subscription_start_date_asignar" 
                               name="subscription_start_date" value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-plus me-1"></i>Asignar Plan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection