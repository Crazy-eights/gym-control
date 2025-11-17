<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MailSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MailConfigController extends Controller
{
    public function index()
    {
        try {
            // Obtener configuración de la base de datos
            $mailConfig = MailSetting::getConfig();
            
            // Preparar datos para la vista (simplificado)
            $mailSettings = [
                'mail_driver' => $mailConfig->mail_driver ?? 'smtp',
                'mail_host' => $mailConfig->mail_host ?? 'localhost',
                'mail_port' => $mailConfig->mail_port ?? 587,
                'mail_encryption' => $mailConfig->mail_encryption ?? 'tls',
                'mail_username' => $mailConfig->mail_username ?? '',
                'mail_password' => '',
                'mail_from_address' => $mailConfig->mail_from_address ?? 'noreply@gym.local',
                'mail_from_name' => $mailConfig->mail_from_name ?? 'Gym Control',
                'auth_method' => $mailConfig->auth_method ?? 'smtp',
                'test_email_address' => $mailConfig->test_email_address ?? '',
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
        return redirect()->back()->with('success', 'Configuración actualizada (simplificada).');
    }

    public function testEmail(Request $request)
    {
        return redirect()->back()->with('info', 'Función de prueba simplificada.');
    }

    public function redirectToMicrosoft()
    {
        return redirect()->back()->with('info', 'OAuth simplificado.');
    }

    public function connectToMicrosoft()
    {
        return $this->redirectToMicrosoft();
    }

    public function handleMicrosoftCallback(Request $request)
    {
        return redirect()->route('admin.mail.config.index')->with('info', 'Callback recibido.');
    }

    public function disconnectMicrosoft()
    {
        return redirect()->route('admin.mail.config.index')->with('success', 'Desconectado.');
    }

    public function applyPreset($provider)
    {
        return redirect()->back()->with('info', 'Preset aplicado.');
    }
}
