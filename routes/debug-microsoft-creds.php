<?php

use Illuminate\Support\Facades\Route;
use App\Models\MailSetting;

Route::get('/debug-microsoft-credentials', function () {
    try {
        $mailConfig = MailSetting::getConfig();
        
        if (!$mailConfig) {
            return response()->json(['error' => 'No hay configuración de email']);
        }
        
        return response()->json([
            'auth_method' => $mailConfig->auth_method,
            'microsoft_client_id' => $mailConfig->microsoft_client_id ? 'Configurado' : 'No configurado',
            'microsoft_client_secret_encrypted' => !empty($mailConfig->microsoft_client_secret) ? 'Configurado' : 'No configurado',
            'microsoft_client_secret_length' => strlen($mailConfig->microsoft_client_secret ?? ''),
            'microsoft_refresh_token_encrypted' => !empty($mailConfig->microsoft_refresh_token) ? 'Configurado' : 'No configurado',
            'microsoft_refresh_token_length' => strlen($mailConfig->microsoft_refresh_token ?? ''),
            'can_decrypt_client_secret' => !empty($mailConfig->getDecryptedMicrosoftClientSecret()) ? 'Sí' : 'No',
            'can_decrypt_refresh_token' => !empty($mailConfig->getDecryptedMicrosoftRefreshToken()) ? 'Sí' : 'No',
        ]);
        
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
    }
});