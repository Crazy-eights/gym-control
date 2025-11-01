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

// Procesa el login y redirige según el tipo de usuario (CON RATE LIMITING)
Route::post('/login', [LoginController::class, 'login'])
    ->name('login.attempt')
    ->middleware('throttle:5,1'); // 5 intentos por minuto

Route::post('/admin/login', [LoginController::class, 'login'])
    ->name('admin.login.attempt')
    ->middleware('throttle:5,1'); // 5 intentos por minuto

// Cierra la sesión
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout.get'); // TEMPORAL

// --- RUTAS DE RECUPERACIÓN DE CONTRASEÑA (CON RATE LIMITING) ---
// Formulario para solicitar recuperación (forgot password)
Route::get('/password/reset', [LoginController::class, 'showForgotPasswordForm'])->name('password.request');

Route::post('/password/email', [LoginController::class, 'sendResetLinkEmail'])
    ->name('password.email')
    ->middleware('throttle:3,1'); // 3 intentos por minuto

Route::post('/admin/password/email', [LoginController::class, 'sendResetLinkEmail'])
    ->name('admin.password.email')
    ->middleware('throttle:3,1'); // 3 intentos por minuto

Route::get('/password/reset/{token}', [LoginController::class, 'showResetPasswordForm'])->name('password.reset');
Route::get('/admin/password/reset/{token}', [LoginController::class, 'showResetPasswordForm'])->name('admin.password.reset');

Route::post('/password/reset', [LoginController::class, 'resetPassword'])
    ->name('password.update')
    ->middleware('throttle:3,1'); // 3 intentos por minuto

Route::post('/admin/password/reset', [LoginController::class, 'resetPassword'])
    ->name('admin.password.update')
    ->middleware('throttle:3,1'); // 3 intentos por minuto

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
    Route::get('/clases', [\App\Http\Controllers\Portal\ClassBookingController::class, 'availableClasses'])->name('clases');
    Route::get('/clases/{gymClass}', [\App\Http\Controllers\Portal\ClassBookingController::class, 'classDetails'])->name('classes.details');
    Route::get('/mis-reservas', [\App\Http\Controllers\Portal\ClassBookingController::class, 'myBookings'])->name('classes.bookings');
    Route::post('/reservar-clase', [\App\Http\Controllers\Portal\ClassBookingController::class, 'book'])->name('classes.book');
    Route::patch('/cancelar-reserva/{booking}', [\App\Http\Controllers\Portal\ClassBookingController::class, 'cancel'])->name('classes.cancel');
    Route::get('/reserva/{booking}', [\App\Http\Controllers\Portal\ClassBookingController::class, 'show'])->name('classes.booking.show');
    
    // API endpoints para disponibilidad de clases
    Route::get('/api/clases/{class_id}/disponibilidad', [\App\Http\Controllers\Portal\ClassBookingController::class, 'getAvailability'])->name('classes.availability');
    
    // Rutas del módulo de clases
    Route::get('/classes', [\App\Http\Controllers\Portal\ClassController::class, 'index'])->name('classes.index');
    Route::get('/classes/{class}', [\App\Http\Controllers\Portal\ClassController::class, 'show'])->name('classes.show');
    
    // Rutas de reservas de clases
    Route::get('/class-bookings', [\App\Http\Controllers\Portal\ClassBookingController::class, 'myBookings'])->name('class-bookings.my-bookings');
    Route::post('/class-bookings', [\App\Http\Controllers\Portal\ClassBookingController::class, 'store'])->name('class-bookings.store');
    Route::patch('/class-bookings/{booking}/cancel', [\App\Http\Controllers\Portal\ClassBookingController::class, 'cancel'])->name('class-bookings.cancel');
    
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
    Route::middleware(['auth.admin'])->group(function () {        
        // Dashboard de Admin (ej. /admin/dashboard) - NUEVA INTERFAZ MODERNA
        Route::get('/dashboard', function () {
            return view('admin.dashboard-modern'); 
        })->name('dashboard');
        
        // Gestión de Administradores
        Route::resource('admins', AdminController::class);
        
        // Configuraciones del Sistema
        Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
        Route::post('settings/reset', [SettingsController::class, 'reset'])->name('settings.reset');
        
        // Configuración de Email/Correo (CON RATE LIMITING)
        Route::get('mail-config', [MailConfigController::class, 'index'])->name('mail.config.index');
        Route::put('mail-config', [MailConfigController::class, 'update'])->name('mail.config.update');
        
        Route::post('mail-config/test', [MailConfigController::class, 'testEmail'])
            ->name('mail.config.test')
            ->middleware('throttle:10,1'); // 10 tests por minuto
            
        Route::post('mail-config/preset/{provider}', [MailConfigController::class, 'applyPreset'])->name('mail.config.preset');
        
        // Configuración Visual
        Route::get('visual-config', [\App\Http\Controllers\Admin\VisualConfigController::class, 'index'])->name('visual.config.index');
        Route::put('visual-config', [\App\Http\Controllers\Admin\VisualConfigController::class, 'update'])->name('visual.config.update');
        Route::delete('visual-config/reset', [\App\Http\Controllers\Admin\VisualConfigController::class, 'reset'])->name('visual.config.reset');
        Route::post('visual-config/seed', [\App\Http\Controllers\Admin\VisualConfigController::class, 'seedDefaults'])->name('visual.config.seed');
        Route::post('visual-config/preview', [\App\Http\Controllers\Admin\VisualConfigController::class, 'preview'])->name('visual.config.preview');
        
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
        Route::get('membership-plans/{plan}/duration', [MembershipPlansController::class, 'getDuration'])->name('membership-plans.duration');
        
        // === MÓDULO DE CLASES ===
        Route::resource('classes', \App\Http\Controllers\Admin\GymClassController::class);
        
        // === MÓDULO DE HORARIOS ===
        Route::resource('schedules', \App\Http\Controllers\Admin\ClassScheduleController::class);
        
        // Route::resource('employees', EmployeeController::class);  // Comentado hasta crear el controlador
    });
});

// Include test routes ONLY in local development (SECURITY: Never in production)
if (app()->environment('local', 'testing') && config('app.debug')) {
    // Additional security check: only load if debug is enabled
    $debugRoutes = [
        '/test-email.php',
        '/debug-microsoft.php', 
        '/debug-microsoft-creds.php',
        '/test-reset-process.php',
        '/demo-reset.php',
        '/fix-microsoft.php',
        '/sync-microsoft.php',
        '/microsoft-health.php'
    ];
    
    foreach ($debugRoutes as $routeFile) {
        if (file_exists(__DIR__ . $routeFile)) {
            include __DIR__ . $routeFile;
        }
    }
    
    // Ruta temporal para probar la nueva interfaz moderna (SOLO EN DESARROLLO)
    Route::get('/admin/preview-modern', function () {
        return view('admin.dashboard-modern');
    })->name('admin.preview.modern');
}