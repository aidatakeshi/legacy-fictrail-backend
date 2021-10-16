<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use App\Models\VehiclePerformanceGroup;

class VehiclePerformanceItem extends Model{

    protected $table = 'vehicle_performance_items';
    protected $primaryKey = 'id_auto';
    protected $hidden = ['created_at', 'updated_at', 'id_auto', 'isDeleted'];

    //Fields Modifiable by PATCH / POST
    protected $fillable = [
        'sort', 'group_id',
        'name_chi', 'name_eng', 'remarks',
        'motor_ratio', 'motor_rated_kW', 'motor_overclock_ratio',
        'crush_capacity', 'empty_mass_avg_t', 'max_accel_kph_s',
        'resistance_loss_per_100kph', 'resistance_loss_per_100kph_q',
        'const_power_accel_ratio', 'max_speed_kph', 'max_decel_kph_s', 'min_decel_kph_s',
        'const_decel_max_kph', 'depart_additional_time_s',
        'has_calc_results', 'calc_results_other', 'calc_results_by_kph',
        'other_info',
    ];

    //Data validations
    public static $validations_update = [

    ];
    public static $validations_new = [

    ];

    //Filters
    public static function filters($query, $param){
    }
    
    //Sortings
    public static $sort_default = 'sort';
    public static $sortable = [];

    //Resource Relationships
    public function group(){
        return $this->belongsTo(VehiclePerformanceGroup::class, 'group_id', 'id');
    }

    //Display data returned for GET
    public function displayData($request){
        return [

        ];
    }

    //Additional processing of data
    public function whenGet($request){

    }
    public function whenSet($request){
        
    }
    public function whenCreated($request){

    }
    public function whenRemoved($request){

    }

    /**
     * Custom Methods
     */


}
