<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use App\Models\Operator;

class OperatorType extends Model{

    protected $table = 'operator_types';
    protected $primaryKey = 'id_auto';
    protected $hidden = ['created_at', 'updated_at', 'id_auto', 'isDeleted'];

    //Fields Modifiable by PATCH / POST
    protected $fillable = [
        'sort', 'name_chi', 'name_eng', 'remarks', 'other_info',
    ];
    
    //JSON fields
    protected $casts = [
        'other_info' => 'object',
    ];

    //Data validations
    public static $validations_update = [
        'sort' => 'integer',
        'other_info' => 'json',
    ];
    public static $validations_new = [
        'sort' => 'integer',
        'name_chi' => 'required',
        'name_eng' => 'required',
        'other_info' => 'json',
    ];

    //Filters
    public static function filters($query, $param){
    }
    
    //Sortings
    public static $sort_default = 'sort';
    public static $sortable = ['sort', 'name_chi', 'name_eng'];

    //Resource Relationships
    public function operators(){
        return $this->hasMany(Operator::class, 'operator_type_id', 'id');
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
