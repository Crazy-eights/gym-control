<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('members')) {
        Schema::create('members', function (Blueprint $table) {
            $table->id(); // int(11) NOT NULL AUTO_INCREMENT
            $table->string('member_id', 15);
            $table->string('firstname', 50);
            $table->string('lastname', 50);
            $table->text('address');
            $table->date('birthdate');
            $table->string('contact_info', 100);
            $table->string('gender', 10);
            
            // Foreign key that can be NULL and is set to NULL if the plan is deleted
            $table->foreignId('plan_id')->nullable()->constrained('membership_plans')->onDelete('set null');
            
            $table->date('subscription_start_date')->nullable();
            $table->date('subscription_end_date')->nullable();
            $table->string('photo', 200)->nullable();
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
        Schema::dropIfExists('members');
    }
}
