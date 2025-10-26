<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembershipPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('membership_plans')) {
        Schema::create('membership_plans', function (Blueprint $table) {
            $table->id(); // int(11) NOT NULL AUTO_INCREMENT
            $table->string('plan_name', 100);
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->integer('duration_days');
            $table->timestamps();
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
        Schema::dropIfExists('membership_plans');
    }
}
