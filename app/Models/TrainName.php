<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use App\Models\TrainType;
use App\Models\Operator;

class TrainName extends Model{

    protected $table = 'train_names';
    protected $primaryKey = 'id_auto';
    protected $hidden = ['created_at', 'updated_at', 'id_auto', 'isDeleted'];

    //Fields Modifiable by PATCH / POST
    protected $fillable = [
        'train_type_id', 'major_operator_id',
        'name_chi', 'name_eng', 'color', 'color_text',
        'max_speed_kph',
        'remarks', 'other_info',
    ];
    
    //JSON fields
    protected $casts = [
        'other_info' => 'object',
    ];

    //Data validations
    public static $validations_update = [
        'train_type_id' => 'exists:train_types,id',
        'major_operator_id' => 'nullable|exists:operators,id',
        'max_speed_kph' => 'integer',
        'other_info' => 'json',
    ];
    public static $validations_new = [
        'train_type_id' => 'exists:train_types,id',
        'major_operator_id' => 'nullable|exists:operators,id',
        'name_chi' => 'required',
        'name_eng' => 'required',
        'max_speed_kph' => 'integer',
        'other_info' => 'json',
    ];

    //Filters
    public static function filters($query, $param){
        switch ($query){
            case 'train_type_id':
            return ['query' => 'train_type_id = ?', 'params' => [$param]];
            case 'major_operator_id':
            return ['query' => 'major_operator_id = ?', 'params' => [$param]];
            case 'name_chi':
            return ['query' => 'LOWER(name_chi) LIKE LOWER(?)', 'params' => ["%$param%"]];
            case 'name_eng':
            return ['query' => 'LOWER(name_eng) LIKE LOWER(?)', 'params' => ["%$param%"]];
        }
    }
    
    //Sortings
    public static $sort_default = 'name_eng';
    public static $sortable = [
        'train_type_id', 'major_operator_id', 'name_chi', 'name_eng', 'max_speed_kph',
    ];

    //Resource Relationships
    public function operator(){
        return $this->belongsTo(Operator::class, 'major_operator_id', 'id')->where('isDeleted', false);
    }
    public function trainType(){
        return $this->belongsTo(TrainType::class, 'train_type_id', 'id')->where('isDeleted', false);
    }

    //Display data returned for GET
    public function displayData($request){
        $data = clone $this;
        //"from_selecter" -> Only essential fields for selecter
        if ($request->input("from_selecter")){
            $data = (object)[
                "id" => $data->id,
                "name_chi" => $data->name_chi,
                "name_eng" => $data->name_eng,
            ];
        }
        //"more" -> Get also operator & train type as well
        if ($request->input('more')){
            $data->operator = $this->operator;
            $data->trainType = $this->trainType;
        }
        return $data;
    }

    /**
     * Custom Methods
     */


}
