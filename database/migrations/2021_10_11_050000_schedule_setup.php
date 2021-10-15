<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ScheduleSetup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){

        Schema::create('schdraft_categories', function (Blueprint $table) {
            $table->id('id_auto');
            $table->string('id');
            $table->bigInteger('sort')->default(0);
            //
            $table->text('title')->nullable();
            //
            $table->text('remarks')->nullable();
            $table->json('other_info')->default('{}');
            $table->boolean('is_enabled')->default(true);
            //
            $table->boolean('isDeleted')->default(false);
            $table->timestamps();
        });

        Schema::create('schdraft_groups', function (Blueprint $table) {
            $table->id('id_auto');
            $table->string('id');
            $table->bigInteger('sort')->default(0);
            //
            $table->string('category_id')->nullable();
            //
            $table->text('title')->nullable();
            //
            $table->json('trip_number_rule')->default('{}');
            //
            $table->text('remarks')->nullable();
            $table->json('other_info')->default('{}');
            $table->boolean('is_enabled')->default(true);
            //
            $table->boolean('isDeleted')->default(false);
            $table->timestamps();
        });

        Schema::create('schdraft_templates', function (Blueprint $table) {
            $table->id('id_auto');
            $table->string('id');
            $table->bigInteger('sort')->default(0);
            //
            $table->string('group_id')->nullable();
            //
            $table->text('title')->nullable();
            $table->boolean('is_upbound')->default(true);
            $table->string('coupled_template_id')->nullable();
            //
            $table->bigInteger('pivot_time')->default(43200);
            $table->bigInteger('pivot_time_adj')->default(0);
            $table->string('train_type_id')->nullable();
            $table->json('train_type_mod')->default('{}');
            $table->string('train_name_id')->nullable();
            $table->json('train_name_mod')->default('{}');
            $table->string('operator_id')->nullable();
            $table->json('operator_id_mod')->default('{}');
            $table->string('vehicle_performance_id')->nullable();
            //
            $table->json('train_number_rule')->default('{}');
            $table->json('sch_template')->default('[]'); //Originally "schedule" in old backend
            $table->json('mods')->default('{}');
            $table->json('deployment')->default('{}');
            //
            $table->json('sch_output')->default('{}');
            $table->text('line_ids_involved')->default('');
            $table->text('station_ids_involved')->default('');
            //
            $table->text('remarks')->nullable();
            $table->json('other_info')->default('{}');
            $table->boolean('is_enabled')->default(true);
            //
            $table->boolean('isDeleted')->default(false);
            $table->timestamps();
        });

        Schema::create('schedule_trips', function (Blueprint $table) {
            $table->id('id_auto');
            $table->string('id');
            //
            $table->string('version_id')->nullable(); //For future use
            $table->string('schdraft_template_id')->nullable();
            //
            $table->string('operator_id')->nullable();
            $table->string('train_type_id')->nullable();
            $table->string('train_name_id')->nullable();
            //
            $table->json('operator_info')->default('{}');
            $table->json('train_type_info')->default('{}');
            $table->json('train_name_info')->default('{}');
            $table->json('train_consist_info')->default('{}');
            //
            $table->string('train_number')->nullable();
            $table->string('trip_number')->nullable();
            $table->string('run_number')->nullable();
            //
            $table->boolean('wk')->default(true);
            $table->boolean('ph')->default(true);
            //
            $table->text('remarks')->nullable();
            $table->json('other_info')->default('{}');
            //
            $table->boolean('isDeleted')->default(false);
            $table->timestamps();
        });

        Schema::create('schedule_trip_stations', function (Blueprint $table) {
            $table->id('id_auto');
            $table->string('id');
            //
            $table->string('trip_id')->nullable();
            $table->string('line_id')->nullable();
            $table->string('station_id')->nullable();
            $table->bigInteger('is_upbound')->nullable();
            $table->bigInteger('is_express_track')->nullable();
            //
            $table->json('line_info')->default('{}');
            $table->json('station_info')->default('{}');
            //
            $table->bigInteger('arrive_time')->nullable();
            $table->bigInteger('depart_time')->nullable();
            $table->bigInteger('pass_time')->nullable();
            $table->bigInteger('track')->nullable();
            //
            $table->float('mileage_km', 8, 1)->default(0);
            //
            $table->text('remarks')->nullable();
            $table->json('other_info')->default('{}');
            //
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
        Schema::dropIfExists('schdraft_categories');
        Schema::dropIfExists('schdraft_groups');
        Schema::dropIfExists('schdraft_templates');
        Schema::dropIfExists('schedule_trips');
        Schema::dropIfExists('schedule_trip_stations');
    }
}
