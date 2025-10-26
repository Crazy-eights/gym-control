<?php

use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\EmployeeController;  // Comentado hasta crear el controlador
// Importa el nuevo controlador de login
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\MailConfigController;
use App\Http\Controllers\Admin\SociosController;
use App\Http\Controllers\Admin\MembershipPlansController;
use App\Http\Controllers\Portal\SociosPortalController;

/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS Y DE AUTENTICACIÓN UNIFICADA
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome'); // Esta es la página de inicio para clientes
});

// === AUTENTICACIÓN UNIFICADA (Socios y Admins) ===
// Muestra el formulario de login unificado
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::get('/admin/login', [LoginController::class, 'showLoginForm'])->name('admin.login.form');

// Procesa el login y redirige según el tipo de usuario
Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
Route::post('/admin/login', [LoginController::class, 'login'])->name('admin.login.attempt');

// Cierra la sesión
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout.get'); // TEMPORAL

// --- RUTAS DE RECUPERACIÓN DE CONTRASEÑA ---
// Formulario para solicitar recuperación (forgot password)
Route::get('/password/reset', [LoginController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/password/email', [LoginController::class, 'sendResetLinkEmail'])->name('password.email');
Route::post('/admin/password/email', [LoginController::class, 'sendResetLinkEmail'])->name('admin.password.email');
Route::get('/password/reset/{token}', [LoginController::class, 'showResetPasswordForm'])->name('password.reset');
Route::get('/admin/password/reset/{token}', [LoginController::class, 'showResetPasswordForm'])->name('admin.password.reset');
Route::post('/password/reset', [LoginController::class, 'resetPassword'])->name('password.update');
Route::post('/admin/password/reset', [LoginController::class, 'resetPassword'])->name('admin.password.update');

/*
|--------------------------------------------------------------------------
| PORTAL DE SOCIOS
|--------------------------------------------------------------------------
*/
Route::prefix('portal')->name('portal.')->middleware(['auth'])->group(function () {
    
    // Dashboard del socio
    Route::get('/dashboard', [SociosPortalController::class, 'dashboard'])->name('dashboard');
    
    // Perfil del socio
    Route::get('/perfil', [SociosPortalController::class, 'perfil'])->name('perfil');
    Route::put('/perfil', [SociosPortalController::class, 'actualizarPerfil'])->name('perfil.actualizar');
    
    // Cambio de contraseña
    Route::put('/password', [SociosPortalController::class, 'cambiarPassword'])->name('password.cambiar');
    
    // Información de membresía y pagos
    Route::get('/membresia', [SociosPortalController::class, 'membresia'])->name('membresia');
    
    // Clases disponibles y reservas
    Route::get('/clases', [SociosPortalController::class, 'clases'])->name('clases');
    
    // Rutinas de entrenamiento
    Route::get('/rutinas', [SociosPortalController::class, 'rutinas'])->name('rutinas');
    
    // Configuración de cuenta
    Route::get('/configuracion', [SociosPortalController::class, 'configuracion'])->name('configuracion');
});

/*
|--------------------------------------------------------------------------
| RUTAS DE ADMIN
|--------------------------------------------------------------------------
*/

// Agrupamos todas las rutas de admin bajo el prefijo 'admin/'
Route::prefix('admin')->name('admin.')->group(function () {

    // --- RUTAS DE AUTENTICACIÓN ADMIN (Duplicadas para compatibilidad) ---
    
    // Muestra el formulario de login (ej. /admin/login) - Redirige al login unificado
    Route::get('/login', function() {
        return redirect()->route('login');
    })->name('login.form');
    
    // Procesa el formulario de login - Redirige al login unificado
    Route::post('/login', function() {
        return redirect()->route('login.attempt');
    })->name('login.attempt');

    // Cierra la sesión - Redirige al logout unificado
    Route::post('/logout', function() {
        return redirect()->route('logout');
    })->name('logout');

    // === RUTAS PROTEGIDAS DE ADMIN ===
    Route::middleware(['auth:admin'])->group(function () {        
        // Dashboard de Admin (ej. /admin/dashboard)
        Route::get('/dashboard', function () {
            return view('admin.dashboard'); 
        })->name('dashboard');
        
        // Gestión de Administradores
        Route::resource('admins', AdminController::class);
        
        // Configuraciones del Sistema
        Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
        Route::post('settings/reset', [SettingsController::class, 'reset'])->name('settings.reset');
        
        // Configuración de Email/Correo
        Route::get('mail-config', [MailConfigController::class, 'index'])->name('mail.config.index');
        Route::put('mail-config', [MailConfigController::class, 'update'])->name('mail.config.update');
        Route::post('mail-config/test', [MailConfigController::class, 'testEmail'])->name('mail.config.test');
        Route::post('mail-config/preset/{provider}', [MailConfigController::class, 'applyPreset'])->name('mail.config.preset');
        
        // OAuth Microsoft Routes
        Route::get('mail-config/oauth/microsoft', [MailConfigController::class, 'redirectToMicrosoft'])->name('mail.oauth.microsoft');
        Route::post('mail-config/oauth/microsoft/connect', [MailConfigController::class, 'connectToMicrosoft'])->name('mail.oauth.microsoft.connect');
        Route::get('mail-config/oauth/microsoft/callback', [MailConfigController::class, 'handleMicrosoftCallback'])->name('mail.oauth.microsoft.callback');
        Route::post('mail-config/oauth/microsoft/disconnect', [MailConfigController::class, 'disconnectMicrosoft'])->name('mail.oauth.microsoft.disconnect');
        
        // === MÓDULO DE SOCIOS ===
        Route::resource('socios', SociosController::class);
        Route::post('socios/{socio}/renovar-membresia', [SociosController::class, 'renovarMembresia'])->name('socios.renovar-membresia');
        
        // === MÓDULO DE PLANES DE MEMBRESÍA ===
        Route::resource('membership-plans', MembershipPlansController::class);
        Route::post('membership-plans/{membershipPlan}/duplicate', [MembershipPlansController::class, 'duplicate'])->name('membership-plans.duplicate');
        
        // Route::resource('employees', EmployeeController::class);  // Comentado hasta crear el controlador
    });
});

// Include test routes in development
if (app()->environment('local') || app()->environment('testing')) {
    include __DIR__ . '/test-email.php';
    include __DIR__ . '/debug-microsoft.php';
    include __DIR__ . '/debug-microsoft-creds.php';
    include __DIR__ . '/test-reset-process.php';
    include __DIR__ . '/demo-reset.php';
    include __DIR__ . '/fix-microsoft.php';
    include __DIR__ . '/sync-microsoft.php';
    include __DIR__ . '/microsoft-health.php';
}