<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAttendanceDateToMemberAttendanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('member_attendance', function (Blueprint $table) {
            $table->date('attendance_date')->after('member_id')->nullable();
            $table->timestamps(); // Agregar created_at y updated_at si no existen
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('member_attendance', function (Blueprint $table) {
            $table->dropColumn(['attendance_date', 'created_at', 'updated_at']);
        });
    }
}
