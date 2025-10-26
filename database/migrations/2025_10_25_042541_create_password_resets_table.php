<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePasswordResetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('password_resets')) {
        Schema::create('password_resets', function (Blueprint $table) {
           $table->id(); // Tu 'id' int(11)
            
            $table->foreignId('user_id')->constrained('admin')->onDelete('cascade');
            $table->string('token_hash', 128);
            $table->dateTime('expires_at');
            
            // Tu 'created_at' que por defecto usa la fecha actual
            $table->timestamp('created_at')->useCurrent();
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
        Schema::dropIfExists('password_resets');
    }
    
}
