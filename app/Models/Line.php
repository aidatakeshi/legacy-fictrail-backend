<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use App\Models\LineGroup;
use App\Models\LineType;
use App\Models\Operator;

class Line extends Model{

    protected $table = 'lines';
    protected $primaryKey = 'id_auto';
    protected $hidden = ['created_at', 'updated_at'];

    //Fields Modifiable by PATCH / POST
    protected $fillable = [
        'line_group_id', 'line_type_id', 'operator_id',
        'name_chi', 'name_eng', 'name_eng_short',
        'color', 'color_text', 'max_speed_kph',
        'length_km', 'x_min', 'x_max', 'y_min', 'y_max',
        'remarks', 'other_info',
    ];

    //Data validations
    public static $validations_update = [

    ];
    public static $validations_new = [

    ];

    //Filters
    public static function filters($param){
    }
    
    //Sortings
    public static $sort_default = 'sort';
    public static $sortable = [];

    //Resource Relationships
    public function operator(){
        return $this->belongsTo(Operator::class, 'operator_id', 'id');
    }
    public function lineGroup(){
        return $this->belongsTo(LineGroup::class, 'line_group_id', 'id');
    }
    public function lineType(){
        return $this->belongsTo(LineType::class, 'line_type_id', 'id');
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
