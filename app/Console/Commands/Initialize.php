<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\Setting;
use App\Models\MembershipPlan;
use App\Models\MailSetting;

class Initialize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gym:initialize 
                           {--fresh : Ejecutar migraciones frescas (elimina datos existentes)}
                           {--admin-email= : Email del administrador (por defecto: admin@gymcontrol.com)}
                           {--admin-password= : Contraseña del administrador (por defecto: admin123)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inicializa el sistema Gym Control con la estructura de base de datos y configuraciones básicas';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🏋️  Iniciando configuración del sistema Gym Control...');
        $this->newLine();

        // Verificar si se debe hacer fresh migration
        $fresh = $this->option('fresh');
        $adminEmail = $this->option('admin-email') ?? 'admin@gymcontrol.com';
        $adminPassword = $this->option('admin-password') ?? 'admin123';

        if ($fresh) {
            if ($this->confirm('⚠️  ¿Estás seguro de que quieres eliminar todos los datos existentes?')) {
                $this->call('migrate:fresh');
            } else {
                $this->info('Operación cancelada.');
                return 1;
            }
        } else {
            $this->call('migrate');
        }

        $this->info('✅ Migraciones ejecutadas correctamente');
        $this->newLine();

        // Crear configuraciones básicas del sistema
        $this->createBasicSettings();
        
        // Crear planes de membresía por defecto
        $this->createDefaultMembershipPlans();
        
        // Crear configuración de email básica
        $this->createBasicMailSettings();
        
        // Crear usuario administrador
        $this->createAdminUser($adminEmail, $adminPassword);

        $this->newLine();
        $this->info('🎉 ¡Sistema Gym Control inicializado correctamente!');
        $this->newLine();
        $this->info('📋 Credenciales de acceso:');
        $this->info("   Email: {$adminEmail}");
        $this->info("   Contraseña: {$adminPassword}");
        $this->newLine();
        $this->info('🌐 Accede al sistema en: ' . config('app.url') . '/admin/login');

        return 0;
    }

    /**
     * Crear configuraciones básicas del sistema
     */
    private function createBasicSettings()
    {
        $this->info('📝 Creando configuraciones básicas del sistema...');

        $settings = [
            [
                'key' => 'gym_name',
                'value' => 'Gym Control',
                'description' => 'Nombre del gimnasio'
            ],
            [
                'key' => 'gym_address',
                'value' => 'Av. Principal #123, Ciudad',
                'description' => 'Dirección del gimnasio'
            ],
            [
                'key' => 'gym_phone',
                'value' => '(555) 123-4567',
                'description' => 'Teléfono del gimnasio'
            ],
            [
                'key' => 'gym_email',
                'value' => 'contacto@gymcontrol.com',
                'description' => 'Email de contacto del gimnasio'
            ],
            [
                'key' => 'business_hours',
                'value' => 'Lun-Vie: 5:00-23:00, Sáb-Dom: 6:00-22:00',
                'description' => 'Horarios de funcionamiento'
            ],
            [
                'key' => 'currency',
                'value' => 'MXN',
                'description' => 'Moneda del sistema'
            ],
            [
                'key' => 'timezone',
                'value' => 'America/Mexico_City',
                'description' => 'Zona horaria'
            ]
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']], 
                $setting
            );
        }

        $this->info('   ✅ Configuraciones básicas creadas');
    }

    /**
     * Crear planes de membresía por defecto
     */
    private function createDefaultMembershipPlans()
    {
        $this->info('💳 Creando planes de membresía por defecto...');

        $plans = [
            [
                'plan_name' => 'Plan Básico',
                'description' => 'Acceso al área de pesas y cardio',
                'price' => 500.00,
                'duration_days' => 30,
                'status' => 'active'
            ],
            [
                'plan_name' => 'Plan Premium',
                'description' => 'Acceso completo + clases grupales',
                'price' => 800.00,
                'duration_days' => 30,
                'status' => 'active'
            ],
            [
                'plan_name' => 'Plan VIP',
                'description' => 'Acceso completo + entrenador personal',
                'price' => 1200.00,
                'duration_days' => 30,
                'status' => 'active'
            ],
            [
                'plan_name' => 'Plan Anual',
                'description' => 'Plan Premium con descuento anual',
                'price' => 8000.00,
                'duration_days' => 365,
                'status' => 'active'
            ]
        ];

        foreach ($plans as $plan) {
            MembershipPlan::updateOrCreate(
                ['plan_name' => $plan['plan_name']], 
                $plan
            );
        }

        $this->info('   ✅ Planes de membresía creados');
    }

    /**
     * Crear configuración básica de email
     */
    private function createBasicMailSettings()
    {
        $this->info('📧 Configurando sistema de email...');

        MailSetting::updateOrCreate(
            ['id' => 1],
            [
                'auth_method' => 'smtp',
                'smtp_host' => env('MAIL_HOST', 'smtp.gmail.com'),
                'smtp_port' => env('MAIL_PORT', 587),
                'smtp_username' => env('MAIL_USERNAME', ''),
                'smtp_password' => env('MAIL_PASSWORD', ''),
                'smtp_encryption' => env('MAIL_ENCRYPTION', 'tls'),
                'mail_from_address' => env('MAIL_FROM_ADDRESS', 'noreply@gymcontrol.com'),
                'mail_from_name' => env('MAIL_FROM_NAME', 'Gym Control'),
                'provider' => 'other'
            ]
        );

        $this->info('   ✅ Configuración de email establecida');
    }

    /**
     * Crear usuario administrador
     */
    private function createAdminUser($email, $password)
    {
        $this->info('👤 Creando usuario administrador...');

        $admin = Admin::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Administrador',
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'super_admin',
                'status' => 'active'
            ]
        );

        $this->info("   ✅ Usuario administrador creado: {$email}");
    }
}
