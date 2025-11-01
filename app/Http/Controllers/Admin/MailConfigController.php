<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MailSetting;
use App\Mail\TestEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Exception;

class MailConfigController extends Controller
{
    /**
     * Mostrar configuración de correos.
     */
    public function index()
    {
        $mailSettings = MailSetting::getConfig()->toConfigArray();
        
        return view('admin.mail-config.index', compact('mailSettings'));
    }

    /**
     * Actualizar configuración de correos.
     */
    public function update(Request $request)
    {
        Log::info('=== UPDATE METHOD CALLED ===', [
            'method' => $request->method(),
            'url' => $request->url(),
            'has_auth_method' => $request->has('auth_method'),
            'auth_method' => $request->input('auth_method'),
            'mail_driver' => $request->input('mail_driver')
        ]);
        
        $validated = $request->validate([
            'mail_driver' => 'required|in:smtp,sendmail,log',
            'mail_host' => 'required_if:auth_method,smtp|nullable|string|max:100',
            'mail_port' => 'required_if:auth_method,smtp|nullable|integer|min:1|max:65535',
            'mail_encryption' => 'nullable|in:tls,ssl,',
            'auth_method' => 'required|in:smtp,oauth_microsoft',
            'mail_username' => 'required_if:auth_method,smtp|nullable|string|max:100',
            'mail_password' => 'required_if:auth_method,smtp|nullable|string',
            'mail_from_address' => 'required|email|max:100',
            'mail_from_name' => 'required|string|max:100',
            'mail_reply_to' => 'nullable|email|max:100',
            'mail_provider' => 'required|in:custom,gmail,outlook,yahoo,sendgrid',
            
            // OAuth Microsoft (solo requeridos si no hay conexión OAuth activa)
            'microsoft_client_id' => 'nullable|string',
            'microsoft_client_secret' => 'nullable|string', 
            'microsoft_tenant_id' => 'nullable|string|max:100',
            'microsoft_redirect_uri' => 'nullable|url',
            
            // Configuraciones adicionales
            'email_notifications_enabled' => 'boolean',
            'email_queue_enabled' => 'boolean',
            'email_log_enabled' => 'boolean',
            'test_email_address' => 'nullable|email|max:100',
            'email_timeout' => 'nullable|integer|min:10|max:300',
            'email_retry_attempts' => 'nullable|integer|min:1|max:10',
            'verify_ssl' => 'boolean',
        ]);

        Log::info('Validation passed successfully', [
            'validated_data' => $validated
        ]);

        try {
            // Si se está usando OAuth Microsoft, preservar configuraciones OAuth existentes
            if ($validated['auth_method'] === 'oauth_microsoft') {
                $currentConfig = MailSetting::getConfig();
                
                // Preservar email del remitente de Microsoft
                if (!empty($currentConfig->getDecryptedMicrosoftAccessToken()) && !empty($currentConfig->microsoft_user_email)) {
                    $validated['mail_from_address'] = $currentConfig->microsoft_user_email;
                    Log::info('OAuth Update: Preservando email del remitente de Microsoft', [
                        'microsoft_email' => $currentConfig->microsoft_user_email
                    ]);
                }
                
                // Preservar configuraciones OAuth si no se enviaron o están vacías
                if (empty($validated['microsoft_client_id']) && !empty($currentConfig->microsoft_client_id)) {
                    $validated['microsoft_client_id'] = $currentConfig->microsoft_client_id;
                }
                if (empty($validated['microsoft_client_secret']) && !empty($currentConfig->microsoft_client_secret)) {
                    $validated['microsoft_client_secret'] = $currentConfig->microsoft_client_secret;
                }
                if (empty($validated['microsoft_tenant_id']) && !empty($currentConfig->microsoft_tenant_id)) {
                    $validated['microsoft_tenant_id'] = $currentConfig->microsoft_tenant_id;
                }
                
                Log::info('OAuth Update: Configuraciones OAuth preservadas');
            }
            
            // Actualizar configuración usando el modelo MailSetting
            MailSetting::updateConfig($validated);
            
            Log::info('Configuration updated successfully', [
                'final_data' => $validated
            ]);

            // Actualizar configuración de Laravel en tiempo real
            $this->updateLaravelMailConfig();

            // Responder según el tipo de petición
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Configuración de correo actualizada exitosamente.'
                ]);
            }

            return redirect()->route('admin.mail.config.index')
                ->with('success', 'Configuración de correo actualizada exitosamente.');

        } catch (ValidationException $e) {
            Log::error('Validation Error in update:', [
                'errors' => $e->errors(),
                'failed_fields' => array_keys($e->errors())
            ]);
            
            return redirect()->route('admin.mail.config.index')
                ->withErrors($e->errors())
                ->withInput();
        } catch (Exception $e) {
            Log::error('Error updating mail config: ' . $e->getMessage());
            
            // Responder según el tipo de petición
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar la configuración: ' . $e->getMessage()
                ], 422);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar la configuración: ' . $e->getMessage());
        }
    }

    /**
     * Enviar correo de prueba.
     */
    public function testEmail(Request $request)
    {
        // Establecer un límite de tiempo para evitar timeouts
        set_time_limit(30); // 30 segundos máximo
        
        $validated = $request->validate([
            'test_email' => 'required|email',
            'test_subject' => 'required|string|max:255',
            'test_message' => 'required|string|max:1000',
        ]);

        try {
            // Obtener configuración actual con timeout
            $mailConfig = MailSetting::getConfig();
            if (!$mailConfig) {
                throw new Exception('No se pudo obtener la configuración de correo');
            }

            // Verificar si se debe usar OAuth Microsoft
            $useOAuth = $mailConfig->auth_method === 'oauth_microsoft' && 
                       !empty($mailConfig->getDecryptedMicrosoftAccessToken());
            
            if ($useOAuth) {
                Log::info('Test Email: Enviando via Microsoft Graph API (OAuth con token almacenado)');
                
                // Verificar si el token necesita renovación
                if ($this->tokenNeedsRefresh($mailConfig)) {
                    Log::info('Token expirado, intentando renovar...');
                    $renewed = $mailConfig->refreshMicrosoftAccessToken();
                    if (!$renewed) {
                        Log::warning('No se pudo renovar el token, intentando con token actual');
                    }
                }
                
                // Usar token almacenado (desencriptado)
                $success = $this->sendEmailViaMicrosoftGraphWithStoredToken(
                    $validated['test_email'],
                    $validated['test_subject'],
                    $validated['test_message'],
                    $mailConfig,
                    20 // 20 segundos timeout
                );
                
                if (!$success) {
                    throw new Exception('Error enviando email via Microsoft Graph API con token almacenado');
                }
                
            } else {
                Log::info('Test Email: Enviando via SMTP tradicional');
                
                // Actualizar configuración antes de enviar
                $this->updateLaravelMailConfig();

                // Configurar timeout para el envío de correo
                ini_set('default_socket_timeout', 20);

                // Crear y enviar correo de prueba usando Mailable
                $testEmail = new TestEmail(
                    $validated['test_subject'],
                    $validated['test_message'],
                    [
                        'mail_driver' => $mailConfig->mail_driver,
                        'mail_host' => $mailConfig->mail_host,
                        'mail_port' => $mailConfig->mail_port,
                        'mail_provider' => $mailConfig->mail_provider,
                    ]
                );

                Mail::to($validated['test_email'])->send($testEmail);
            }

            // Registrar prueba exitosa
            $mailConfig->recordEmailTest('success');
            $mailConfig->incrementEmailStats('sent');

            // Guardar email de prueba para la próxima vez
            $mailConfig->test_email_address = $validated['test_email'];
            $mailConfig->save();

            return redirect()->back()
                ->with('success', 'Correo de prueba enviado exitosamente a ' . $validated['test_email'] . 
                       ($useOAuth ? ' (via OAuth Microsoft con ENV)' : ' (via SMTP)'));
            
        } catch (Exception $e) {
            Log::error('Error sending test email: ' . $e->getMessage());
            
            // Registrar prueba fallida
            try {
                $mailConfig = MailSetting::getConfig();
                $mailConfig->recordEmailTest('failed', $e->getMessage());
                $mailConfig->incrementEmailStats('failed');
            } catch (Exception $logError) {
                Log::error('Error logging test failure: ' . $logError->getMessage());
            }

            // Analizar el tipo de error para dar mejor orientación
            $errorMessage = $e->getMessage();
            $suggestion = $this->getErrorSuggestion($errorMessage, $mailConfig ?? null);

            return redirect()->back()
                ->with('error', 'Error al enviar correo de prueba: ' . $suggestion);
        }
    }

    /**
     * Aplicar preset de proveedor.
     */
    public function applyPreset($provider)
    {
        try {
            $mailConfig = MailSetting::getConfig();
            $mailConfig->applyProviderPreset($provider);

            return redirect()->back()
                ->with('success', "Configuración de {$provider} aplicada exitosamente.");
                
        } catch (Exception $e) {
            Log::error('Error applying provider preset: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al aplicar configuración del proveedor: ' . $e->getMessage());
        }
    }

    /**
     * Redireccionar a Microsoft OAuth.
     */
    public function redirectToMicrosoft()
    {
        // Usar configuración desde el .env
        $clientId = env('MS_CLIENT_ID');
        $redirectUri = env('MS_REDIRECT_URI');
        $scopes = env('MS_SCOPES', 'offline_access openid profile email https://graph.microsoft.com/.default');
        $authority = env('MS_AUTHORITY', 'https://login.microsoftonline.com/common/');
        
        Log::info('OAuth: Iniciando redirección a Microsoft', [
            'client_id' => $clientId ? substr($clientId, 0, 8) . '...' : 'NULL',
            'redirect_uri' => $redirectUri,
            'scopes' => $scopes,
            'authority' => $authority
        ]);
        
        if (!$clientId || !$redirectUri) {
            Log::error('OAuth: Configuración incompleta en .env');
            return redirect()->back()
                ->with('error', 'La configuración de OAuth Microsoft no está completa en el archivo .env');
        }

        // Generar estado único para seguridad
        $state = bin2hex(random_bytes(16));
        session(['oauth_state' => $state]);
        
        // Forzar guardado de sesión
        session()->save();
        
        Log::info('OAuth: Estado generado para seguridad', [
            'state' => $state,
            'session_id' => session()->getId(),
            'session_driver' => config('session.driver'),
            'session_saved' => session()->get('oauth_state')
        ]);

        // Construir URL de autorización de Microsoft
        $authUrl = $authority . "oauth2/v2.0/authorize?" . http_build_query([
            'client_id' => $clientId,
            'response_type' => 'code',
            'redirect_uri' => $redirectUri,
            'scope' => $scopes,
            'state' => $state,
            'response_mode' => 'query',
        ]);
        
        Log::info('OAuth: Redirigiendo a Microsoft', ['auth_url' => $authUrl]);

        return redirect($authUrl);
    }

    /**
     * Conectar a Microsoft OAuth desde la interfaz.
     */
    public function connectToMicrosoft()
    {
        Log::info('OAuth: Método connectToMicrosoft llamado');
        return $this->redirectToMicrosoft();
    }

    /**
     * Manejar callback de Microsoft OAuth.
     */
    public function handleMicrosoftCallback(Request $request)
    {
        Log::info('OAuth Callback: Recibido', [
            'query_params' => $request->query(),
            'has_code' => $request->has('code'),
            'has_state' => $request->has('state'),
            'has_error' => $request->has('error')
        ]);
        
        // Validar parámetros requeridos
        if (!$request->has('code') || !$request->has('state')) {
            Log::error('OAuth Callback: Parámetros faltantes');
            return redirect()->route('admin.mail.config.index')
                ->with('error', 'Parámetros de autorización faltantes.');
        }

        // Verificar estado CSRF
        $sessionState = session('oauth_state');
        $requestState = $request->state;
        
        Log::info('OAuth Callback: Verificando estado', [
            'session_state' => $sessionState,
            'request_state' => $requestState,
            'states_match' => $sessionState === $requestState,
            'session_id' => session()->getId(),
            'all_session_data' => session()->all()
        ]);
        
        // Si no hay estado en sesión, intentar regenerar la sesión y continuar
        if (!$sessionState) {
            Log::warning('OAuth Callback: No hay estado en sesión, posible pérdida de sesión');
            
            // Para desarrollo, podemos ser más permisivos con la validación de estado
            if (app()->environment('local')) {
                Log::info('OAuth Callback: Modo desarrollo - omitiendo validación estricta de estado');
            } else {
                return redirect()->route('admin.mail.config.index')
                    ->with('error', 'Sesión expirada. Intenta conectar nuevamente.');
            }
        } elseif ($requestState !== $sessionState) {
            Log::error('OAuth Callback: Estado de seguridad inválido', [
                'expected' => $sessionState,
                'received' => $requestState
            ]);
            return redirect()->route('admin.mail.config.index')
                ->with('error', 'Estado de seguridad inválido. Intenta conectar nuevamente.');
        }

        try {
            Log::info('OAuth Callback: Iniciando intercambio de tokens');
            
            // Intercambiar código por tokens
            $tokenResponse = $this->exchangeCodeForTokens($request->code);
            
            Log::info('OAuth Callback: Tokens recibidos', [
                'has_access_token' => isset($tokenResponse['access_token']),
                'has_refresh_token' => isset($tokenResponse['refresh_token']),
                'expires_in' => $tokenResponse['expires_in'] ?? 'N/A'
            ]);
            
            // Obtener información del usuario
            $userInfo = $this->getMicrosoftUserInfo($tokenResponse['access_token']);
            
            Log::info('OAuth Callback: Información de usuario obtenida', [
                'email' => $userInfo['mail'] ?? $userInfo['userPrincipalName'] ?? 'N/A',
                'name' => $userInfo['displayName'] ?? 'N/A'
            ]);
            
            // Guardar tokens en la base de datos usando updateConfig para limpiar caché
            MailSetting::updateConfig([
                'auth_method' => 'oauth_microsoft',
                'mail_provider' => 'outlook',
                'microsoft_access_token' => $tokenResponse['access_token'],
                'microsoft_refresh_token' => $tokenResponse['refresh_token'] ?? null,
                'microsoft_token_expires_at' => now()->addSeconds($tokenResponse['expires_in']),
                'microsoft_connected_at' => now(), // Fecha de conexión OAuth
                'mail_from_address' => $userInfo['mail'] ?? $userInfo['userPrincipalName'],
                'mail_from_name' => $userInfo['displayName'] ?? 'Sistema Gym',
                'microsoft_user_email' => $userInfo['mail'] ?? $userInfo['userPrincipalName'],
                'microsoft_user_name' => $userInfo['displayName'] ?? '',
            ]);

            // Limpiar estado de sesión
            session()->forget('oauth_state');
            
            Log::info('OAuth Callback: Configuración guardada exitosamente');

            return redirect()->route('admin.mail.config.index')
                ->with('success', 'Conectado exitosamente con Microsoft. Email configurado: ' . ($userInfo['mail'] ?? $userInfo['userPrincipalName']));
                
        } catch (Exception $e) {
            Log::error('OAuth Callback: Error en el proceso', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return redirect()->route('admin.mail.config.index')
                ->with('error', 'Error en la autenticación con Microsoft: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar configuración de Laravel Mail en tiempo real.
     */
    private function updateLaravelMailConfig()
    {
        try {
            $mailConfig = MailSetting::getConfig();
            
            if (!$mailConfig) {
                Log::warning('No mail config available, skipping update');
                return;
            }
            
            // Verificar si se debe usar OAuth Microsoft
            if ($mailConfig->auth_method === 'oauth_microsoft' && 
                !empty($mailConfig->getDecryptedMicrosoftAccessToken())) {
                
                Log::info('Mail Config: Configurando OAuth Microsoft para envío de emails');
                
                // Para OAuth Microsoft, usamos un transport personalizado o configuramos SMTP con OAuth
                // Por ahora, configuramos SMTP con los datos de Outlook
                Config::set('mail.mailers.smtp', [
                    'transport' => 'smtp',
                    'host' => 'smtp-mail.outlook.com',
                    'port' => 587,
                    'encryption' => 'tls',
                    'username' => $mailConfig->microsoft_user_email ?? $mailConfig->mail_from_address,
                    'password' => $mailConfig->getDecryptedMicrosoftAccessToken(), // Usar token como "password"
                    'timeout' => min($mailConfig->email_timeout ?? 30, 30), // Máximo 30 segundos
                    'verify_peer' => true,
                    'auth_mode' => 'xoauth2', // Indicar que use OAuth2
                ]);
                
                // Actualizar el from address para que coincida con la cuenta OAuth
                Config::set('mail.from', [
                    'address' => $mailConfig->microsoft_user_email ?? $mailConfig->mail_from_address,
                    'name' => $mailConfig->mail_from_name,
                ]);
                
            } else {
                Log::info('Mail Config: Configurando SMTP tradicional');
                
                // Configuración SMTP tradicional
                Config::set('mail.mailers.smtp', [
                    'transport' => 'smtp',
                    'host' => $mailConfig->mail_host ?? 'localhost',
                    'port' => $mailConfig->mail_port ?? 587,
                    'encryption' => $mailConfig->mail_encryption,
                    'username' => $mailConfig->mail_username,
                    'password' => $mailConfig->getDecryptedPassword(),
                    'timeout' => min($mailConfig->email_timeout ?? 30, 30), // Máximo 30 segundos
                    'verify_peer' => $mailConfig->verify_ssl ?? true,
                ]);
                
                // Actualizar configuración de from
                Config::set('mail.from', [
                    'address' => $mailConfig->mail_from_address ?? 'noreply@example.com',
                    'name' => $mailConfig->mail_from_name ?? 'Gym Control',
                ]);
            }

            // Actualizar mailer por defecto
            Config::set('mail.default', $mailConfig->mail_driver ?? 'smtp');
            
            // Purgar instancias de Mail para forzar reconfiguración
            app()->forgetInstance('mail.manager');
            app()->forgetInstance('mailer');
            
        } catch (Exception $e) {
            Log::error('Error updating Laravel mail config: ' . $e->getMessage());
            // Configurar valores por defecto seguros si hay error
            Config::set('mail.default', 'log');
            Config::set('mail.mailers.smtp.timeout', 30);
        }
    }

    /**
     * Intercambiar código de autorización por tokens de acceso.
     */
    private function exchangeCodeForTokens($code)
    {
        $clientId = env('MS_CLIENT_ID');
        $clientSecret = env('MS_CLIENT_SECRET');
        $redirectUri = env('MS_REDIRECT_URI');
        $authority = env('MS_AUTHORITY', 'https://login.microsoftonline.com/common/');
        
        $tokenUrl = $authority . "oauth2/v2.0/token";
        
        $postData = [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $redirectUri,
            'scope' => env('MS_SCOPES', 'offline_access openid profile email https://graph.microsoft.com/.default'),
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $tokenUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_error($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception('Error en la conexión con Microsoft: ' . $error);
        }
        
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception('Error al obtener tokens de Microsoft: HTTP ' . $httpCode . ' - ' . $response);
        }

        $tokenData = json_decode($response, true);
        
        if (isset($tokenData['error'])) {
            throw new Exception('Error de Microsoft OAuth: ' . ($tokenData['error_description'] ?? $tokenData['error']));
        }

        return $tokenData;
    }

    /**
     * Obtener información del usuario de Microsoft.
     */
    private function getMicrosoftUserInfo($accessToken)
    {
        $userInfoUrl = 'https://graph.microsoft.com/v1.0/me';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $userInfoUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_error($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception('Error obteniendo información del usuario: ' . $error);
        }
        
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception('Error al obtener información del usuario: HTTP ' . $httpCode . ' - ' . $response);
        }

        $userData = json_decode($response, true);
        
        if (isset($userData['error'])) {
            throw new Exception('Error de Microsoft Graph: ' . ($userData['error']['message'] ?? 'Error desconocido'));
        }

        return $userData;
    }

    /**
     * Desconectar Microsoft OAuth.
     */
    public function disconnectMicrosoft()
    {
        try {
            // Limpiar tokens OAuth usando updateConfig para limpiar caché
            MailSetting::updateConfig([
                'microsoft_access_token' => null,
                'microsoft_refresh_token' => null,
                'microsoft_token_expires_at' => null,
                'microsoft_user_email' => null,
                'microsoft_user_name' => null,
            ]);

            return redirect()->route('admin.mail.config.index')
                ->with('success', 'Desconectado de Microsoft OAuth exitosamente.');
                
        } catch (Exception $e) {
            Log::error('Error disconnecting Microsoft OAuth: ' . $e->getMessage());
            
            return redirect()->route('admin.mail.config.index')
                ->with('error', 'Error al desconectar Microsoft OAuth: ' . $e->getMessage());
        }
    }

    /**
     * Enviar email usando Microsoft Graph API
     */
    private function sendEmailViaMicrosoftGraph($toEmail, $subject, $message, $mailConfig)
    {
        try {
            Log::info('Microsoft Graph: Enviando email', [
                'to' => $toEmail,
                'subject' => $subject,
                'from_email' => $mailConfig->microsoft_user_email,
                'from_name' => $mailConfig->mail_from_name,
                'config_object' => [
                    'mail_from_name' => $mailConfig->mail_from_name,
                    'mail_from_address' => $mailConfig->mail_from_address,
                    'microsoft_user_email' => $mailConfig->microsoft_user_email,
                    'microsoft_user_name' => $mailConfig->microsoft_user_name
                ]
            ]);

            $accessToken = $mailConfig->getDecryptedMicrosoftAccessToken();
            
            if (empty($accessToken)) {
                throw new Exception('Token de acceso de Microsoft no disponible');
            }

            // Construir el payload del email para Microsoft Graph API
            $emailPayload = [
                'message' => [
                    'subject' => $subject,
                    'body' => [
                        'contentType' => 'HTML',
                        'content' => '<p>' . nl2br(htmlspecialchars($message)) . '</p>'
                    ],
                    'toRecipients' => [
                        [
                            'emailAddress' => [
                                'address' => $toEmail
                            ]
                        ]
                    ],
                    'from' => [
                        'emailAddress' => [
                            'address' => $mailConfig->microsoft_user_email,
                            'name' => $mailConfig->mail_from_name
                        ]
                    ],
                    'sender' => [
                        'emailAddress' => [
                            'address' => $mailConfig->microsoft_user_email,
                            'name' => $mailConfig->mail_from_name
                        ]
                    ],
                    'replyTo' => [
                        [
                            'emailAddress' => [
                                'address' => $mailConfig->mail_reply_to ?? $mailConfig->mail_from_address,
                                'name' => $mailConfig->mail_from_name
                            ]
                        ]
                    ]
                ]
            ];

            Log::info('Microsoft Graph: Payload completo', [
                'email_payload' => $emailPayload
            ]);

            // Hacer petición HTTP a Microsoft Graph API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post('https://graph.microsoft.com/v1.0/me/sendMail', $emailPayload);

            if ($response->successful()) {
                Log::info('Microsoft Graph: Email enviado exitosamente');
                return true;
            } else {
                $errorData = $response->json();
                Log::error('Microsoft Graph: Error enviando email', [
                    'status' => $response->status(),
                    'error' => $errorData
                ]);
                
                // Si el token expiró, intentar renovarlo
                if ($response->status() === 401) {
                    Log::info('Microsoft Graph: Token expirado, intentando renovar...');
                    
                    if ($this->refreshMicrosoftToken($mailConfig)) {
                        Log::info('Microsoft Graph: Token renovado, reintentando envío...');
                        return $this->sendEmailViaMicrosoftGraph($toEmail, $subject, $message, $mailConfig);
                    }
                }
                
                throw new Exception('Error de Microsoft Graph: ' . ($errorData['error']['message'] ?? 'Error desconocido'));
            }

        } catch (Exception $e) {
            Log::error('Microsoft Graph: Error general', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Renovar token de Microsoft usando refresh token
     */
    private function refreshMicrosoftToken($mailConfig)
    {
        try {
            $refreshToken = $mailConfig->getDecryptedMicrosoftRefreshToken();
            
            if (empty($refreshToken)) {
                Log::error('Microsoft Token Refresh: No hay refresh token disponible');
                return false;
            }

            $response = Http::asForm()->post('https://login.microsoftonline.com/common/oauth2/v2.0/token', [
                'client_id' => env('MS_CLIENT_ID'),
                'client_secret' => env('MS_CLIENT_SECRET'),
                'refresh_token' => $refreshToken,
                'grant_type' => 'refresh_token',
                'scope' => env('MS_SCOPES', 'offline_access https://graph.microsoft.com/.default'),
            ]);

            if ($response->successful()) {
                $tokenData = $response->json();
                
                // Actualizar tokens en la base de datos usando updateConfig para limpiar caché
                MailSetting::updateConfig([
                    'microsoft_access_token' => $tokenData['access_token'],
                    'microsoft_refresh_token' => $tokenData['refresh_token'] ?? $refreshToken,
                    'microsoft_token_expires_at' => now()->addSeconds($tokenData['expires_in'] ?? 3600),
                ]);

                Log::info('Microsoft Token: Renovado exitosamente');
                return true;
            } else {
                Log::error('Microsoft Token Refresh: Error renovando token', [
                    'status' => $response->status(),
                    'error' => $response->json()
                ]);
                return false;
            }

        } catch (Exception $e) {
            Log::error('Microsoft Token Refresh: Error general', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Analizar errores y dar sugerencias específicas.
     */
    private function getErrorSuggestion($errorMessage, $mailConfig)
    {
        // Error de autenticación básica deshabilitada (Microsoft)
        if (str_contains($errorMessage, 'basic authentication is disabled') || 
            str_contains($errorMessage, 'Authentication unsuccessful, basic authentication is disabled')) {
            
            if (str_contains($mailConfig->mail_username, '@outlook.com') || 
                str_contains($mailConfig->mail_username, '@hotmail.com') || 
                str_contains($mailConfig->mail_username, '@live.com')) {
                
                return "Microsoft ha deshabilitado la autenticación básica para cuentas personales. " .
                       "Debes usar OAuth Microsoft o cambiar a una cuenta de Gmail con contraseña de aplicación. " .
                       "Cambia el método de autenticación a 'OAuth Microsoft' para usar tu cuenta de Outlook.";
            }
        }

        // Error de contraseña incorrecta
        if (str_contains($errorMessage, 'Authentication unsuccessful') || 
            str_contains($errorMessage, 'Invalid login') ||
            str_contains($errorMessage, 'Username and Password not accepted')) {
            
            if (str_contains($mailConfig->mail_username, '@gmail.com')) {
                return "Credenciales incorrectas. Para Gmail, debes usar una 'Contraseña de Aplicación' " .
                       "en lugar de tu contraseña normal. Ve a tu cuenta de Google > Seguridad > " .
                       "Contraseñas de aplicaciones para generar una.";
            }
            
            return "Credenciales de autenticación incorrectas. Verifica tu usuario y contraseña.";
        }

        // Error de conexión
        if (str_contains($errorMessage, 'Connection refused') || 
            str_contains($errorMessage, 'Connection timeout')) {
            return "No se puede conectar al servidor SMTP. Verifica el host y puerto. " .
                   "Asegúrate de que no haya firewall bloqueando la conexión.";
        }

        // Error de TLS/SSL
        if (str_contains($errorMessage, 'TLS') || str_contains($errorMessage, 'SSL')) {
            return "Error de encriptación. Intenta cambiar entre TLS y SSL, o desactiva la encriptación " .
                   "temporalmente para probar.";
        }

        // Error genérico con sugerencia de OAuth para Microsoft
        if (str_contains($mailConfig->mail_username, '@outlook.com') || 
            str_contains($mailConfig->mail_username, '@hotmail.com') || 
            str_contains($mailConfig->mail_username, '@live.com')) {
            
            return $errorMessage . " | SUGERENCIA: Microsoft recomienda usar OAuth en lugar de SMTP tradicional. " .
                   "Cambia a 'OAuth Microsoft' para mejor compatibilidad.";
        }

        return $errorMessage;
    }
    
    /**
     * Verificar si el token necesita renovación
     */
    private function tokenNeedsRefresh($mailConfig)
    {
        if (!$mailConfig->microsoft_token_expires_at) {
            return true; // Si no hay fecha de expiración, asumir que necesita renovación
        }
        
        $expiresAt = \Carbon\Carbon::parse($mailConfig->microsoft_token_expires_at);
        $now = \Carbon\Carbon::now();
        
        // Renovar si expira en los próximos 5 minutos
        return $expiresAt->subMinutes(5)->isPast();
    }
    
    /**
     * Enviar email via Microsoft Graph API con token almacenado
     */
    private function sendEmailViaMicrosoftGraphWithStoredToken($to, $subject, $message, $mailConfig, $timeout = 20)
    {
        try {
            $accessToken = $mailConfig->getDecryptedMicrosoftAccessToken();
            
            if (empty($accessToken)) {
                throw new Exception('Token de acceso de Microsoft no disponible en base de datos');
            }

            // Verificar que el token tenga el formato correcto y no haya expirado
            $tokenExpiry = $mailConfig->microsoft_token_expires_at;
            if ($tokenExpiry && now()->isAfter($tokenExpiry)) {
                Log::info('Token expirado, intentando renovar...', [
                    'expiry' => $tokenExpiry,
                    'now' => now(),
                    'token_preview' => substr($accessToken, 0, 30) . '...'
                ]);
                
                // Intentar renovar el token
                $newToken = $mailConfig->refreshMicrosoftAccessToken();
                if ($newToken) {
                    Log::info('Token renovado exitosamente');
                    $accessToken = $newToken; // Usar el token renovado directamente
                } else {
                    throw new Exception('No se pudo renovar el token de acceso');
                }
            }

            // Validar que el token no esté vacío
            if (empty($accessToken)) {
                throw new Exception('Token de acceso no disponible');
            }

            // Los tokens de Microsoft Graph pueden ser JWT u opacos, ambos son válidos
            Log::info('Enviando email via Microsoft Graph API con token válido...', [
                'to' => $to,
                'from' => $mailConfig->microsoft_user_email ?? $mailConfig->mail_from_address,
                'subject' => $subject,
                'token_format_valid' => !empty($accessToken)
            ]);

            $fromEmail = $mailConfig->microsoft_user_email ?? $mailConfig->mail_from_address;
            $fromName = $mailConfig->mail_from_name ?? 'Gym Control';

            $emailData = [
                'message' => [
                    'subject' => $subject,
                    'body' => [
                        'contentType' => 'HTML',
                        'content' => nl2br(htmlspecialchars($message))
                    ],
                    'toRecipients' => [
                        [
                            'emailAddress' => [
                                'address' => $to
                            ]
                        ]
                    ],
                    'from' => [
                        'emailAddress' => [
                            'address' => $fromEmail,
                            'name' => $fromName
                        ]
                    ]
                ]
            ];

            Log::info('Enviando email via Microsoft Graph API con token válido...', [
                'to' => $to,
                'from' => $fromEmail,
                'subject' => $subject,
                'token_format_valid' => count(explode('.', $accessToken)) === 3
            ]);

            $response = Http::timeout($timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json'
                ])
                ->post('https://graph.microsoft.com/v1.0/users/' . urlencode($fromEmail) . '/sendMail', $emailData);

            if ($response->successful()) {
                Log::info('Email enviado exitosamente via Microsoft Graph API');
                return true;
            } else {
                Log::error('Error en Microsoft Graph API', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return false;
            }
            
        } catch (Exception $e) {
            Log::error('Error en sendEmailViaMicrosoftGraphWithStoredToken: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar email via OAuth con credenciales del ENV
     */
    private function sendEmailViaOAuthWithEnvCredentials($to, $subject, $message, $mailConfig, $timeout = 20)
    {
        try {
            Log::info('Obteniendo token de acceso usando credenciales ENV...');
            
            // Obtener token de acceso usando Client Credentials Flow
            $accessToken = $this->getAccessTokenFromEnv();
            
            if (empty($accessToken)) {
                throw new Exception('No se pudo obtener token de acceso desde ENV');
            }

            $fromEmail = $mailConfig->microsoft_user_email ?? $mailConfig->mail_from_address;
            $fromName = $mailConfig->mail_from_name ?? 'Gym Control';

            $emailData = [
                'message' => [
                    'subject' => $subject,
                    'body' => [
                        'contentType' => 'HTML',
                        'content' => nl2br(htmlspecialchars($message))
                    ],
                    'toRecipients' => [
                        [
                            'emailAddress' => [
                                'address' => $to
                            ]
                        ]
                    ],
                    'from' => [
                        'emailAddress' => [
                            'address' => $fromEmail,
                            'name' => $fromName
                        ]
                    ]
                ]
            ];

            Log::info('Enviando email via Microsoft Graph API...', [
                'to' => $to,
                'from' => $fromEmail,
                'subject' => $subject
            ]);

            $response = Http::timeout($timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json'
                ])
                ->post('https://graph.microsoft.com/v1.0/users/' . urlencode($fromEmail) . '/sendMail', $emailData);

            if ($response->successful()) {
                Log::info('Email enviado exitosamente via Microsoft Graph API');
                return true;
            } else {
                Log::error('Error en Microsoft Graph API', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return false;
            }
            
        } catch (Exception $e) {
            Log::error('Error en sendEmailViaOAuthWithEnvCredentials: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener token de acceso usando credenciales del ENV
     */
    private function getAccessTokenFromEnv()
    {
        try {
            $clientId = env('MS_CLIENT_ID');
            $clientSecret = env('MS_CLIENT_SECRET');
            $authority = env('MS_AUTHORITY', 'https://login.microsoftonline.com/common/');
            
            if (empty($clientId) || empty($clientSecret)) {
                throw new Exception('Credenciales de Microsoft no configuradas en ENV');
            }

            $tokenUrl = $authority . "oauth2/v2.0/token";
            
            $postData = [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'grant_type' => 'client_credentials',
                'scope' => 'https://graph.microsoft.com/.default'
            ];

            Log::info('Solicitando token de acceso a Microsoft...', [
                'url' => $tokenUrl,
                'client_id' => $clientId
            ]);

            $response = Http::timeout(30)
                ->asForm()
                ->post($tokenUrl, $postData);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['access_token'])) {
                    Log::info('Token de acceso obtenido exitosamente');
                    return $data['access_token'];
                } else {
                    Log::error('No se encontró access_token en la respuesta', $data);
                    return null;
                }
            } else {
                Log::error('Error obteniendo token de acceso', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }
            
        } catch (Exception $e) {
            Log::error('Error en getAccessTokenFromEnv: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Enviar email via Microsoft Graph API con timeout
     */
    private function sendEmailViaMicrosoftGraphWithTimeout($to, $subject, $message, $mailConfig, $timeout = 20)
    {
        try {
            $accessToken = $mailConfig->getDecryptedMicrosoftAccessToken();
            
            if (empty($accessToken)) {
                throw new Exception('Token de acceso de Microsoft no disponible');
            }

            $fromEmail = $mailConfig->microsoft_user_email ?? $mailConfig->mail_from_address;
            $fromName = $mailConfig->mail_from_name ?? 'Gym Control';

            $emailData = [
                'message' => [
                    'subject' => $subject,
                    'body' => [
                        'contentType' => 'HTML',
                        'content' => nl2br(htmlspecialchars($message))
                    ],
                    'toRecipients' => [
                        [
                            'emailAddress' => [
                                'address' => $to
                            ]
                        ]
                    ],
                    'from' => [
                        'emailAddress' => [
                            'address' => $fromEmail,
                            'name' => $fromName
                        ]
                    ]
                ]
            ];

            $response = Http::timeout($timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json'
                ])
                ->post('https://graph.microsoft.com/v1.0/me/sendMail', $emailData);

            if ($response->successful()) {
                Log::info('Email enviado exitosamente via Microsoft Graph API');
                return true;
            } else {
                Log::error('Error en Microsoft Graph API', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return false;
            }
            
        } catch (Exception $e) {
            Log::error('Error en sendEmailViaMicrosoftGraphWithTimeout: ' . $e->getMessage());
            return false;
        }
    }
}