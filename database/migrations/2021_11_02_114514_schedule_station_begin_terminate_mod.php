<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ScheduleStationBeginTerminateMod extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){

        Schema::table('schdraft_templates', function (Blueprint $table) {
            $table->json('station_begin_mod')->default('[]');
            $table->json('station_terminate_mod')->default('[]');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
    }
}
