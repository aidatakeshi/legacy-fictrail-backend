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
        'operator_id' => 'nullable|exists:operators,id',
        'is_premium' => 'boolean',
        'other_info' => 'json',
    ];
    public static $validations_new = [
        'sort' => 'integer',
        'operator_id' => 'nullable|exists:operators,id',
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
    public function operator(){
        return $this->belongsTo(Operator::class, 'operator_id', 'id')->where('isDeleted', false);
    }
    public function trainNames(){
        return $this->hasMany(TrainName::class, 'train_type_id', 'id')->where('isDeleted', false);
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
        //"more" -> Get also train names as well
        if ($request->input('more')){
            //Operator
            $data->operator = $this->operator;

            //Train Names
            $query = $this->trainNames()->orderBy('name_eng', 'asc');

            //Filters
            if ($request->input("major_operator_id")){
                $query = $query->where('major_operator_id', $request->input("major_operator_id"));
            }

            //"from_selecter"
            if ($request->input("from_selecter")){
                $data->trainNames = $query->selectRaw('id, name_chi, name_eng')->get();
            }else{
                $data->trainNames = $query->get();
            }

            //"list" -> For listing
            if ($request->input('list')){
                foreach ($data->trainNames as $i => $trainName){
                    $data->trainNames[$i] = $trainName->dataForList();
                }
            }
        }
        //"list" -> For listing
        else if ($request->input('list')){
            $operator = $this->operator;
            $data->operator_name_chi = $operator ? $operator->name_chi : null;
            $data->operator_name_eng = $operator ? $operator->name_eng : null;
            $data->operator_color = $operator ? $operator->color : null;
        }
        return $data;
    }

    /**
     * Custom Methods
     */


}
