<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashadvanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('cashadvance')) {
        Schema::create('cashadvance', function (Blueprint $table) {
            $table->id(); // int(11) NOT NULL AUTO_INCREMENT
            $table->date('date_advance');
            // Esta columna NO es una llave foránea, es un varchar
            $table->string('employee_id', 15); 
            $table->double('amount');      
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
        Schema::dropIfExists('cashadvance');
    }
}
