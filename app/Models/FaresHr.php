<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FaresHr extends Model{

    protected $table = 'fares_hr';
    protected $primaryKey = 'id_auto';
    protected $hidden = ['created_at', 'updated_at', 'id_auto', 'isDeleted'];

    //Fields Modifiable by PATCH / POST
    protected $fillable = [
        'version', 'remarks', 'data',
    ];

    //Data validations
    public static $validations_update = [
        'data' => 'json',
    ];
    public static $validations_new = [
        'version' => 'required',
        'data' => 'json',
    ];
    
    //JSON fields
    protected $casts = [
        'data' => 'object',
    ];

    //Filters
    public static function filters($query, $param){
    }
    
    //Sortings
    public static $sort_default = 'id';
    public static $sortable = ['id', 'version'];

    //Resource Relationships

    //Display data returned for GET
    public function displayData($request){

    }

    /**
     * Custom Methods
     */


}
