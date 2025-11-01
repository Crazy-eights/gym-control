<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('class_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_schedule_id')->constrained()->onDelete('cascade');
            $table->date('booking_date'); // Fecha específica de la clase
            $table->enum('status', ['confirmed', 'cancelled', 'attended', 'no_show'])->default('confirmed');
            $table->timestamp('booked_at')->useCurrent(); // Cuando se hizo la reserva
            $table->timestamp('cancelled_at')->nullable(); // Si fue cancelada
            $table->text('notes')->nullable(); // Notas adicionales
            $table->timestamps();
            
            // Un miembro no puede reservar la misma clase el mismo día
            $table->unique(['member_id', 'class_schedule_id', 'booking_date']);
            $table->index(['booking_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('class_bookings');
    }
}
