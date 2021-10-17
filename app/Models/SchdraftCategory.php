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
    
    //JSON fields
    protected $casts = [
        'other_info' => 'object',
    ];

    //Data validations
    public static $validations_update = [
        'sort' => 'integer',
        'other_info' => 'json',
        'is_enabled' => 'boolean',
    ];
    public static $validations_new = [
        'sort' => 'integer',
        'title' => 'required',
        'other_info' => 'json',
        'is_enabled' => 'boolean',
    ];

    //Filters
    public static function filters($query, $param){

    }
    
    //Sortings
    public static $sort_default = 'sort';
    public static $sortable = ['id', 'sort', 'title'];

    //Resource Relationships
    public function groups(){
        return $this->hasMany(SchdraftGroup::class, 'category_id', 'id')->where('isDeleted', false);
    }

    //Display data returned for GET
    public function displayData($request){
        $data = clone $this;
        //"more" -> Get also groups as well
        if ($request->input('more')){
            $data->groups = $this->groups()->orderBy('id', 'asc')->get();
        }
        return $data;
    }

    /**
     * Custom Methods
     */


}
