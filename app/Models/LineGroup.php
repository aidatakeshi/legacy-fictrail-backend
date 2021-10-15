<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use App\Models\Line;

class LineGroup extends Model{

    protected $table = 'line_groups';
    protected $primaryKey = 'id_auto';
    protected $hidden = ['created_at', 'updated_at'];

    //Fields Modifiable by PATCH / POST
    protected $fillable = [
        'sort',
        'name_chi', 'name_eng', 'name_eng_short',
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
    public function lines(){
        return $this->hasMany(Line::class, 'line_group_id', 'id');
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
