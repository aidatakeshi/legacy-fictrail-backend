<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InitialSetup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){

        /**
         * Operators
         */
        Schema::create('operator_types', function (Blueprint $table) {
            $table->id('id_auto');
            $table->string('id')->unique();
            $table->bigInteger('sort')->default(0);
            //
            $table->text('name_chi')->nullable();
            $table->text('name_eng')->nullable();
            //
            $table->text('remarks')->nullable();
            $table->json('other_info')->default('{}');
            //
            $table->boolean('isDeleted')->default(false);
            $table->timestamps();
        });

        Schema::create('operators', function (Blueprint $table) {
            $table->id('id_auto');
            $table->string('id')->unique();
            //
            $table->string('operator_type_id')->nullable();
            //
            $table->text('name_chi')->nullable();
            $table->text('name_chi_short')->nullable();
            $table->text('name_eng')->nullable();
            $table->text('name_eng_short')->nullable();
            $table->string('color')->nullable();
            $table->string('color_text')->nullable();
            $table->boolean('is_passenger_hr')->default(false);
            $table->text('logo_fileid')->nullable();
            //
            $table->text('remarks')->nullable();
            $table->json('other_info')->default('{}');
            //
            $table->boolean('isDeleted')->default(false);
            $table->timestamps();
        });

        /**
         * Prefectures
         */
        Schema::create('prefecture_areas', function (Blueprint $table) {
            $table->id('id_auto');
            $table->string('id')->unique();
            $table->bigInteger('sort')->default(0);
            //
            $table->text('name_chi')->nullable();
            $table->text('name_eng')->nullable();
            //
            $table->text('remarks')->nullable();
            $table->json('other_info')->default('{}');
            //
            $table->boolean('isDeleted')->default(false);
            $table->timestamps();
        });

        Schema::create('prefectures', function (Blueprint $table) {
            $table->id('id_auto');
            $table->string('id')->unique();
            $table->bigInteger('sort')->default(0);
            //
            $table->string('area_id')->nullable();
            //
            $table->text('name_chi')->nullable();
            $table->text('name_chi_suffix')->nullable();
            $table->text('name_chi_short')->nullable();
            $table->text('name_eng')->nullable();
            $table->text('name_eng_suffix')->nullable();
            $table->text('name_eng_short')->nullable();
            //
            $table->text('remarks')->nullable();
            $table->json('other_info')->default('{}');
            //
            $table->boolean('isDeleted')->default(false);
            $table->timestamps();
        });

        /**
         * Lines
         */
        Schema::create('line_types', function (Blueprint $table) {
            $table->id('id_auto');
            $table->string('id')->unique();
            $table->bigInteger('sort')->default(0);
            //
            $table->text('name_chi')->nullable();
            $table->text('name_eng')->nullable();
            $table->boolean('major')->default(false);
            $table->boolean('is_passenger_hr')->default(false);
            $table->string('color')->nullable();
            //
            $table->text('remarks')->nullable();
            $table->json('other_info')->default('{}');
            //
            $table->boolean('isDeleted')->default(false);
            $table->timestamps();
        });

        Schema::create('line_groups', function (Blueprint $table) {
            $table->id('id_auto');
            $table->string('id')->unique();
            //
            $table->text('name_chi')->nullable();
            $table->text('name_eng')->nullable();
            $table->text('name_eng_short')->nullable();
            //
            $table->text('remarks')->nullable();
            $table->json('other_info')->default('{}');
            //
            $table->boolean('isDeleted')->default(false);
            $table->timestamps();
        });

        Schema::create('lines', function (Blueprint $table) {
            $table->id('id_auto');
            $table->string('id')->unique();
            //
            $table->string('line_group_id')->nullable();
            $table->string('line_type_id')->nullable();
            $table->string('operator_id')->nullable();
            //
            $table->text('name_chi')->nullable();
            $table->text('name_eng')->nullable();
            $table->text('name_eng_short')->nullable();
            $table->string('color')->nullable();
            $table->string('color_text')->nullable();
            $table->integer('max_speed_kph')->nullable();
            $table->text('remarks')->nullable();
            //
            $table->float('length_km', 8, 1)->default(0);
            $table->integer('x_min')->nullable();
            $table->integer('x_max')->nullable();
            $table->integer('y_min')->nullable();
            $table->integer('y_max')->nullable();
            //
            $table->json('other_info')->default('{}');
            //
            $table->boolean('isDeleted')->default(false);
            $table->timestamps();
        });

        Schema::create('lines_stations', function (Blueprint $table) {
            $table->id('id_auto');
            $table->string('id')->unique();
            $table->bigInteger('sort')->default(0);
            //
            $table->string('line_id')->nullable();
            $table->string('station_id')->nullable();
            //
            $table->float('distance_km', 8, 1)->default(0);
            $table->float('mileage_km', 8, 1)->default(0);
            $table->boolean('show_arrival')->default(false);
            $table->integer('no_tracks')->default(2);
            $table->json('segments')->default('[]');
            $table->integer('max_speed_kph')->nullable();
            $table->json('additional_time')->default('{}');
            //
            $table->text('remarks')->nullable();
            $table->json('other_info')->default('{}');
            //
            $table->boolean('isDeleted')->default(false);
            $table->timestamps();
        });

        /**
         * Stations
         */

        Schema::create('stations', function (Blueprint $table) {
            $table->id('id_auto');
            $table->string('id')->unique();
            //
            $table->string('operator_id')->nullable();
            $table->string('prefecture_id')->nullable();
            //
            $table->text('name_chi')->nullable();
            $table->text('name_eng')->nullable();
            $table->float('x', 10, 3)->nullable();
            $table->float('y', 10, 3)->nullable();
            $table->integer('height_m')->nullable();
            $table->json('tracks')->default('[]');
            //
            $table->json('track_cross_points')->default('[]');
            //
            $table->boolean('major')->default(false);
            $table->boolean('is_signal_only')->default(false);
            $table->boolean('is_abandoned')->default(false);
            //
            $table->text('remarks')->nullable();
            $table->json('other_info')->default('{}');
            //
            $table->boolean('isDeleted')->default(false);
            $table->timestamps();
        });

        /**
         * Trains
         */
        Schema::create('train_types', function (Blueprint $table) {
            $table->id('id_auto');
            $table->string('id')->unique();
            $table->bigInteger('sort')->default(0);
            //
            $table->string('operator_id')->nullable();
            //
            $table->string('color')->nullable();
            $table->string('color_text')->nullable();
            $table->text('name_chi')->nullable();
            $table->text('name_chi_short')->nullable();
            $table->text('name_eng')->nullable();
            $table->text('name_eng_short')->nullable();
            $table->boolean('is_premium')->default(false);
            //
            $table->text('remarks')->nullable();
            $table->json('other_info')->default('{}');
            //
            $table->boolean('isDeleted')->default(false);
            $table->timestamps();
        });

        Schema::create('train_names', function (Blueprint $table) {
            $table->id('id_auto');
            $table->string('id')->unique();
            //
            $table->string('train_type_id')->nullable();
            $table->string('major_operator_id')->nullable();
            //
            $table->string('color')->nullable();
            $table->string('color_text')->nullable();
            $table->text('name_chi')->nullable();
            $table->text('name_eng')->nullable();
            $table->integer('max_speed_kph')->nullable();
            //
            $table->text('remarks')->nullable();
            $table->json('other_info')->default('{}');
            //
            $table->boolean('isDeleted')->default(false);
            $table->timestamps();
        });

        /**
         * Vehicle Performance
         */
        Schema::create('vehicle_performance_groups', function (Blueprint $table) {
            $table->id('id_auto');
            $table->string('id')->unique();
            $table->bigInteger('sort')->default(0);
            //
            $table->text('name_chi')->nullable();
            $table->text('name_eng')->nullable();
            //
            $table->text('remarks')->nullable();
            $table->json('other_info')->default('{}');
            //
            $table->boolean('isDeleted')->default(false);
            $table->timestamps();
        });

        Schema::create('vehicle_performance_items', function (Blueprint $table) {
            $table->id('id_auto');
            $table->string('id')->unique();
            $table->bigInteger('sort')->default(0);
            //
            $table->string('group_id')->nullable();
            //
            $table->text('name_chi')->nullable();
            $table->text('name_eng')->nullable();
            $table->text('remarks')->nullable();
            //
            $table->float('motor_ratio', 10, 5)->nullable();
            $table->float('motor_rated_kw', 10, 5)->nullable();
            $table->float('motor_overclock_ratio', 10, 5)->nullable();
            $table->float('crush_capacity', 10, 5)->nullable();
            $table->float('empty_mass_avg_t', 10, 5)->nullable();
            $table->float('max_accel_kph_s', 10, 5)->nullable();
            $table->float('resistance_loss_per_100kph', 10, 5)->nullable();
            $table->float('resistance_loss_per_100kph_q', 10, 5)->nullable();
            $table->float('const_power_accel_ratio', 10, 5)->nullable();
            $table->float('max_speed_kph', 10, 5)->nullable();
            $table->float('max_decel_kph_s', 10, 5)->nullable();
            $table->float('min_decel_kph_s', 10, 5)->nullable();
            $table->float('const_decel_max_kph', 10, 5)->nullable();
            $table->float('depart_additional_time_s', 10, 5)->nullable();
            //
            $table->boolean('has_calc_results')->default(false);
            $table->json('calc_results_other')->default('{}');
            $table->json('calc_results_by_kph')->default('[]');
            //
            $table->json('other_info')->default('{}');
            //
            $table->boolean('isDeleted')->default(false);
            $table->timestamps();
        });

        /**
         * Misc
         */
        Schema::create('map_editor_settings', function (Blueprint $table) {
            $table->id('id_auto');
            $table->string('id')->unique();
            //
            $table->boolean('lock_stations')->default(false);
            $table->boolean('hide_stations')->default(false);
            $table->json('line_groups_locked')->default('{}');
            $table->json('line_groups_hidden')->default('{}');
            $table->json('line_groups_collapsed')->default('{}');
            $table->json('line_locked')->default('{}');
            $table->json('line_hidden')->default('{}');
            //
            $table->boolean('isDeleted')->default(false);
            $table->timestamps();
        });
        Schema::create('fares_hr', function (Blueprint $table) {
            $table->id('id_auto');
            $table->string('id')->unique();
            //
            $table->string('version')->nullable();
            $table->text('remarks')->nullable();
            $table->json('data')->default('{}');
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
        Schema::dropIfExists('operator_types');
        Schema::dropIfExists('operators');
        Schema::dropIfExists('prefecture_areas');
        Schema::dropIfExists('prefectures');
        Schema::dropIfExists('line_types');
        Schema::dropIfExists('line_groups');
        Schema::dropIfExists('lines');
        Schema::dropIfExists('lines_stations');
        Schema::dropIfExists('stations');
        Schema::dropIfExists('train_types');
        Schema::dropIfExists('train_names');
        Schema::dropIfExists('vehicle_performance_groups');
        Schema::dropIfExists('vehicle_performance_items');
        Schema::dropIfExists('map_editor_settings');
        Schema::dropIfExists('fares_hr');
    }
}
