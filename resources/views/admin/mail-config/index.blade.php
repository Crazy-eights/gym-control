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
    <div class="card-modern">
        <div class="p-4">
            <form method="POST" action="{{ route('admin.mail.config.update') }}">
                @csrf
                        @method('PUT')
                        
                        <!-- Campos ocultos para OAuth -->
                        <input type="hidden" id="microsoft_redirect_uri" name="microsoft_redirect_uri" 
                               value="{{ route('admin.mail.oauth.microsoft.callback') }}">
                        
                        <!-- Campos ocultos necesarios para validación OAuth -->
                        <input type="hidden" id="hidden_auth_method" name="auth_method" value="smtp">
                        <input type="hidden" id="hidden_mail_driver" name="mail_driver" value="smtp">
                        <input type="hidden" id="hidden_mail_provider" name="mail_provider" value="custom">
                        <input type="hidden" id="hidden_microsoft_client_id" name="microsoft_client_id" value="{{ old('microsoft_client_id', $mailSettings['microsoft_client_id'] ?? '') }}">
                        <input type="hidden" id="hidden_microsoft_client_secret" name="microsoft_client_secret" value="{{ old('microsoft_client_secret', $mailSettings['microsoft_client_secret'] ?? '') }}">
                        <input type="hidden" id="hidden_microsoft_tenant_id" name="microsoft_tenant_id" value="{{ old('microsoft_tenant_id', $mailSettings['microsoft_tenant_id'] ?? '') }}">

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
                                            <input class="form-check-input" type="radio" name="auth_method" id="auth_smtp" 
                                                   value="smtp" {{ old('auth_method', $mailSettings['auth_method'] ?? 'smtp') == 'smtp' ? 'checked' : '' }}
                                                   onchange="window.toggleAuthMethod()">
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
                                            <input class="form-check-input" type="radio" name="auth_method" id="auth_oauth" 
                                                   value="oauth_microsoft" {{ old('auth_method', $mailSettings['auth_method'] ?? '') == 'oauth_microsoft' ? 'checked' : '' }}
                                                   onchange="window.toggleAuthMethod()">
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
                                    <option value="outlook" id="outlook-option" {{ old('mail_provider', $mailSettings['mail_provider'] ?? '') == 'outlook' ? 'selected' : '' }}>Outlook/Hotmail</option>
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

                        <!-- Alertas específicas por proveedor -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <div id="provider-alert" class="alert alert-warning" style="display: none;">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <span id="provider-message"></span>
                                </div>
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
                                    <span id="oauth-email-notice" style="display: none;" class="text-muted small">
                                        (Obtenido automáticamente de Microsoft)
                                    </span>
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
                            <div class="col-md-6 mt-3">
                                <label for="mail_reply_to" class="form-label">Email de Respuesta (Opcional)</label>
                                <input type="email" class="form-control" id="mail_reply_to" name="mail_reply_to" 
                                       value="{{ old('mail_reply_to', $mailSettings['mail_reply_to'] ?? '') }}" 
                                       placeholder="respuestas@gimnasio.com">
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
                            
                            <!-- Estado de conexión OAuth -->
                            <div class="col-12" id="oauth-status">
                                @if(isset($mailSettings['microsoft_access_token']) && !empty($mailSettings['microsoft_access_token']))
                                    <div class="alert alert-success d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-check-circle me-2"></i>
                                            <strong>¡Conectado con Microsoft!</strong><br>
                                            <small class="text-muted">
                                                Usuario: {{ $mailSettings['microsoft_user_email'] ?? 'No disponible' }}<br>
                                                Conectado el: {{ \App\Models\MailSetting::first()->getFormattedConnectionDate() }}
                                            </small>
                                        </div>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="reconnectMicrosoft()">
                                                <i class="fas fa-sync me-1"></i>Reconectar
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Botón separado para desconectar OAuth -->
                                    <div class="text-center mb-3">
                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="disconnectOAuthAjax()">
                                            <i class="fas fa-unlink me-1"></i>Desconectar OAuth
                                        </button>
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>OAuth Microsoft no configurado</strong><br>
                                        Para usar tu cuenta de Microsoft, debes conectarte primero usando OAuth.
                                    </div>
                                    
                                    <!-- Botón de conexión OAuth -->
                                    <div class="row mb-3">
                                        <div class="col-12 text-center">
                                            <a href="{{ route('admin.mail.oauth.microsoft') }}" class="btn btn-primary btn-lg" id="oauth-button">
                                                <i class="fab fa-microsoft me-2"></i>
                                                Conectar con Microsoft
                                            </a>
                                            <p class="mt-2 text-muted">
                                                <small>
                                                    <i class="fas fa-shield-alt me-1"></i>
                                                    Al hacer clic, serás redirigido a Microsoft para autorizar la aplicación.
                                                </small>
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <!-- Información sobre OAuth (solo se muestra si no está conectado) -->
                                    <div class="card mt-3">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <i class="fas fa-info me-2"></i>Información sobre OAuth Microsoft
                                            </h6>
                                            <p class="card-text">
                                                OAuth es un método seguro de autenticación que te permite:
                                            </p>
                                            <ul>
                                                <li>No necesitar almacenar tu contraseña en el sistema</li>
                                                <li>Acceso seguro a tu cuenta de Microsoft/Outlook</li>
                                                <li>Tokens de acceso renovables automáticamente</li>
                                                <li>Compatible con autenticación de dos factores</li>
                                            </ul>
                                            <div class="alert alert-warning mt-3">
                                                <small>
                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                    <strong>Importante:</strong> Microsoft requiere OAuth para cuentas personales (@outlook.com, @hotmail.com, @live.com) 
                                                    desde que deshabilitó la autenticación básica SMTP.
                                                </small>
                                            </div>
                                        </div>
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
                                    <input class="form-check-input" type="checkbox" id="email_notifications_enabled" 
                                           name="email_notifications_enabled" value="1" 
                                           {{ old('email_notifications_enabled', $mailSettings['email_notifications_enabled'] ?? '1') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="email_notifications_enabled">
                                        Habilitar notificaciones por email
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="email_queue_enabled" 
                                           name="email_queue_enabled" value="1" 
                                           {{ old('email_queue_enabled', $mailSettings['email_queue_enabled'] ?? '0') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="email_queue_enabled">
                                        Usar cola para envío de emails
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="email_log_enabled" 
                                           name="email_log_enabled" value="1" 
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
                                            @if(isset($mailSettings['microsoft_access_token']) && !empty($mailSettings['microsoft_access_token']))
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fas fa-save me-2"></i>Guardar Configuración OAuth
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-success btn-lg" onclick="startOAuthFlow()" id="btn-start-oauth">
                                                    <i class="fab fa-microsoft me-2"></i>Conectar con Microsoft
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary ms-2" data-bs-toggle="modal" data-bs-target="#oauthHelpModal">
                                                    <i class="fas fa-question-circle me-2"></i>¿Necesitas ayuda?
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    @if($mailSettings['last_email_test'] ?? false)
                                        <div class="text-muted">
                                            <small>
                                                <i class="fas fa-clock me-1"></i>
                                                Última prueba: {{ $mailSettings['last_email_test'] }}
                                                @if($mailSettings['email_test_status'] ?? false)
                                                    <span class="badge bg-{{ $mailSettings['email_test_status'] == 'success' ? 'success' : 'danger' }} ms-1">
                                                        {{ $mailSettings['email_test_status'] == 'success' ? 'Exitosa' : 'Falló' }}
                                                    </span>
                                                @endif
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Ayuda OAuth Microsoft -->
<div class="modal fade" id="oauthHelpModal" tabindex="-1" aria-labelledby="oauthHelpModalLabel" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-gradient-primary text-white py-2">
                <h6 class="modal-title fw-bold mb-0" id="oauthHelpModalLabel">
                    <i class="fab fa-microsoft me-2"></i>Configurar OAuth Microsoft - Guía Paso a Paso
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-3">
                <div class="alert alert-info">
                    <strong><i class="fas fa-info-circle me-2"></i>¿Por qué OAuth?</strong><br>
                    Microsoft ha deshabilitado la autenticación básica (usuario/contraseña) para cuentas personales por seguridad. OAuth es más seguro y es el método recomendado.
                </div>

                <h6 class="text-success"><i class="fas fa-step-forward me-2"></i>Paso 1: Crear Aplicación en Azure</h6>
                <ol>
                    <li>Ve a <a href="https://portal.azure.com" target="_blank">Azure Portal</a> e inicia sesión</li>
                    <li>Busca "Azure Active Directory" o "App registrations"</li>
                    <li>Haz clic en "New registration"</li>
                    <li>Configura:
                        <ul>
                            <li><strong>Name:</strong> Gym Control Email</li>
                            <li><strong>Supported account types:</strong> Accounts in any organizational directory and personal Microsoft accounts</li>
                            <li><strong>Redirect URI:</strong> Web - <code>{{ route('admin.mail.oauth.microsoft.callback') }}</code></li>
                        </ul>
                    </li>
                    <li>Haz clic en "Register"</li>
                </ol>

                <h6 class="text-success"><i class="fas fa-step-forward me-2"></i>Paso 2: Obtener Credenciales</h6>
                <ol>
                    <li>En tu aplicación registrada, ve a "Overview"</li>
                    <li>Copia el <strong>Application (client) ID</strong></li>
                    <li>Copia el <strong>Directory (tenant) ID</strong></li>
                    <li>Ve a "Certificates & secrets"</li>
                    <li>Haz clic en "New client secret"</li>
                    <li>Copia el <strong>Value</strong> del secreto (¡no el ID!)</li>
                </ol>

                <h6 class="text-success"><i class="fas fa-step-forward me-2"></i>Paso 3: Configurar Permisos</h6>
                <ol>
                    <li>Ve a "API permissions"</li>
                    <li>Haz clic en "Add a permission"</li>
                    <li>Selecciona "Microsoft Graph"</li>
                    <li>Selecciona "Delegated permissions"</li>
                    <li>Busca y agrega: <code>Mail.Send</code></li>
                    <li>Haz clic en "Grant admin consent" si aparece</li>
                </ol>

                <h6 class="text-success"><i class="fas fa-step-forward me-2"></i>Paso 4: Configurar en Gym Control</h6>
                <ol>
                    <li>Pega el <strong>Client ID</strong> en el campo correspondiente</li>
                    <li>Pega el <strong>Client Secret</strong> en el campo correspondiente</li>
                    <li>Pega el <strong>Tenant ID</strong> en el campo correspondiente</li>
                    <li>Guarda la configuración</li>
                    <li>Haz clic en "Conectar con Microsoft" para autorizar</li>
                </ol>

                <div class="alert alert-success">
                    <strong><i class="fas fa-check-circle me-2"></i>¡Listo!</strong><br>
                    Una vez completados estos pasos, podrás enviar emails usando tu cuenta de Microsoft de forma segura.
                </div>

                <h6 class="text-warning"><i class="fas fa-exclamation-triangle me-2"></i>Problemas Comunes</h6>
                <ul>
                    <li><strong>Redirect URI mismatch:</strong> Asegúrate de que la URL de redirección en Azure coincida exactamente</li>
                    <li><strong>Permisos insuficientes:</strong> Verifica que Mail.Send esté agregado y consentido</li>
                    <li><strong>Tenant incorrecto:</strong> Para cuentas personales, usa el tenant ID mostrado en Overview</li>
                </ul>
            </div>
            <div class="modal-footer bg-light border-0 py-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cerrar
                </button>
                <a href="https://portal.azure.com" target="_blank" class="btn btn-primary">
                    <i class="fab fa-microsoft me-1"></i>Ir a Azure Portal
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Prueba de Email -->
<div class="modal fade" id="testEmailModal" tabindex="-1" aria-labelledby="testEmailModalLabel" aria-hidden="true" role="dialog">
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
                        <textarea class="form-control form-control-sm" id="test_message" name="test_message" rows="4" required>
Este es un email de prueba enviado desde Gym Control System.
Si recibes este mensaje, la configuración de email está funcionando correctamente.

Fecha y hora: {{ now()->format('d/m/Y H:i:s') }}
                        </textarea>
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
window.toggleAuthMethod = function() {
    const authMethod = document.querySelector('input[name="auth_method"]:checked');
    if (!authMethod) {
        return;
    }
    
    const authValue = authMethod.value;
    
    const smtpConfig = document.getElementById('smtp-config');
    const oauthConfig = document.getElementById('oauth-config');
    const identityConfig = document.getElementById('identity-config');
    const providerConfig = document.getElementById('provider-config');
    const smtpActions = document.getElementById('smtp-actions');
    const oauthActions = document.getElementById('oauth-actions');
    const outlookOption = document.getElementById('outlook-option');
    const providerSelect = document.getElementById('mail_provider');
    
    if (authValue === 'smtp') {
        // Mostrar configuración SMTP
        if (smtpConfig) smtpConfig.style.display = 'block';
        if (oauthConfig) oauthConfig.style.display = 'none';
        if (identityConfig) identityConfig.style.display = 'block';
        if (providerConfig) providerConfig.style.display = 'block';
        if (smtpActions) smtpActions.style.display = 'block';
        if (oauthActions) oauthActions.style.display = 'none';
        
        // Mostrar opción de Outlook en SMTP
        if (outlookOption) {
            outlookOption.style.display = 'block';
        }
        
        // Hacer campos SMTP requeridos
        const hostField = document.getElementById('mail_host');
        const portField = document.getElementById('mail_port');
        const userField = document.getElementById('mail_username');
        const passField = document.getElementById('mail_password');
        
        if (hostField) hostField.required = true;
        if (portField) portField.required = true;
        if (userField) userField.required = true;
        if (passField) passField.required = true;
        
        // Quitar requeridos de OAuth
        const oauthFields = ['microsoft_client_id', 'microsoft_client_secret', 'microsoft_tenant_id'];
        oauthFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) field.required = false;
        });
        
    } else if (authValue === 'oauth_microsoft') {
        
        try {
            if (smtpConfig) smtpConfig.style.display = 'none';
            if (oauthConfig) oauthConfig.style.display = 'block';
            if (identityConfig) identityConfig.style.display = 'block';
            if (providerConfig) providerConfig.style.display = 'none';
            if (smtpActions) smtpActions.style.display = 'none';
            if (oauthActions) oauthActions.style.display = 'block';
            
            // Establecer automáticamente proveedor como Microsoft para OAuth
            if (providerSelect) providerSelect.value = 'outlook';
            
            // Quitar requeridos de SMTP
            const hostField = document.getElementById('mail_host');
            const portField = document.getElementById('mail_port');
            const userField = document.getElementById('mail_username');
            const passField = document.getElementById('mail_password');
            
            if (hostField) hostField.required = false;
            if (portField) portField.required = false;
            if (userField) userField.required = false;
            if (passField) passField.required = false;
            console.log('✅ Campos SMTP no requeridos');
            
            // Los campos OAuth se manejan dinámicamente según el estado de conexión
            console.log('🔍 Verificando estado de conexión OAuth...');
            const isConnected = document.querySelector('#oauth-status .alert-success');
            console.log('🔍 OAuth conectado:', !!isConnected);
            
            if (!isConnected) {
                // Si no está conectado, hacer requeridos los campos OAuth
                console.log('🔍 Configurando campos OAuth como requeridos...');
                const oauthFields = ['microsoft_client_id', 'microsoft_client_secret', 'microsoft_tenant_id'];
                oauthFields.forEach(fieldId => {
                    const field = document.getElementById(fieldId);
                    if (field) field.required = true;
                });
                console.log('✅ Campos OAuth configurados como requeridos');
            }
            
            // Configurar eventos para el formulario OAuth (después de que sea visible)
            console.log('🔄 Configurando eventos OAuth en 100ms...');
            setTimeout(() => {
                console.log('⏰ Ejecutando setupOAuthFormEvents después del timeout');
                setupOAuthFormEvents();
            }, 100);
            
        } catch (error) {
            console.error('❌ Error en configuración OAuth Microsoft:', error);
            console.error('Stack trace:', error.stack);
        }
    }
    
    // Actualizar alerta del proveedor solo si la configuración está visible
    if (providerConfig && providerConfig.style.display !== 'none') {
        const provider = providerSelect ? providerSelect.value : '';
        showProviderAlert(provider, authValue);
    } else {
        // Ocultar alerta cuando no hay selección de proveedor
        const alertDiv = document.getElementById('provider-alert');
        if (alertDiv) alertDiv.style.display = 'none';
    }
    
    console.log('🔧 Configuración completada - VERSIÓN 3.0');
    
    // Actualizar campos ocultos después de cambiar método
    updateHiddenFields();
};

// Ejecutar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado, ejecutando configuración inicial');
    window.toggleAuthMethod();
});

// Reconectar OAuth Microsoft
function reconnectMicrosoft() {
    if (confirm('¿Estás seguro de que quieres reconectar con Microsoft? Esto invalidará la conexión actual.')) {
        window.location.href = '{{ route("admin.mail.oauth.microsoft") }}';
    }
}

// Conectar OAuth Microsoft
function connectMicrosoft() {
    window.location.href = '{{ route("admin.mail.oauth.microsoft") }}';
}

// Configurar eventos para el formulario OAuth
function setupOAuthFormEvents() {
    console.log('🔍 setupOAuthFormEvents: Iniciando búsqueda de elementos...');
    
    // Primero buscar el botón OAuth
    const oauthButton = document.getElementById('oauth-button');
    console.log('🔍 oauth-button element:', oauthButton);
    
    // Verificar si OAuth ya está conectado
    const isConnected = document.querySelector('#oauth-status .alert-success');
    console.log('🔍 OAuth ya conectado:', !!isConnected);
    
    if (isConnected) {
        console.log('✅ OAuth ya está conectado - no se necesita configurar eventos del formulario de conexión');
        return;
    }
    
    if (!oauthButton) {
        console.log('❌ Botón OAuth no encontrado');
        return;
    }
    
    // Buscar el formulario que contiene el botón OAuth
    let oauthForm = oauthButton.closest('form');
    console.log('🔍 Formulario encontrado via closest():', oauthForm);
    
    // Si no encontramos el formulario con closest, buscar por ID
    if (!oauthForm) {
        oauthForm = document.getElementById('oauth-form');
        console.log('🔍 Formulario encontrado via getElementById:', oauthForm);
    }
    
    // Si aún no encontramos el formulario, buscar todos los formularios y ver cuál tiene la acción OAuth
    if (!oauthForm) {
        console.log('🔍 Buscando formulario OAuth en todos los formularios de la página...');
        const allForms = document.querySelectorAll('form');
        for (let form of allForms) {
            if (form.action && form.action.includes('oauth/microsoft/connect')) {
                oauthForm = form;
                console.log('🔍 Formulario OAuth encontrado por acción:', form);
                break;
            }
        }
    }
    
    if (oauthForm && oauthButton) {
        console.log('✅ OAuth form y button encontrados, configurando eventos');
        
        // Remover eventos anteriores para evitar duplicados
        oauthForm.removeEventListener('submit', handleOAuthSubmit);
        oauthButton.removeEventListener('click', handleOAuthClick);
        
        // Agregar nuevos eventos
        oauthForm.addEventListener('submit', handleOAuthSubmit);
        oauthButton.addEventListener('click', handleOAuthClick);
        
        console.log('✅ Eventos OAuth configurados correctamente');
        console.log('🔍 Form action:', oauthForm.action);
        console.log('🔍 Form method:', oauthForm.method);
        
    } else {
        console.log('❌ OAuth form o button NO encontrados después de búsqueda exhaustiva');
        console.log('Form exists:', !!oauthForm);
        console.log('Button exists:', !!oauthButton);
        
        // Debugging adicional
        const allForms = document.querySelectorAll('form');
        console.log('📋 Total forms in page:', allForms.length);
        allForms.forEach((form, index) => {
            console.log(`  Form ${index}: action="${form.action}", method="${form.method}", id="${form.id}"`);
        });
    }
}

// Manejar envío del formulario OAuth
function handleOAuthSubmit(e) {
    console.log('🚀 OAuth form submit event capturado');
    console.log('Action:', e.target.action);
    console.log('Method:', e.target.method);
    
    // Agregar indicador de carga
    const button = e.target.querySelector('button[type="submit"]');
    if (button) {
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Conectando...';
    }
}

// Manejar click del botón OAuth
function handleOAuthClick(e) {
    console.log('🔘 OAuth button click event capturado');
}

console.log('Función definida:', typeof window.toggleAuthMethod);

/*
// Presets de proveedores de email comentados temporalmente
const emailPresets = {
    gmail: {
        mail_host: 'smtp.gmail.com',
        mail_port: '587',
        mail_encryption: 'tls'
    },
    outlook: {
        mail_host: 'smtp-mail.outlook.com',
        mail_port: '587',
        mail_encryption: 'tls'
    },
    yahoo: {
        mail_host: 'smtp.mail.yahoo.com',
        mail_port: '587',
        mail_encryption: 'tls'
    },
    sendgrid: {
        mail_host: 'smtp.sendgrid.net',
        mail_port: '587',
        mail_encryption: 'tls'
    }
};

// Mensajes de alerta por proveedor
const providerAlerts = {
    gmail: {
        smtp: "Para Gmail con SMTP, debes usar una 'Contraseña de Aplicación' en lugar de tu contraseña normal. Ve a tu cuenta de Google > Seguridad > Contraseñas de aplicaciones.",
        oauth: "Gmail soporta SMTP tradicional con contraseñas de aplicación, no necesitas OAuth para cuentas personales."
    },
    outlook: {
        smtp: "⚠️ IMPORTANTE: Microsoft ha deshabilitado la autenticación básica para cuentas personales (@outlook.com, @hotmail.com, @live.com). Debes usar OAuth Microsoft.",
        oauth: "Perfecto! OAuth Microsoft es el método recomendado para cuentas de Outlook/Hotmail/Live."
    },
    yahoo: {
        smtp: "Para Yahoo Mail, debes generar una 'Contraseña de Aplicación' en la configuración de seguridad de tu cuenta.",
        oauth: "Yahoo Mail no soporta OAuth en este sistema. Usa SMTP con contraseña de aplicación."
    },
    sendgrid: {
        smtp: "Para SendGrid, usa tu clave API como contraseña y 'apikey' como usuario.",
        oauth: "SendGrid no requiere OAuth. Usa SMTP con tu clave API."
    }
};

// Aplicar preset del proveedor y mostrar alerta
function applyProviderPreset() {
    const provider = document.getElementById('mail_provider').value;
    const authMethod = document.querySelector('input[name="auth_method"]:checked')?.value || 'smtp';
    
    // Aplicar configuración del proveedor
    if (provider !== 'custom' && emailPresets[provider]) {
        const preset = emailPresets[provider];
        document.getElementById('mail_host').value = preset.mail_host;
        document.getElementById('mail_port').value = preset.mail_port;
        document.getElementById('mail_encryption').value = preset.mail_encryption;
    }
    
    // Mostrar alerta específica
    showProviderAlert(provider, authMethod);
}

// Mostrar alerta específica del proveedor
function showProviderAlert(provider, authMethod) {
    const alertDiv = document.getElementById('provider-alert');
    const messageSpan = document.getElementById('provider-message');
    
    if (provider !== 'custom' && providerAlerts[provider]) {
        const message = providerAlerts[provider][authMethod];
        if (message) {
            messageSpan.textContent = message;
            alertDiv.style.display = 'block';
            
            // Cambiar color de alerta según el proveedor y método
            alertDiv.className = 'alert';
            if (provider === 'outlook' && authMethod === 'smtp') {
                alertDiv.classList.add('alert-danger'); // Rojo para Outlook + SMTP
            } else if (provider === 'outlook' && authMethod === 'oauth_microsoft') {
                alertDiv.classList.add('alert-success'); // Verde para Outlook + OAuth
            } else {
                alertDiv.classList.add('alert-warning'); // Amarillo para otros casos
            }
        } else {
            alertDiv.style.display = 'none';
        }
    } else {
        alertDiv.style.display = 'none';
    }
}

// Mostrar/ocultar campos según método de autenticación (FUNCIÓN DETALLADA)
function toggleAuthMethodDetailed() {
    console.log('toggleAuthMethod ejecutado');
    
    const authMethod = document.querySelector('input[name="auth_method"]:checked');
    if (!authMethod) {
        console.log('No auth method selected');
        return;
    }
    
    const authValue = authMethod.value;
    console.log('Método de autenticación seleccionado:', authValue);
    
    const smtpConfig = document.getElementById('smtp-config');
    const oauthConfig = document.getElementById('oauth-config');
    const identityConfig = document.getElementById('identity-config');
    const providerConfig = document.getElementById('provider-config');
    const smtpActions = document.getElementById('smtp-actions');
    const oauthActions = document.getElementById('oauth-actions');
    const outlookOption = document.getElementById('outlook-option');
    const providerSelect = document.getElementById('mail_provider');
    
    if (authValue === 'smtp') {
        // Mostrar configuración SMTP
        if (smtpConfig) smtpConfig.style.display = 'block';
        if (oauthConfig) oauthConfig.style.display = 'none';
        if (identityConfig) identityConfig.style.display = 'block';
        if (providerConfig) providerConfig.style.display = 'block';
        if (smtpActions) smtpActions.style.display = 'block';
        if (oauthActions) oauthActions.style.display = 'none';
        
        // Mostrar opción de Outlook en SMTP
        if (outlookOption) {
            outlookOption.style.display = 'block';
        }
        
        // Hacer campos SMTP requeridos
        const hostField = document.getElementById('mail_host');
        const portField = document.getElementById('mail_port');
        const userField = document.getElementById('mail_username');
        const passField = document.getElementById('mail_password');
        
        if (hostField) hostField.required = true;
        if (portField) portField.required = true;
        if (userField) userField.required = true;
        if (passField) passField.required = true;
        
        // Quitar requeridos de OAuth
        const oauthFields = ['microsoft_client_id', 'microsoft_client_secret', 'microsoft_tenant_id'];
        oauthFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) field.required = false;
        });
        
    }
    
    console.log('🔍 DEBUG: Verificando si authValue es oauth_microsoft:', authValue, authValue === 'oauth_microsoft');
    
    // Manejo detallado de OAuth Microsoft
    if (authValue === 'oauth_microsoft') {
        console.log('🎯 ENTRANDO en sección detallada de OAuth Microsoft');
        try {
            console.log('🔵 Configurando OAuth Microsoft - ocultando sección de proveedor');
            
            // Definir variables necesarias
            const smtpActions = document.getElementById('smtp-actions');
            const oauthActions = document.getElementById('oauth-actions');
            const providerSelect = document.getElementById('mail_provider');
            
            console.log('🔍 Ocultando elementos SMTP...');
            if (smtpConfig) smtpConfig.style.display = 'none';
            if (oauthConfig) oauthConfig.style.display = 'block';
            if (identityConfig) identityConfig.style.display = 'block';
            if (providerConfig) providerConfig.style.display = 'none'; // ¡Importante! Ocultar selección de proveedor
            if (smtpActions) smtpActions.style.display = 'none';
            if (oauthActions) oauthActions.style.display = 'block';
            console.log('✅ Elementos mostrados/ocultados correctamente');
            
            // Establecer automáticamente proveedor como Microsoft para OAuth
            console.log('🔍 Configurando proveedor automático...');
            if (providerSelect) providerSelect.value = 'outlook';
            console.log('✅ Proveedor configurado como outlook');
            
            // Quitar requeridos de SMTP
            console.log('🔍 Removiendo campos requeridos SMTP...');
            const hostField = document.getElementById('mail_host');
            const portField = document.getElementById('mail_port');
            const userField = document.getElementById('mail_username');
            const passField = document.getElementById('mail_password');
            
            if (hostField) hostField.required = false;
            if (portField) portField.required = false;
            if (userField) userField.required = false;
            if (passField) passField.required = false;
            console.log('✅ Campos SMTP no requeridos');
            
            // Los campos OAuth se manejan dinámicamente según el estado de conexión
            console.log('🔍 Verificando estado de conexión OAuth...');
            const isConnected = document.querySelector('#oauth-status .alert-success');
            console.log('🔍 OAuth conectado:', !!isConnected);
            
            if (!isConnected) {
                // Si no está conectado, hacer requeridos los campos OAuth
                console.log('🔍 Configurando campos OAuth como requeridos...');
                const oauthFields = ['microsoft_client_id', 'microsoft_client_secret', 'microsoft_tenant_id'];
                oauthFields.forEach(fieldId => {
                    const field = document.getElementById(fieldId);
                    if (field) field.required = true;
                });
                console.log('✅ Campos OAuth configurados como requeridos');
            }
            
            // Configurar eventos para el formulario OAuth (después de que sea visible)
            console.log('🔄 Configurando eventos OAuth en 100ms...');
            setTimeout(() => {
                console.log('⏰ Ejecutando setupOAuthFormEvents después del timeout');
                setupOAuthFormEvents();
            }, 100);
            
        } catch (error) {
            console.error('❌ Error en configuración OAuth Microsoft:', error);
            console.error('Stack trace:', error.stack);
        }
    }
    
    // Actualizar alerta del proveedor solo si la configuración está visible
    if (providerConfig && providerConfig.style.display !== 'none') {
        const provider = providerSelect ? providerSelect.value : '';
        showProviderAlert(provider, authValue);
    } else {
        // Ocultar alerta cuando está en modo OAuth
        const alertDiv = document.getElementById('provider-alert');
        if (alertDiv) {
            alertDiv.style.display = 'none';
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

// Detectar proveedor automáticamente basado en el email
function detectProviderFromEmail() {
    const emailInput = document.getElementById('mail_username');
    const providerSelect = document.getElementById('mail_provider');
    const authMethodRadios = document.querySelectorAll('input[name="auth_method"]');
    
    emailInput.addEventListener('blur', function() {
        const email = this.value.toLowerCase();
        let detectedProvider = 'custom';
        let suggestedAuth = 'smtp';
        
        if (email.includes('@gmail.com')) {
            detectedProvider = 'gmail';
            suggestedAuth = 'smtp';
        } else if (email.includes('@outlook.com') || email.includes('@hotmail.com') || email.includes('@live.com')) {
            detectedProvider = 'outlook';
            suggestedAuth = 'oauth_microsoft'; // Recomendar OAuth para Microsoft
            
            // Auto-cambiar a OAuth si está usando SMTP
            const currentAuth = document.querySelector('input[name="auth_method"]:checked').value;
            if (currentAuth === 'smtp') {
                // Sugerir cambio a OAuth para Microsoft
                setTimeout(() => {
                    if (confirm('🔍 Detectamos que usas una cuenta de Microsoft.\n\nMicrosoft ya no permite autenticación SMTP tradicional para cuentas personales.\n\n¿Quieres cambiar automáticamente a OAuth Microsoft?')) {
                        authMethodRadios.forEach(radio => {
                            if (radio.value === 'oauth_microsoft') {
                                radio.checked = true;
                                toggleAuthMethod();
                            }
                        });
                    }
                }, 500);
            }
        } else if (email.includes('@yahoo.com')) {
            detectedProvider = 'yahoo';
            suggestedAuth = 'smtp';
        }
        
        // Actualizar proveedor si se detectó uno específico
        if (detectedProvider !== 'custom' && providerSelect.value === 'custom') {
            providerSelect.value = detectedProvider;
            applyProviderPreset();
        }
    });
}

// Iniciar flujo OAuth Microsoft
function startOAuthFlow() {
    const clientId = document.getElementById('microsoft_client_id').value;
    const clientSecret = document.getElementById('microsoft_client_secret').value;
    const tenantId = document.getElementById('microsoft_tenant_id').value;
    
    if (!clientId || !clientSecret || !tenantId) {
        alert('Por favor, completa todos los campos de OAuth antes de conectar:\n- Client ID\n- Client Secret\n- Tenant ID\n\nSi necesitas ayuda, haz clic en "¿Necesitas ayuda?"');
        return;
    }
    
    // Primero guardamos la configuración básica
    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    formData.append('_method', 'PUT');
    formData.append('auth_method', 'oauth_microsoft');
    formData.append('mail_driver', 'smtp');
    formData.append('mail_provider', document.getElementById('mail_provider').value);
    formData.append('microsoft_client_id', clientId);
    formData.append('microsoft_client_secret', clientSecret);
    formData.append('microsoft_tenant_id', tenantId);
    formData.append('microsoft_redirect_uri', document.getElementById('microsoft_redirect_uri').value);
    formData.append('mail_from_address', document.getElementById('mail_from_address').value || 'noreply@gymcontrol.com');
    formData.append('mail_from_name', document.getElementById('mail_from_name').value || 'Gym Control');
    formData.append('email_notifications_enabled', '1');
    formData.append('email_log_enabled', '1');
    
    // Deshabilitar botón mientras se procesa
    const button = document.getElementById('btn-start-oauth');
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando configuración...';
    
    fetch('{{ route("admin.mail.config.update") }}', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (response.ok) {
            // Configuración guardada, ahora redirigir a OAuth
            button.innerHTML = '<i class="fab fa-microsoft me-2"></i>Redirigiendo a Microsoft...';
            window.location.href = '{{ route("admin.mail.oauth.microsoft") }}';
        } else {
            throw new Error('Error al guardar configuración');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al guardar la configuración. Por favor, verifica los datos e intenta nuevamente.');
        button.disabled = false;
        button.innerHTML = originalText;
    });
}

// Reconectar OAuth Microsoft
function reconnectMicrosoft() {
    if (confirm('¿Estás seguro de que quieres reconectar con Microsoft? Esto invalidará la conexión actual.')) {
        window.location.href = '{{ route("admin.mail.oauth.microsoft") }}';
    }
}

// Conectar OAuth Microsoft
function connectMicrosoft() {
    window.location.href = '{{ route("admin.mail.oauth.microsoft") }}';
}
    // Función legacy - redirigir a startOAuthFlow
    startOAuthFlow();

// Restablecer formulario
function resetForm() {
    if (confirm('¿Estás seguro de que quieres restablecer la configuración?')) {
        location.reload();
    }
}

// Mostrar configuración inicial según método seleccionado
document.addEventListener('DOMContentLoaded', function() {
    // Ejecutar configuración inicial
    toggleAuthMethod();
    
    // Mostrar alerta inicial del proveedor
    const provider = document.getElementById('mail_provider').value;
    const authMethod = document.querySelector('input[name="auth_method"]:checked')?.value || 'smtp';
    showProviderAlert(provider, authMethod);
    
    // Configurar detección automática de proveedor
    detectProviderFromEmail();
    
    // Agregar listeners para cambios
    document.getElementById('mail_provider').addEventListener('change', function() {
        applyProviderPreset();
    });
    
*/

// Función para desconectar OAuth usando AJAX
function disconnectOAuthAjax() {
    console.log('disconnectOAuthAjax: Iniciando desconexión...');
    
    if (!confirm('¿Estás seguro de desconectar Microsoft OAuth?')) {
        console.log('disconnectOAuthAjax: Cancelado por usuario');
        return;
    }
    
    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    console.log('disconnectOAuthAjax: Enviando petición POST...');
    
    fetch('{{ route("admin.mail.oauth.microsoft.disconnect") }}', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('disconnectOAuthAjax: Respuesta recibida', response.status, response.statusText);
        
        if (response.ok) {
            console.log('disconnectOAuthAjax: Respuesta exitosa, recargando página...');
            window.location.reload();
        } else {
            console.error('disconnectOAuthAjax: Error en respuesta', response.status);
            return response.text().then(text => {
                console.error('disconnectOAuthAjax: Contenido de error:', text);
                alert('Error al desconectar OAuth: ' + response.status);
            });
        }
    })
    .catch(error => {
        console.error('disconnectOAuthAjax: Error en fetch:', error);
        alert('Error de conexión al desconectar OAuth');
    });
}

// Función para configurar el campo de email según el estado de OAuth
function configureEmailFieldForOAuth() {
    const isConnected = document.querySelector('#oauth-status .alert-success');
    const emailFromField = document.getElementById('mail_from_address');
    const oauthEmailNotice = document.getElementById('oauth-email-notice');
    
    if (isConnected && emailFromField) {
        // OAuth conectado: hacer campo de solo lectura visual pero mantener envío
        emailFromField.readOnly = true;
        emailFromField.classList.add('bg-light');
        emailFromField.title = 'Este email se obtiene automáticamente de Microsoft OAuth y no se puede modificar';
        emailFromField.style.pointerEvents = 'none'; // Prevenir clics
        if (oauthEmailNotice) oauthEmailNotice.style.display = 'inline';
        console.log('🔒 Campo email del remitente bloqueado (OAuth conectado)');
    } else if (emailFromField) {
        // OAuth no conectado: permitir edición
        emailFromField.readOnly = false;
        emailFromField.classList.remove('bg-light');
        emailFromField.title = '';
        emailFromField.style.pointerEvents = 'auto';
        if (oauthEmailNotice) oauthEmailNotice.style.display = 'none';
        console.log('🔓 Campo email del remitente editable (OAuth no conectado)');
    }
}

// Llamar la función cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    configureEmailFieldForOAuth();
    updateHiddenFields(); // Actualizar campos ocultos al cargar
});

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
            
            // Para OAuth, los valores ya están en los campos ocultos desde la base de datos
            // No necesitamos actualizarlos desde campos que pueden no existir
            
            console.log('🔄 Campos ocultos actualizados para OAuth');
        } else {
            document.getElementById('hidden_mail_driver').value = 'smtp';
            const provider = document.getElementById('mail_provider');
            if (provider) document.getElementById('hidden_mail_provider').value = provider.value;
            
            console.log('🔄 Campos ocultos actualizados para SMTP');
        }
    }
}

// Comentado todo el JavaScript complejo temporalmente
</script>
@endpush