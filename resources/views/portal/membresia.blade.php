@extends('layouts.portal')

@section('title', 'Mi Membresía')

@section('content')
<div class="row">
    <!-- Membership Info -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-id-card me-2"></i>Información de Membresía
                </h5>
            </div>
            <div class="card-body">
                @if($plan)
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="text-primary mb-3">{{ $plan->plan_name }}</h4>
                            
                            <div class="mb-3">
                                <label class="small text-muted">Descripción:</label>
                                <p class="mb-2">{{ $plan->description }}</p>
                            </div>
                            
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label class="small text-muted">Precio:</label>
                                    <div class="h5 text-success">
                                        ${{ number_format($plan->price, 0) }}
                                        <small class="text-muted">/ {{ $plan->duration_type }}</small>
                                    </div>
                                </div>
                                
                                <div class="col-6 mb-3">
                                    <label class="small text-muted">Duración:</label>
                                    <div class="h6">{{ $plan->duration_value }} {{ $plan->duration_type }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="text-center">
                                <div class="position-relative d-inline-block mb-3">
                                    @php
                                        $diasRestantes = $socio->subscription_end_date ? now()->diffInDays($socio->subscription_end_date, false) : 0;
                                        $porcentaje = $diasRestantes > 0 ? min(100, ($diasRestantes / 30) * 100) : 0;
                                    @endphp
                                    
                                    <svg width="120" height="120" viewBox="0 0 42 42" class="donut">
                                        <circle cx="21" cy="21" r="15.91549430918954" fill="transparent" 
                                                stroke="#e9ecef" stroke-width="3"></circle>
                                        <circle cx="21" cy="21" r="15.91549430918954" fill="transparent" 
                                                stroke="{{ $diasRestantes > 7 ? '#28a745' : ($diasRestantes > 0 ? '#ffc107' : '#dc3545') }}" 
                                                stroke-width="3"
                                                stroke-dasharray="{{ $porcentaje }} {{ 100 - $porcentaje }}"
                                                stroke-dashoffset="25"></circle>
                                    </svg>
                                    
                                    <div class="position-absolute top-50 start-50 translate-middle text-center">
                                        <div class="h4 mb-0 {{ $diasRestantes > 7 ? 'text-success' : ($diasRestantes > 0 ? 'text-warning' : 'text-danger') }}">
                                            {{ $diasRestantes > 0 ? $diasRestantes : '0' }}
                                        </div>
                                        <small class="text-muted">días</small>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="small text-muted">Estado:</label>
                                    <div>
                                        <span class="badge badge-status-{{ $socio->status }} fs-6">
                                            @switch($socio->status)
                                                @case('activo')
                                                    <i class="fas fa-check-circle me-1"></i>Activa
                                                    @break
                                                @case('vencido')
                                                    <i class="fas fa-times-circle me-1"></i>Vencida
                                                    @break
                                                @case('proximo_vencimiento')
                                                    <i class="fas fa-exclamation-triangle me-1"></i>Por Vencer
                                                    @break
                                                @default
                                                    <i class="fas fa-question-circle me-1"></i>Sin Plan
                                            @endswitch
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <!-- Membership Dates -->
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <div class="border-end">
                                <i class="fas fa-play-circle fa-2x text-success mb-2"></i>
                                <div class="small text-muted">Fecha de Inicio</div>
                                <div class="fw-bold">
                                    {{ $socio->subscription_start_date ? $socio->subscription_start_date->format('d/m/Y') : 'No definida' }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 text-center">
                            <div class="border-end">
                                <i class="fas fa-stop-circle fa-2x text-danger mb-2"></i>
                                <div class="small text-muted">Fecha de Vencimiento</div>
                                <div class="fw-bold">
                                    {{ $socio->subscription_end_date ? $socio->subscription_end_date->format('d/m/Y') : 'No definida' }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 text-center">
                            <i class="fas fa-credit-card fa-2x text-primary mb-2"></i>
                            <div class="small text-muted">Próximo Pago</div>
                            <div class="fw-bold">
                                @if($socio->subscription_end_date)
                                    {{ \Carbon\Carbon::parse($socio->subscription_end_date)->addDay()->format('d/m/Y') }}
                                @else
                                    No programado
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Renewal Button -->
                    @if($socio->status === 'vencido' || $diasRestantes <= 7)
                        <div class="alert alert-{{ $socio->status === 'vencido' ? 'danger' : 'warning' }} mt-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="alert-heading mb-1">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        @if($socio->status === 'vencido')
                                            ¡Membresía Vencida!
                                        @else
                                            ¡Membresía por Vencer!
                                        @endif
                                    </h6>
                                    <p class="mb-0">
                                        @if($socio->status === 'vencido')
                                            Tu membresía venció el {{ $socio->subscription_end_date->format('d/m/Y') }}. 
                                            Renuévala para seguir disfrutando de todos los beneficios.
                                        @else
                                            Tu membresía vence en {{ $diasRestantes }} días. 
                                            Te recomendamos renovarla con anticipación.
                                        @endif
                                    </p>
                                </div>
                                <div class="flex-shrink-0 ms-3">
                                    <button class="btn {{ $socio->status === 'vencido' ? 'btn-light' : 'btn-dark' }}" 
                                            onclick="renewMembership()">
                                        <i class="fas fa-sync-alt me-2"></i>Renovar Ahora
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    <!-- No Membership -->
                    <div class="text-center py-5">
                        <i class="fas fa-id-card fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">Sin Membresía Activa</h4>
                        <p class="text-muted">Actualmente no tienes un plan de membresía asignado.</p>
                        <button class="btn btn-primary" onclick="contactAdmin()">
                            <i class="fas fa-phone me-2"></i>Contactar Administración
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Payment History -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>Historial de Pagos
                </h5>
            </div>
            <div class="card-body">
                @if($historialPagos->count() > 0)
                    @foreach($historialPagos as $pago)
                        <div class="d-flex align-items-center mb-3 {{ !$loop->last ? 'border-bottom pb-3' : '' }}">
                            <div class="flex-shrink-0 me-3">
                                <div class="rounded-circle bg-success d-flex align-items-center justify-content-center text-white"
                                     style="width: 40px; height: 40px;">
                                    <i class="fas fa-check"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">${{ number_format($pago->monto, 0) }}</div>
                                <small class="text-muted d-block">{{ $pago->concepto }}</small>
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($pago->fecha)->format('d/m/Y') }}
                                </small>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="badge bg-success">Pagado</span>
                            </div>
                        </div>
                    @endforeach
                    
                    <div class="text-center mt-3">
                        <button class="btn btn-sm btn-outline-primary" onclick="showAllPayments()">
                            Ver Historial Completo
                        </button>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No hay pagos registrados</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-credit-card me-2"></i>Métodos de Pago
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary btn-sm" onclick="payOnline()">
                        <i class="fas fa-globe me-2"></i>Pago en Línea
                    </button>
                    <button class="btn btn-outline-success btn-sm" onclick="payInPerson()">
                        <i class="fas fa-building me-2"></i>Pago en Gimnasio
                    </button>
                    <button class="btn btn-outline-info btn-sm" onclick="bankTransfer()">
                        <i class="fas fa-university me-2"></i>Transferencia
                    </button>
                </div>
                
                <hr>
                
                <div class="text-center">
                    <small class="text-muted">
                        <i class="fas fa-shield-alt me-1"></i>
                        Pagos 100% seguros
                    </small>
                </div>
            </div>
        </div>

        <!-- Contact Info -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-question-circle me-2"></i>¿Necesitas Ayuda?
                </h6>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <p class="small text-muted mb-3">
                        Para renovaciones o dudas sobre tu membresía
                    </p>
                    
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary btn-sm" onclick="callGym()">
                            <i class="fas fa-phone me-2"></i>(555) 123-4567
                        </button>
                        <button class="btn btn-outline-success btn-sm" onclick="whatsappGym()">
                            <i class="fab fa-whatsapp me-2"></i>WhatsApp
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment History Modal -->
<div class="modal fade" id="paymentHistoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-history me-2"></i>Historial Completo de Pagos
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Concepto</th>
                                <th>Monto</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($historialPagos as $pago)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($pago->fecha)->format('d/m/Y') }}</td>
                                    <td>{{ $pago->concepto }}</td>
                                    <td class="fw-bold text-success">${{ number_format($pago->monto, 0) }}</td>
                                    <td>
                                        <span class="badge bg-success">{{ ucfirst($pago->estado) }}</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="downloadReceipt({{ $loop->index }})">
                                            <i class="fas fa-download"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="exportPayments()">
                    <i class="fas fa-file-excel me-2"></i>Exportar Excel
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.donut {
    transform: rotate(-90deg);
}

.badge-status-activo {
    background-color: #28a745;
}

.badge-status-vencido {
    background-color: #dc3545;
}

.badge-status-proximo_vencimiento {
    background-color: #ffc107;
    color: #000;
}

.badge-status-sin_plan {
    background-color: #6c757d;
}
</style>
@endpush

@push('scripts')
<script>
function renewMembership() {
    alert('Próximamente: Sistema de renovación automática\n\nPor ahora, contacta al gimnasio para renovar tu membresía.');
}

function contactAdmin() {
    alert('Contacta al gimnasio:\nTeléfono: (555) 123-4567\nEmail: admin@gymcontrol.com');
}

function showAllPayments() {
    const modal = new bootstrap.Modal(document.getElementById('paymentHistoryModal'));
    modal.show();
}

function payOnline() {
    alert('Próximamente: Pagos en línea con tarjeta de crédito/débito');
}

function payInPerson() {
    alert('Puedes pagar directamente en el gimnasio:\n\nHorarios de caja:\nLunes a Viernes: 6:00 AM - 10:00 PM\nSábados y Domingos: 7:00 AM - 8:00 PM');
}

function bankTransfer() {
    alert('Datos para transferencia bancaria:\n\nBanco: Banco Nacional\nCuenta: 1234567890\nCLABE: 012345678901234567\nTitular: Gym Control S.A. de C.V.');
}

function callGym() {
    window.location.href = 'tel:+15551234567';
}

function whatsappGym() {
    window.open('https://wa.me/15551234567?text=Hola,%20necesito%20ayuda%20con%20mi%20membresía', '_blank');
}

function downloadReceipt(index) {
    alert('Descargando recibo #' + (index + 1) + '...\nPróximamente: Descarga automática de recibos');
}

function exportPayments() {
    alert('Próximamente: Exportación de historial de pagos a Excel');
}

// Animate progress circle on load
document.addEventListener('DOMContentLoaded', function() {
    const circle = document.querySelector('.donut circle:last-child');
    if (circle) {
        const length = circle.getTotalLength();
        circle.style.strokeDasharray = length + ' ' + length;
        circle.style.strokeDashoffset = length;
        
        setTimeout(() => {
            circle.style.transition = 'stroke-dashoffset 2s ease-in-out';
            const percent = circle.getAttribute('stroke-dasharray').split(' ')[0];
            circle.style.strokeDashoffset = length - (length * percent / 100);
        }, 500);
    }
});
</script>
@endpush