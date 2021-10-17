<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use App\Models\TrainName;

class TrainType extends Model{

    protected $table = 'train_types';
    protected $primaryKey = 'id_auto';
    protected $hidden = ['created_at', 'updated_at', 'id_auto', 'isDeleted'];

    //Fields Modifiable by PATCH / POST
    protected $fillable = [
        'sort', 'operator_id',
        'name_chi', 'name_chi_short', 'name_eng', 'name_eng_short',
        'color', 'color_text', 'is_premium',
        'remarks', 'other_info',
    ];
    
    //JSON fields
    protected $casts = [
        'other_info' => 'object',
    ];

    //Data validations
    public static $validations_update = [
        'sort' => 'integer',
        'operator_id' => 'exists:operators,id',
        'is_premium' => 'boolean',
        'other_info' => 'json',
    ];
    public static $validations_new = [
        'sort' => 'integer',
        'operator_id' => 'exists:operators,id',
        'name_chi' => 'required',
        'name_eng' => 'required',
        'is_premium' => 'boolean',
        'other_info' => 'json',
    ];

    //Filters
    public static function filters($query, $param){
    }
    
    //Sortings
    public static $sort_default = '-sort';
    public static $sortable = [
        'sort', 'operator_id', 'name_chi', 'name_chi_short', 'name_eng', 'name_eng_short', 'is_premium',
    ];

    //Resource Relationships
    public function trainNames(){
        return $this->hasMany(TrainName::class, 'train_type_id', 'id')->where('isDeleted', false);
    }

    //Display data returned for GET
    public function displayData($request){
        $data = clone $this;
        //"more" -> Get also train names as well
        if ($request->input('more')){
            $data->trainNames = $this->trainNames()->orderBy('name_eng', 'asc')->get();
        }
        return $data;
    }

    /**
     * Custom Methods
     */


}
