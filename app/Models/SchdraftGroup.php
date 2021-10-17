<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use App\Models\SchdraftCategory;
use App\Models\SchdraftGroup;
use App\Models\SchdraftTemplate;

class SchdraftGroup extends Model{

    protected $table = 'schdraft_groups';
    protected $primaryKey = 'id_auto';
    protected $hidden = ['created_at', 'updated_at', 'id_auto', 'isDeleted'];

    //Fields Modifiable by PATCH / POST
    protected $fillable = [
        'sort', 'category_id', 'title',
        'trip_number_rule',
        'remarks', 'other_info', 'is_enabled',
    ];
    
    //JSON fields
    protected $casts = [
        'trip_number_rule' => 'object',
        'other_info' => 'object',
    ];

    //Data validations
    public static $validations_update = [
        'sort' => 'integer',
        'category_id' => 'exists:schdraft_categories,id',
        'trip_number_rule' => 'json',
        'other_info' => 'json',
        'is_enabled' => 'boolean',
    ];
    public static $validations_new = [
        'sort' => 'integer',
        'category_id' => 'exists:schdraft_categories,id',
        'title' => 'required',
        'trip_number_rule' => 'json',
        'other_info' => 'json',
        'is_enabled' => 'boolean',
    ];

    //Filters
    public static function filters($query, $param){

    }
    
    //Sortings
    public static $sort_default = 'sort';
    public static $sortable = ['sort', 'category_id', 'title'];

    //Resource Relationships
    public function category(){
        return $this->belongsTo(SchdraftCategory::class, 'category_id', 'id')->where('isDeleted', false);
    }
    public function templates(){
        return $this->hasMany(SchdraftTemplate::class, 'group_id', 'id')->where('isDeleted', false);
    }

    //Display data returned for GET
    public function displayData($request){
        $data = clone $this;
        //"more" -> Get also category & templates (selected fields) as well
        if ($request->input('more')){
            $data->category = $this->category;
            $data->templates = $this->templates()
            ->selectRaw('sort, group_id, title, is_upbound, train_type_id, train_name_id, operator_id, remarks, is_enabled')
            ->orderBy('id', 'asc')->get();
        }
        return $data;
    }

    /**
     * Custom Methods
     */


}
