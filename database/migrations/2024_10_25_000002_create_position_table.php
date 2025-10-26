<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePositionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    if (!Schema::hasTable('position')) {
        Schema::create('position', function (Blueprint $table) {
            $table->id(); // Tu 'id'
            $table->string('description', 150); // Tu 'description'
            $table->double('rate'); // Tu 'rate'
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
        Schema::dropIfExists('position');
    }
}
