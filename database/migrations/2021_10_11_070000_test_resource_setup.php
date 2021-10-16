<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TestResourceSetup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){

        Schema::create('test_items', function (Blueprint $table) {
            $table->id('id_auto');
            $table->string('id')->unique();
            $table->bigInteger('sort')->default(0);
            //
            $table->text('my_text')->nullable();
            $table->bigInteger('my_int')->nullable();
            $table->float('my_float', 10, 3)->nullable();
            $table->json('my_json', 10, 3)->nullable();
            $table->boolean('my_bool')->default(false);
            //
            $table->bigInteger('readCount')->default(0);
            $table->bigInteger('updateCount')->default(0);
            $table->boolean('isDeleted')->default(false);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('test_items');
    }
}
