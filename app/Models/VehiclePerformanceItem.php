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
    
    //JSON fields
    protected $casts = [
        'calc_results_other' => 'object',
        'calc_results_by_kph' => 'object',
        'other_info' => 'object',
        'motor_ratio' => 'float',
        'motor_rated_kw' => 'object',
        'motor_overclock_ratio' => 'object',
        'crush_capacity' => 'object',
        'empty_mass_avg_t' => 'object',
        'max_accel_kph_s' => 'object',
        'resistance_loss_per_100kph' => 'object',
        'resistance_loss_per_100kph_q' => 'object',
        'const_power_accel_ratio' => 'object',
        'max_speed_kph' => 'object',
        'max_decel_kph_s' => 'object',
        'min_decel_kph_s' => 'object',
        'const_decel_max_kph' => 'object',
        'depart_additional_time_s' => 'object',
    ];

    //Data validations
    public static $validations_update = [
        'sort' => 'integer',
        'group_id' => 'exists:vehicle_performance_groups,id',
        'motor_ratio' => 'numeric',
        'motor_rated_kW' => 'numeric',
        'motor_overclock_ratio' => 'numeric',
        'crush_capacity' => 'numeric',
        'empty_mass_avg_t' => 'numeric',
        'max_accel_kph_s' => 'numeric',
        'resistance_loss_per_100kph' => 'numeric',
        'resistance_loss_per_100kph_q' => 'numeric',
        'const_power_accel_ratio' => 'numeric',
        'max_speed_kph' => 'numeric',
        'max_decel_kph_s' => 'numeric',
        'min_decel_kph_s' => 'numeric',
        'const_decel_max_kph' => 'numeric',
        'depart_additional_time_s' => 'numeric',
        'has_calc_results' => 'boolean',
        'calc_results_other' => 'json',
        'calc_results_by_kph' => 'json',
        'other_info' => 'json',
    ];
    public static $validations_new = [
        'group_id' => 'required|exists:vehicle_performance_groups,id',
        'name_chi' => 'required',
        'name_eng' => 'required',
    ];

    //Filters
    public static function filters($query, $param){
        switch ($query){
            case 'name_chi':
            return ['query' => 'LOWER(name_chi) LIKE LOWER(?)', 'params' => ["%$param%"]];
            case 'name_eng':
            return ['query' => 'LOWER(name_eng) LIKE LOWER(?)', 'params' => ["%$param%"]];
        }
    }
    
    //Sortings
    public static $sort_default = 'id';
    public static $sortable = [
        'id', 'sort', 'name_chi', 'name_eng', 'remarks', 'max_speed_kph', 'max_accel_kph_s',
    ];

    //Resource Relationships
    public function group(){
        return $this->belongsTo(VehiclePerformanceGroup::class, 'group_id', 'id')->where('isDeleted', false);
    }

    //Display data returned for GET
    public function displayData($request){
        $data = clone $this;
        //"hide_result" -> Hide result fields
        if ($request->input('hide_result')){
            unset($data->calc_results_by_kph);
            unset($data->calc_results_other);
        }
        //"more" -> Get also operator & train type as well
        if ($request->input('more')){
            $data->group = $this->group;
        }
        return $data;
    }

    /**
     * Custom Methods
     */


}
