<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MapEditorSettings extends Model{

    protected $table = 'map_editor_settings';
    protected $primaryKey = 'id_auto';
    protected $hidden = ['created_at', 'updated_at', 'id_auto', 'isDeleted'];

    //Fields Modifiable by PATCH / POST
    protected $fillable = [
        'lock_stations', 'hide_stations',
        'line_groups_locked', 'line_groups_hidden', 'line_groups_collapsed',
        'line_locked', 'line_hidden',
    ];
    
    //JSON fields
    protected $casts = [
        'line_groups_locked' => 'array',
        'line_groups_hidden' => 'array',
        'line_groups_collapsed' => 'array',
        'line_locked' => 'array',
        'line_hidden' => 'array',
    ];

    //Data validations
    public static $validations_update = [
        'lock_stations' => 'boolean',
        'hide_stations' => 'boolean',
        'line_groups_locked' => 'json',
        'line_groups_hidden' => 'json',
        'line_groups_collapsed' => 'json',
        'line_locked' => 'json',
        'line_hidden' => 'json',
    ];
    public static $validations_new = [
        'lock_stations' => 'boolean',
        'hide_stations' => 'boolean',
        'line_groups_locked' => 'json',
        'line_groups_hidden' => 'json',
        'line_groups_collapsed' => 'json',
        'line_locked' => 'json',
        'line_hidden' => 'json',
    ];

    //Filters
    public static function filters($query, $param){
    }
    
    //Sortings
    public static $sort_default = 'id';
    public static $sortable = [];

    //Resource Relationships

    //Display data returned for GET
    public function displayData($request){
        
    }

    /**
     * Custom Methods
     */


}
