<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class MailSetting extends Model
{
    use HasFactory;

    protected $table = 'mail_settings';

    protected $fillable = [
        'mail_driver',
        'mail_host',
        'mail_port',
        'mail_encryption',
        'auth_method', // smtp, oauth_microsoft
        'mail_username',
        'mail_password',
        'mail_from_address',
        'mail_from_name',
        'mail_reply_to',
        'mail_provider',
        'microsoft_client_id',
        'microsoft_client_secret',
        'microsoft_tenant_id',
        'microsoft_redirect_uri',
        'microsoft_access_token',
        'microsoft_refresh_token',
        'microsoft_token_expires_at',
        'microsoft_user_email',
        'microsoft_user_name',
        'email_notifications_enabled',
        'email_queue_enabled',
        'email_log_enabled',
        'test_email_address',
        'last_email_test',
        'email_test_status',
        'last_email_error',
        'email_timeout',
        'email_retry_attempts',
        'verify_ssl',
        'email_header_template',
        'email_footer_template',
        'email_logo_url',
        'emails_sent_today',
        'emails_sent_month',
        'emails_failed_today',
        'stats_last_reset',
    ];

    protected $casts = [
        'auth_method' => 'string',
        'email_notifications_enabled' => 'boolean',
        'email_queue_enabled' => 'boolean',
        'email_log_enabled' => 'boolean',
        'verify_ssl' => 'boolean',
        'mail_port' => 'integer',
        'email_timeout' => 'integer',
        'email_retry_attempts' => 'integer',
        'emails_sent_today' => 'integer',
        'emails_sent_month' => 'integer',
        'emails_failed_today' => 'integer',
        'last_email_test' => 'datetime',
        'microsoft_token_expires_at' => 'datetime',
        'microsoft_connected_at' => 'datetime',
        'stats_last_reset' => 'date',
    ];

    // Campos que deben ser encriptados
    protected $encrypted = [
        'mail_password',
        'microsoft_client_secret',
        'microsoft_access_token',
        'microsoft_refresh_token',
    ];

    /**
     * Obtener la configuración de email (singleton)
     */
    public static function getConfig()
    {
        try {
            return Cache::remember('mail_settings', 3600, function () {
                $config = self::first();
                if (!$config) {
                    // Crear configuración por defecto si no existe
                    $config = self::create([
                        'mail_driver' => 'smtp',
                        'mail_host' => 'localhost',
                        'mail_port' => 587,
                        'mail_encryption' => 'tls',
                        'auth_method' => 'smtp',
                        'mail_from_address' => 'noreply@example.com',
                        'mail_from_name' => 'Gym Control',
                        'email_timeout' => 30,
                        'email_retry_attempts' => 3,
                        'verify_ssl' => true,
                        'email_notifications_enabled' => true,
                        'email_queue_enabled' => false,
                        'email_log_enabled' => true,
                    ]);
                }
                return $config;
            });
        } catch (Exception $e) {
            Log::error('Error getting mail config: ' . $e->getMessage());
            // Retornar configuración básica si hay error
            return new self([
                'mail_driver' => 'log',
                'mail_host' => 'localhost',
                'mail_port' => 587,
                'mail_from_address' => 'noreply@example.com',
                'mail_from_name' => 'Gym Control',
                'email_timeout' => 30,
            ]);
        }
    }

    /**
     * Actualizar configuración y limpiar cache
     */
    public static function updateConfig(array $data)
    {
        $config = self::getConfig();
        $config->update($data);
        Cache::forget('mail_settings');
        return $config;
    }

    /**
     * Obtener un valor específico de configuración
     */
    public static function get($key, $default = null)
    {
        $config = self::getConfig();
        return $config->{$key} ?? $default;
    }

    /**
     * Establecer un valor específico de configuración
     */
    public static function set($key, $value)
    {
        $config = self::getConfig();
        $config->{$key} = $value;
        $config->save();
        Cache::forget('mail_settings');
        return $config;
    }

    /**
     * Obtener configuración para Laravel Mail
     */
    public static function getMailConfig()
    {
        $config = self::getConfig();
        
        return [
            'mailers' => [
                'smtp' => [
                    'transport' => 'smtp',
                    'host' => $config->mail_host,
                    'port' => $config->mail_port,
                    'encryption' => $config->mail_encryption,
                    'username' => $config->mail_username,
                    'password' => $config->getDecryptedPassword(),
                    'timeout' => $config->email_timeout,
                    'verify_peer' => $config->verify_ssl,
                ],
            ],
            'from' => [
                'address' => $config->mail_from_address,
                'name' => $config->mail_from_name,
            ],
        ];
    }

    /**
     * Obtener configuración de proveedores predefinidos
     */
    public static function getProviderPresets()
    {
        return [
            'gmail' => [
                'mail_host' => 'smtp.gmail.com',
                'mail_port' => 587,
                'mail_encryption' => 'tls',
            ],
            'outlook' => [
                'mail_host' => 'smtp-mail.outlook.com',
                'mail_port' => 587,
                'mail_encryption' => 'tls',
            ],
            'yahoo' => [
                'mail_host' => 'smtp.mail.yahoo.com',
                'mail_port' => 587,
                'mail_encryption' => 'tls',
            ],
            'sendgrid' => [
                'mail_host' => 'smtp.sendgrid.net',
                'mail_port' => 587,
                'mail_encryption' => 'tls',
            ],
        ];
    }

    /**
     * Aplicar preset de proveedor
     */
    public function applyProviderPreset($provider)
    {
        $presets = self::getProviderPresets();
        
        if (isset($presets[$provider])) {
            $this->update(array_merge($presets[$provider], [
                'mail_provider' => $provider
            ]));
            Cache::forget('mail_settings');
        }
    }

    /**
     * Verificar si OAuth Microsoft está configurado y activo
     */
    public function isMicrosoftOAuthConfigured()
    {
        return $this->auth_method === 'oauth_microsoft' &&
               !empty($this->microsoft_client_id) &&
               !empty($this->getDecryptedMicrosoftClientSecret()) &&
               !empty($this->getDecryptedMicrosoftRefreshToken());
    }

    /**
     * Verificar si Microsoft Graph está operativo (con tokens válidos)
     */
    public function isMicrosoftGraphOperational()
    {
        if (!$this->isMicrosoftOAuthConfigured()) {
            return false;
        }

        try {
            // Intentar obtener un token válido
            $token = $this->getValidMicrosoftAccessToken();
            return !empty($token);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Microsoft Graph no operativo', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Verificar si OAuth Microsoft está conectado (tiene tokens válidos)
     */
    public function isMicrosoftOAuthConnected()
    {
        return !empty($this->microsoft_access_token) &&
               !empty($this->microsoft_user_email) &&
               ($this->microsoft_token_expires_at ? $this->microsoft_token_expires_at->isFuture() : false);
    }

    /**
     * Verificar si SMTP está configurado
     */
    public function isSMTPConfigured()
    {
        return $this->auth_method === 'smtp' &&
               !empty($this->mail_host) &&
               !empty($this->mail_username) &&
               !empty($this->mail_password);
    }

    /**
     * Verificar si está usando OAuth Microsoft
     */
    public function isUsingOAuth()
    {
        return $this->auth_method === 'oauth_microsoft';
    }

    /**
     * Verificar si está usando SMTP tradicional
     */
    public function isUsingSMTP()
    {
        return $this->auth_method === 'smtp';
    }

    /**
     * Verificar si el token de Microsoft está válido
     */
    public function isMicrosoftTokenValid()
    {
        return !empty($this->microsoft_access_token) &&
               $this->microsoft_token_expires_at &&
               $this->microsoft_token_expires_at->isFuture();
    }

    /**
     * Incrementar estadísticas de emails
     */
    public function incrementEmailStats($type = 'sent')
    {
        // Resetear estadísticas si es un nuevo día
        if ($this->stats_last_reset !== now()->toDateString()) {
            $this->update([
                'emails_sent_today' => 0,
                'emails_failed_today' => 0,
                'stats_last_reset' => now()->toDateString(),
            ]);
        }

        // Resetear estadísticas mensuales si es un nuevo mes
        if ($this->updated_at->month !== now()->month) {
            $this->update(['emails_sent_month' => 0]);
        }

        // Incrementar contador correspondiente
        if ($type === 'sent') {
            $this->increment('emails_sent_today');
            $this->increment('emails_sent_month');
        } elseif ($type === 'failed') {
            $this->increment('emails_failed_today');
        }

        Cache::forget('mail_settings');
    }

    /**
     * Registrar prueba de email
     */
    public function recordEmailTest($status, $error = null)
    {
        $this->update([
            'last_email_test' => now(),
            'email_test_status' => $status,
            'last_email_error' => $error,
        ]);
        
        Cache::forget('mail_settings');
    }

    // Mutators para encriptar datos sensibles
    public function setMailPasswordAttribute($value)
    {
        $this->attributes['mail_password'] = $value ? Crypt::encryptString($value) : null;
    }

    public function setMicrosoftClientSecretAttribute($value)
    {
        $this->attributes['microsoft_client_secret'] = $value ? Crypt::encryptString($value) : null;
    }

    public function setMicrosoftAccessTokenAttribute($value)
    {
        $this->attributes['microsoft_access_token'] = $value ? Crypt::encryptString($value) : null;
    }

    public function setMicrosoftRefreshTokenAttribute($value)
    {
        $this->attributes['microsoft_refresh_token'] = $value ? Crypt::encryptString($value) : null;
    }

    // Accessors para desencriptar datos sensibles
    public function getDecryptedPassword()
    {
        return $this->mail_password ? Crypt::decryptString($this->mail_password) : null;
    }

    public function getDecryptedSmtpPassword()
    {
        return $this->getDecryptedPassword();
    }

    public function getDecryptedMicrosoftClientSecret()
    {
        return $this->microsoft_client_secret ? Crypt::decryptString($this->microsoft_client_secret) : null;
    }

    public function getDecryptedMicrosoftAccessToken()
    {
        return $this->microsoft_access_token ? Crypt::decryptString($this->microsoft_access_token) : null;
    }

    public function getDecryptedMicrosoftRefreshToken()
    {
        return $this->microsoft_refresh_token ? Crypt::decryptString($this->microsoft_refresh_token) : null;
    }

    /**
     * Renovar token de acceso usando refresh token
     */
    public function refreshMicrosoftAccessToken()
    {
        try {
            $refreshToken = $this->getDecryptedMicrosoftRefreshToken();
            // Usar siempre los valores del .env para client_id y client_secret
            $clientId = env('MS_CLIENT_ID');
            $clientSecret = env('MS_CLIENT_SECRET');

            if (!$refreshToken) {
                throw new \Exception('Refresh token no disponible - se requiere reconexión OAuth');
            }
            if (!$clientId || !$clientSecret) {
                throw new \Exception('Credenciales de cliente no disponibles en .env (MS_CLIENT_ID, MS_CLIENT_SECRET)');
            }



            $response = \Illuminate\Support\Facades\Http::timeout(30)->asForm()->post('https://login.microsoftonline.com/common/oauth2/v2.0/token', [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'refresh_token' => $refreshToken,
                'grant_type' => 'refresh_token',
                'scope' => 'https://graph.microsoft.com/.default offline_access'
            ]);

            if ($response->successful()) {
                $tokenData = $response->json();
                
                // Actualizar tokens
                $updateData = [
                    'microsoft_access_token' => $tokenData['access_token'], // El mutator se encarga de encriptar
                    'microsoft_token_expires_at' => now()->addSeconds($tokenData['expires_in'] ?? 3600),
                ];

                // Si viene un nuevo refresh token, actualizarlo también
                if (isset($tokenData['refresh_token'])) {
                    $updateData['microsoft_refresh_token'] = $tokenData['refresh_token']; // El mutator se encarga de encriptar
                }

                $this->update($updateData);


                
                return $tokenData['access_token'];
                
            } else {
                $error = $response->json();
                \Illuminate\Support\Facades\Log::error('Error renovando token de Microsoft', [
                    'status' => $response->status(),
                    'error' => $error,
                    'error_description' => $error['error_description'] ?? 'Sin descripción'
                ]);
                
                // Si el error es de refresh token inválido, marcar como requiere reconexión
                if (isset($error['error']) && in_array($error['error'], ['invalid_grant', 'invalid_request'])) {
                    $this->update(['microsoft_refresh_token' => null]);
                    throw new \Exception('Refresh token expirado - se requiere reconexión OAuth');
                }
                
                throw new \Exception('Error renovando token: ' . ($error['error_description'] ?? $response->status()));
            }

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Excepción renovando token de Microsoft', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Obtener token de acceso válido (renovándolo si es necesario)
     */
    public function getValidMicrosoftAccessToken()
    {
        // Verificar si tenemos credenciales básicas
        if (!$this->getDecryptedMicrosoftRefreshToken()) {
            throw new \Exception('No hay refresh token disponible - se requiere reconexión OAuth');
        }

        $currentToken = $this->getDecryptedMicrosoftAccessToken();
        $expiresAt = $this->microsoft_token_expires_at;

        // Verificar si el token actual es válido y no está próximo a expirar
        if ($currentToken && substr_count($currentToken, '.') >= 2) {
            // Si no tenemos fecha de expiración o quedan más de 10 minutos, usar token actual
            if (!$expiresAt || now()->addMinutes(10)->lt($expiresAt)) {

                return $currentToken;
            }
        }

        return $this->refreshMicrosoftAccessToken();
    }

    /**
     * Obtener configuración como array para la vista
     */
    public function toConfigArray()
    {
        return [
            'mail_driver' => $this->mail_driver,
            'mail_host' => $this->mail_host,
            'mail_port' => $this->mail_port,
            'mail_encryption' => $this->mail_encryption,
            'auth_method' => $this->auth_method,
            'mail_username' => $this->mail_username,
            'mail_password' => $this->getDecryptedPassword(),
            'mail_from_address' => $this->mail_from_address,
            'mail_from_name' => $this->mail_from_name,
            'mail_reply_to' => $this->mail_reply_to,
            'mail_provider' => $this->mail_provider,
            'microsoft_client_id' => $this->microsoft_client_id,
            'microsoft_client_secret' => $this->getDecryptedMicrosoftClientSecret(),
            'microsoft_tenant_id' => $this->microsoft_tenant_id,
            'microsoft_redirect_uri' => $this->microsoft_redirect_uri,
            
            // Campos OAuth necesarios para la vista
            'microsoft_access_token' => $this->getDecryptedMicrosoftAccessToken(),
            'microsoft_refresh_token' => $this->getDecryptedMicrosoftRefreshToken(),
            'microsoft_user_email' => $this->microsoft_user_email,
            'microsoft_user_name' => $this->microsoft_user_name,
            'microsoft_token_expires_at' => $this->microsoft_token_expires_at ? $this->microsoft_token_expires_at->format('d/m/Y H:i:s') : null,
            'microsoft_connected_at' => $this->microsoft_connected_at ? $this->microsoft_connected_at->format('d/m/Y H:i:s') : null,
            
            'email_notifications_enabled' => $this->email_notifications_enabled,
            'email_queue_enabled' => $this->email_queue_enabled,
            'email_log_enabled' => $this->email_log_enabled,
            'test_email_address' => $this->test_email_address,
            'last_email_test' => $this->last_email_test ? $this->last_email_test->format('d/m/Y H:i:s') : null,
            'email_test_status' => $this->email_test_status,
            'email_timeout' => $this->email_timeout,
            'email_retry_attempts' => $this->email_retry_attempts,
            'verify_ssl' => $this->verify_ssl,
            'updated_at' => $this->updated_at ? $this->updated_at->format('d/m/Y H:i:s') : null,
        ];
    }

    /**
     * Obtiene la fecha de conexión OAuth formateada de manera segura
     */
    public function getFormattedConnectionDate()
    {
        // Prioridad 1: Fecha específica de conexión
        if ($this->microsoft_connected_at) {
            try {
                return $this->microsoft_connected_at->format('d/m/Y H:i');
            } catch (\Exception $e) {
                return $this->microsoft_connected_at;
            }
        }

        // Prioridad 2: Calcular desde fecha de expiración del token (aprox.)
        if ($this->microsoft_token_expires_at) {
            try {
                $connectedAt = $this->microsoft_token_expires_at->subHour();
                return $connectedAt->format('d/m/Y H:i') . ' (aprox.)';
            } catch (\Exception $e) {
                return $this->microsoft_token_expires_at . ' (aprox.)';
            }
        }

        return 'Fecha no disponible';
    }
}