<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use App\Models\SchdraftCategory;
use App\Models\SchdraftGroup;
use App\Models\SchdraftTemplate;

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
        'mods' => 'array',
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
        'pivot_time_adj' => 'json',
        'train_type_id' => 'exists:train_types,id',
        'train_type_mod' => 'json',
        'train_name_id' => 'exists:train_names,id',
        'train_name_mod' => 'json',
        'operator_id' => 'exists:operators,id',
        'operator_id_mod' => 'json',
        'vehicle_performance_id' => 'exists:vehicle_performance_items,id',
        'train_number_rule' => 'json',
        'sch_template' => 'json',
        'mods' => 'json',
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
        'pivot_time_adj' => 'json',
        'train_type_id' => 'required|exists:train_types,id',
        'train_type_mod' => 'json',
        'train_name_id' => 'required|exists:train_names,id',
        'train_name_mod' => 'json',
        'operator_id' => 'required|exists:operators,id',
        'operator_id_mod' => 'json',
        'vehicle_performance_id' => 'required|exists:vehicle_performance_items,id',
        'train_number_rule' => 'json',
        'sch_template' => 'json',
        'mods' => 'json',
        'deployment' => 'json',
        'other_info' => 'json',
        'is_enabled' => 'boolean',
    ];

    //Filters
    public static function filters($query, $param){
        
    }
    
    //Sortings
    public static $sort_default = 'sort';
    public static $sortable = ['id', 'sort', 'group_id', 'title', 'is_upbound'];

    //Resource Relationships
    public function group(){
        return $this->belongsTo(SchdraftGroup::class, 'group_id', 'id')->where('isDeleted', false);
    }

    //Display data returned for GET
    public function displayData($request){
        $data = clone $this;
        //"more" -> Get also group as well
        if ($request->input('more')){
            $data->group = $this->group;
        }
        return $data;
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
