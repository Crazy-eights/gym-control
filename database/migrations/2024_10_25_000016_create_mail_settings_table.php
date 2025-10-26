<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mail_settings', function (Blueprint $table) {
            $table->id();
            
            // Configuración SMTP básica
            $table->string('mail_driver', 20)->default('smtp');
            $table->string('mail_host', 100)->nullable();
            $table->integer('mail_port')->default(587);
            $table->string('mail_username', 100)->nullable();
            $table->text('mail_password')->nullable(); // Encriptado
            $table->string('mail_encryption', 10)->nullable(); // tls, ssl, starttls
            
            // Identidad del remitente
            $table->string('mail_from_address', 100)->nullable();
            $table->string('mail_from_name', 100)->default('Gym Control System');
            $table->string('mail_reply_to', 100)->nullable();
            
            // Proveedor y configuraciones preestablecidas
            $table->string('mail_provider', 20)->default('custom'); // gmail, outlook, yahoo, sendgrid, custom
            
            // OAuth Microsoft
            $table->boolean('oauth_microsoft_enabled')->default(false);
            $table->text('microsoft_client_id')->nullable();
            $table->text('microsoft_client_secret')->nullable(); // Encriptado
            $table->string('microsoft_tenant_id', 100)->nullable();
            $table->text('microsoft_redirect_uri')->nullable();
            $table->text('microsoft_access_token')->nullable(); // Encriptado
            $table->text('microsoft_refresh_token')->nullable(); // Encriptado
            $table->timestamp('microsoft_token_expires_at')->nullable();
            
            // Configuraciones adicionales
            $table->boolean('email_notifications_enabled')->default(true);
            $table->boolean('email_queue_enabled')->default(false);
            $table->boolean('email_log_enabled')->default(true);
            
            // Testing y logs
            $table->string('test_email_address', 100)->nullable();
            $table->timestamp('last_email_test')->nullable();
            $table->enum('email_test_status', ['success', 'failed', 'pending'])->nullable();
            $table->text('last_email_error')->nullable();
            
            // Configuraciones avanzadas
            $table->integer('email_timeout')->default(30); // segundos
            $table->integer('email_retry_attempts')->default(3);
            $table->boolean('verify_ssl')->default(true);
            
            // Plantillas de email
            $table->text('email_header_template')->nullable();
            $table->text('email_footer_template')->nullable();
            $table->string('email_logo_url', 255)->nullable();
            
            // Estadísticas
            $table->integer('emails_sent_today')->default(0);
            $table->integer('emails_sent_month')->default(0);
            $table->integer('emails_failed_today')->default(0);
            $table->date('stats_last_reset')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index('mail_provider');
            $table->index('email_test_status');
            $table->index('last_email_test');
        });

        // Insertar configuración por defecto
        DB::table('mail_settings')->insert([
            'mail_driver' => 'smtp',
            'mail_port' => 587,
            'mail_encryption' => 'tls',
            'mail_from_name' => 'Gym Control System',
            'mail_provider' => 'custom',
            'oauth_microsoft_enabled' => false,
            'email_notifications_enabled' => true,
            'email_queue_enabled' => false,
            'email_log_enabled' => true,
            'email_timeout' => 30,
            'email_retry_attempts' => 3,
            'verify_ssl' => true,
            'emails_sent_today' => 0,
            'emails_sent_month' => 0,
            'emails_failed_today' => 0,
            'stats_last_reset' => now()->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_settings');
    }
};