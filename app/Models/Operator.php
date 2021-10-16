<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use App\Models\OperatorType;

class Operator extends Model{

    protected $table = 'operators';
    protected $primaryKey = 'id_auto';
    protected $hidden = ['created_at', 'updated_at', 'id_auto', 'isDeleted'];

    //Fields Modifiable by PATCH / POST
    protected $fillable = [
        'operator_type_id',
        'name_chi', 'name_chi_short', 'name_eng', 'name_eng_short',
        'color', 'color_text', 'is_passenger_hr', 'logo_fileid',
        'remarks', 'other_info',
    ];
    
    //JSON fields
    protected $casts = [
        'other_info' => 'object',
    ];

    //Data validations
    public static $validations_update = [
        'operator_type_id' => 'exists:operator_types,id',
        'sort' => 'integer',
        'other_info' => 'json',
    ];
    public static $validations_new = [
        'operator_type_id' => 'required|exists:operator_types,id',
        'sort' => 'integer',
        'name_chi' => 'required',
        'name_eng' => 'required',
        'other_info' => 'json',
    ];

    //Filters
    public static function filters($query, $param){
        switch ($query){
            case 'operator_type_id':
            return ['query' => 'operator_type_id = ?', 'params' => [$param]];
            case 'name_chi':
            return ['query' => 'LOWER(name_chi) LIKE LOWER(?)', 'params' => ["%$param%"]];
            case 'name_eng':
            return ['query' => 'LOWER(name_eng) LIKE LOWER(?)', 'params' => ["%$param%"]];
            case 'is_passenger_hr':
            return ['query' => 'is_passenger_hr = TRUE', 'params' => []];
            case 'not_passenger_hr':
            return ['query' => 'is_passenger_hr = FALSE', 'params' => []];
        }
    }
    
    //Sortings
    public static $sort_default = 'name_eng';
    public static $sortable = ['operator_type_id', 'name_chi', 'name_chi_short', 'name_eng', 'name_eng_short'];

    //Resource Relationships
    public function operatorType(){
        return $this->belongsTo(OperatorType::class, 'operator_type_id', 'id')->where('isDeleted', false);
    }

    //Display data returned for GET
    public function displayData($request){
        $data = clone $this;
        //"more" -> Get also operator type as well
        if ($request->input('more')){
            $data->operatorType = $this->operatorType;
        }
        return $data;
    }

    /**
     * Custom Methods
     */


}
