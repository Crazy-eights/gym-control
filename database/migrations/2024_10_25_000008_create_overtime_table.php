<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOvertimeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('overtime')) {
        Schema::create('overtime', function (Blueprint $table) {
           $table->id(); // int(11) NOT NULL AUTO_INCREMENT
            // Esta columna NO es una llave foránea, es un varchar
            $table->string('employee_id', 15);
            $table->double('hours');
            $table->double('rate');
            $table->date('date_overtime');
        });
    }
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('overtime');
    }
}
