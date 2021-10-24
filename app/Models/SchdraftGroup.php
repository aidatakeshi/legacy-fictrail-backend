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
        switch ($query){
            case 'category_id':
            return ['query' => 'category_id = ?', 'params' => [$param]];
        }
    }
    
    //Sortings
    public static $sort_default = 'sort,title';
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
        //"from_selecter" -> Only essential fields for selecter
        //TBD
        
        //"more" -> Get also category as well
        if ($request->input('more')){
            $data->category = $this->category;
        }
        //"templates" -> Get also templates as well
        if ($request->input('templates')){
            $data->templates = $this->templates()
            ->selectRaw('id, sort, group_id, title, is_upbound, pivot_time, pivot_time_adj, train_type_id, train_name_id, operator_id, remarks, is_enabled')
            ->orderBy('sort', 'asc')->orderBy('title', 'asc')->get();
        }
        //"list" -> For listing
        if ($request->input('list')){
            foreach ($data->templates as $i => $template){
                $data->templates[$i] = $template->dataForList();
            }
        }
        return $data;
    }

    /**
     * Custom Methods
     */


}
