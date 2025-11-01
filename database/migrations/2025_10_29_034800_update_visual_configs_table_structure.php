<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateVisualConfigsTableStructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Primero eliminar la tabla existente y recrearla con la nueva estructura
        Schema::dropIfExists('visual_configs');
        
        Schema::create('visual_configs', function (Blueprint $table) {
            $table->id();
            
            // Logos
            $table->string('logo')->nullable();
            $table->string('secondary_logo')->nullable();
            
            // Colores
            $table->string('primary_color')->default('#007bff');
            $table->string('secondary_color')->default('#6c757d');
            $table->string('accent_color')->default('#28a745');
            $table->string('navbar_color')->default('#ffffff');
            $table->string('sidebar_color')->default('#5a5c69');
            
            // Tipografía
            $table->string('font_family')->default('Nunito, sans-serif');
            
            // Configuraciones adicionales
            $table->string('favicon')->nullable();
            $table->text('meta_description')->nullable();
            $table->longText('custom_css')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Restaurar la estructura original
        Schema::dropIfExists('visual_configs');
        
        Schema::create('visual_configs', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // Llave de configuración (ej: primary_color, logo_url)
            $table->text('value')->nullable(); // Valor de la configuración
            $table->string('type')->default('string'); // Tipo: string, color, file, json, boolean
            $table->string('category')->default('general'); // Categoría: branding, colors, typography, layout
            $table->string('label'); // Etiqueta legible para humanos
            $table->text('description')->nullable(); // Descripción de la configuración
            $table->json('options')->nullable(); // Opciones adicionales (para selects, validaciones, etc.)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['category', 'is_active']);
        });
    }
}
