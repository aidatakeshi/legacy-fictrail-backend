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
        'max_speed_kph' => 'nullable|integer',
        'length_km' => 'nullable|numeric',
        'x_min' => 'nullable|numeric',
        'x_max' => 'nullable|numeric',
        'y_min' => 'nullable|numeric',
        'y_max' => 'nullable|numeric',
        'other_info' => 'json',
    ];
    public static $validations_new = [
        'line_group_id' => 'nullable|exists:line_groups,id',
        'line_type_id' => 'required|exists:line_types,id',
        'operator_id' => 'required|exists:operators,id',
        'name_chi' => 'required',
        'name_eng' => 'required',
        'max_speed_kph' => 'nullable|integer',
        'length_km' => 'nullable|numeric',
        'x_min' => 'nullable|numeric',
        'x_max' => 'nullable|numeric',
        'y_min' => 'nullable|numeric',
        'y_max' => 'nullable|numeric',
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
            case 'name':
            return [
                'query' => '(LOWER(name_chi) LIKE LOWER(?)) OR (LOWER(name_eng) LIKE LOWER(?))',
                'params' => ["%$param%", "%$param%"]
            ];
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
            $data->stations = Line_Station::where('line_id', $this->id)->where('isDeleted', false)
            ->orderBy('sort', 'asc')->get();
        }
        //"list" -> For listing
        if ($request->input('list')){
            return $this->dataForList();
        }
        return $data;
    }

    //Data For List
    public function dataForList(){
        $data = $this->toArray();
        //Operator
        $operator = $this->operator;
        $data['operator_name_chi'] = ($operator) ? $operator->name_chi : null;
        $data['operator_name_eng'] = ($operator) ? $operator->name_eng : null;
        $data['operator_color'] = ($operator) ? $operator->color : null;
        //Line Group
        $lineGroup = $this->lineGroup;
        $data['line_group_name_chi'] = ($lineGroup) ? $lineGroup->name_chi : null;
        $data['line_group_name_eng'] = ($lineGroup) ? $lineGroup->name_eng : null;
        //Stations
        $data['station_count'] = Line_Station::where('line_id', $this->id)->where('isDeleted', false)->count();
        return (object) $data;
    }

    /**
     * Custom Methods
     */


}
