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
    ];

    //Filters
    public static function filters($query, $param){
        switch ($query){
            case 'group_id':
            return ['query' => 'group_id = ?', 'params' => [$param]];
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
        
        //"from_selecter" -> Only essential fields for selecter
        if ($request->input("from_selecter")){
            $data = (object)[
                "id" => $data->id,
                "name_chi" => $data->name_chi,
                "name_eng" => $data->name_eng,
            ];
        }
        //"show_result" missing -> Hide result fields
        else if (!$request->input('show_result')){
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

    //Calculate Travel Time
    //{time_stop_stop, time_stop_pass, time_pass_stop, time_pass_pass}
    public function getTravelTime($line, $line_stations, $is_upbound, $is_express_track){
        //Vehicle / Line Max Speed
        $max_speed_kph = min($this->max_speed_kph, $line->max_speed_kph);
        $depart_additional_time_s = $this->depart_additional_time_s;

        //Line-Station Section Max Speed; Get Distance
        $distance_m = 0;
        foreach ($line_stations as $ls_item){
            $distance_m += ($ls_item->distance_km ?? 0) * 1000;
            if ($ls_item->max_speed_kph){
                if ($ls_item->max_speed_kph < $max_speed_kph) $max_speed_kph = $ls_item->max_speed_kph;
            }
        }
        $speed_step = 5;
        $full_speed_min_time = 5;
        $max_speed_kph = floor($max_speed_kph / $speed_step) * $speed_step;

        //time_stop_stop
        for ($speed = $max_speed_kph; $speed >= $speed_step; $speed -= $speed_step){
            $accel_dist = $this->calc_results_by_kph[$max_speed_kph]->accel_dist;
            $accel_time = $this->calc_results_by_kph[$max_speed_kph]->accel_time;
            $decel_dist = $this->calc_results_by_kph[$max_speed_kph]->decel_dist;
            $decel_time = $this->calc_results_by_kph[$max_speed_kph]->decel_time;
            $full_speed_time = ($distance_m - $accel_dist - $decel_dist) / $max_speed_kph * 3.6;
            if ($full_speed_time >= $full_speed_min_time){
                $time_stop_stop = ceil($accel_time + $decel_time + $full_speed_time + $depart_additional_time_s);
                $max_speed_stop_stop = $speed;
                break;
            }
        }

        //time_stop_pass
        for ($speed = $max_speed_kph; $speed >= $speed_step; $speed -= $speed_step){
            $accel_dist = $this->calc_results_by_kph[$max_speed_kph]->accel_dist;
            $accel_time = $this->calc_results_by_kph[$max_speed_kph]->accel_time;
            $full_speed_time = ($distance_m - $accel_dist) / $max_speed_kph * 3.6;
            if ($full_speed_time >= $full_speed_min_time){
                $time_stop_pass = ceil($accel_time + $full_speed_time + $depart_additional_time_s / 2);
                $max_speed_stop_pass = $speed;
                break;
            }
        }

        //time_pass_stop
        /*for ($speed = $max_speed_kph; $speed >= $speed_step; $speed -= $speed_step){
            $decel_dist = $this->calc_results_by_kph[$max_speed_kph]->decel_dist;
            $decel_time = $this->calc_results_by_kph[$max_speed_kph]->decel_time;
            $full_speed_time = ($distance_m - $decel_dist) / $max_speed_kph * 3.6;
            if ($full_speed_time >= $full_speed_min_time){
                $time_pass_stop = ceil($decel_time + $full_speed_time + $depart_additional_time_s / 2);
                $max_speed_pass_stop = $speed;
                break;
            }
        }*/
        $time_pass_stop = $time_stop_pass;
        $max_speed_pass_stop = $max_speed_stop_pass;

        //time_pass_pass
        $time_pass_pass = ceil($distance_m / $max_speed_kph * 3.6);
        $max_speed_pass_pass = $max_speed_kph;

        //Additional Time
        foreach ($line_stations as $i => $ls_item){
            $is_first = $i == 0;
            $is_last = $i == count($line_stations) - 1;
            $additional_time = $ls_item->additional_time ?? (object)[];
            //Basic
            $a_time_s = ($additional_time->basic ?? 0);
            $time_stop_stop += $a_time_s;
            $time_stop_pass += $a_time_s;
            $time_pass_stop += $a_time_s;
            $time_pass_pass += $a_time_s;
            //Upbound / Downbound
            $a_time_s = $is_upbound ? ($additional_time->upbound ?? 0) : ($additional_time->downbound ?? 0);
            $time_stop_stop += $a_time_s;
            $time_stop_pass += $a_time_s;
            $time_pass_stop += $a_time_s;
            $time_pass_pass += $a_time_s;
            //Local / Express
            $a_time_s = $is_express_track ? ($additional_time->express ?? 0) : ($additional_time->local ?? 0);
            $time_stop_stop += $a_time_s;
            $time_stop_pass += $a_time_s;
            $time_pass_stop += $a_time_s;
            $time_pass_pass += $a_time_s;
            //Stop / Pass
            $pass1_s = $is_upbound ? ($additional_time->pass_down ?? 0) : ($additional_time->pass_up ?? 0);
            $pass2_s = $is_upbound ? ($additional_time->pass_up ?? 0) : ($additional_time->pass_down ?? 0);
            $stop1_s = $is_upbound ? ($additional_time->stop_down ?? 0) : ($additional_time->stop_up ?? 0);
            $stop2_s = $is_upbound ? ($additional_time->stop_up ?? 0) : ($additional_time->stop_down ?? 0);
            $time_stop_stop += $stop1_s + $stop2_s;
            $time_stop_pass += $stop1_s + $pass2_s;
            $time_pass_stop += $pass1_s + $stop2_s;
            $time_pass_pass += $pass1_s + $pass2_s;
        }

        //Return Data
        return [
            'time_stop_stop' => $time_stop_stop,
            'max_speed_stop_stop' => $max_speed_stop_stop,
            'time_stop_pass' => $time_stop_pass,
            'max_speed_stop_pass' => $max_speed_stop_pass,
            'time_pass_stop' => $time_pass_stop,
            'max_speed_pass_stop' => $max_speed_pass_stop,
            'time_pass_pass' => $time_pass_pass,
            'max_speed_pass_pass' => $max_speed_pass_pass,
        ];
    }

}
