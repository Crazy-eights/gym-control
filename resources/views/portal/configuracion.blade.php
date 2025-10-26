@extends('layouts.portal')

@section('title', 'Configuración')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-cog me-2"></i>Configuración de Cuenta
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Account Settings -->
                    <div class="col-lg-8">
                        <h6 class="text-primary border-bottom pb-2 mb-4">
                            <i class="fas fa-user-cog me-2"></i>Configuración de Cuenta
                        </h6>
                        
                        <form>
                            <div class="row">
                                <!-- Notification Preferences -->
                                <div class="col-12 mb-4">
                                    <h6 class="mb-3">
                                        <i class="fas fa-bell me-2"></i>Preferencias de Notificaciones
                                    </h6>
                                    
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="emailNotifications" checked>
                                        <label class="form-check-label" for="emailNotifications">
                                            <strong>Notificaciones por Email</strong>
                                            <br><small class="text-muted">Recibir recordatorios de pagos y actualizaciones importantes</small>
                                        </label>
                                    </div>
                                    
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="classReminders" checked>
                                        <label class="form-check-label" for="classReminders">
                                            <strong>Recordatorios de Clases</strong>
                                            <br><small class="text-muted">Notificaciones 30 minutos antes de las clases reservadas</small>
                                        </label>
                                    </div>
                                    
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="paymentReminders" checked>
                                        <label class="form-check-label" for="paymentReminders">
                                            <strong>Recordatorios de Pago</strong>
                                            <br><small class="text-muted">Avisos 3 días antes del vencimiento de membresía</small>
                                        </label>
                                    </div>
                                    
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="promotionalEmails">
                                        <label class="form-check-label" for="promotionalEmails">
                                            <strong>Emails Promocionales</strong>
                                            <br><small class="text-muted">Ofertas especiales y noticias del gimnasio</small>
                                        </label>
                                    </div>
                                </div>

                                <!-- Privacy Settings -->
                                <div class="col-12 mb-4">
                                    <h6 class="mb-3">
                                        <i class="fas fa-shield-alt me-2"></i>Privacidad
                                    </h6>
                                    
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="profileVisibility" checked>
                                        <label class="form-check-label" for="profileVisibility">
                                            <strong>Perfil Visible</strong>
                                            <br><small class="text-muted">Permitir que otros socios vean tu perfil público</small>
                                        </label>
                                    </div>
                                    
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="attendanceTracking" checked>
                                        <label class="form-check-label" for="attendanceTracking">
                                            <strong>Seguimiento de Asistencias</strong>
                                            <br><small class="text-muted">Registrar automáticamente tus visitas al gimnasio</small>
                                        </label>
                                    </div>
                                </div>

                                <!-- App Preferences -->
                                <div class="col-12 mb-4">
                                    <h6 class="mb-3">
                                        <i class="fas fa-palette me-2"></i>Preferencias de Interfaz
                                    </h6>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="language" class="form-label">Idioma</label>
                                            <select class="form-select" id="language">
                                                <option value="es" selected>Español</option>
                                                <option value="en">English</option>
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="timezone" class="form-label">Zona Horaria</label>
                                            <select class="form-select" id="timezone">
                                                <option value="America/Mexico_City" selected>México (GMT-6)</option>
                                                <option value="America/New_York">New York (GMT-5)</option>
                                                <option value="Europe/Madrid">Madrid (GMT+1)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-outline-secondary me-2">Cancelar</button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Guardar Configuración
                                </button>
                            </div>
                        </form>
                        
                        <!-- Change Password Section -->
                        <div class="mt-5">
                            <h6 class="text-primary border-bottom pb-2 mb-4">
                                <i class="fas fa-key me-2"></i>Cambiar Contraseña
                            </h6>
                            
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif
                            
                            <form method="POST" action="{{ route('portal.password.cambiar') }}">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="current_password" class="form-label">Contraseña Actual</label>
                                        <input type="password" 
                                               class="form-control @error('current_password') is-invalid @enderror" 
                                               id="current_password" 
                                               name="current_password" 
                                               required>
                                        @error('current_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="password" class="form-label">Nueva Contraseña</label>
                                        <input type="password" 
                                               class="form-control @error('password') is-invalid @enderror" 
                                               id="password" 
                                               name="password" 
                                               required>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Mínimo 8 caracteres</div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="password_confirmation" class="form-label">Confirmar Nueva Contraseña</label>
                                        <input type="password" 
                                               class="form-control" 
                                               id="password_confirmation" 
                                               name="password_confirmation" 
                                               required>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-key me-2"></i>Cambiar Contraseña
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Quick Info Panel -->
                    <div class="col-lg-4">
                        <!-- Account Info -->
                        <div class="card bg-light">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Información de Cuenta
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <small class="text-muted">Número de Socio</small>
                                    <div class="fw-bold">#{{ $socio->member_id }}</div>
                                </div>
                                
                                <div class="mb-3">
                                    <small class="text-muted">Miembro desde</small>
                                    <div class="fw-bold">{{ $socio->created_at->format('d/m/Y') }}</div>
                                </div>
                                
                                <div class="mb-3">
                                    <small class="text-muted">Último acceso</small>
                                    <div class="fw-bold">{{ now()->format('d/m/Y H:i') }}</div>
                                </div>
                                
                                <div class="mb-0">
                                    <small class="text-muted">Estado de cuenta</small>
                                    <div>
                                        <span class="badge badge-status-{{ $socio->status }}">
                                            {{ ucfirst($socio->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-bolt me-2"></i>Acciones Rápidas
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('portal.perfil') }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-user me-2"></i>Editar Perfil
                                    </a>
                                    
                                    <a href="{{ route('portal.membresia') }}" class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-id-card me-2"></i>Ver Membresía
                                    </a>
                                    
                                    <button class="btn btn-outline-info btn-sm" onclick="downloadData()">
                                        <i class="fas fa-download me-2"></i>Descargar Datos
                                    </button>
                                    
                                    <button class="btn btn-outline-warning btn-sm" onclick="resetPreferences()">
                                        <i class="fas fa-undo me-2"></i>Restaurar Configuración
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Support -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-life-ring me-2"></i>Soporte
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="small text-muted mb-3">
                                    ¿Necesitas ayuda con tu cuenta o tienes alguna pregunta?
                                </p>
                                
                                <div class="d-grid gap-2">
                                    <button class="btn btn-outline-secondary btn-sm" onclick="openHelp()">
                                        <i class="fas fa-question-circle me-2"></i>Centro de Ayuda
                                    </button>
                                    
                                    <button class="btn btn-outline-secondary btn-sm" onclick="contactSupport()">
                                        <i class="fas fa-envelope me-2"></i>Contactar Soporte
                                    </button>
                                </div>
                                
                                <div class="text-center mt-3">
                                    <small class="text-muted">
                                        <i class="fas fa-phone me-1"></i>
                                        Teléfono: (555) 123-4567
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Data Download Modal -->
<div class="modal fade" id="downloadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-download me-2"></i>Descargar Mis Datos
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Puedes descargar una copia de todos tus datos personales:</p>
                
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="includeProfile" checked>
                    <label class="form-check-label" for="includeProfile">
                        Información de perfil
                    </label>
                </div>
                
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="includeAttendance" checked>
                    <label class="form-check-label" for="includeAttendance">
                        Historial de asistencias
                    </label>
                </div>
                
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="includePayments" checked>
                    <label class="form-check-label" for="includePayments">
                        Historial de pagos
                    </label>
                </div>
                
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="includeClasses">
                    <label class="form-check-label" for="includeClasses">
                        Clases reservadas
                    </label>
                </div>
                
                <div class="alert alert-info">
                    <small>
                        <i class="fas fa-info-circle me-1"></i>
                        El archivo se generará en formato PDF y será enviado a tu email.
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="confirmDownload()">
                    <i class="fas fa-download me-2"></i>Generar y Enviar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Save settings automatically when changed
    const settingsForm = document.querySelector('form');
    const switches = settingsForm.querySelectorAll('.form-check-input');
    const selects = settingsForm.querySelectorAll('.form-select');
    
    switches.forEach(switch_ => {
        switch_.addEventListener('change', function() {
            showToast('Configuración guardada automáticamente', 'success');
        });
    });
    
    selects.forEach(select => {
        select.addEventListener('change', function() {
            showToast('Configuración guardada automáticamente', 'success');
        });
    });
});

function downloadData() {
    const modal = new bootstrap.Modal(document.getElementById('downloadModal'));
    modal.show();
}

function confirmDownload() {
    // Simulate download process
    showToast('Generando archivo... Se enviará a tu email en unos minutos.', 'info');
    bootstrap.Modal.getInstance(document.getElementById('downloadModal')).hide();
}

function resetPreferences() {
    if (confirm('¿Estás seguro de que quieres restaurar la configuración por defecto?')) {
        // Reset all switches to default
        document.getElementById('emailNotifications').checked = true;
        document.getElementById('classReminders').checked = true;
        document.getElementById('paymentReminders').checked = true;
        document.getElementById('promotionalEmails').checked = false;
        document.getElementById('profileVisibility').checked = true;
        document.getElementById('attendanceTracking').checked = true;
        
        showToast('Configuración restaurada por defecto', 'success');
    }
}

function openHelp() {
    // Simulate opening help center
    alert('Próximamente: Centro de Ayuda');
}

function contactSupport() {
    // Simulate opening support contact
    alert('Próximamente: Sistema de tickets de soporte');
}

function showToast(message, type) {
    // Create toast notification
    const toastContainer = document.getElementById('toastContainer') || createToastContainer();
    
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-check-circle me-2"></i>${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    // Remove toast after it's hidden
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toastContainer';
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '1050';
    document.body.appendChild(container);
    return container;
}
</script>
@endpush