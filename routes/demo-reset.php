<?php

use Illuminate\Support\Facades\Route;
use App\Models\Member;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

Route::get('/demo-reset-success/{email}', function ($email) {
    try {
        // Buscar usuario
        $member = Member::where('email', $email)->first();
        $admin = Admin::where('email', $email)->first();
        
        if (!$member && !$admin) {
            return response()->json(['error' => 'Usuario no encontrado']);
        }
        
        $user = $member ?: $admin;
        $userType = $member ? 'member' : 'admin';
        
        // Crear token real
        $token = Str::random(60);
        
        if ($userType === 'member') {
            // Guardar en tabla de miembros
            DB::table('member_password_resets')->updateOrInsert(
                ['email' => $email],
                [
                    'email' => $email,
                    'token' => bcrypt($token),
                    'created_at' => now()
                ]
            );
        } else {
            // Guardar en tabla de admins
            \App\Models\AdminPasswordReset::create([
                'email' => $email,
                'token' => bcrypt($token),
                'created_at' => now()
            ]);
        }
        
        // Crear URL de reset
        $resetUrl = url('/password/reset/' . $token . '?email=' . urlencode($email));
        
        return response()->json([
            'success' => true,
            'message' => '✅ DEMOSTRACIÓN: Token de recuperación creado exitosamente',
            'details' => [
                'email' => $email,
                'user_type' => $userType,
                'user_name' => $userType === 'member' ? $member->firstname : $admin->name,
                'token_length' => strlen($token),
                'reset_url' => $resetUrl,
                'email_would_contain' => [
                    'subject' => $userType === 'member' ? 'Restablecer Contraseña - Portal de Socios' : 'Restablecer Contraseña - Gym Control Admin',
                    'greeting' => $userType === 'member' ? "Hola {$member->firstname}" : "Hola {$admin->name}",
                    'button_text' => 'Restablecer Contraseña',
                    'expiry' => '60 minutos'
                ]
            ],
            'database_status' => 'Token guardado en base de datos correctamente',
            'next_step' => 'El usuario haría clic en el enlace del email para restablecer su contraseña'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
});