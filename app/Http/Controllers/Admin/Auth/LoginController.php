<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminPasswordReset;
use App\Models\MailSetting;
use App\Mail\AdminResetPasswordMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /**
     * Muestra el formulario de login para el admin.
     */
    public function showLoginForm()
    {
        return view('admin.auth.login'); 
    }

    /**
     * Maneja el intento de inicio de sesión.
     */
    public function login(Request $request)
    {
        // 1. Valida los datos del formulario
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $inputUser = $credentials['username'];
        $inputPassword = $credentials['password'];

        // 2. Verificar si es un email (para socios)
        if (filter_var($inputUser, FILTER_VALIDATE_EMAIL)) {
            // Limpiar cualquier sesión de admin anterior
            Auth::guard('admin')->logout();
            
            $member = \App\Models\Member::where('email', $inputUser)->first();
            
            if ($member && \Hash::check($inputPassword, $member->password)) {
                // Login del socio
                Auth::guard('web')->login($member);
                $request->session()->regenerate();
                return redirect('/portal/dashboard');
            }
            
            return back()->withErrors(['username' => 'Credenciales de socio incorrectas.']);
        } else {
            // Limpiar cualquier sesión de socio anterior
            Auth::guard('web')->logout();
            
            // Es username (para admins)
            if (Auth::guard('admin')->attempt(['username' => $inputUser, 'password' => $inputPassword])) {
                $request->session()->regenerate();
                return redirect()->intended(route('admin.dashboard'));
            }
            
            return back()->withErrors(['username' => 'Credenciales de admin incorrectas.']);
        }
    }

    /**
     * Cierra la sesión del admin.
     */
    public function logout(Request $request)
    {
        // Cerrar sesión de cualquier guard activo
        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        }
        
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * Mostrar formulario para solicitar reset de contraseña
     */
    public function showForgotPasswordForm(Request $request)
    {
        // Siempre mostrar la vista unificada
        return view('auth.forgot-password-unified');
    }

    /**
     * Enviar email con enlace de reset de contraseña (detección automática)
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = $request->email;

        try {
            // 1. Primero buscar si es un socio
            $member = \App\Models\Member::where('email', $email)->first();
            
            if ($member) {
                // Es un socio - crear token en la tabla standard
                $token = Str::random(60);
                
                // Limpiar tokens antiguos y crear nuevo
                DB::table('member_password_resets')->where('email', $email)->delete();
                DB::table('member_password_resets')->insert([
                    'email' => $email,
                    'token' => bcrypt($token),
                    'created_at' => now()
                ]);

                // Crear URL de reset
                $resetUrl = route('password.reset', $token) . '?email=' . urlencode($email);

                // Enviar email usando la configuración existente
                $this->sendMemberResetEmail($email, $member, $resetUrl, $token);
                
                return back()->with('status', 'Te hemos enviado un enlace de recuperación por email.');
            }

            // 2. Si no es socio, buscar si es un admin
            $admin = Admin::where('email', $email)->first();
            
            if ($admin) {
                // Es un admin - usar la lógica existente de admin
                AdminPasswordReset::cleanExpiredTokens();

                $token = Str::random(64);
                AdminPasswordReset::create([
                    'email' => $email,
                    'token' => $token,
                    'created_at' => now(),
                ]);

                // Crear URL de reset para admin
                $resetUrl = route('admin.password.reset', $token) . '?email=' . urlencode($email);

                // Enviar email usando la configuración existente
                $this->sendAdminResetEmail($email, $admin, $resetUrl, $token);
                    
                return back()->with('Te hemos enviado un enlace de recuperación por email.');
            }

            // 3. Si no es ni socio ni admin
            return back()->withErrors([
                'email' => 'No encontramos una cuenta (ni de socio ni de administrador) con ese email.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error en sendResetLinkEmail: ' . $e->getMessage());
            return back()->withErrors([
                'email' => 'Error al procesar la solicitud. Inténtalo más tarde.'
            ]);
        }
    }

    /**
     * Mostrar formulario para restablecer contraseña
     */
    public function showResetPasswordForm(Request $request, $token)
    {
        return view('auth.reset-password', compact('token'));
    }

    /**
     * Procesar el restablecimiento de contraseña
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $email = $request->email;
        $token = $request->token;
        $password = $request->password;

        try {
            // 1. Primero verificar si es un socio
            $member = \App\Models\Member::where('email', $email)->first();
            
            if ($member) {
                // Verificar token de socio
                $tokenRecord = DB::table('member_password_resets')
                    ->where('email', $email)
                    ->first();

                if (!$tokenRecord || !Hash::check($token, $tokenRecord->token)) {
                    return back()->withErrors(['email' => 'Token inválido o expirado.']);
                }

                // Verificar expiración (60 minutos)
                if (now()->diffInMinutes($tokenRecord->created_at) > 60) {
                    return back()->withErrors(['email' => 'El token ha expirado.']);
                }

                // Actualizar contraseña del socio
                $member->password = Hash::make($password);
                $member->save();

                // Limpiar token usado
                DB::table('member_password_resets')->where('email', $email)->delete();

                return redirect('/login')->with('status', 'Contraseña de socio actualizada exitosamente.');
            }

            // 2. Si no es socio, verificar si es admin
            $admin = Admin::where('email', $email)->first();
            
            if ($admin) {
                // Verificar token de admin
                $tokenRecord = AdminPasswordReset::where('email', $email)
                    ->where('token', $token)
                    ->first();

                if (!$tokenRecord) {
                    return back()->withErrors(['email' => 'Token inválido o expirado.']);
                }

                // Verificar expiración
                if (now()->diffInMinutes($tokenRecord->created_at) > 60) {
                    $tokenRecord->delete();
                    return back()->withErrors(['email' => 'El token ha expirado.']);
                }

                // Actualizar contraseña del admin
                $admin->password = Hash::make($password);
                $admin->save();

                // Limpiar token usado
                $tokenRecord->delete();

                return redirect('/login')->with('status', 'Contraseña actualizada exitosamente.');
            }

            // 3. Si no se encuentra el usuario
            return back()->withErrors(['email' => 'No se encontró un usuario con ese email.']);

        } catch (\Exception $e) {
            Log::error('Error en resetPassword: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Error al procesar el restablecimiento. Inténtalo más tarde.']);
        }
    }

    /**
     * Enviar email de reset para socio
     */
    private function sendMemberResetEmail($email, $member, $resetUrl, $token)
    {
        // Obtener configuración de email
        $mailConfig = MailSetting::getConfig();

        if (!$mailConfig) {
            throw new \Exception('No hay configuración de email disponible');
        }



        // Detectar automáticamente el método configurado y usar ese
        if ($mailConfig->auth_method === 'oauth_microsoft' && $mailConfig->isMicrosoftOAuthConfigured()) {
            // Usar Microsoft Graph si está configurado
            $this->sendMemberResetEmailViaMicrosoftGraph($email, $member, $resetUrl, $token, $mailConfig);
        } elseif ($mailConfig->auth_method === 'smtp' || (!empty($mailConfig->smtp_host) && !empty($mailConfig->smtp_username))) {
            // Usar SMTP si está configurado
            $this->sendMemberResetEmailViaSMTP($email, $member, $resetUrl, $token);
        } else {
            // Si no hay ningún método configurado correctamente
            throw new \Exception('No hay ningún método de email configurado correctamente. Configure SMTP o Microsoft Graph OAuth.');
        }
    }

    /**
     * Enviar email de reset para admin
     */
    private function sendAdminResetEmail($email, $admin, $resetUrl, $token)
    {
        // Obtener configuración de email
        $mailConfig = MailSetting::getConfig();

        if (!$mailConfig) {
            throw new \Exception('No hay configuración de email disponible');
        }



        // Detectar automáticamente el método configurado y usar ese
        if ($mailConfig->auth_method === 'oauth_microsoft' && $mailConfig->isMicrosoftOAuthConfigured()) {
            // Usar Microsoft Graph si está configurado
            $this->sendResetEmailViaMicrosoftGraph($email, $admin, $resetUrl, $token, $mailConfig);
        } elseif ($mailConfig->auth_method === 'smtp' || (!empty($mailConfig->smtp_host) && !empty($mailConfig->smtp_username))) {
            // Usar SMTP si está configurado
            $this->sendAdminResetEmailViaSMTP($email, $admin, $resetUrl, $token);
        } else {
            // Si no hay ningún método configurado correctamente
            throw new \Exception('No hay ningún método de email configurado correctamente. Configure SMTP o Microsoft Graph OAuth.');
        }
    }

    /**
     * Enviar email de reset para socio via SMTP
     */
    private function sendMemberResetEmailViaSMTP($email, $member, $resetUrl, $token)
    {
        try {
            // Obtener configuración de email de la base de datos
            $mailConfig = MailSetting::getConfig();
            
            if ($mailConfig && $mailConfig->smtp_host) {
                // Configurar SMTP dinámicamente
                Config::set('mail.mailers.smtp.host', $mailConfig->smtp_host);
                Config::set('mail.mailers.smtp.port', $mailConfig->smtp_port);
                Config::set('mail.mailers.smtp.username', $mailConfig->smtp_username);
                Config::set('mail.mailers.smtp.password', $mailConfig->getDecryptedSmtpPassword());
                Config::set('mail.mailers.smtp.encryption', $mailConfig->smtp_encryption);
                Config::set('mail.from.address', $mailConfig->smtp_username);
                Config::set('mail.from.name', 'Gym Control - Portal de Socios');
            }

            $subject = 'Restablecer Contraseña - Portal de Socios';
            $content = "
                <h2>Restablecer Contraseña - Portal de Socios</h2>
                <p>Hola {$member->firstname},</p>
                <p>Recibimos una solicitud para restablecer tu contraseña del portal de socios.</p>
                <p><a href='{$resetUrl}' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Restablecer Contraseña</a></p>
                <p>Si no solicitaste este cambio, ignora este email.</p>
                <p>El enlace expira en 60 minutos.</p>
            ";

            Mail::send([], [], function ($message) use ($email, $subject, $content, $member) {
                $message->to($email, $member->firstname . ' ' . $member->lastname)
                        ->subject($subject)
                        ->setBody($content, 'text/html');
            });



        } catch (\Exception $e) {
            Log::error('Error enviando email via SMTP para socio', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Enviar email de reset para admin via SMTP
     */
    private function sendAdminResetEmailViaSMTP($email, $admin, $resetUrl, $token)
    {
        try {
            // Obtener configuración de email de la base de datos
            $mailConfig = MailSetting::getConfig();
            
            if ($mailConfig && $mailConfig->smtp_host) {
                // Configurar SMTP dinámicamente
                Config::set('mail.mailers.smtp.host', $mailConfig->smtp_host);
                Config::set('mail.mailers.smtp.port', $mailConfig->smtp_port);
                Config::set('mail.mailers.smtp.username', $mailConfig->smtp_username);
                Config::set('mail.mailers.smtp.password', $mailConfig->getDecryptedSmtpPassword());
                Config::set('mail.mailers.smtp.encryption', $mailConfig->smtp_encryption);
                Config::set('mail.from.address', $mailConfig->smtp_username);
                Config::set('mail.from.name', 'Gym Control - Admin');
            }

            $subject = 'Restablecer Contraseña - Gym Control Admin';
            $content = "
                <h2>Restablecer Contraseña - Gym Control</h2>
                <p>Hola {$admin->name},</p>
                <p>Recibimos una solicitud para restablecer tu contraseña de administrador.</p>
                <p><a href='{$resetUrl}' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Restablecer Contraseña</a></p>
                <p>Si no solicitaste este cambio, ignora este email.</p>
                <p>El enlace expira en 60 minutos.</p>
            ";

            Mail::send([], [], function ($message) use ($email, $subject, $content, $admin) {
                $message->to($email, $admin->name ?? $admin->username)
                        ->subject($subject)
                        ->setBody($content, 'text/html');
            });



        } catch (\Exception $e) {
            Log::error('Error enviando email via SMTP para admin', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Enviar email usando configuración básica del .env (último recurso)
     */
    private function sendMemberResetEmailViaBasicConfig($email, $member, $resetUrl, $token)
    {
        try {

            
            $subject = 'Restablecer Contraseña - Portal de Socios';
            $content = "
                <h2>Restablecer Contraseña - Portal de Socios</h2>
                <p>Hola {$member->firstname},</p>
                <p>Recibimos una solicitud para restablecer tu contraseña del portal de socios.</p>
                <p><a href='{$resetUrl}' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Restablecer Contraseña</a></p>
                <p>Si no solicitaste este cambio, ignora este email.</p>
                <p>El enlace expira en 60 minutos.</p>
                <p><small>Email enviado desde: " . config('app.url') . "</small></p>
            ";

            // Usar configuración básica de Laravel
            Mail::send([], [], function ($message) use ($email, $subject, $content, $member) {
                $message->to($email, $member->firstname . ' ' . $member->lastname)
                        ->subject($subject)
                        ->setBody($content, 'text/html');
            });



        } catch (\Exception $e) {
            Log::error('Error enviando email via configuración básica para socio', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Enviar email de reset para socio via Microsoft Graph
     */
    private function sendMemberResetEmailViaMicrosoftGraph($email, $member, $resetUrl, $token, $mailConfig)
    {
        try {
            // Obtener token válido (renovándolo si es necesario)
            $accessToken = $mailConfig->getValidMicrosoftAccessToken();
            
            if (empty($accessToken)) {
                throw new \Exception('No se pudo obtener un token de acceso válido');
            }

            $emailContent = "
                <h2>Restablecer Contraseña - Portal de Socios</h2>
                <p>Hola {$member->firstname},</p>
                <p>Recibimos una solicitud para restablecer tu contraseña del portal de socios.</p>
                <p><a href='{$resetUrl}' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Restablecer Contraseña</a></p>
                <p>Si no solicitaste este cambio, ignora este email.</p>
                <p>El enlace expira en 60 minutos.</p>
            ";

            $emailPayload = [
                'message' => [
                    'subject' => 'Restablecer Contraseña - Portal de Socios',
                    'body' => [
                        'contentType' => 'HTML',
                        'content' => $emailContent
                    ],
                    'toRecipients' => [
                        [
                            'emailAddress' => [
                                'address' => $email,
                                'name' => $member->firstname . ' ' . $member->lastname
                            ]
                        ]
                    ]
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post('https://graph.microsoft.com/v1.0/me/sendMail', $emailPayload);

            if (!$response->successful()) {
                Log::error('Microsoft Graph: Error enviando email de reset para socio', [
                    'status' => $response->status(),
                    'error' => $response->json()
                ]);
                throw new \Exception('Error de Microsoft Graph API: ' . $response->status());
            }



        } catch (\Exception $e) {
            Log::error('Microsoft Graph: Error enviando reset email para socio', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Enviar email de reset usando Microsoft Graph API
     */
    private function sendResetEmailViaMicrosoftGraph($toEmail, $admin, $resetUrl, $token, $mailConfig)
    {
        try {


            // Obtener token válido (renovándolo si es necesario)
            $accessToken = $mailConfig->getValidMicrosoftAccessToken();
            
            if (empty($accessToken)) {
                throw new \Exception('No se pudo obtener un token de acceso válido');
            }

            // Contenido básico del email
            $emailContent = "
                <h2>Restablecer Contraseña - Gym Control</h2>
                <p>Hola {$admin->name},</p>
                <p>Recibimos una solicitud para restablecer tu contraseña.</p>
                <p><a href='{$resetUrl}' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Restablecer Contraseña</a></p>
                <p>Si no solicitaste este cambio, ignora este email.</p>
                <p>El enlace expira en 60 minutos.</p>
            ";

            // Construir el payload del email para Microsoft Graph API
            $emailPayload = [
                'message' => [
                    'subject' => 'Restablecer Contraseña - Gym Control Admin',
                    'body' => [
                        'contentType' => 'HTML',
                        'content' => $emailContent
                    ],
                    'toRecipients' => [
                        [
                            'emailAddress' => [
                                'address' => $toEmail,
                                'name' => $admin->name ?? $admin->username
                            ]
                        ]
                    ]
                ]
            ];

            // Hacer petición HTTP a Microsoft Graph API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post('https://graph.microsoft.com/v1.0/me/sendMail', $emailPayload);

            if ($response->successful()) {

                return true;
            } else {
                Log::error('Microsoft Graph: Error enviando email de reset', [
                    'status' => $response->status(),
                    'error' => $response->json()
                ]);
                throw new \Exception('Error de Microsoft Graph API: ' . $response->status());
            }

        } catch (\Exception $e) {
            Log::error('Microsoft Graph: Error general enviando reset email', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Enviar email de reset para admin via configuración básica del .env
     */
    private function sendAdminResetEmailViaBasicConfig($email, $admin, $resetUrl, $token)
    {
        try {
            // Usar configuración directa del .env como último recurso
            Config::set('mail.mailer', 'smtp');
            Config::set('mail.host', env('MAIL_HOST', 'smtp.gmail.com'));
            Config::set('mail.port', env('MAIL_PORT', 587));
            Config::set('mail.username', env('MAIL_USERNAME'));
            Config::set('mail.password', env('MAIL_PASSWORD'));
            Config::set('mail.encryption', env('MAIL_ENCRYPTION', 'tls'));
            Config::set('mail.from.address', env('MAIL_FROM_ADDRESS', env('MAIL_USERNAME')));
            Config::set('mail.from.name', env('MAIL_FROM_NAME', 'Gym Control'));

            

            $emailContent = "
                <h2>Restablecer Contraseña - Gym Control</h2>
                <p>Hola {$admin->name},</p>
                <p>Recibimos una solicitud para restablecer tu contraseña.</p>
                <p><a href='{$resetUrl}' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Restablecer Contraseña</a></p>
                <p>Si no solicitaste este cambio, ignora este email.</p>
                <p>El enlace expira en 60 minutos.</p>
            ";

            Mail::html($emailContent, function ($message) use ($email, $admin) {
                $message->to($email, $admin->name ?? $admin->username)
                        ->subject('Restablecer Contraseña - Gym Control Admin');
            });



        } catch (\Exception $e) {
            Log::error('Error enviando email de reset para admin via .env', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}