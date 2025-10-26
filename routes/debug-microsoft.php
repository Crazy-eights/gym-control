<?php

use Illuminate\Support\Facades\Route;
use App\Models\MailSetting;

Route::get('/debug-microsoft-token', function () {
    try {
        $mailConfig = MailSetting::getConfig();
        
        if (!$mailConfig) {
            return response()->json(['error' => 'No hay configuración de email']);
        }
        
        $accessToken = $mailConfig->getDecryptedMicrosoftAccessToken();
        $refreshToken = $mailConfig->getDecryptedMicrosoftRefreshToken();
        
        return response()->json([
            'auth_method' => $mailConfig->auth_method,
            'user_email' => $mailConfig->microsoft_user_email,
            'access_token_length' => strlen($accessToken ?? ''),
            'access_token_preview' => substr($accessToken ?? '', 0, 100),
            'access_token_has_dots' => substr_count($accessToken ?? '', '.'),
            'refresh_token_length' => strlen($refreshToken ?? ''),
            'refresh_token_preview' => substr($refreshToken ?? '', 0, 50),
            'connected_at' => $mailConfig->microsoft_connected_at,
            'expires_at' => $mailConfig->microsoft_token_expires_at,
            'is_expired' => $mailConfig->microsoft_token_expires_at ? now()->gt($mailConfig->microsoft_token_expires_at) : 'No expiry set'
        ]);
        
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
    }
});