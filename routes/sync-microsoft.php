<?php

use Illuminate\Support\Facades\Route;
use App\Models\MailSetting;
use Illuminate\Support\Facades\Crypt;

Route::get('/sync-microsoft-credentials', function () {
    try {
        $mailConfig = MailSetting::getConfig();
        
        if (!$mailConfig) {
            return response()->json(['error' => 'No hay configuración de email']);
        }
        
        // Obtener credenciales del .env
        $clientId = env('MS_CLIENT_ID');
        $clientSecret = env('MS_CLIENT_SECRET');
        $redirectUri = env('MS_REDIRECT_URI');
        
        if (!$clientId || !$clientSecret) {
            return response()->json(['error' => 'Credenciales de Microsoft no encontradas en .env']);
        }
        
        // Actualizar en base de datos con encriptación
        $mailConfig->update([
            'microsoft_client_id' => $clientId,
            'microsoft_client_secret' => Crypt::encryptString($clientSecret),
            'microsoft_redirect_uri' => $redirectUri,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => '✅ Credenciales de Microsoft sincronizadas desde .env',
            'details' => [
                'client_id' => $clientId,
                'client_secret_length' => strlen($clientSecret),
                'redirect_uri' => $redirectUri,
                'next_step' => 'Ahora necesitas reconectar OAuth para obtener tokens válidos'
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ]);
    }
});