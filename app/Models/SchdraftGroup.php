<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use App\Models\SchdraftCategory;
use App\Models\SchdraftGroup;
use App\Models\SchdraftTemplate;

class SchdraftGroup extends Model{

    protected $table = 'schdraft_groups';
    protected $primaryKey = 'id_auto';
    protected $hidden = ['created_at', 'updated_at'];

    //Fields Modifiable by PATCH / POST
    protected $fillable = [
        'sort', 'category_id', 'title',
        'trip_number_rule'
        'remarks', 'other_info', 'is_enabled',
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
    public function group(){
        return $this->belongsTo(SchdraftCategory::class, 'category_id', 'id');
    }
    public function templates(){
        return $this->hasMany(SchdraftTemplate::class, 'group_id', 'id');
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
