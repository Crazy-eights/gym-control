<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisualConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('visual_configs');
    }
}
