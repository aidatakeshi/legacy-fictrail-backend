<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use App\Models\Prefecture;

class PrefectureArea extends Model{

    protected $table = 'prefecture_areas';
    protected $primaryKey = 'id_auto';
    protected $hidden = ['created_at', 'updated_at'];

    //Fields Modifiable by PATCH / POST
    protected $fillable = [
        'sort', 'name_chi', 'name_eng', 'remarks', 'other_info',
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
    public static function filters($param){
    }
    
    //Sortings
    public static $sort_default = 'sort';
    public static $sortable = ['sort', 'name_chi', 'name_eng'];

    //Resource Relationships
    public function prefectures(){
        return $this->hasMany(Prefecture::class, 'area_id', 'id');
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
