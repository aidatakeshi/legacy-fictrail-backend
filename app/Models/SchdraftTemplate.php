<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use App\Models\SchdraftCategory;
use App\Models\SchdraftGroup;
use App\Models\SchdraftTemplate;
use App\Models\Operator;
use App\Models\TrainType;
use App\Models\TrainName;
use App\Models\Line_Station;
use App\Models\VehiclePerformanceItem;

class SchdraftTemplate extends Model{

    protected $table = 'schdraft_templates';
    protected $primaryKey = 'id_auto';
    protected $hidden = ['created_at', 'updated_at', 'id_auto', 'isDeleted'];

    //Fields Modifiable by PATCH / POST
    protected $fillable = [
        'sort', 'group_id', 'title',
        'is_upbound', 'coupled_template_id',
        'pivot_time', 'pivot_time_adj',
        'train_type_id', 'train_type_mod',
        'train_name_id', 'train_name_mod',
        'operator_id', 'operator_id_mod',
        'vehicle_performance_id',
        'train_number_rule', 'sch_template', 'mods', 'deployment',
        'station_begin_mod', 'station_terminate_mod',
        'remarks', 'other_info', 'is_enabled',
    ];
    
    //JSON fields
    protected $casts = [
        'train_type_mod' => 'object',
        'train_name_mod' => 'object',
        'operator_id_mod' => 'object',
        'trip_number_rule' => 'object',
        'train_number_rule' => 'object',
        'sch_template' => 'array',
        'sch_output' => 'array',
        'mods' => 'array',
        'station_begin_mod' => 'array',
        'station_terminate_mod' => 'array',
        'deployment' => 'object',
        'other_info' => 'object',
    ];

    //Data validations
    public static $validations_update = [
        'sort' => 'integer',
        'group_id' => 'exists:schdraft_groups,id',
        'is_upbound' => 'boolean',
        'coupled_template_id' => 'nullable|exists:schdraft_templates,id',
        'pivot_time' => 'integer',
        'pivot_time_adj' => 'integer',
        'train_type_id' => 'exists:train_types,id',
        'train_type_mod' => 'json',
        'train_name_id' => 'nullable|exists:train_names,id',
        'train_name_mod' => 'json',
        'operator_id' => 'exists:operators,id',
        'operator_id_mod' => 'json',
        'vehicle_performance_id' => 'exists:vehicle_performance_items,id',
        'train_number_rule' => 'json',
        'sch_template' => 'json',
        'mods' => 'json',
        'station_begin_mod' => 'json',
        'station_terminate_mod' => 'json',
        'deployment' => 'json',
        'other_info' => 'json',
        'is_enabled' => 'boolean',
    ];
    public static $validations_new = [
        'sort' => 'integer',
        'group_id' => 'required|exists:schdraft_groups,id',
        'title' => 'required',
        'is_upbound' => 'boolean',
        'coupled_template_id' => 'nullable|exists:schdraft_templates,id',
        'pivot_time' => 'required|integer',
        'pivot_time_adj' => 'integer',
        'train_type_id' => 'required|exists:train_types,id',
        'train_type_mod' => 'json',
        'train_name_id' => 'nullable|exists:train_names,id',
        'train_name_mod' => 'json',
        'operator_id' => 'required|exists:operators,id',
        'operator_id_mod' => 'json',
        'vehicle_performance_id' => 'required|exists:vehicle_performance_items,id',
        'train_number_rule' => 'json',
        'sch_template' => 'json',
        'mods' => 'json',
        'station_begin_mod' => 'json',
        'station_terminate_mod' => 'json',
        'deployment' => 'json',
        'other_info' => 'json',
        'is_enabled' => 'boolean',
    ];

    //Filters
    public static function filters($query, $param){
        switch ($query){
            case 'group_id':
            return ['query' => 'group_id = ?', 'params' => [$param]];
        }
    }
    
    //Sortings
    public static $sort_default = 'is_upbound,pivot_time,pivot_time_adj,title';
    public static $sortable = [
        'id', 'pivot_time', 'pivot_time_adj', 'group_id', 'title', 'is_upbound',
        'train_type_id', 'train_name_id', 'operator_id',
    ];

    //Resource Relationships
    public function group(){
        return $this->belongsTo(SchdraftGroup::class, 'group_id', 'id')->where('isDeleted', false);
    }
    public function operator(){
        return $this->belongsTo(Operator::class, 'operator_id', 'id')->where('isDeleted', false);
    }
    public function trainType(){
        return $this->belongsTo(TrainType::class, 'train_type_id', 'id')->where('isDeleted', false);
    }
    public function trainName(){
        return $this->belongsTo(TrainName::class, 'train_name_id', 'id')->where('isDeleted', false);
    }

    //Display data returned for GET
    public function displayData($request){
        $data = clone $this;
        //"from_selecter" -> Only essential fields for selecter
        if ($request->input("from_selecter")){
            $data = (object)[
                "id" => $data->id,
                "title" => $data->title,
            ];
        }
        //"breadcrumb"
        if ($request->input('breadcrumb')){
            $group = $this->group;
            $category = $group ? $group->category : null;
            $data = (object)[
                "id" => $data->id,
                "title" => $data->title,
                "group_id" => $data->group_id,
                "group_title" => $group ? $group->title : null,
                "category_id" => $group->category_id,
                "category_title" => $category ? $category->title : null,
            ];
        }
        //"group"
        if ($request->input('group')){
            $data->group = $this->group;
        }
        //"more" -> Get also group and other relational info as well
        if ($request->input('more')){
            $data->group = $this->group;
            $data->operator = $this->operator;
            $data->trainType = $this->trainType;
            $data->trainName = $this->trainName;
        }
        //"list" -> For listing
        if ($request->input('list')){
            return $this->dataForList();
        }
        //"list" -> Get data for list (overrides all other params)
        return $data;
    }

    public function dataForList(){
        $data = $this->toArray();
        //Operator
        $operator = $this->operator;
        $data['operator_name_chi'] = ($operator) ? $operator->name_chi : null;
        $data['operator_name_eng'] = ($operator) ? $operator->name_eng : null;
        $data['operator_color'] = ($operator) ? $operator->color : null;
        //TrainType
        $attributes = ['name_chi', 'name_chi_short', 'name_eng', 'name_eng_short', 'color'];
        $trainType = $this->trainType;
        foreach ($attributes as $attr){
            $data['train_type_'.$attr] = ($trainType) ? $trainType->{$attr} : null;
        }
        //TrainName
        $attributes = ['name_chi', 'name_eng', 'color'];
        $trainName = $this->trainName;
        foreach ($attributes as $attr){
            $data['train_name_'.$attr] = ($trainName) ? $trainName->{$attr} : null;
        }
        //Data
        return (object) $data;
    }

    //Additional processing of data
    public function whenCreated($request){
        $this->whenSet($request);
    }
    public function whenSet($request){
        $this->updateInvolvedLinesAndStations();
        $this->updateScheduleOutput();
    }

    /**
     * Custom Methods
     */

    //Is Enabled?
    public function isEnabled(){
        if (!$this->is_enabled) return false;
        $group = SchdraftGroup::where('id', $this->group_id)
            ->where('isDeleted', false)->where('is_enabled', true)->first();
        if (!$group) return false;
        $category = SchdraftCategory::where('id', $group->category_id)
            ->where('isDeleted', false)->where('is_enabled', true)->first();
        if (!$category) return false;
        return true;
    }

    //Update Involved Lines & Stations
    public function updateInvolvedLinesAndStations(){
        $lines = [];
        $stations = [];
        foreach ($this->sch_template as $item){
            $line_id = $item['line_id'] ?? null;
            $station_id = $item['station_id'] ?? null;
            if ($line_id && !in_array($line_id, $lines)) array_push($lines, $line_id);
            if ($station_id && !in_array($station_id, $stations)) array_push($stations, $station_id);
        }
        $this->line_ids_involved = '|'.implode('|', $lines).'|';
        $this->station_ids_involved = '|'.implode('|', $stations).'|';
        $this->save();
    }

    //Update Schedule Output
    public function updateScheduleOutput(){
        $sch_output = (object)["wk" => [], "ph" => []];
        //For Each Deployment Item
        foreach ($this->deployment as $deployment_item){
            if ($deployment_item->interval > 0){
                $a = $deployment_item->pivot_from;
                $b = $deployment_item->pivot_to;
                $c = $deployment_item->interval;
                $wk = $deployment_item->wk;
                $ph = $deployment_item->ph;
                $index = 0;
                for ($pivot_time = $a; $pivot_time <= $b; $pivot_time += $c){
                    if ($wk){
                        $trip = $this->getOneTrip($pivot_time, false, $deployment_item, $index);
                        if ($trip) array_push($sch_output->wk, $trip);
                    }
                    if ($ph){
                        $trip = $this->getOneTrip($pivot_time, true, $deployment_item, $index);
                        if ($trip) array_push($sch_output->ph, $trip);
                    }
                    $index++;
                }
            }
        }
        //Save Data
        $this->sch_output = $sch_output;
        $this->save();
    }

    //Get Index (in Sch Template) By Station ID
    public function getIndexInTemplate($station_id, $is_reverse = false){
        if (!$station_id) return null;
        if (!$is_reverse){
            for ($i = 0; $i < count($this->sch_template); $i++){
                if (!isset($this->sch_template[$i]['is_cross'])){
                    if (($this->sch_template[$i]['station_id'] ?? null) == $station_id) return $i;
                }
            }
        }else{
            for ($i = count($this->sch_template) - 1; $i >= 0; $i--){
                if (!isset($this->sch_template[$i]['is_cross'])){
                    if (($this->sch_template[$i]['station_id'] ?? null) == $station_id) return $i;
                }
            }
        }
        return null;
    }

    //Is Trip Exist (by Pivot Time & Station to Check)
    public function isTripExist($pivot_time, $isPH, $station_id = null, $is_reverse = false){
        //Station Index in Template
        $station_index_in_template = null;
        if ($station_id){
            $station_index_in_template = $this->getIndexInTemplate($station_id, $is_reverse);
        }
        //Search in Deployment
        foreach ($this->deployment as $deployment_item){
            if (!$isPH && !($deployment_item->wk) ?? null) continue;
            if ($isPH && !($deployment_item->ph) ?? null) continue;
            //Check pivot_to, pivot_from, interval
            if (!$deployment_item->pivot_from ?? null) continue;
            if (!$deployment_item->pivot_to ?? null) continue;
            if (!$deployment_item->interval ?? null) continue;
            if ($pivot_time < $deployment_item->pivot_from) continue;
            if ($pivot_time > $deployment_item->pivot_to) continue;
            if (($pivot_time - $deployment_item->pivot_from) % $deployment_item->interval) continue;
            //Check station_begin, station_terminate
            if ($station_index_in_template){
                $station_begin = $deployment_item->station_begin;
                $station_begin_reverse = $deployment_item->station_begin_reverse ?? false;
                $station_index_begin = $this->getIndexInTemplate($station_begin, $station_begin_reverse) ?? 0;
                $station_terminate = $deployment_item->station_terminate;
                $station_terminate_reverse = $deployment_item->station_terminate_reverse ?? false;
                $station_index_terminate = $this->getIndexInTemplate($station_terminate, $station_terminate_reverse)
                ?? (count($this->sch_template) - 1);
                if ($station_index_begin > $station_index_in_template) continue;
                if ($station_index_terminate < $station_index_in_template) continue;
                return true;
            }else{
                return true;
            }
        }
        return false;
    }

    //Get Mods (by Pivot Time) [returns array]
    public function getMods($pivot_time, $isPH, $mods_manual = []){
        $array = $mods_manual;
        foreach ($this->mods as $mod_item){
            //Mod ID
            $mod_id = $mod_item['mod'] ?? null;
            if ($mod_id === null) continue;
            //Rules
            $rules = $mod_item['rules'] ?? [];
            if (!count($rules)) continue;
            $rules_pass = true;
            foreach ($rules as $rule_item){
                $rule_id = $rule_item['rule'] ?? null;
                $params = $rule_item['param'] ?? [];
                if (!$this->rulePasses($rule_id, $params, $pivot_time, $isPH)) $rules_pass = false;
            }
            //If All Rules Pass
            if ($rules_pass) array_push($array, $mod_id);
        }
        return $array;
    }

    private function rulePasses($rule_id, $params, $pivot_time, $isPH){
        if ($rule_id == 'pivot_remainder_eq'){
            $dividand = $params[0] ?? 3600;
            $remainder = $params[1] ?? 0;
            return ($pivot_time % $dividand == $remainder);
        }
        if ($rule_id == 'pivot_remainder_not'){
            $dividand = $params[0] ?? 3600;
            $remainder = $params[1] ?? 0;
            return ($pivot_time % $dividand != $remainder);
        }
        if ($rule_id == 'pivot_eq'){
            $pivot_check = $params[0] ?? 0;
            return ($pivot_time == $pivot_check);
        }
        if ($rule_id == 'pivot_less'){
            $pivot_check = $params[0] ?? 0;
            return ($pivot_time < $pivot_check);
        }
        if ($rule_id == 'pivot_less_eq'){
            $pivot_check = $params[0] ?? 0;
            return ($pivot_time <= $pivot_check);
        }
        if ($rule_id == 'pivot_greater'){
            $pivot_check = $params[0] ?? 0;
            return ($pivot_time > $pivot_check);
        }
        if ($rule_id == 'pivot_greater_eq'){
            $pivot_check = $params[0] ?? 0;
            return ($pivot_time >= $pivot_check);
        }
        if ($rule_id == 'pivot_between'){
            $pivot_check1 = $params[0] ?? 0;
            $pivot_check2 = $params[1] ?? 0;
            return ($pivot_time >= $pivot_check1 && $pivot_time <= $pivot_check2);
        }
        if ($rule_id == 'pivot_not_between'){
            $pivot_check1 = $params[0] ?? 0;
            $pivot_check2 = $params[1] ?? 0;
            return ($pivot_time <= $pivot_check1 || $pivot_time >= $pivot_check2);
        }
        if ($rule_id == 'template_exist'){
            $relative_time = $params[0] ?? 0;
            $pivot_check = $pivot_time + $relative_time;
            $template_id = $params[1] ?? null;
            $station_id = $params[3] ?? null;
            return $this->isTripExist($pivot_check, $isPH, $station_id);
        }
        if ($rule_id == 'template_not_exist'){
            $relative_time = $params[0] ?? 0;
            $pivot_check = $pivot_time + $relative_time;
            $template_id = $params[1] ?? null;
            $station_id = $params[3] ?? null;
            return !$this->isTripExist($pivot_check, $isPH, $station_id);
        }
        if ($rule_id == 'template_exist_reverse'){
            $relative_time = $params[0] ?? 0;
            $pivot_check = $pivot_time + $relative_time;
            $template_id = $params[1] ?? null;
            $station_id = $params[3] ?? null;
            return $this->isTripExist($pivot_check, $isPH, $station_id, true);
        }
        if ($rule_id == 'template_not_exist_reverse'){
            $relative_time = $params[0] ?? 0;
            $pivot_check = $pivot_time + $relative_time;
            $template_id = $params[1] ?? null;
            $station_id = $params[3] ?? null;
            return !$this->isTripExist($pivot_check, $isPH, $station_id, true);
        }
    }

    //Get Schedule for a Trip [for invalid trip, return false]
    public function getOneTrip($pivot_time, $isPH, $deployment_item, $index){

        //Validate First
        if (!$pivot_time) return false;

        //Prepare Data
        $data = (object)[];
        $data->schdraft_template_id = $this->id;
        $data->pivot_time = $pivot_time;
        $data->pivot_shift = $deployment_item->pivot_shift ?? 0;
        $mods = $this->getMods($pivot_time, $isPH, $deployment_item->mods) ?? [];

        //operator_id
        if (!$this->operator_id) return false;
        $data->operator_id = $this->operator_id;
        foreach ($mods as $mod){
            $mod_item = self::getMod($this->operator_id_mod, $mod);
            if ($mod_item) $data->operator_id = $mod_item->value;
        }

        //train_type_id
        if (!$this->train_type_id) return false;
        $data->train_type_id = $this->train_type_id;
        foreach ($mods as $mod){
            $mod_item = self::getMod($this->train_type_mod, $mod);
            if ($mod_item) $data->train_type = $mod_item->value;
        }

        //train_name_id
        if (!$this->train_name_id) return false;
        $data->train_name_id = $this->train_name_id;
        foreach ($mods as $mod){
            $mod_item = self::getMod($this->train_name_mod, $mod);
            if ($mod_item) $data->train_name_id = $mod_item->value;
        }

        //train_number
        $data->train_number = null;
        $service_no_min = $deployment_item->service_no_min ?? null;
        if ($service_no_min !== null){
            $service_no_step = $deployment_item->service_no_step ?? 2;
            $data->train_number = $service_no_min + $service_no_step * $index;
        }

        //trip_number, run_number (TBD)
        $data->trip_number = null;
        $data->run_number = null;

        //wk, ph
        $data->wk = !$isPH;
        $data->ph = $isPH;

        //is_temp
        $data->is_temp = $deployment_item->is_temp;
 
        //Determine Begin / Terminate Index (by deployment_item)
        $begin_index = 0;
        $terminate_index = count($this->sch_template) - 1;
        if ($deployment_item->station_begin ?? null){
            $is_reverse = $deployment_item->station_begin_reverse ?? false;
            $begin_index_alt = $this->getIndexInTemplate($deployment_item->station_begin, $is_reverse);
            if ($begin_index_alt) $begin_index = $begin_index_alt;
        }
        if ($deployment_item->station_terminate ?? null){
            $is_reverse = $deployment_item->station_terminate_reverse ?? false;
            $terminate_index_alt = $this->getIndexInTemplate($deployment_item->station_terminate, $is_reverse);
            if ($terminate_index_alt) $terminate_index = $terminate_index_alt;
        }

        //Determine Begin / Terminate Index (by station_begin_mod, station_terminate_mod)
        $begin_index2 = null;
        $terminate_index2 = null;
        foreach ($mods as $mod){
            $mod_item = self::getMod($this->station_begin_mod, $mod);
            if ($mod_item){
                $is_reverse = $mod_item->is_reverse ?? false;
                $begin_index_alt = $this->getIndexInTemplate($mod_item->station_id, $is_reverse);
                if ($begin_index_alt) $begin_index2 = $begin_index_alt;
            }
            $mod_item = self::getMod($this->station_terminate_mod, $mod);
            if ($mod_item){
                $is_reverse = $mod_item->is_reverse ?? false;
                $terminate_index_alt = $this->getIndexInTemplate($mod_item->station_id, $is_reverse);
                if ($terminate_index_alt) $terminate_index2 = $terminate_index_alt;
            }
        }
        if ($begin_index2) $begin_index = min($begin_index, $begin_index2);
        if ($terminate_index2) $terminate_index = min($terminate_index, $terminate_index2);
        $data->begin_index = $begin_index;
        $data->terminate_index = $terminate_index;

        //schedule, crossings
        $pivot_time_diff = $pivot_time - $this->pivot_time;
        $data->schedule = [];
        $data->crossings = [];
        //For each item
        for ($i = $begin_index; $i <= $terminate_index; $i++){
            $template_item = (object)$this->sch_template[$i];
            $is_cross = ($template_item->is_cross ?? false);
            //Prepare Schedule Item
            $item = (object)[];
            //Line, Station, is_express_track, etc
            if (!$is_cross){
                $item->line_id = ($i < $terminate_index) ? ($template_item->line_id ?? null) : null;
                $item->is_upbound = ($i < $terminate_index) ? ($template_item->is_upbound ?? null) : null;
                $item->is_express_track = ($i < $terminate_index) ? ($template_item->is_express_track ?? false) : null;
            }
            $item->station_id = $template_item->station_id ?? null;
            if ($is_cross){
                $item->cross_id = $template_item->cross_id ?? null;
            }
            //time1, time2, is_pass, track
            if (!$is_cross){
                $item->track = $template_item->track ?? null;
                $item->is_pass = $template_item->is_pass ?? false;
            }
            $time1_in_template = $template_item->time1 ?? null;
            $time2_in_template = $template_item->time2 ?? null;
            foreach ($mods as $mod){
                $mod_item = self::getMod($template_item->mod ?? [], $mod);
                if (!$mod_item) continue;
                if (($mod_item->track ?? null) !== null)
                    $item->track = $mod_item->track;
                if (($mod_item->is_express_track ?? null) !== null)
                    $item->is_express_track = $mod_item->is_express_track;
                if (($mod_item->is_pass ?? null) !== null)
                    $item->is_pass = $mod_item->is_pass;
                //time1
                if ($mod_item->time1_shift !== null && $time1_in_template){
                    $time1_in_template = $template_item->time1 + $mod_item->time1_shift;
                }else if ($mod_item->time1 === false){
                    $time1_in_template = null;
                }else if ($mod_item->time1 !== null){
                    $time1_in_template = $mod_item->time1;
                }
                //time2
                if ($mod_item->time2_shift !== null && $time2_in_template){
                    $time2_in_template = $template_item->time2 + $mod_item->time2_shift;
                }else if ($mod_item->time2 === false){
                    $time2_in_template = null;
                }else if ($mod_item->time2 !== null){
                    $time2_in_template = $mod_item->time2;
                }
            }
            $item->time1 = ($time1_in_template !== null && $i > $begin_index)
            ? ($time1_in_template + $pivot_time_diff) : null;
            $item->time2 = ($time2_in_template !== null && $i < $terminate_index)
            ? ($time2_in_template + $pivot_time_diff) : null;

            //Push Schedule Item
            if (!$is_cross){
                array_push($data->schedule, $item);
            }else{
                array_push($data->crossings, $item);
            }
        }

        //Return Data
        return $data;
    }

    //Misc Functions (Private)
    private function xor($a, $b){
        return ($a && !$b) || (!$a && $b);
    }

    private static function getMod($array, $mod){
        foreach ($array as $item){
            $item = (object) $item;
            if (($item->mod ?? null) == $mod) return $item;
        }
        return null;
    }

}
