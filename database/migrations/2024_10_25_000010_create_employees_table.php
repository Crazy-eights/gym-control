<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Crea la tabla 'employees'
        Schema::create('employees', function (Blueprint $table) {
            $table->id(); // Tu 'id' int(11) NOT NULL AUTO_INCREMENT
            $table->string('employee_id', 15)->unique(); 
            $table->string('firstname', 50);    
            $table->string('lastname', 50);     
            $table->text('address')->nullable(); 
            $table->date('birthdate')->nullable(); 
            $table->string('contact_info', 100); 
            $table->string('gender', 10);       
            $table->foreignId('position_id')->constrained('position');
            $table->foreignId('schedule_id')->constrained('schedules');
            $table->string('photo', 200); 
            $table->date('created_on');  
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees');
    }
}