@extends('layouts.admin-modern')
@section('title', 'Configuración de Email')
@section('page-title', 'Configuración de Email')
@section('header-color', 'bg-success')

@push('styles')
<style>
.form-check-card .form-check-input:checked + .form-check-label {
    border-color: #28a745 !important;
    background-color: #f8f9fa;
    box-shadow: 0 0 0 2px rgba(40, 167, 69, 0.25);
}

.form-check-card .form-check-input {
    position: absolute;
    clip: rect(0, 0, 0, 0);
    pointer-events: none;
}

.form-check-card .form-check-label {
    cursor: pointer;
    transition: all 0.3s ease;
    margin-bottom: 0;
    width: 100%;
    border-radius: 8px !important;
}

.form-check-card .form-check-label:hover {
    border-color: #28a745 !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.alert-info {
    border-left: 4px solid #17a2b8;
}

.text-danger {
    color: #dc3545 !important;
}

#smtp-config, #oauth-config {
    transition: all 0.3s ease;
}
</style>
@endpush

@section('content')
<div class="animate-fade-in-up">
    <!-- Header Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-success mb-1">
                <i class="fas fa-envelope me-2"></i>Configuración de Email
            </h2>
            <p class="text-muted mb-0">Configura el sistema de correo electrónico para tu gimnasio</p>
        </div>
        <button type="button" class="btn btn-success btn-modern" data-bs-toggle="modal" data-bs-target="#testEmailModal">
            <i class="fas fa-paper-plane me-2"></i>Enviar Prueba
        </button>
    </div>

    <!-- Main Configuration Card -->
    <div class="content-section">
        <form method="POST" action="{{ route('admin.mail.config.update') }}">
                @csrf
                @method('PUT')

                <!-- Campos ocultos para el formulario -->
                <input type="hidden" id="hidden_auth_method" name="auth_method" value="smtp">
                <input type="hidden" id="hidden_mail_driver" name="mail_driver" value="smtp">
                <input type="hidden" id="hidden_mail_provider" name="mail_provider" value="custom">

                <!-- Método de Autenticación -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="text-success border-bottom pb-2">
                            <i class="fas fa-key me-2"></i>Método de Autenticación
                        </h5>
                    </div>
                    <div class="col-12">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check form-check-card">
                                    <input class="form-check-input" type="radio" name="auth_method" id="auth_smtp" value="smtp" 
                                        {{ old('auth_method', $mailSettings['auth_method'] ?? 'smtp') == 'smtp' ? 'checked' : '' }} 
                                        onchange="toggleAuthMethod()">
                                    <label class="form-check-label card p-3 border" for="auth_smtp">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-envelope fa-2x text-success me-3"></i>
                                            <div>
                                                <h6 class="mb-1">SMTP Tradicional</h6>
                                                <small class="text-muted">Usar usuario y contraseña</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-check-card">
                                    <input class="form-check-input" type="radio" name="auth_method" id="auth_oauth" value="oauth_microsoft" 
                                        {{ old('auth_method', $mailSettings['auth_method'] ?? '') == 'oauth_microsoft' ? 'checked' : '' }} 
                                        onchange="toggleAuthMethod()">
                                    <label class="form-check-label card p-3 border" for="auth_oauth">
                                        <div class="d-flex align-items-center">
                                            <i class="fab fa-microsoft fa-2x text-info me-3"></i>
                                            <div>
                                                <h6 class="mb-1">OAuth Microsoft</h6>
                                                <small class="text-muted">Autenticación moderna y segura</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Proveedor de Email - Solo visible para SMTP -->
                <div class="row mb-4" id="provider-config">
                    <div class="col-12">
                        <h5 class="text-success border-bottom pb-2">
                            <i class="fas fa-server me-2"></i>Proveedor de Email
                        </h5>
                    </div>
                    <div class="col-md-6">
                        <label for="mail_provider" class="form-label">Proveedor de Email</label>
                        <select class="form-select" id="mail_provider" name="mail_provider" onchange="applyProviderPreset()">
                            <option value="custom" {{ old('mail_provider', $mailSettings['mail_provider'] ?? 'custom') == 'custom' ? 'selected' : '' }}>Configuración Personalizada</option>
                            <option value="gmail" {{ old('mail_provider', $mailSettings['mail_provider'] ?? '') == 'gmail' ? 'selected' : '' }}>Gmail</option>
                            <option value="outlook" {{ old('mail_provider', $mailSettings['mail_provider'] ?? '') == 'outlook' ? 'selected' : '' }}>Outlook/Hotmail</option>
                            <option value="yahoo" {{ old('mail_provider', $mailSettings['mail_provider'] ?? '') == 'yahoo' ? 'selected' : '' }}>Yahoo Mail</option>
                            <option value="sendgrid" {{ old('mail_provider', $mailSettings['mail_provider'] ?? '') == 'sendgrid' ? 'selected' : '' }}>SendGrid</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="mail_driver" class="form-label">Controlador de Email</label>
                        <select class="form-select" id="mail_driver" name="mail_driver">
                            <option value="smtp" {{ old('mail_driver', $mailSettings['mail_driver'] ?? 'smtp') == 'smtp' ? 'selected' : '' }}>SMTP</option>
                            <option value="sendmail" {{ old('mail_driver', $mailSettings['mail_driver'] ?? '') == 'sendmail' ? 'selected' : '' }}>SendMail</option>
                            <option value="log" {{ old('mail_driver', $mailSettings['mail_driver'] ?? '') == 'log' ? 'selected' : '' }}>Log (Desarrollo)</option>
                        </select>
                    </div>
                </div>

                <!-- Configuración SMTP -->
                <div class="row mb-4" id="smtp-config" style="display: none;">
                    <div class="col-12">
                        <h5 class="text-success border-bottom pb-2">
                            <i class="fas fa-cog me-2"></i>Configuración SMTP
                        </h5>
                    </div>
                    <div class="col-md-6">
                        <label for="mail_host" class="form-label">Servidor SMTP (Host) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="mail_host" name="mail_host" 
                            value="{{ old('mail_host', $mailSettings['mail_host'] ?? '') }}" 
                            placeholder="smtp.gmail.com" required>
                    </div>
                    <div class="col-md-3">
                        <label for="mail_port" class="form-label">Puerto <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="mail_port" name="mail_port" 
                            value="{{ old('mail_port', $mailSettings['mail_port'] ?? '587') }}" 
                            placeholder="587" required>
                    </div>
                    <div class="col-md-3">
                        <label for="mail_encryption" class="form-label">Encriptación</label>
                        <select class="form-select" id="mail_encryption" name="mail_encryption">
                            <option value="tls" {{ old('mail_encryption', $mailSettings['mail_encryption'] ?? 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                            <option value="ssl" {{ old('mail_encryption', $mailSettings['mail_encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                            <option value="" {{ old('mail_encryption', $mailSettings['mail_encryption'] ?? '') == '' ? 'selected' : '' }}>Sin Encriptación</option>
                        </select>
                    </div>
                    <div class="col-md-6 mt-3">
                        <label for="mail_username" class="form-label">Usuario SMTP <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="mail_username" name="mail_username" 
                            value="{{ old('mail_username', $mailSettings['mail_username'] ?? '') }}" 
                            placeholder="tu-email@gmail.com" required>
                    </div>
                    <div class="col-md-6 mt-3">
                        <label for="mail_password" class="form-label">Contraseña SMTP <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="mail_password" name="mail_password" 
                                value="{{ old('mail_password', $mailSettings['mail_password'] ?? '') }}" 
                                placeholder="Contraseña o App Password" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('mail_password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <small class="form-text text-muted">Para Gmail, usa una contraseña de aplicación</small>
                    </div>
                </div>

                <!-- Configuración de Identidad -->
                <div class="row mb-4" id="identity-config">
                    <div class="col-12">
                        <h5 class="text-success border-bottom pb-2">
                            <i class="fas fa-id-card me-2"></i>Identidad del Remitente
                        </h5>
                    </div>
                    <div class="col-md-6">
                        <label for="mail_from_address" class="form-label">
                            Email del Remitente <span class="text-danger">*</span>
                        </label>
                        <input type="email" class="form-control" id="mail_from_address" name="mail_from_address" 
                            value="{{ old('mail_from_address', $mailSettings['mail_from_address'] ?? '') }}" 
                            placeholder="noreply@gimnasio.com" required>
                    </div>
                    <div class="col-md-6">
                        <label for="mail_from_name" class="form-label">Nombre del Remitente <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="mail_from_name" name="mail_from_name" 
                            value="{{ old('mail_from_name', $mailSettings['mail_from_name'] ?? 'Gym Control System') }}" 
                            placeholder="Gym Control System" required>
                    </div>
                </div>

                <!-- OAuth Microsoft -->
                <div class="row mb-4" id="oauth-config" style="display: none;">
                    <div class="col-12">
                        <h5 class="text-success border-bottom pb-2">
                            <i class="fab fa-microsoft me-2"></i>Autenticación OAuth Microsoft 
                            <span class="badge bg-success ms-2">RECOMENDADO</span>
                        </h5>
                    </div>
                    
                    <div class="col-12">
                        @if(!empty($mailSettings['microsoft_access_token']) && !empty($mailSettings['microsoft_user_email']))
                            <!-- Estado conectado -->
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>¡Conectado con Microsoft!</strong><br>
                                <small class="text-muted">
                                    Usuario: {{ $mailSettings['microsoft_user_email'] }}<br>
                                    @if($mailSettings['microsoft_connected_at'])
                                        Conectado: {{ \Carbon\Carbon::parse($mailSettings['microsoft_connected_at'])->format('d/m/Y H:i') }}
                                    @endif
                                </small>
                            </div>
                            
                            <div class="text-center mb-3">
                                <a href="{{ route('admin.mail.oauth.microsoft.disconnect') }}" class="btn btn-outline-danger">
                                    <i class="fas fa-unlink me-2"></i>Desconectar OAuth
                                </a>
                                <a href="{{ route('admin.mail.oauth.microsoft') }}" class="btn btn-outline-primary ms-2">
                                    <i class="fas fa-sync me-2"></i>Reconectar
                                </a>
                            </div>
                        @else
                            <!-- Estado no conectado -->
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>OAuth Microsoft no configurado</strong><br>
                                Para usar tu cuenta de Microsoft, debes conectarte primero usando OAuth.
                            </div>
                            
                            <div class="text-center mb-3">
                                @if(env('MICROSOFT_CLIENT_ID') && env('MICROSOFT_CLIENT_SECRET') && env('MICROSOFT_TENANT_ID'))
                                    <a href="{{ route('admin.mail.oauth.microsoft') }}" class="btn btn-primary btn-lg">
                                        <i class="fab fa-microsoft me-2"></i> Conectar con Microsoft
                                    </a>
                                @else
                                    <div class="alert alert-warning mt-3">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>Credenciales no configuradas</strong><br>
                                        Para usar OAuth Microsoft, configura las siguientes variables en tu archivo .env:<br>
                                        <code>MICROSOFT_CLIENT_ID</code>, <code>MICROSOFT_CLIENT_SECRET</code>, <code>MICROSOFT_TENANT_ID</code>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Configuraciones Adicionales -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="text-success border-bottom pb-2">
                            <i class="fas fa-cogs me-2"></i>Configuraciones Adicionales
                        </h5>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="email_notifications_enabled" name="email_notifications_enabled" value="1" 
                                {{ old('email_notifications_enabled', $mailSettings['email_notifications_enabled'] ?? '1') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="email_notifications_enabled">
                                Habilitar notificaciones por email
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="email_queue_enabled" name="email_queue_enabled" value="1" 
                                {{ old('email_queue_enabled', $mailSettings['email_queue_enabled'] ?? '0') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="email_queue_enabled">
                                Usar cola para envío de emails
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="email_log_enabled" name="email_log_enabled" value="1" 
                                {{ old('email_log_enabled', $mailSettings['email_log_enabled'] ?? '1') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="email_log_enabled">
                                Registrar logs de emails
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <div>
                                <!-- Botones para SMTP -->
                                <div id="smtp-actions" style="display: none;">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save me-2"></i>Guardar Configuración SMTP
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary ms-2" onclick="resetForm()">
                                        <i class="fas fa-undo me-2"></i>Restablecer
                                    </button>
                                </div>
                                
                                <!-- Botones para OAuth -->
                                <div id="oauth-actions" style="display: none;">
                                    @if(!empty($mailSettings['microsoft_access_token']))
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-save me-2"></i>Guardar Configuración OAuth
                                        </button>
                                    @else
                                        <a href="{{ route('admin.mail.oauth.microsoft') }}" class="btn btn-success btn-lg">
                                            <i class="fab fa-microsoft me-2"></i>Conectar con Microsoft
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
    </div>
</div>

<!-- Modal para Prueba de Email -->
<div class="modal fade" id="testEmailModal" tabindex="-1" aria-labelledby="testEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-gradient-success text-white py-2">
                <h6 class="modal-title fw-bold mb-0" id="testEmailModalLabel">
                    <i class="fas fa-paper-plane me-2"></i>Enviar Email de Prueba
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form method="POST" action="{{ route('admin.mail.config.test') }}">
                @csrf
                <div class="modal-body p-3">
                    <div class="mb-3">
                        <label for="test_email" class="form-label fw-semibold">Dirección de Email para Prueba</label>
                        <input type="email" class="form-control form-control-sm" id="test_email" name="test_email" 
                            value="{{ old('test_email', $mailSettings['test_email_address'] ?? '') }}" 
                            placeholder="prueba@ejemplo.com" required>
                        <small class="form-text text-muted">Se enviará un email de prueba a esta dirección</small>
                    </div>
                    <div class="mb-3">
                        <label for="test_subject" class="form-label fw-semibold">Asunto del Email</label>
                        <input type="text" class="form-control form-control-sm" id="test_subject" name="test_subject" 
                            value="Prueba de Configuración de Email - Gym Control" required>
                    </div>
                    <div class="mb-0">
                        <label for="test_message" class="form-label fw-semibold">Mensaje</label>
                        <textarea class="form-control form-control-sm" id="test_message" name="test_message" rows="4" required>Este es un email de prueba enviado desde Gym Control System.

Si recibes este mensaje, la configuración de email está funcionando correctamente.

Fecha y hora: {{ now()->format('d/m/Y H:i:s') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-paper-plane me-1"></i>Enviar Prueba
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Función principal para alternar métodos de autenticación
function toggleAuthMethod() {
    console.log('toggleAuthMethod ejecutado');
    const authMethod = document.querySelector('input[name="auth_method"]:checked');
    if (!authMethod) {
        console.log('No hay método de autenticación seleccionado');
        return;
    }

    const authValue = authMethod.value;
    console.log('Método seleccionado:', authValue);

    // Obtener elementos
    const smtpConfig = document.getElementById('smtp-config');
    const oauthConfig = document.getElementById('oauth-config');
    const providerConfig = document.getElementById('provider-config');
    const smtpActions = document.getElementById('smtp-actions');
    const oauthActions = document.getElementById('oauth-actions');
    const providerSelect = document.getElementById('mail_provider');

    if (authValue === 'smtp') {
        console.log('Configurando SMTP');
        // Mostrar configuración SMTP
        if (smtpConfig) smtpConfig.style.display = 'block';
        if (oauthConfig) oauthConfig.style.display = 'none';
        if (providerConfig) providerConfig.style.display = 'block';
        if (smtpActions) smtpActions.style.display = 'block';
        if (oauthActions) oauthActions.style.display = 'none';
    } else if (authValue === 'oauth_microsoft') {
        console.log('Configurando OAuth Microsoft');
        // Mostrar configuración OAuth
        if (smtpConfig) smtpConfig.style.display = 'none';
        if (oauthConfig) oauthConfig.style.display = 'block';
        if (providerConfig) providerConfig.style.display = 'none';
        if (smtpActions) smtpActions.style.display = 'none';
        if (oauthActions) oauthActions.style.display = 'block';
        
        // Configurar proveedor automáticamente
        if (providerSelect) providerSelect.value = 'outlook';
    }

    console.log('toggleAuthMethod completado');
    updateHiddenFields();
}

// Función para actualizar campos ocultos según el método de autenticación
function updateHiddenFields() {
    const authMethod = document.querySelector('input[name="auth_method"]:checked');
    if (authMethod) {
        const authValue = authMethod.value;
        
        // Actualizar campos ocultos
        document.getElementById('hidden_auth_method').value = authValue;
        
        if (authValue === 'oauth_microsoft') {
            document.getElementById('hidden_mail_driver').value = 'smtp';
            document.getElementById('hidden_mail_provider').value = 'outlook';
            console.log('🔄 Campos ocultos actualizados para OAuth');
        } else {
            document.getElementById('hidden_mail_driver').value = 'smtp';
            const provider = document.getElementById('mail_provider');
            if (provider) document.getElementById('hidden_mail_provider').value = provider.value;
            console.log('🔄 Campos ocultos actualizados para SMTP');
        }
    }
}

// Mostrar/ocultar contraseña
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const button = field.nextElementSibling.querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        button.classList.remove('fa-eye');
        button.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        button.classList.remove('fa-eye-slash');
        button.classList.add('fa-eye');
    }
}

// Restablecer formulario
function resetForm() {
    if (confirm('¿Estás seguro de que quieres restablecer la configuración?')) {
        location.reload();
    }
}

// Función placeholder para el select de proveedor
function applyProviderPreset() {
    console.log('applyProviderPreset ejecutado');
}

// Ejecutar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado, ejecutando configuración inicial');
    toggleAuthMethod();
});

console.log('✅ Script de configuración de email cargado correctamente');
</script>
@endpush