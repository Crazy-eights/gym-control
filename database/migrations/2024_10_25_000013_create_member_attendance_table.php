<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema; // <-- ¡Asegúrate de que esto esté!

class CreateMemberAttendanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('member_attendance')) {

            Schema::create('member_attendance', function (Blueprint $table) {
                $table->id(); 

                $table->foreignId('member_id')->constrained('members')->onDelete('cascade');

                $table->dateTime('checkin_time');
            });

        } // <-- ¡NO OLVIDES CERRAR EL IF!
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('member_attendance');
    }
}