<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmailToMemberPasswordResetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('member_password_resets', function (Blueprint $table) {
            $table->string('email')->after('id');
            $table->string('token')->after('email');
            $table->index('email');
            $table->index('token');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('member_password_resets', function (Blueprint $table) {
            $table->dropIndex(['email']);
            $table->dropIndex(['token']);
            $table->dropColumn(['email', 'token']);
        });
    }
}
