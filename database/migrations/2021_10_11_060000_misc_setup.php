<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MiscSetup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){

        Schema::create('users', function (Blueprint $table) {
            $table->id('id_auto');
            $table->string('user');
            $table->string('password');
            //
            $table->timestamps();
        });

        Schema::create('user_login_sessions', function (Blueprint $table) {
            $table->id('id_auto');
            $table->string('user');
            $table->string('bearer_token');
            $table->bigInteger('login_time');
            $table->bigInteger('last_activity_time');
            //
            $table->timestamps();
        });

        Schema::create('files', function (Blueprint $table) {
            $table->id('id_auto');
            $table->string('uuid');
            $table->string('directory');
            $table->string('extension');
            $table->string('mimetype');
            $table->bigInteger('size');
            $table->bigInteger('upload_time');
            //
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('users');
        Schema::dropIfExists('user_login_sessions');
        Schema::dropIfExists('files');
    }
}
