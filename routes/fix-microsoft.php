<?php

use Illuminate\Support\Facades\Route;
use App\Models\MailSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

Route::get('/fix-microsoft-token', function () {
    try {
        $mailConfig = MailSetting::getConfig();
        
        if (!$mailConfig) {
            return response()->json(['error' => 'No hay configuración de email']);
        }
        
        $clientId = $mailConfig->microsoft_client_id;
        $clientSecret = $mailConfig->getDecryptedMicrosoftClientSecret();
        $refreshToken = $mailConfig->getDecryptedMicrosoftRefreshToken();
        
        $diagnostics = [
            'client_id' => $clientId ? 'Configurado (' . strlen($clientId) . ' chars)' : 'No configurado',
            'client_secret' => $clientSecret ? 'Configurado (' . strlen($clientSecret) . ' chars)' : 'No configurado',
            'refresh_token' => $refreshToken ? 'Configurado (' . strlen($refreshToken) . ' chars)' : 'No configurado',
        ];
        
        if (!$clientId || !$clientSecret || !$refreshToken) {
            return response()->json([
                'error' => 'Faltan credenciales para renovar',
                'diagnostics' => $diagnostics,
                'solution' => 'Necesitas reconectar Microsoft Graph desde el panel admin'
            ]);
        }
        
        // Intentar renovar el token
        Log::info('Intentando renovar token de Microsoft manualmente');
        
        $response = Http::asForm()->post('https://login.microsoftonline.com/common/oauth2/v2.0/token', [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token',
            'scope' => 'https://graph.microsoft.com/.default offline_access'
        ]);
        
        if ($response->successful()) {
            $tokenData = $response->json();
            
            // Actualizar tokens
            $mailConfig->update([
                'microsoft_access_token' => \Illuminate\Support\Facades\Crypt::encryptString($tokenData['access_token']),
                'microsoft_token_expires_at' => now()->addSeconds($tokenData['expires_in'] ?? 3600),
            ]);
            
            // Si viene un nuevo refresh token, actualizarlo también
            if (isset($tokenData['refresh_token'])) {
                $mailConfig->update([
                    'microsoft_refresh_token' => \Illuminate\Support\Facades\Crypt::encryptString($tokenData['refresh_token'])
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => '✅ Token de Microsoft renovado exitosamente',
                'details' => [
                    'access_token_length' => strlen($tokenData['access_token']),
                    'expires_in' => $tokenData['expires_in'] . ' segundos',
                    'new_expiry' => now()->addSeconds($tokenData['expires_in'])->format('Y-m-d H:i:s'),
                    'has_new_refresh_token' => isset($tokenData['refresh_token']) ? 'Sí' : 'No'
                ]
            ]);
            
        } else {
            $error = $response->json();
            return response()->json([
                'error' => 'Error renovando token',
                'microsoft_error' => $error,
                'status' => $response->status(),
                'suggestion' => 'Es posible que necesites reconectar Microsoft Graph desde el panel admin'
            ]);
        }
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'suggestion' => 'Verifica que las credenciales estén correctamente encriptadas en la base de datos'
        ]);
    }
});