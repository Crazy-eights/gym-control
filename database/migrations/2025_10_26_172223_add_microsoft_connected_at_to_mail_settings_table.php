<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMicrosoftConnectedAtToMailSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mail_settings', function (Blueprint $table) {
            $table->timestamp('microsoft_connected_at')->nullable()->after('microsoft_token_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mail_settings', function (Blueprint $table) {
            $table->dropColumn('microsoft_connected_at');
        });
    }
}
