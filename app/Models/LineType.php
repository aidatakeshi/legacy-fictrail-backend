<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use App\Models\Line;

class LineType extends Model{

    protected $table = 'line_types';
    protected $primaryKey = 'id_auto';
    protected $hidden = ['created_at', 'updated_at', 'id_auto', 'isDeleted'];

    //Fields Modifiable by PATCH / POST
    protected $fillable = [
        'sort',
        'name_chi', 'name_eng', 'major', 'is_passenger_hr', 'color',
        'remarks', 'other_info',
    ];
    
    //JSON fields
    protected $casts = [
        'other_info' => 'object',
    ];

    //Data validations
    public static $validations_update = [
        'sort' => 'integer',
        'major' => 'boolean',
        'is_passenger_hr' => 'boolean',
        'other_info' => 'json',
    ];
    public static $validations_new = [
        'sort' => 'integer',
        'name_chi' => 'required',
        'name_eng' => 'required',
        'major' => 'boolean',
        'is_passenger_hr' => 'boolean',
        'other_info' => 'json',
    ];

    //Filters
    public static function filters($query, $param){
    }
    
    //Sortings
    public static $sort_default = 'sort';
    public static $sortable = ['sort', 'name_chi', 'name_eng', 'major', 'is_passenger_hr'];

    //Resource Relationships
    public function lines(){
        return $this->hasMany(Line::class, 'line_type_id', 'id')->where('isDeleted', false);
    }

    //Display data returned for GET
    public function displayData($request){
        $data = clone $this;
        //"more" -> Get also lines as well
        if ($request->input('more')){
            $data->lines = $this->lines()->orderBy('name_eng', 'asc')->get();
        }
        return $data;
    }

    /**
     * Custom Methods
     */


}
