<?php

use Illuminate\Support\Facades\Route;
use App\Models\MailSetting;
use App\Models\Member;
use App\Models\Admin;

Route::get('/test-email-config', function () {
    try {
        $mailConfig = MailSetting::getConfig();
        
        if (!$mailConfig) {
            return response()->json(['error' => 'No hay configuración de email']);
        }
        
        $config = [
            'auth_method' => $mailConfig->auth_method,
            'microsoft_user_email' => $mailConfig->microsoft_user_email,
            'has_access_token' => !empty($mailConfig->getDecryptedMicrosoftAccessToken()),
            'smtp_host' => $mailConfig->smtp_host ?? 'No configurado',
            'smtp_port' => $mailConfig->smtp_port ?? 'No configurado',
        ];
        
        return response()->json(['config' => $config]);
        
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

Route::get('/test-password-reset/{email}', function ($email) {
    try {
        // Simular el proceso de reset de contraseña
        $member = Member::where('email', $email)->first();
        $admin = Admin::where('email', $email)->first();
        
        if (!$member && !$admin) {
            return response()->json(['error' => 'Usuario no encontrado']);
        }
        
        $user = $member ?: $admin;
        $userType = $member ? 'member' : 'admin';
        
        // Crear token
        $token = \Illuminate\Support\Str::random(64);
        
        // Guardar en base de datos según el tipo
        if ($userType === 'member') {
            \Illuminate\Support\Facades\DB::table('member_password_resets')->updateOrInsert(
                ['email' => $member->email],
                [
                    'email' => $member->email,
                    'token' => bcrypt($token),
                    'created_at' => now()
                ]
            );
        } else {
            \App\Models\AdminPasswordReset::create([
                'email' => $admin->email,
                'token' => bcrypt($token),
                'created_at' => now()
            ]);
        }
        
        // Crear URL de reset
        $resetUrl = url('/password/reset/' . $token . '?email=' . urlencode($email));
        
        // Obtener configuración de email
        $mailConfig = MailSetting::getConfig();
        
        if (!$mailConfig) {
            return response()->json(['error' => 'No hay configuración de email']);
        }
        
        // Simular envío
        $emailData = [
            'to' => $email,
            'user_type' => $userType,
            'user_name' => $userType === 'member' ? $member->firstname : $admin->name,
            'reset_url' => $resetUrl,
            'token' => $token,
            'auth_method' => $mailConfig->auth_method,
            'has_token' => !empty($mailConfig->getDecryptedMicrosoftAccessToken())
        ];
        
        return response()->json([
            'success' => true,
            'message' => 'Token creado exitosamente',
            'data' => $emailData
        ]);
        
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});