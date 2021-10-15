<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use App\Models\TrainType;
use App\Models\Operator;

class TrainName extends Model{

    protected $table = 'train_names';
    protected $primaryKey = 'id_auto';
    protected $hidden = ['created_at', 'updated_at'];

    //Fields Modifiable by PATCH / POST
    protected $fillable = [
        'train_type_id', 'major_operator_id',
        'name_chi', 'name_eng', 'color', 'color_text',
        'max_speed_kph',
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
        return $this->belongsTo(Operator::class, 'major_operator_id', 'id');
    }
    public function trainType(){
        return $this->belongsTo(TrainType::class, 'train_type_id', 'id');
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
