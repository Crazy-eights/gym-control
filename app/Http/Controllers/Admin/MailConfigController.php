<?php

//namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MailSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class MailConfigController extends Controller
{
    public function index()
    {
        try {
            // Obtener configuración de la base de datos
            $mailConfig = MailSetting::getConfig();
            
            // Preparar datos para la vista
            $mailSettings = [
                // Configuración básica SMTP
                'mail_driver' => $mailConfig->mail_driver ?? 'smtp',
                'mail_host' => $mailConfig->mail_host ?? 'localhost',
                'mail_port' => $mailConfig->mail_port ?? 587,
                'mail_encryption' => $mailConfig->mail_encryption ?? 'tls',
                'mail_username' => $mailConfig->mail_username ?? '',
                'mail_password' => $mailConfig->getDecryptedPassword() ?? '',
                'mail_from_address' => $mailConfig->mail_from_address ?? 'noreply@gym.local',
                'mail_from_name' => $mailConfig->mail_from_name ?? 'Gym Control',
                'mail_reply_to' => $mailConfig->mail_reply_to ?? '',
                'mail_provider' => $mailConfig->mail_provider ?? 'custom',
                'auth_method' => $mailConfig->auth_method ?? 'smtp',
                
                // Configuraciones adicionales
                'email_notifications_enabled' => $mailConfig->email_notifications_enabled ?? true,
                'email_queue_enabled' => $mailConfig->email_queue_enabled ?? false,
                'email_log_enabled' => $mailConfig->email_log_enabled ?? true,
                'test_email_address' => $mailConfig->test_email_address ?? '',
                'last_email_test' => $mailConfig->last_email_test ? $mailConfig->last_email_test->format('d/m/Y H:i:s') : null,
                'email_test_status' => $mailConfig->email_test_status ?? null,
                
                // Datos OAuth de Microsoft (desde ENV, no de la BD)
                'microsoft_client_id' => env('MICROSOFT_CLIENT_ID', ''),
                'microsoft_client_secret' => env('MICROSOFT_CLIENT_SECRET', ''),
                'microsoft_tenant_id' => env('MICROSOFT_TENANT_ID', ''),
                'microsoft_redirect_uri' => route('admin.mail.oauth.microsoft.callback'),
                
                // Estado de conexión OAuth (desde BD)
                'microsoft_access_token' => $mailConfig->getDecryptedMicrosoftAccessToken() ?? '',
                'microsoft_refresh_token' => $mailConfig->getDecryptedMicrosoftRefreshToken() ?? '',
                'microsoft_user_email' => $mailConfig->microsoft_user_email ?? '',
                'microsoft_user_name' => $mailConfig->microsoft_user_name ?? '',
                'microsoft_token_expires_at' => $mailConfig->microsoft_token_expires_at,
                'microsoft_connected_at' => $mailConfig->microsoft_connected_at,
            ];

            return view('admin.mail-config.index', compact('mailSettings'));
            
        } catch (\Exception $e) {
            Log::error('Error loading mail config: ' . $e->getMessage());
            
            return redirect()->route('admin.dashboard')
                ->with('error', 'Error al cargar la configuración de correo.');
        }
    }

    public function update(Request $request)
    {
        try {
            // Validación de datos
            $rules = [
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
                
                // Configuraciones adicionales
                'email_notifications_enabled' => 'sometimes|boolean',
                'email_queue_enabled' => 'sometimes|boolean',
                'email_log_enabled' => 'sometimes|boolean',
            ];

            $validated = $request->validate($rules);

            // Convertir checkboxes a boolean
            $validated['email_notifications_enabled'] = $request->has('email_notifications_enabled');
            $validated['email_queue_enabled'] = $request->has('email_queue_enabled');
            $validated['email_log_enabled'] = $request->has('email_log_enabled');

            Log::info('Mail Config Update: Datos validados', $validated);

            // Verificar si está intentando usar OAuth sin credenciales en ENV
            if ($validated['auth_method'] === 'oauth_microsoft') {
                if (empty(env('MICROSOFT_CLIENT_ID')) || empty(env('MICROSOFT_CLIENT_SECRET')) || empty(env('MICROSOFT_TENANT_ID'))) {
                    return redirect()->route('admin.mail.config.index')
                        ->with('error', 'Para usar OAuth Microsoft, debes configurar MICROSOFT_CLIENT_ID, MICROSOFT_CLIENT_SECRET y MICROSOFT_TENANT_ID en tu archivo .env');
                }
            }

            // Obtener configuración actual para preservar datos OAuth si es necesario
            $currentConfig = MailSetting::getConfig();
            
            // Si está cambiando de OAuth a SMTP, preservar los tokens pero cambiar el método
            if ($validated['auth_method'] === 'smtp' && $currentConfig->auth_method === 'oauth_microsoft') {
                Log::info('Switching from OAuth to SMTP, preserving OAuth tokens');
                // No eliminar tokens, solo cambiar método
            }
            
            // Si está usando OAuth, asegurarse de que el proveedor sea Outlook
            if ($validated['auth_method'] === 'oauth_microsoft') {
                $validated['mail_provider'] = 'outlook';
            }

            // Actualizar configuración usando el modelo MailSetting
            MailSetting::updateConfig($validated);
            
            Log::info('Mail configuration updated successfully', $validated);

            return redirect()->route('admin.mail.config.index')
                ->with('success', 'Configuración de correo actualizada exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error updating mail config: ' . $e->getMessage());
            
            return redirect()->route('admin.mail.config.index')
                ->with('error', 'Error al actualizar la configuración: ' . $e->getMessage());
        }
    }

    public function testEmail(Request $request)
    {
        try {
            // Debug: Log request data
            Log::info('Mail Test: Request iniciado', [
                'request_data' => $request->all(),
                'method' => $request->method()
            ]);

            // Validar dirección de correo
            $validator = \Validator::make($request->all(), [
                'test_email' => 'required|email'
            ], [
                'test_email.required' => 'La dirección de email es requerida.',
                'test_email.email' => 'Debe ser una dirección de email válida.'
            ]);

            if ($validator->fails()) {
                Log::warning('Mail Test: Validación falló', [
                    'errors' => $validator->errors()->toArray()
                ]);
                
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Datos de entrada inválidos: ' . $validator->errors()->first());
            }

            $testEmail = $request->input('test_email');
            $mailConfig = MailSetting::getConfig();

            if (!$mailConfig) {
                Log::error('Mail Test: No se pudo obtener configuración');
                return redirect()->back()
                    ->with('error', 'No se pudo obtener la configuración de correo.');
            }

            Log::info('Mail Test: Iniciando prueba de correo', [
                'test_email' => $testEmail,
                'auth_method' => $mailConfig->auth_method,
                'mail_provider' => $mailConfig->mail_provider,
                'config_id' => $mailConfig->id ?? 'N/A'
            ]);

            // Determinar el método de envío según la configuración
            if ($mailConfig->auth_method === 'oauth_microsoft') {
                Log::info('Mail Test: Using OAuth Microsoft');
                $result = $this->sendTestEmailViaOAuth($testEmail, $mailConfig);
            } else {
                Log::info('Mail Test: Using SMTP');
                $result = $this->sendTestEmailViaSMTP($testEmail, $mailConfig);
            }

            Log::info('Mail Test: Resultado', ['result' => $result]);

            if ($result['success']) {
                // Actualizar registro de última prueba
                MailSetting::updateConfig([
                    'test_email_address' => $testEmail,
                    'last_email_test' => now(),
                    'email_test_status' => 'success'
                ]);

                Log::info('Mail Test: Éxito completo', ['test_email' => $testEmail]);

                return redirect()->back()
                    ->with('success', 'Correo de prueba enviado exitosamente a ' . $testEmail . '. Revisa tu bandeja de entrada.');
            } else {
                // Registrar fallo
                MailSetting::updateConfig([
                    'test_email_address' => $testEmail,
                    'last_email_test' => now(),
                    'email_test_status' => 'failed'
                ]);

                Log::error('Mail Test: Falló envío', ['error' => $result['message']]);

                return redirect()->back()
                    ->with('error', 'Error al enviar correo de prueba: ' . $result['message']);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Mail Test: Error de validación', [
                'message' => $e->getMessage(),
                'errors' => $e->errors()
            ]);

            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Error de validación: ' . $e->getMessage());

        } catch (\Exception $e) {
            Log::error('Mail Test: Error general', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return redirect()->back()
                ->with('error', 'Error inesperado al procesar correo de prueba: ' . $e->getMessage())
                ->withInput();
        }
    }

    private function sendTestEmailViaSMTP($testEmail, $mailConfig)
    {
        try {
            // Configurar SMTP temporalmente
            config([
                'mail.default' => 'smtp',
                'mail.mailers.smtp.host' => $mailConfig->mail_host,
                'mail.mailers.smtp.port' => $mailConfig->mail_port,
                'mail.mailers.smtp.encryption' => $mailConfig->mail_encryption,
                'mail.mailers.smtp.username' => $mailConfig->mail_username,
                'mail.mailers.smtp.password' => $mailConfig->getDecryptedPassword(),
                'mail.from.address' => $mailConfig->mail_from_address,
                'mail.from.name' => $mailConfig->mail_from_name,
            ]);

            // Enviar correo de prueba
            \Mail::raw('Este es un correo de prueba enviado desde el sistema Gym Control.', function ($message) use ($testEmail, $mailConfig) {
                $message->to($testEmail)
                        ->subject('Correo de Prueba - Gym Control')
                        ->from($mailConfig->mail_from_address, $mailConfig->mail_from_name);
            });

            Log::info('Mail Test: SMTP enviado exitosamente', ['test_email' => $testEmail]);
            return ['success' => true, 'message' => 'Correo enviado via SMTP'];

        } catch (\Exception $e) {
            Log::error('Mail Test: Error SMTP', [
                'message' => $e->getMessage(),
                'test_email' => $testEmail
            ]);
            
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function sendTestEmailViaOAuth($testEmail, $mailConfig)
    {
        try {
            Log::info('OAuth Test: Iniciando envío via OAuth', ['test_email' => $testEmail]);

            // Verificar que tengamos un token válido
            $accessToken = $mailConfig->getDecryptedMicrosoftAccessToken();
            
            if (empty($accessToken)) {
                Log::error('OAuth Test: No hay token de acceso');
                return ['success' => false, 'message' => 'No hay token de acceso OAuth disponible. Reconecta tu cuenta de Microsoft.'];
            }

            Log::info('OAuth Test: Token encontrado, verificando expiración');

            // Verificar si el token ha expirado y renovar si es necesario
            if ($mailConfig->microsoft_token_expires_at && $mailConfig->microsoft_token_expires_at->isPast()) {
                Log::info('OAuth Test: Token expirado, renovando');
                $renewResult = $this->renewMicrosoftToken($mailConfig);
                if (!$renewResult['success']) {
                    Log::error('OAuth Test: Error renovando token', ['error' => $renewResult['message']]);
                    return ['success' => false, 'message' => 'Error renovando token OAuth: ' . $renewResult['message']];
                }
                $accessToken = $renewResult['access_token'];
                Log::info('OAuth Test: Token renovado exitosamente');
            } else {
                Log::info('OAuth Test: Token válido, procediendo con envío');
            }

            // Preparar datos del correo para Microsoft Graph API
            $messageData = [
                'message' => [
                    'subject' => 'Correo de Prueba - Gym Control',
                    'body' => [
                        'contentType' => 'Text',
                        'content' => "Este es un correo de prueba enviado desde el sistema Gym Control usando OAuth de Microsoft.\n\nFecha y hora: " . now()->format('d/m/Y H:i:s') . "\n\nSi recibes este mensaje, la configuración de email está funcionando correctamente."
                    ],
                    'toRecipients' => [
                        [
                            'emailAddress' => [
                                'address' => $testEmail
                            ]
                        ]
                    ]
                ]
            ];

            Log::info('OAuth Test: Enviando correo via Graph API', [
                'message_data' => $messageData,
                'token_preview' => substr($accessToken, 0, 10) . '...'
            ]);

            // Enviar correo via Microsoft Graph API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json'
            ])->post('https://graph.microsoft.com/v1.0/me/sendMail', $messageData);

            Log::info('OAuth Test: Respuesta de Graph API', [
                'status' => $response->status(),
                'headers' => $response->headers(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                Log::info('OAuth Test: Correo enviado exitosamente', ['test_email' => $testEmail]);
                return ['success' => true, 'message' => 'Correo enviado via OAuth Microsoft'];
            } else {
                Log::error('OAuth Test: Error en Graph API', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'test_email' => $testEmail
                ]);
                
                // Intentar parsear el error de Microsoft
                $errorBody = $response->json();
                $errorMessage = 'Error de Graph API';
                
                if (isset($errorBody['error']['message'])) {
                    $errorMessage = $errorBody['error']['message'];
                } elseif (isset($errorBody['error']['code'])) {
                    $errorMessage = 'Error: ' . $errorBody['error']['code'];
                } else {
                    $errorMessage = 'HTTP ' . $response->status() . ': ' . $response->body();
                }
                
                return ['success' => false, 'message' => $errorMessage];
            }

        } catch (\Exception $e) {
            Log::error('OAuth Test: Error general', [
                'message' => $e->getMessage(),
                'test_email' => $testEmail,
                'trace' => $e->getTraceAsString()
            ]);
            
            return ['success' => false, 'message' => 'Error interno: ' . $e->getMessage()];
        }
    }

    private function renewMicrosoftToken($mailConfig)
    {
        try {
            $refreshToken = $mailConfig->getDecryptedMicrosoftRefreshToken();
            
            if (empty($refreshToken)) {
                return ['success' => false, 'message' => 'No hay refresh token disponible'];
            }

            $clientId = env('MICROSOFT_CLIENT_ID');
            $clientSecret = env('MICROSOFT_CLIENT_SECRET');
            $tenantId = env('MICROSOFT_TENANT_ID');

            $tokenUrl = "https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/token";
            
            $response = Http::asForm()->post($tokenUrl, [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'refresh_token' => $refreshToken,
                'grant_type' => 'refresh_token',
                'scope' => 'https://graph.microsoft.com/Mail.Send offline_access User.Read'
            ]);

            if (!$response->successful()) {
                Log::error('Token Renewal: Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                return ['success' => false, 'message' => 'Error renovando token: ' . $response->body()];
            }

            $tokenData = $response->json();
            
            // Actualizar tokens en la base de datos
            MailSetting::updateConfig([
                'microsoft_access_token' => $tokenData['access_token'],
                'microsoft_refresh_token' => $tokenData['refresh_token'] ?? $refreshToken, // Mantener el anterior si no viene uno nuevo
                'microsoft_token_expires_at' => now()->addSeconds($tokenData['expires_in']),
            ]);

            Log::info('Token Renewal: Exitoso');
            
            return ['success' => true, 'access_token' => $tokenData['access_token']];

        } catch (\Exception $e) {
            Log::error('Token Renewal: Error', ['message' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function applyPreset($provider)
    {
        return redirect()->back()
            ->with('info', 'Presets de proveedor temporalmente deshabilitados.');
    }

    public function redirectToMicrosoft()
    {
        try {
            // Verificar que las credenciales OAuth estén configuradas en ENV
            $clientId = env('MICROSOFT_CLIENT_ID');
            $tenantId = env('MICROSOFT_TENANT_ID');
            $redirectUri = route('admin.mail.oauth.microsoft.callback');

            if (empty($clientId) || empty($tenantId)) {
                return redirect()->route('admin.mail.config.index')
                    ->with('error', 'Credenciales de Microsoft OAuth no configuradas en el archivo .env. Configura MICROSOFT_CLIENT_ID y MICROSOFT_TENANT_ID.');
            }

            // Generar estado para seguridad
            $state = bin2hex(random_bytes(16));
            session(['oauth_state' => $state]);

            // URL de autorización de Microsoft
            $authUrl = "https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/authorize?" . http_build_query([
                'client_id' => $clientId,
                'response_type' => 'code',
                'redirect_uri' => $redirectUri,
                'scope' => 'https://graph.microsoft.com/Mail.Send offline_access User.Read',
                'state' => $state,
                'response_mode' => 'query'
            ]);

            Log::info('OAuth Microsoft: Redirecting to Microsoft', [
                'auth_url' => $authUrl,
                'state' => $state
            ]);

            return redirect($authUrl);
            
        } catch (\Exception $e) {
            Log::error('Error redirecting to Microsoft OAuth: ' . $e->getMessage());
            
            return redirect()->route('admin.mail.config.index')
                ->with('error', 'Error al conectar con Microsoft OAuth: ' . $e->getMessage());
        }
    }

    public function connectToMicrosoft()
    {
        return $this->redirectToMicrosoft();
    }

    public function handleMicrosoftCallback(Request $request)
    {
        try {
            // Verificar estado para prevenir CSRF
            $state = $request->get('state');
            $sessionState = session('oauth_state');
            
            if (empty($state) || $state !== $sessionState) {
                Log::warning('OAuth Callback: Estado inválido', [
                    'received_state' => $state,
                    'session_state' => $sessionState
                ]);
                
                return redirect()->route('admin.mail.config.index')
                    ->with('error', 'Estado de autenticación inválido. Intenta conectar nuevamente.');
            }

            // Verificar código de autorización
            $code = $request->get('code');
            if (empty($code)) {
                $error = $request->get('error_description') ?? 'No se recibió código de autorización';
                Log::error('OAuth Callback: Error en autorización', ['error' => $error]);
                
                return redirect()->route('admin.mail.config.index')
                    ->with('error', 'Error de autorización: ' . $error);
            }

            // Obtener credenciales del ENV
            $clientId = env('MICROSOFT_CLIENT_ID');
            $clientSecret = env('MICROSOFT_CLIENT_SECRET');
            $tenantId = env('MICROSOFT_TENANT_ID');
            $redirectUri = route('admin.mail.oauth.microsoft.callback');

            // Intercambiar código por tokens
            $tokenUrl = "https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/token";
            
            $response = Http::asForm()->post($tokenUrl, [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'code' => $code,
                'redirect_uri' => $redirectUri,
                'grant_type' => 'authorization_code',
                'scope' => 'https://graph.microsoft.com/Mail.Send offline_access User.Read'
            ]);

            if (!$response->successful()) {
                Log::error('OAuth Token Exchange: Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                return redirect()->route('admin.mail.config.index')
                    ->with('error', 'Error al obtener tokens de Microsoft. Intenta nuevamente.');
            }

            $tokenData = $response->json();
            
            // Obtener información del usuario
            $userResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $tokenData['access_token']
            ])->get('https://graph.microsoft.com/v1.0/me');

            if (!$userResponse->successful()) {
                Log::error('OAuth User Info: Error', [
                    'status' => $userResponse->status(),
                    'body' => $userResponse->body()
                ]);
                
                return redirect()->route('admin.mail.config.index')
                    ->with('error', 'Error al obtener información del usuario de Microsoft.');
            }

            $userData = $userResponse->json();

            // Guardar tokens y configuración OAuth
            MailSetting::updateConfig([
                'auth_method' => 'oauth_microsoft',
                'mail_provider' => 'outlook',
                'microsoft_access_token' => $tokenData['access_token'],
                'microsoft_refresh_token' => $tokenData['refresh_token'] ?? null,
                'microsoft_token_expires_at' => now()->addSeconds($tokenData['expires_in']),
                'microsoft_connected_at' => now(),
                'microsoft_user_email' => $userData['mail'] ?? $userData['userPrincipalName'] ?? '',
                'microsoft_user_name' => $userData['displayName'] ?? '',
                'mail_from_address' => $userData['mail'] ?? $userData['userPrincipalName'] ?? '',
            ]);

            // Limpiar sesión OAuth
            session()->forget('oauth_state');

            Log::info('OAuth Microsoft: Conexión exitosa', [
                'user_email' => $userData['mail'] ?? $userData['userPrincipalName'] ?? 'N/A',
                'user_name' => $userData['displayName'] ?? 'N/A'
            ]);

            return redirect()->route('admin.mail.config.index')
                ->with('success', 'Conexión con Microsoft OAuth establecida exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error en callback de Microsoft OAuth: ' . $e->getMessage());
            
            return redirect()->route('admin.mail.config.index')
                ->with('error', 'Error procesando respuesta de Microsoft: ' . $e->getMessage());
        }
    }

    public function disconnectMicrosoft()
    {
        try {
            // Actualizar configuración para eliminar tokens OAuth
            MailSetting::updateConfig([
                'auth_method' => 'smtp', // Volver a SMTP por defecto
                'microsoft_access_token' => null,
                'microsoft_refresh_token' => null,
                'microsoft_token_expires_at' => null,
                'microsoft_connected_at' => null,
                'microsoft_user_email' => null,
                'microsoft_user_name' => null,
            ]);

            Log::info('OAuth Microsoft: Desconexión exitosa');

            return redirect()->route('admin.mail.config.index')
                ->with('success', 'Desconexión de Microsoft OAuth realizada exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error desconectando Microsoft OAuth: ' . $e->getMessage());
            
            return redirect()->route('admin.mail.config.index')
                ->with('error', 'Error al desconectar OAuth: ' . $e->getMessage());
        }
    }
}
