<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('class_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gym_class_id')->constrained()->onDelete('cascade');
            $table->enum('day_of_week', ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo']);
            $table->time('start_time'); // Hora de inicio
            $table->time('end_time'); // Hora de fin
            $table->date('start_date')->nullable(); // Fecha de inicio (para clases temporales)
            $table->date('end_date')->nullable(); // Fecha de fin (para clases temporales)
            $table->boolean('is_recurring')->default(true); // Si es recurrente semanalmente
            $table->boolean('active')->default(true);
            $table->timestamps();
            
            $table->index(['day_of_week', 'start_time']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('class_schedules');
    }
}
