<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use App\Models\SchdraftCategory;
use App\Models\SchdraftGroup;
use App\Models\SchdraftTemplate;

class SchdraftTemplate extends Model{

    protected $table = 'schdraft_templates';
    protected $primaryKey = 'id_auto';
    protected $hidden = ['created_at', 'updated_at'];

    //Fields Modifiable by PATCH / POST
    protected $fillable = [
        'sort' 'group_id', 'title',
        'is_upbound', 'coupled_template_id',
        'pivot_time', 'pivot_time_adj',
        'train_type_id', 'train_type_mod'
        'train_name_id', 'train_name_mod',
        'operator_id', 'operator_id_mod',
        'vehicle_performance_id',
        'train_number_rule', 'sch_template', 'mods', 'deployment',
        'sch_output', 'line_ids_involved', 'station_ids_involved',
        'remarks', 'other_info', 'is_enabled',
    ];

    //Data validations
    public static $validations_update = [

    ];
    public static $validations_new = [

    ];

    //Filters
    public static $filters = [

    ];
    
    //Sortings
    public static $sorting = [

    ];

    //Resource Relationships
    public function group(){
        return $this->belongsTo(SchdraftGroup::class, 'group_id', 'id');
    }

    //Additional data returned for GET
    public function getAdditionalData($request){
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
