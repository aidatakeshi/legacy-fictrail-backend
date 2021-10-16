<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use App\Models\VehiclePerformanceItem;

class VehiclePerformanceGroup extends Model{

    protected $table = 'vehicle_performance_groups';
    protected $primaryKey = 'id_auto';
    protected $hidden = ['created_at', 'updated_at', 'id_auto', 'isDeleted'];

    //Fields Modifiable by PATCH / POST
    protected $fillable = [
        'sort',
        'name_chi', 'name_eng',
        'remarks', 'other_info',
    ];
    
    //JSON fields
    protected $casts = [
        'other_info' => 'object',
    ];

    //Data validations
    public static $validations_update = [
        'sort' => 'integer',
        'other_info' => 'json',
    ];
    public static $validations_new = [
        'sort' => 'integer',
        'name_chi' => 'required',
        'name_eng' => 'required',
        'other_info' => 'json',
    ];

    //Filters
    public static function filters($query, $param){
        switch ($query){
            case 'name_chi':
            return ['query' => 'LOWER(name_chi) LIKE LOWER(?)', 'params' => ["%$param%"]];
            case 'name_eng':
            return ['query' => 'LOWER(name_eng) LIKE LOWER(?)', 'params' => ["%$param%"]];
        }
    }
    
    //Sortings
    public static $sort_default = 'sort';
    public static $sortable = ['sort', 'name_chi', 'name_eng'];

    //Resource Relationships
    public function items(){
        return $this->hasMany(VehiclePerformanceItem::class, 'group_id', 'id');
    }

    //Display data returned for GET
    public function displayData($request){
        $data = clone $this;
        //"more" -> Get also items as well (limited fields)
        if ($request->input('more')){
            $data->items = $this->items()
            ->selectRaw('id, sort, name_chi, name_eng, remarks, max_speed_kph, max_accel_kph_s')
            ->orderBy('sort', 'asc')->orderBy('id', 'asc')->get();
        }
        return $data;
    }

    /**
     * Custom Methods
     */


}
