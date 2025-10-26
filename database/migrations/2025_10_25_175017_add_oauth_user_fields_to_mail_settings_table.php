<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOauthUserFieldsToMailSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mail_settings', function (Blueprint $table) {
            $table->string('microsoft_user_email')->nullable()->after('microsoft_token_expires_at');
            $table->string('microsoft_user_name')->nullable()->after('microsoft_user_email');
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
            $table->dropColumn(['microsoft_user_email', 'microsoft_user_name']);
        });
    }
}
