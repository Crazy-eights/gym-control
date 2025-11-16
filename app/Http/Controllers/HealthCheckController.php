<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Models\MailSetting;

class HealthCheckController extends Controller
{
    public function microsoftHealthCheck(): JsonResponse
    {
        try {
            $mailConfig = $this->getMailConfig();

            if (!$mailConfig) {
                return response()->json(['error' => 'No hay configuración de email']);
            }

            $health = $this->initializeHealthData($mailConfig);
            $this->checkMicrosoftOAuth($mailConfig, $health);
            $this->checkSmtpAlternative($mailConfig, $health);

            return response()->json($health);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    private function getMailConfig(): ?MailSetting
    {
        return MailSetting::getConfig();
    }

    private function initializeHealthData(MailSetting $mailConfig): array
    {
        return [
            'auth_method' => $mailConfig->auth_method,
            'is_configured' => $mailConfig->isMicrosoftOAuthConfigured(),
            'is_operational' => false,
            'needs_reconnection' => false,
            'last_token_refresh' => null,
            'token_expires_at' => $mailConfig->microsoft_token_expires_at ?
                $mailConfig->microsoft_token_expires_at->format('Y-m-d H:i:s') : null,
            'recommendations' => []
        ];
    }

    private function checkMicrosoftOAuth(MailSetting $mailConfig, array &$health): void
    {
        if (!$mailConfig->isMicrosoftOAuthConfigured()) {
            $this->addNotConfiguredRecommendations($health);
            return;
        }

        try {
            $this->validateMicrosoftToken($mailConfig, $health);
        } catch (\Exception $e) {
            $this->handleMicrosoftError($e, $health);
        }
    }

    private function validateMicrosoftToken(MailSetting $mailConfig, array &$health): void
    {
        $token = $mailConfig->getValidMicrosoftAccessToken();
        $health['is_operational'] = !empty($token);
        $health['last_token_refresh'] = now()->format('Y-m-d H:i:s');

        if ($health['is_operational']) {
            $this->addOperationalRecommendations($health);
        }
    }

    private function handleMicrosoftError(\Exception $e, array &$health): void
    {
        $health['error'] = $e->getMessage();

        if (str_contains($e->getMessage(), 'refresh token')) {
            $this->addRefreshTokenErrorRecommendations($health);
        } else {
            $health['recommendations'][] = '🔍 Verificar credenciales y conectividad';
        }
    }

    private function addNotConfiguredRecommendations(array &$health): void
    {
        $health['recommendations'][] = '⚙️ Microsoft Graph no está configurado';
        $health['recommendations'][] = '📧 Configura SMTP como alternativa';
    }

    private function addOperationalRecommendations(array &$health): void
    {
        $health['recommendations'][] = '✅ Microsoft Graph está funcionando correctamente';
        $health['recommendations'][] = '🔄 Los tokens se renovarán automáticamente cuando sea necesario';
    }

    private function addRefreshTokenErrorRecommendations(array &$health): void
    {
        $health['needs_reconnection'] = true;
        $health['recommendations'][] = '🔄 Se requiere reconexión OAuth (el refresh token expiró)';
        $health['recommendations'][] = '⏰ Esto puede ocurrir cada 90 días según las políticas de Microsoft';
        $health['recommendations'][] = '🛠️ Configura SMTP como respaldo mientras tanto';
    }

    private function checkSmtpAlternative(MailSetting $mailConfig, array &$health): void
    {
        if (!empty($mailConfig->smtp_host)) {
            $this->addSmtpAvailableData($mailConfig, $health);
        } else {
            $this->addSmtpNotAvailableData($health);
        }
    }

    private function addSmtpAvailableData(MailSetting $mailConfig, array &$health): void
    {
        $health['smtp_alternative'] = [
            'available' => true,
            'host' => $mailConfig->smtp_host,
            'port' => $mailConfig->smtp_port
        ];
        $health['recommendations'][] = '✅ SMTP configurado como respaldo';
    }

    private function addSmtpNotAvailableData(array &$health): void
    {
        $health['smtp_alternative'] = ['available' => false];

        if (!$health['is_operational']) {
            $health['recommendations'][] = '⚠️ Configura SMTP como respaldo para mayor confiabilidad';
        }
    }
}
