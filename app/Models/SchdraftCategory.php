<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use App\Models\SchdraftCategory;
use App\Models\SchdraftGroup;
use App\Models\SchdraftTemplate;

class SchdraftCategory extends Model{

    protected $table = 'schdraft_categories';
    protected $primaryKey = 'id_auto';
    protected $hidden = ['created_at', 'updated_at', 'id_auto', 'isDeleted'];

    //Fields Modifiable by PATCH / POST
    protected $fillable = [
        'sort', 'title', 'remarks', 'other_info', 'is_enabled',
    ];

    //Data validations
    public static $validations_update = [

    ];
    public static $validations_new = [

    ];

    //Filters
    public static function filters($query, $param){
    }
    
    //Sortings
    public static $sort_default = 'sort';
    public static $sortable = [];

    //Resource Relationships
    public function groups(){
        return $this->hasMany(SchdraftGroup::class, 'category_id', 'id');
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
