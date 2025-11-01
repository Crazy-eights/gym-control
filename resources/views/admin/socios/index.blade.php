@extends('layouts.admin-modern')

@section('title', 'Gestión de Socios')
@section('page-title', 'Gestión de Socios')

@section('content')
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

    <!-- Filtros y búsqueda -->
    <div class="card-modern mb-4">
        <div class="card-header-modern">
            <h5 class="card-title-modern text-success">
                <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.socios.index') }}">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="search" class="form-label">Buscar por nombre o ID</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Nombre, apellido o ID...">
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
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
    </div>

    <!-- Lista de socios -->
    <div class="card-modern">
        <div class="card-header-modern">
            <h5 class="card-title-modern text-success">
                <i class="fas fa-list me-2"></i>Lista de Socios
            </h5>
        </div>
        <div class="card-body">
            @if($socios->count() > 0)
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
                                    <td>
                                        @if($socio->email)
                                            <a href="mailto:{{ $socio->email }}" class="text-success">{{ $socio->email }}</a>
                                        @else
                                            <span class="text-muted">Sin email</span>
                                        @endif
                                    </td>
                                    <td>{{ $socio->contact_info }}</td>
                                    <td>
                                        @if($socio->membershipPlan)
                                            <span class="badge badge-modern badge-success">{{ $socio->membershipPlan->plan_name }}</span>
                                        @else
                                            <span class="badge badge-modern badge-secondary">Sin plan</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $status = $socio->status;
                                        @endphp
                                        @switch($status)
                                            @case('activo')
                                                <span class="badge badge-modern badge-success">
                                                    <i class="fas fa-check-circle me-1"></i>Activo
                                                </span>
                                                @break
                                            @case('vencido')
                                                <span class="badge badge-modern badge-danger">
                                                    <i class="fas fa-times-circle me-1"></i>Vencido
                                                </span>
                                                @break
                                            @case('proximo_vencimiento')
                                                <span class="badge badge-modern badge-warning">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>Próximo a vencer
                                                </span>
                                                @break
                                            @case('sin_plan')
                                            @default
                                                <span class="badge badge-modern badge-secondary">
                                                    <i class="fas fa-user-slash me-1"></i>Sin plan
                                                </span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        @if($socio->subscription_end_date)
                                            @php
                                                $endDate = $socio->subscription_end_date instanceof \Carbon\Carbon 
                                                    ? $socio->subscription_end_date 
                                                    : \Carbon\Carbon::parse($socio->subscription_end_date);
                                                $daysUntilExpiry = $endDate->diffInDays(now(), false);
                                            @endphp
                                            
                                            <div>
                                                <strong>{{ $endDate->format('d/m/Y') }}</strong>
                                                @if($endDate->isAfter(now()))
                                                    <br><small class="text-success">
                                                        <i class="fas fa-clock me-1"></i>{{ abs($daysUntilExpiry) }} días restantes
                                                    </small>
                                                @elseif($endDate->isToday())
                                                    <br><small class="text-warning">
                                                        <i class="fas fa-exclamation-triangle me-1"></i>Vence hoy
                                                    </small>
                                                @else
                                                    <br><small class="text-danger">
                                                        <i class="fas fa-times-circle me-1"></i>Venció hace {{ $daysUntilExpiry }} días
                                                    </small>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.socios.show', $socio) }}" 
                                               class="btn btn-sm btn-outline-success" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-warning" 
                                                    onclick="abrirModalEditar({{ $socio->id }})" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
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
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Mostrando {{ $socios->firstItem() }} a {{ $socios->lastItem() }} de {{ $socios->total() }} socios
                    </div>
                    <div>
                        {{ $socios->links() }}
                    </div>
                </div>
            @else
                <div class="empty-state text-center py-5">
                    <div class="empty-icon mb-3">
                        <i class="fas fa-users fa-3x text-muted"></i>
                    </div>
                    <h5 class="mb-2">No hay socios registrados</h5>
                    <p class="text-muted mb-3">Comienza registrando tu primer socio.</p>
                    <a href="{{ route('admin.socios.create') }}" class="btn btn-success btn-modern">
                        <i class="fas fa-plus me-2"></i>Registrar Socio
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal para editar socio -->
<div class="modal fade" id="editSocioModal" tabindex="-1" aria-labelledby="editSocioModalLabel" aria-hidden="true" role="dialog">
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
                        <!-- Información Personal -->
                        <div class="col-lg-8">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-light border-0 py-2">
                                    <h6 class="card-title mb-0 fw-semibold text-success">
                                        <i class="fas fa-user me-2"></i>Información Personal
                                    </h6>
                                </div>
                                <div class="card-body p-3">
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <label for="edit_member_id" class="form-label fw-semibold small">
                                                ID del Socio <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" 
                                                   class="form-control form-control-sm" 
                                                   id="edit_member_id" 
                                                   name="member_id" 
                                                   readonly
                                                   style="background-color: #f8f9fa; border: 1px solid #e9ecef;">
                                            <div class="form-text small">
                                                <i class="fas fa-lock me-1"></i>El ID no se puede modificar
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="edit_gender" class="form-label fw-semibold small">
                                                Género <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select form-select-sm" 
                                                    id="edit_gender" 
                                                    name="gender" 
                                                    required>
                                                <option value="">Seleccionar género</option>
                                                <option value="M">Masculino</option>
                                                <option value="F">Femenino</option>
                                                <option value="Otro">Otro</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="edit_firstname" class="form-label fw-semibold small">
                                                Nombre <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" 
                                                   class="form-control form-control-sm" 
                                                   id="edit_firstname" 
                                                   name="firstname" 
                                                   placeholder="Nombre del socio"
                                                   required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="edit_lastname" class="form-label fw-semibold small">
                                                Apellidos <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" 
                                                   class="form-control form-control-sm" 
                                                   id="edit_lastname" 
                                                   name="lastname" 
                                                   placeholder="Apellidos del socio"
                                                   required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="edit_email" class="form-label fw-semibold small">
                                                Email
                                            </label>
                                            <input type="email" 
                                                   class="form-control form-control-sm" 
                                                   id="edit_email" 
                                                   name="email" 
                                                   placeholder="correo@ejemplo.com">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="edit_contact_info" class="form-label fw-semibold small">
                                                Información de Contacto <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" 
                                                   class="form-control form-control-sm" 
                                                   id="edit_contact_info" 
                                                   name="contact_info" 
                                                   placeholder="Teléfono"
                                                   required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="edit_birthdate" class="form-label fw-semibold small">
                                                Fecha de Nacimiento
                                            </label>
                                            <input type="date" 
                                                   class="form-control form-control-sm" 
                                                   id="edit_birthdate" 
                                                   name="birthdate">
                                        </div>
                                        <div class="col-12">
                                            <label for="edit_address" class="form-label fw-semibold small">
                                                Dirección
                                            </label>
                                            <textarea class="form-control form-control-sm" 
                                                      id="edit_address" 
                                                      name="address" 
                                                      rows="2" 
                                                      placeholder="Dirección completa del socio"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Foto y Estado -->
                        <div class="col-lg-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-light border-0 py-2">
                                    <h6 class="card-title mb-0 fw-semibold text-success">
                                        <i class="fas fa-camera me-2"></i>Foto Actual
                                    </h6>
                                </div>
                                <div class="card-body p-3 text-center">
                                    <!-- Foto actual -->
                                    <div class="mb-3" id="current-photo-container">
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto border" 
                                             style="width: 80px; height: 80px; border: 2px solid var(--bs-success) !important;">
                                            <i class="fas fa-user fa-2x text-muted"></i>
                                        </div>
                                    </div>

                                    <!-- Input para nueva foto -->
                                    <div class="mb-3">
                                        <label for="edit_photo" class="form-label fw-semibold text-success small">
                                            <i class="fas fa-upload me-1"></i>Cambiar Foto
                                        </label>
                                        <input type="file" 
                                               class="form-control form-control-sm" 
                                               id="edit_photo" 
                                               name="photo" 
                                               accept="image/*">
                                        <div class="form-text mt-1">
                                            <small class="text-muted">
                                                Dejar vacío para mantener la foto actual<br>
                                                JPG, PNG, GIF. Máx 2MB
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Estado activo -->
                                    <div class="form-check form-switch d-flex justify-content-center align-items-center">
                                        <input class="form-check-input me-2" 
                                               type="checkbox" 
                                               id="edit_status" 
                                               name="status" 
                                               value="active"
                                               checked>
                                        <label class="form-check-label fw-semibold text-success small" for="edit_status">
                                            <i class="fas fa-user-check me-1"></i>Socio Activo
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Membresía -->
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-light border-0 py-2">
                                    <h6 class="card-title mb-0 fw-semibold text-success">
                                        <i class="fas fa-id-card me-2"></i>Información de Membresía
                                    </h6>
                                </div>
                                <div class="card-body p-3">
                                    <div class="row g-2">
                                        <div class="col-md-4">
                                            <label for="edit_plan_id" class="form-label fw-semibold small">
                                                Plan de Membresía
                                            </label>
                                            <select class="form-select form-select-sm" 
                                                    id="edit_plan_id" 
                                                    name="plan_id">
                                                <option value="">Sin plan asignado</option>
                                                @foreach($planes as $plan)
                                                    <option value="{{ $plan->id }}">
                                                        {{ $plan->plan_name }} - ${{ number_format($plan->price, 2) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="edit_subscription_start_date" class="form-label fw-semibold small">
                                                Fecha de Inicio
                                            </label>
                                            <input type="date" 
                                                   class="form-control form-control-sm" 
                                                   id="edit_subscription_start_date" 
                                                   name="subscription_start_date">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="edit_subscription_end_date" class="form-label fw-semibold small">
                                                Fecha de Fin
                                            </label>
                                            <input type="date" 
                                                   class="form-control form-control-sm" 
                                                   id="edit_subscription_end_date" 
                                                   name="subscription_end_date">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i>Actualizar Socio
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Modal para crear nuevo socio -->
<div class="modal fade" id="createSocioModal" tabindex="-1" aria-labelledby="createSocioModalLabel" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white py-2">
                <h6 class="modal-title mb-0" id="createSocioModalLabel">
                    <i class="fas fa-user-plus me-2"></i>Registrar Nuevo Socio
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.socios.store') }}" method="POST" enctype="multipart/form-data" id="createSocioForm">
                @csrf
                <div class="modal-body p-3">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <!-- Datos Básicos -->
                            <h6 class="text-success border-bottom pb-2 mb-3 small">
                                <i class="fas fa-id-card me-2"></i>Datos Básicos
                            </h6>
                            
                            <div class="mb-2">
                                <label for="member_id" class="form-label fw-semibold small">ID del Socio <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm @error('member_id') is-invalid @enderror" 
                                       id="member_id" name="member_id" value="{{ old('member_id') }}"
                                       placeholder="Ej: SOC001" required>
                                @error('member_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text small">Identificador único del socio</div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label for="firstname" class="form-label fw-semibold small">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm @error('firstname') is-invalid @enderror" 
                                           id="firstname" name="firstname" value="{{ old('firstname') }}"
                                           placeholder="Nombre del socio" required>
                                    @error('firstname')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="lastname" class="form-label fw-semibold small">Apellidos <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm @error('lastname') is-invalid @enderror" 
                                           id="lastname" name="lastname" value="{{ old('lastname') }}"
                                           placeholder="Apellidos del socio" required>
                                    @error('lastname')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-semibold small">Email</label>
                                    <input type="email" class="form-control form-control-sm @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email') }}"
                                           placeholder="correo@ejemplo.com">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="contact_info" class="form-label fw-semibold small">Información de Contacto <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm @error('contact_info') is-invalid @enderror" 
                                           id="contact_info" name="contact_info" value="{{ old('contact_info') }}"
                                           placeholder="Teléfono" required>
                                    @error('contact_info')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label for="gender" class="form-label fw-semibold small">Género <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-sm @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                                        <option value="">Seleccionar género</option>
                                        <option value="M" {{ old('gender') == 'M' ? 'selected' : '' }}>Masculino</option>
                                        <option value="F" {{ old('gender') == 'F' ? 'selected' : '' }}>Femenino</option>
                                        <option value="O" {{ old('gender') == 'O' ? 'selected' : '' }}>Otro</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="birthdate" class="form-label fw-semibold small">Fecha de Nacimiento</label>
                                    <input type="date" class="form-control form-control-sm @error('birthdate') is-invalid @enderror" 
                                           id="birthdate" name="birthdate" value="{{ old('birthdate') }}">
                                    @error('birthdate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-2">
                                <label for="address" class="form-label fw-semibold small">Dirección</label>
                                <textarea class="form-control form-control-sm" id="address" name="address" rows="2" 
                                          placeholder="Dirección completa"></textarea>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <!-- Foto del Socio -->
                            <h6 class="text-success border-bottom pb-2 mb-3 small">
                                <i class="fas fa-camera me-2"></i>Foto del Socio
                            </h6>
                            
                            <div class="mb-3">
                                <label for="photo" class="form-label fw-semibold small">Foto del Socio</label>
                                <input type="file" class="form-control form-control-sm" id="photo" name="photo" accept="image/*">
                                <div class="form-text small">Formatos: JPG, PNG, GIF. Máximo 2MB</div>
                            </div>

                            <!-- Membresía -->
                            <h6 class="text-success border-bottom pb-2 mb-3 small">
                                <i class="fas fa-id-badge me-2"></i>Membresía
                            </h6>
                            
                            <div class="mb-2">
                                <label for="plan_id" class="form-label fw-semibold small">Plan de Membresía</label>
                                <select class="form-select form-select-sm" id="plan_id" name="plan_id">
                                    <option value="">Sin plan asignado</option>
                                    @foreach($planes as $plan)
                                        <option value="{{ $plan->id }}">
                                            {{ $plan->plan_name }} - ${{ number_format($plan->price, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label for="subscription_start_date" class="form-label fw-semibold small">Fecha de Inicio</label>
                                    <input type="date" class="form-control form-control-sm" id="subscription_start_date" 
                                           name="subscription_start_date" value="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="subscription_end_date" class="form-label fw-semibold small">Fecha de Fin</label>
                                    <input type="date" class="form-control form-control-sm" id="subscription_end_date" 
                                           name="subscription_end_date">
                                </div>
                            </div>

                            <div class="mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="status" name="status" 
                                           value="active" checked>
                                    <label class="form-check-label fw-semibold small" for="status">
                                        <i class="fas fa-user-check me-1"></i>Socio Activo
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i>Registrar Socio
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de confirmación para eliminar -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
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
// Función para corregir problemas de accesibilidad en modales
function setupModalAccessibility(modalElement) {
    modalElement.addEventListener('shown.bs.modal', function() {
        this.removeAttribute('aria-hidden');
        // Asegurar que el foco esté en el modal
        this.focus();
    });
    
    modalElement.addEventListener('hidden.bs.modal', function() {
        this.setAttribute('aria-hidden', 'true');
    });
}

function confirmarEliminacion(socioId) {
    const form = document.getElementById('deleteForm');
    form.action = `/admin/socios/${socioId}`;
    
    const modalElement = document.getElementById('deleteModal');
    const modal = new bootstrap.Modal(modalElement);
    
    // Configurar accesibilidad
    setupModalAccessibility(modalElement);
    
    modal.show();
}

// Función para abrir el modal de edición
function abrirModalEditar(socioId) {
    // Hacer petición AJAX para obtener los datos del socio
    fetch(`/admin/socios/${socioId}/edit`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const socio = data.socio;
                
                // Configurar la acción del formulario
                document.getElementById('editSocioForm').action = `/admin/socios/${socioId}`;
                
                // Llenar los campos del formulario
                document.getElementById('edit_member_id').value = socio.member_id || '';
                document.getElementById('edit_firstname').value = socio.firstname || '';
                document.getElementById('edit_lastname').value = socio.lastname || '';
                document.getElementById('edit_email').value = socio.email || '';
                document.getElementById('edit_contact_info').value = socio.contact_info || '';
                
                // Mapear género a valores del select
                let genderValue = '';
                if (socio.gender) {
                    const gender = socio.gender.toLowerCase();
                    if (gender === 'masculino' || gender === 'm') {
                        genderValue = 'M';
                    } else if (gender === 'femenino' || gender === 'f') {
                        genderValue = 'F';
                    } else {
                        genderValue = 'Otro';
                    }
                }
                document.getElementById('edit_gender').value = genderValue;
                
                document.getElementById('edit_birthdate').value = socio.birthdate ? socio.birthdate.split('T')[0] : '';
                document.getElementById('edit_address').value = socio.address || '';
                document.getElementById('edit_plan_id').value = socio.plan_id || '';
                document.getElementById('edit_subscription_start_date').value = socio.subscription_start_date ? socio.subscription_start_date.split('T')[0] : '';
                document.getElementById('edit_subscription_end_date').value = socio.subscription_end_date ? socio.subscription_end_date.split('T')[0] : '';
                
                // Checkbox de estado activo
                document.getElementById('edit_status').checked = socio.status === 'active';
                
                // Mostrar foto actual
                const photoContainer = document.getElementById('current-photo-container');
                if (socio.photo) {
                    photoContainer.innerHTML = `
                        <img src="/storage/${socio.photo}" 
                             alt="Foto de ${socio.firstname} ${socio.lastname}" 
                             class="rounded-circle img-fluid"
                             style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #ffc107;">
                    `;
                } else {
                    photoContainer.innerHTML = `
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto border" 
                             style="width: 120px; height: 120px; border: 3px solid #ffc107 !important;">
                            <i class="fas fa-user fa-3x text-muted"></i>
                        </div>
                    `;
                }
                
                // Actualizar el título del modal
                document.getElementById('editSocioModalLabel').innerHTML = `
                    <i class="fas fa-user-edit me-2"></i>Editar Socio: ${socio.firstname} ${socio.lastname}
                `;
                
                // Mostrar el modal
                const modalElement = document.getElementById('editSocioModal');
                const modal = new bootstrap.Modal(modalElement);
                
                // Configurar accesibilidad
                setupModalAccessibility(modalElement);
                
                modal.show();
            } else {
                alert('Error al cargar los datos del socio');
            }
        })
        .catch(error => {
            console.error('Error completo:', error);
            
            // Mostrar mensaje más detallado
            if (error.message.includes('HTTP error')) {
                alert(`Error del servidor: ${error.message}`);
            } else if (error.name === 'TypeError') {
                alert('Error de conectividad. Verifica tu conexión.');
            } else {
                alert(`Error al cargar los datos del socio: ${error.message}`);
            }
        });
}

// Funcionalidad para el modal de creación de socio
document.addEventListener('DOMContentLoaded', function() {
    const createModal = document.getElementById('createSocioModal');
    const createForm = document.getElementById('createSocioForm');
    const editModal = document.getElementById('editSocioModal');
    const editForm = document.getElementById('editSocioForm');
    const planSelect = document.getElementById('plan_id');
    const startDateInput = document.getElementById('subscription_start_date');
    const endDateInput = document.getElementById('subscription_end_date');

    // Mostrar modal automáticamente si hay errores de validación
    @if($errors->any())
        const modalElement = createModal;
        const modal = new bootstrap.Modal(modalElement);
        
        // Configurar accesibilidad
        setupModalAccessibility(modalElement);
        
        modal.show();
        
        // Restaurar valores del formulario
        @if(old('member_id'))
            document.getElementById('member_id').value = '{{ old('member_id') }}';
        @endif
        @if(old('firstname'))
            document.getElementById('firstname').value = '{{ old('firstname') }}';
        @endif
        @if(old('lastname'))
            document.getElementById('lastname').value = '{{ old('lastname') }}';
        @endif
        @if(old('contact_info'))
            document.getElementById('contact_info').value = '{{ old('contact_info') }}';
        @endif
        @if(old('gender'))
            document.getElementById('gender').value = '{{ old('gender') }}';
        @endif
        @if(old('birthdate'))
            document.getElementById('birthdate').value = '{{ old('birthdate') }}';
        @endif
        @if(old('address'))
            document.getElementById('address').value = '{{ old('address') }}';
        @endif
        @if(old('plan_id'))
            document.getElementById('plan_id').value = '{{ old('plan_id') }}';
        @endif
        @if(old('subscription_start_date'))
            document.getElementById('subscription_start_date').value = '{{ old('subscription_start_date') }}';
        @endif
        @if(old('subscription_end_date'))
            document.getElementById('subscription_end_date').value = '{{ old('subscription_end_date') }}';
        @endif
    @endif

    // Calcular fecha de fin automáticamente cuando se selecciona un plan
    planSelect.addEventListener('change', function() {
        calculateEndDate();
    });

    // También calcular cuando cambia la fecha de inicio
    startDateInput.addEventListener('change', function() {
        calculateEndDate();
    });

    // Función para calcular la fecha de fin
    function calculateEndDate() {
        const planId = planSelect.value;
        const startDate = startDateInput.value;
        
        if (planId && startDate) {
            // Obtener la duración del plan seleccionado
            const selectedOption = planSelect.options[planSelect.selectedIndex];
            if (selectedOption && selectedOption.value) {
                // Hacer petición AJAX para obtener los días del plan
                fetch(`/admin/membership-plans/${planId}/duration`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.duration_days) {
                            const start = new Date(startDate);
                            const end = new Date(start);
                            end.setDate(end.getDate() + data.duration_days);
                            
                            endDateInput.value = end.toISOString().split('T')[0];
                            
                            // Mostrar información visual
                            const infoElement = endDateInput.parentNode.querySelector('.plan-info');
                            if (infoElement) {
                                infoElement.remove();
                            }
                            
                            const info = document.createElement('div');
                            info.className = 'form-text plan-info text-success';
                            info.innerHTML = `<i class="fas fa-info-circle me-1"></i>Duración: ${data.duration_days} días`;
                            endDateInput.parentNode.appendChild(info);
                        }
                    })
                    .catch(error => {
                        console.error('Error al obtener duración del plan:', error);
                        // Fallback: calcular con 30 días por defecto
                        const start = new Date(startDate);
                        const end = new Date(start);
                        end.setDate(end.getDate() + 30);
                        endDateInput.value = end.toISOString().split('T')[0];
                    });
            }
        } else if (!planId) {
            // Si no hay plan seleccionado, limpiar las fechas
            endDateInput.value = '';
            const infoElement = endDateInput.parentNode.querySelector('.plan-info');
            if (infoElement) {
                infoElement.remove();
            }
        }
    }

    // Limpiar formulario cuando se cierra el modal (solo si no hay errores)
    createModal.addEventListener('hidden.bs.modal', function() {
        @if(!$errors->any())
            createForm.reset();
            // Restablecer fecha de inicio a hoy
            startDateInput.value = new Date().toISOString().split('T')[0];
            endDateInput.value = '';
        @endif
    });

    // Envío del formulario con feedback visual
    createForm.addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Registrando...';
            submitBtn.disabled = true;
        }
    });

    // Envío del formulario de edición con feedback visual
    editForm.addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Actualizando...';
            submitBtn.disabled = true;
        }
    });

    // Funcionalidad para el modal de edición - calcular fecha de fin
    const editPlanSelect = document.getElementById('edit_plan_id');
    const editStartDateInput = document.getElementById('edit_subscription_start_date');
    const editEndDateInput = document.getElementById('edit_subscription_end_date');

    if (editPlanSelect && editStartDateInput && editEndDateInput) {
        editPlanSelect.addEventListener('change', function() {
            calculateEditEndDate();
        });

        editStartDateInput.addEventListener('change', function() {
            calculateEditEndDate();
        });

        function calculateEditEndDate() {
            const planId = editPlanSelect.value;
            const startDate = editStartDateInput.value;
            
            if (planId && startDate) {
                fetch(`/admin/membership-plans/${planId}/duration`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.duration_days) {
                            const start = new Date(startDate);
                            const end = new Date(start);
                            end.setDate(end.getDate() + data.duration_days);
                            
                            editEndDateInput.value = end.toISOString().split('T')[0];
                            
                            // Mostrar información visual
                            const infoElement = editEndDateInput.parentNode.querySelector('.plan-info');
                            if (infoElement) {
                                infoElement.remove();
                            }
                            
                            const info = document.createElement('div');
                            info.className = 'form-text plan-info text-success small';
                            info.innerHTML = `<i class="fas fa-info-circle me-1"></i>Duración: ${data.duration_days} días`;
                            editEndDateInput.parentNode.appendChild(info);
                        }
                    })
                    .catch(error => {
                        console.error('Error al obtener duración del plan:', error);
                    });
            } else if (!planId) {
                // Si no hay plan seleccionado, limpiar las fechas
                editEndDateInput.value = '';
                const infoElement = editEndDateInput.parentNode.querySelector('.plan-info');
                if (infoElement) {
                    infoElement.remove();
                }
            }
        }
    }

    // Generar ID automático
    const memberIdInput = document.getElementById('member_id');
    const generateId = () => {
        const now = new Date();
        const year = now.getFullYear().toString().slice(-2);
        const month = (now.getMonth() + 1).toString().padStart(2, '0');
        const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
        return `SOC${year}${month}${random}`;
    };

    // Generar ID cuando el campo está vacío y se abre el modal
    createModal.addEventListener('shown.bs.modal', function() {
        if (!memberIdInput.value) {
            memberIdInput.value = generateId();
        }
    });
});
</script>
@endpush