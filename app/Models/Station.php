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

    //Data validations
    public static $validations_update = [

    ];
    public static $validations_new = [

    ];

    //Filters
    public static function filters($query, $param){
    }
    
    //Sortings
    public static $sort_default = 'sort';
    public static $sortable = [];

    //Resource Relationships
    public function operator(){
        return $this->belongsTo(Operator::class, 'operator_id', 'id');
    }
    public function prefecture(){
        return $this->belongsTo(Prefecture::class, 'prefecture_id', 'id');
    }

    //Display data returned for GET
    public function displayData($request){
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
