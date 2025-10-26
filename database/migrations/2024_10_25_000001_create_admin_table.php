<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('create_admin_table')) {   
        Schema::create('admin', function (Blueprint $table) {
            $table->id(); // int(11) NOT NULL AUTO_INCREMENT
            $table->string('username', 30);
            $table->string('password', 60);
            $table->string('firstname', 50);
            $table->string('lastname', 50);
            $table->string('photo', 200);
            $table->date('created_on');
            $table->string('email')->nullable()->unique();
            $table->string('reset_token', 64)->nullable();
            $table->dateTime('reset_expiry')->nullable();
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
        Schema::dropIfExists('admin');

    }

}

