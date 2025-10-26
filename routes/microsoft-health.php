<?php

use Illuminate\Support\Facades\Route;
use App\Models\MailSetting;

Route::get('/microsoft-health-check', function () {
    try {
        $mailConfig = MailSetting::getConfig();
        
        if (!$mailConfig) {
            return response()->json(['error' => 'No hay configuración de email']);
        }
        
        $health = [
            'auth_method' => $mailConfig->auth_method,
            'is_configured' => $mailConfig->isMicrosoftOAuthConfigured(),
            'is_operational' => false,
            'needs_reconnection' => false,
            'last_token_refresh' => null,
            'token_expires_at' => $mailConfig->microsoft_token_expires_at ? $mailConfig->microsoft_token_expires_at->format('Y-m-d H:i:s') : null,
            'recommendations' => []
        ];
        
        if ($mailConfig->isMicrosoftOAuthConfigured()) {
            try {
                $token = $mailConfig->getValidMicrosoftAccessToken();
                $health['is_operational'] = !empty($token);
                $health['last_token_refresh'] = now()->format('Y-m-d H:i:s');
                
                if ($health['is_operational']) {
                    $health['recommendations'][] = '✅ Microsoft Graph está funcionando correctamente';
                    $health['recommendations'][] = '🔄 Los tokens se renovarán automáticamente cuando sea necesario';
                }
                
            } catch (\Exception $e) {
                $health['error'] = $e->getMessage();
                
                if (str_contains($e->getMessage(), 'refresh token')) {
                    $health['needs_reconnection'] = true;
                    $health['recommendations'][] = '🔄 Se requiere reconexión OAuth (el refresh token expiró)';
                    $health['recommendations'][] = '⏰ Esto puede ocurrir cada 90 días según las políticas de Microsoft';
                    $health['recommendations'][] = '🛠️ Configura SMTP como respaldo mientras tanto';
                } else {
                    $health['recommendations'][] = '🔍 Verificar credenciales y conectividad';
                }
            }
        } else {
            $health['recommendations'][] = '⚙️ Microsoft Graph no está configurado';
            $health['recommendations'][] = '📧 Configura SMTP como alternativa';
        }
        
        // Verificar SMTP como alternativa
        if (!empty($mailConfig->smtp_host)) {
            $health['smtp_alternative'] = [
                'available' => true,
                'host' => $mailConfig->smtp_host,
                'port' => $mailConfig->smtp_port
            ];
            $health['recommendations'][] = '✅ SMTP configurado como respaldo';
        } else {
            $health['smtp_alternative'] = ['available' => false];
            if (!$health['is_operational']) {
                $health['recommendations'][] = '⚠️ Configura SMTP como respaldo para mayor confiabilidad';
            }
        }
        
        return response()->json($health);
        
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});