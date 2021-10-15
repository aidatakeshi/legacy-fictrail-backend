<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use App\Models\Line;
use App\Models\Station;

class Line_Station extends Model{

    protected $table = 'lines_stations';
    protected $primaryKey = 'id_auto';
    protected $hidden = ['created_at', 'updated_at'];

    //Fields Modifiable by PATCH / POST
    protected $fillable = [
        'line_id', 'station_id',
        'distance_km', 'mileage_km',
        'show_arrival', 'no_tracks', 'segments',
        'max_speed_kph', 'additional_time',
        'remarks', 'other_info',
    ];

    //Data validations
    public static $validations_update = [

    ];
    public static $validations_new = [

    ];

    //Filters
    public static function filters($param){
    }
    
    //Sortings
    public static $sort_default = 'sort';
    public static $sortable = [];

    //Resource Relationships
    public function line(){
        return $this->belongsTo(Line::class, 'line_id', 'id');
    }
    public function station(){
        return $this->belongsTo(Station::class, 'station_id', 'id');
    }

    //Additional data returned for GET
    public function getAdditionalData($request){
        return [

        ];
    }

    //Additional processing of data
    public function whenGet($request){

    }
    public function whenSet($request){
        
    }
    public function whenCreated($request){

    }
    public function whenRemoved($request){

    }

    /**
     * Custom Methods
     */


}
