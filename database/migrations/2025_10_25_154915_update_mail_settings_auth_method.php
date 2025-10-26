<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateMailSettingsAuthMethod extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mail_settings', function (Blueprint $table) {
            // Agregar campo para método de autenticación
            $table->enum('auth_method', ['smtp', 'oauth_microsoft'])->default('smtp')->after('mail_encryption');
            
            // Eliminar campo oauth_microsoft_enabled ya que ahora usamos auth_method
            $table->dropColumn('oauth_microsoft_enabled');
        });

        // Actualizar configuración existente
        DB::table('mail_settings')->update([
            'auth_method' => 'smtp',
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mail_settings', function (Blueprint $table) {
            // Restaurar campo oauth_microsoft_enabled
            $table->boolean('oauth_microsoft_enabled')->default(false)->after('mail_provider');
            
            // Eliminar campo auth_method
            $table->dropColumn('auth_method');
        });
    }
}
