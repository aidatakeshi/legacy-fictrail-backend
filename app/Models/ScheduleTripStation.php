<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ScheduleTripStation extends Model{

    protected $table = 'schedule_trip_stations';
    protected $primaryKey = 'id_auto';
    protected $hidden = ['created_at', 'updated_at'];

    //Fields Modifiable by PATCH / POST
    protected $fillable = [

    ];

    //Data validations
    public static $validations_update = [

    ];
    public static $validations_new = [

    ];

    //Filters
    public static $filters = [

    ];
    
    //Sortings
    public static $sorting = [

    ];

    //Resource Relationships

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
