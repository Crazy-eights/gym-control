<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGymClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gym_classes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nombre de la clase (ej: Yoga, CrossFit, Spinning)
            $table->text('description')->nullable(); // Descripción de la clase
            $table->string('instructor_name'); // Nombre del instructor
            $table->integer('duration_minutes'); // Duración en minutos
            $table->integer('max_participants')->default(20); // Máximo de participantes
            $table->decimal('price', 8, 2)->default(0); // Precio de la clase (0 = incluido en membresía)
            $table->enum('difficulty_level', ['principiante', 'intermedio', 'avanzado'])->default('principiante');
            $table->json('equipment_needed')->nullable(); // Equipamiento necesario
            $table->string('room')->nullable(); // Sala donde se imparte
            $table->boolean('active')->default(true);
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
        Schema::dropIfExists('gym_classes');
    }
}
