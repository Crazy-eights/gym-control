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
                            <img src="{{ asset('storage/' . $socio->photo) }}" alt="Foto de {{ $socio->full_name }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
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
                        @if($socio->isSuspended())
                            <span class="badge badge-modern badge-danger" 
                                  data-bs-toggle="tooltip" 
                                  data-bs-placement="top"
                                  data-bs-html="true"
                                  title="<strong>Suspendido desde:</strong><br>{{ $socio->suspended_at ? $socio->suspended_at->format('d/m/Y') : 'N/A' }}<br><br><strong>Motivo:</strong><br>{{ Str::limit($socio->suspension_reason, 100) }}">
                                <i class="fas fa-user-slash me-1"></i>Suspendido
                            </span>
                        @else
                            @php
                                $status = $socio->status_membership;
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
                        @endif
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
                            <a href="{{ route('admin.socios.show', $socio) }}" class="btn btn-sm btn-outline-success" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($socio->isSuspended())
                                <button type="button" class="btn btn-sm btn-outline-success btn-activate-socio" 
                                        data-socio-id="{{ $socio->id }}"
                                        data-socio-name="{{ $socio->full_name }}"
                                        title="Activar">
                                    <i class="fas fa-user-check"></i>
                                </button>
                            @else
                                <button type="button" class="btn btn-sm btn-outline-warning btn-edit-socio" 
                                        data-socio-id="{{ $socio->id }}" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editSocioModal" 
                                        title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-suspend-socio" 
                                        data-socio-id="{{ $socio->id }}"
                                        data-socio-name="{{ $socio->full_name }}"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#suspendModal" 
                                        title="Suspender">
                                    <i class="fas fa-user-slash"></i>
                                </button>
                            @endif
                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-socio" 
                                    data-socio-id="{{ $socio->id }}" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#deleteModal" 
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
        <h5 class="mb-2">No se encontraron socios</h5>
        <p class="text-muted mb-3">
            @if(request()->hasAny(['search', 'status', 'gender', 'plan_id']))
                No hay socios que coincidan con los filtros aplicados.
                <a href="{{ route('admin.socios.index') }}" class="text-success">Limpiar filtros</a>
            @else
                Comienza registrando tu primer socio.
            @endif
        </p>
        @if(!request()->hasAny(['search', 'status', 'gender', 'plan_id']))
            <button type="button" class="btn btn-success btn-modern" data-bs-toggle="modal" data-bs-target="#createSocioModal">
                <i class="fas fa-plus me-2"></i>Registrar Socio
            </button>
        @endif
    </div>
@endif