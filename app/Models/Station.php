<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use App\Models\Operator;
use App\Models\Prefecture;

class Station extends Model{

    protected $table = 'stations';
    protected $primaryKey = 'id_auto';
    protected $hidden = ['created_at', 'updated_at', 'id_auto', 'isDeleted'];

    //Fields Modifiable by PATCH / POST
    protected $fillable = [
        'operator_id', 'prefecture_id',
        'name_chi', 'name_eng',
        'x', 'y', 'height_m',
        'tracks', 'track_cross_points',
        'major', 'is_signal_only', 'is_abandoned',
        'remarks', 'other_info',
    ];
    
    //JSON fields
    protected $casts = [
        'tracks' => 'array',
        'track_cross_points' => 'array',
        'other_info' => 'object',
    ];

    //Data validations
    public static $validations_update = [
        'operator_id' => 'exists:operators,id',
        'prefecture_id' => 'exists:prefectures,id',
        'x' => 'numeric',
        'y' => 'numeric',
        'height_m' => 'nullable|integer',
        'tracks' => 'json',
        'track_cross_points' => 'json',
        'major' => 'boolean',
        'is_signal_only' => 'boolean',
        'is_abandoned' => 'boolean',
        'other_info' => 'json',
    ];
    public static $validations_new = [
        'operator_id' => 'required|exists:operators,id',
        'prefecture_id' => 'required|exists:prefectures,id',
        'name_chi' => 'required',
        'name_eng' => 'required',
        'x' => 'numeric',
        'y' => 'numeric',
        'height_m' => 'integer',
        'tracks' => 'json',
        'track_cross_points' => 'json',
        'major' => 'boolean',
        'is_signal_only' => 'boolean',
        'is_abandoned' => 'boolean',
        'other_info' => 'json',
    ];

    //Default Limit
    public static $limit_default = 50;

    //Filters
    public static function filters($query, $param){
        switch ($query){
            case 'prefecture_id':
            return ['query' => 'prefecture_id = ?', 'params' => [$param]];
            case 'operator_id':
            return ['query' => 'operator_id = ?', 'params' => [$param]];

            case 'is_signal_only':
            return ['query' => 'is_signal_only = TRUE', 'params' => []];
            case 'not_signal_only':
            return ['query' => 'is_signal_only = FALSE', 'params' => []];

            case 'major':
            return ['query' => 'major = TRUE', 'params' => []];
            case 'minor':
            return ['query' => 'major = FALSE', 'params' => []];

            case 'is_abandoned':
            return ['query' => 'is_abandoned = TRUE', 'params' => []];
            case 'not_abandoned':
            return ['query' => 'is_abandoned = FALSE', 'params' => []];

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
    public static $sort_default = 'sort';
    public static $sortable = ['name_chi', 'name_eng', 'prefecture_id', 'operator_id'];

    //Resource Relationships
    public function operator(){
        return $this->belongsTo(Operator::class, 'operator_id', 'id')->where('isDeleted', false);
    }
    public function prefecture(){
        return $this->belongsTo(Prefecture::class, 'prefecture_id', 'id')->where('isDeleted', false);
    }

    //Display data returned for GET
    public function displayData($request){
        $data = clone $this;
        //"more" -> Get also operator & prefecture
        if ($request->input('more')){
            $data->operator = $this->operator;
            $data->prefecture = $this->prefecture;
        }
        //"list" -> For listing
        if ($request->input('list')){
            $operator = $this->operator;
            $data->operator_name_chi = $operator ? $operator->name_chi : null;
            $data->operator_name_eng = $operator ? $operator->name_eng : null;
            $data->operator_color = $operator ? $operator->color : null;
            $prefecture = $this->prefecture;
            $data->prefecture_name_chi = $prefecture ? $prefecture->name_chi : null;
            $data->prefecture_name_chi_suffix = $prefecture ? $prefecture->name_chi_suffix : null;
            $data->prefecture_name_eng = $prefecture ? $prefecture->name_eng : null;
            $data->prefecture_name_eng_suffix = $prefecture ? $prefecture->name_eng_suffix : null;
            $data->track_count = count($data->tracks);
        }
        //"line" -> Get also lines
        if ($request->input('lines')){
            $data->lines = Line_Station::where('station_id', $this->id)->where('isDeleted', false)->get();
        }
        return $data;
    }

    /**
     * Custom Methods
     */


}
