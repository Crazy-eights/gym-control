<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInstructorIdToGymClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gym_classes', function (Blueprint $table) {
            // Agregar campo instructor_id y hacer instructor_name nullable
            $table->unsignedBigInteger('instructor_id')->nullable()->after('description');
            $table->string('instructor_name')->nullable()->change();
            
            // Agregar clave foránea
            $table->foreign('instructor_id')
                  ->references('id')
                  ->on('employees')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gym_classes', function (Blueprint $table) {
            $table->dropForeign(['instructor_id']);
            $table->dropColumn('instructor_id');
            $table->string('instructor_name')->nullable(false)->change();
        });
    }
}
