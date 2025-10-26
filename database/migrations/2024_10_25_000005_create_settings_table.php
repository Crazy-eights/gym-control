<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('settings')) {
        Schema::create('settings', function (Blueprint $table) {
           $table->id(); // int(11) NOT NULL AUTO_INCREMENT
            $table->string('setting_key', 50)->unique();
            $table->text('setting_value')->nullable();
            $table->timestamps(); // created_at and updated_at
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
        Schema::dropIfExists('settings');
    }
}
