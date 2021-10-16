<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use App\Models\Line;
use App\Models\Station;

class Line_Station extends Model{

    protected $table = 'lines_stations';
    protected $primaryKey = 'id_auto';
    protected $hidden = ['created_at', 'updated_at', 'id_auto', 'isDeleted'];

    //Fields Modifiable by PATCH / POST
    protected $fillable = [
        'sort', 'line_id', 'station_id',
        'show_arrival', 'no_tracks', 'segments',
        'distance_km', 'mileage_km', 'max_speed_kph', 'additional_time',
        'remarks', 'other_info',
    ];
    
    //JSON fields
    protected $casts = [
        'segments' => 'object',
        'other_info' => 'object',
        'additional_time' => 'object',
        'distance_km' => 'float',
        'mileage_km' => 'float',
    ];

    //Data validations
    public static $validations_update = [
        'sort' => 'integer',
        'line_id' => 'exists:lines,id',
        'station_id' => 'exists:stations,id',
        'show_arrival' => 'boolean',
        'no_tracks' => 'integer',
        'segments' => 'json',
        'distance_km' => 'numeric',
        'mileage_km' => 'numeric',
        'max_speed_kph' => 'integer',
        'additional_time' => 'json',
        'other_info' => 'json',
    ];
    public static $validations_new = [
        'sort' => 'integer',
        'line_id' => 'required|exists:lines,id',
        'station_id' => 'required|exists:stations,id',
        'show_arrival' => 'boolean',
        'no_tracks' => 'integer',
        'segments' => 'json',
        'distance_km' => 'numeric',
        'mileage_km' => 'numeric',
        'max_speed_kph' => 'integer',
        'additional_time' => 'json',
        'other_info' => 'json',
    ];

    //Default Limit
    public static $limit_default = 50;

    //Filters
    public static function filters($query, $param){
    }
    
    //Sortings
    public static $sort_default = 'line_id,sort';
    public static $sortable = ['sort', 'line_id', 'station_id', 'mileage_km'];

    //Resource Relationships
    public function line(){
        return $this->belongsTo(Line::class, 'line_id', 'id')->where('isDeleted', false);
    }
    public function station(){
        return $this->belongsTo(Station::class, 'station_id', 'id')->where('isDeleted', false);
    }

    //Display data returned for GET
    public function displayData($request){
        $data = clone $this;
        //"station" -> Get also station
        //"line" -> Get also line
        //"more" -> Both
        if ($request->input('station') || $request->input('more')){
            $data->station = $this->station;
        }
        if ($request->input('line') || $request->input('more')){
            $data->line = $this->line;
        }
        return $data;
    }

    /**
     * Custom Methods
     */


}
