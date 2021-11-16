<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use App\Models\Line;
use App\Models\Station;
use App\Models\VehiclePerformanceItem;

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
        'max_speed_kph' => 'nullable|integer',
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
        'max_speed_kph' => 'nullable|integer',
        'additional_time' => 'json',
        'other_info' => 'json',
    ];

    //Default Limit
    public static $limit_default = 50;

    //Filters
    public static function filters($query, $param){
        switch ($query){
            case 'line_id':
            return ['query' => 'line_id = ?', 'params' => [$param]];
            case 'station_id':
            return ['query' => 'station_id = ?', 'params' => [$param]];
        }
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

    //Get Line-Station Items given Line ID, Station IDs (start/end), Direction
    public static function getLineStationItems($line_id, $station1_id, $station2_id, $is_upbound){
        $ls_items_all = self::where('line_id', $line_id)->where('isDeleted', false)->orderBy('sort', 'asc')->get();
        if ($is_upbound){
            $station_upper = $station2_id;
            $station_lower = $station1_id;
        }else{
            $station_upper = $station1_id;
            $station_lower = $station2_id;
        }
        //Find station_upper_index
        $station_upper_index = null;
        foreach ($ls_items_all as $i => $ls_item){
            if (($ls_item->station_id ?? null) == $station_upper){
                $station_upper_index = $i + 1;
                break;
            }
        }
        if ($station_upper_index === null) return null;
        //Find station lower_index
        $station_lower_index = null;
        foreach ($ls_items_all as $i => $ls_item){
            if (($ls_item->station_id ?? null) == $station_lower && $i >= $station_upper_index){
                $station_lower_index = $i;
                break;
            }
        }
        if ($station_lower_index === null) return null;
        //Push relevant data to results array
        $result = [];
        for ($i = $station_upper_index; $i <= $station_lower_index; $i++){
            $item = $ls_items_all[$i];
            //Manipulate Data
            unset($item->segments);
            $item->station1_id = $ls_items_all[$i-($is_upbound ? 0 : 1)]->station_id ?? null;
            $item->station2_id = $ls_items_all[$i-($is_upbound ? 1 : 0)]->station_id ?? null;
            //Push to Results
            array_push($result, $item);
        }
        //If is_upbound, reverse results
        if ($is_upbound){
            $result = array_reverse($result);
        }
        return $result;
        
    }

}
