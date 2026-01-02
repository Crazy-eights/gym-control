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
        // Crear la tabla si no existe
        if (!Schema::hasTable('member_password_resets')) {
            Schema::create('member_password_resets', function (Blueprint $table) {
                $table->id();
                $table->string('email');
                $table->string('token');
                $table->timestamp('created_at')->nullable();
                
                $table->index('email');
                $table->index('token');
            });
        } else {
            // Si existe, agregar las columnas si no están
            Schema::table('member_password_resets', function (Blueprint $table) {
                if (!Schema::hasColumn('member_password_resets', 'email')) {
                    $table->string('email')->after('id');
                }
                if (!Schema::hasColumn('member_password_resets', 'token')) {
                    $table->string('token')->after('email');
                }
                $table->index('email');
                $table->index('token');
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
        Schema::dropIfExists('member_password_resets');
    }
}
