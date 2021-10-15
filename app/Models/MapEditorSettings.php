<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MapEditorSettings extends Model{

    protected $table = 'map_editor_settings';
    protected $primaryKey = 'id_auto';
    protected $hidden = ['created_at', 'updated_at'];

    //Fields Modifiable by PATCH / POST
    protected $fillable = [
        'lock_stations', 'hide_stations',
        'line_groups_locked', 'line_groups_hidden', 'line_groups_collapsed',
        'line_locked', 'line_hidden',
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
