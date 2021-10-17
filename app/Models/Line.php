<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use App\Models\LineGroup;
use App\Models\LineType;
use App\Models\Operator;
use App\Models\Line_Station;

class Line extends Model{

    protected $table = 'lines';
    protected $primaryKey = 'id_auto';
    protected $hidden = ['created_at', 'updated_at', 'id_auto', 'isDeleted'];

    //Fields Modifiable by PATCH / POST
    protected $fillable = [
        'line_group_id', 'line_type_id', 'operator_id',
        'name_chi', 'name_eng', 'name_eng_short',
        'color', 'color_text', 'max_speed_kph',
        'length_km', 'x_min', 'x_max', 'y_min', 'y_max',
        'remarks', 'other_info',
    ];
    
    //JSON fields
    protected $casts = [
        'length_km' => 'float',
        'other_info' => 'object',
    ];

    //Data validations
    public static $validations_update = [
        'line_group_id' => 'nullable|exists:line_groups,id',
        'line_type_id' => 'exists:line_types,id',
        'operator_id' => 'exists:operators,id',
        'max_speed_kph' => 'integer',
        'length_km' => 'numeric',
        'x_min' => 'numeric',
        'x_max' => 'numeric',
        'y_min' => 'numeric',
        'y_max' => 'numeric',
        'other_info' => 'json',
    ];
    public static $validations_new = [
        'line_group_id' => 'nullable|exists:line_groups,id',
        'line_type_id' => 'required|exists:line_types,id',
        'operator_id' => 'required|exists:operators,id',
        'name_chi' => 'required',
        'name_eng' => 'required',
        'max_speed_kph' => 'integer',
        'length_km' => 'numeric',
        'x_min' => 'numeric',
        'x_max' => 'numeric',
        'y_min' => 'numeric',
        'y_max' => 'numeric',
        'other_info' => 'json',
    ];

    //Filters
    public static function filters($query, $param){
        switch ($query){
            case 'line_group_id':
            return ['query' => 'line_group_id = ?', 'params' => [$param]];
            case 'line_type_id':
            return ['query' => 'line_type_id = ?', 'params' => [$param]];
            case 'operator_id':
            return ['query' => 'operator_id = ?', 'params' => [$param]];

            case 'name_chi':
            return ['query' => 'LOWER(name_chi) LIKE LOWER(?)', 'params' => ["%$param%"]];
            case 'name_eng':
            return ['query' => 'LOWER(name_eng) LIKE LOWER(?)', 'params' => ["%$param%"]];
        }
    }
    
    //Sortings
    public static $sort_default = 'name_eng';
    public static $sortable = [
        'line_group_id', 'line_type_id', 'operator_id',
        'name_chi', 'name_eng', 'name_eng_short',
        'max_speed_kph', 'length_km',
    ];

    //Resource Relationships
    public function operator(){
        return $this->belongsTo(Operator::class, 'operator_id', 'id')->where('isDeleted', false);
    }
    public function lineGroup(){
        return $this->belongsTo(LineGroup::class, 'line_group_id', 'id')->where('isDeleted', false);
    }
    public function lineType(){
        return $this->belongsTo(LineType::class, 'line_type_id', 'id')->where('isDeleted', false);
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
        //"more" -> Get also operator, line group & type as well
        if ($request->input('more')){
            $data->operator = $this->operator;
            $data->lineGroup = $this->lineGroup;
            $data->lineType = $this->lineType;
        }
        //"stations" -> Get also stations ("segment" -> show also segments)
        if ($request->input('stations')){
            $line_stations = Line_Station::where('line_id', $this->id)->where('isDeleted', false)
            ->orderBy('sort', 'asc')->get();
            foreach ($line_stations as $i => $line_station){
                $line_stations[$i]->station = $line_station->station;
                if (!$request->input('segments')) unset($line_stations[$i]->segments);
            }
            $data->stations = $line_stations;
        }
        return $data;
    }

    /**
     * Custom Methods
     */


}
