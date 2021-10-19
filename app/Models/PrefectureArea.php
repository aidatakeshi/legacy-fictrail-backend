<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use App\Models\Prefecture;

class PrefectureArea extends Model{

    protected $table = 'prefecture_areas';
    protected $primaryKey = 'id_auto';
    protected $hidden = ['created_at', 'updated_at', 'id_auto', 'isDeleted'];

    //Fields Modifiable by PATCH / POST
    protected $fillable = [
        'sort', 'name_chi', 'name_eng', 'remarks', 'other_info',
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
    }
    
    //Sortings
    public static $sort_default = 'sort';
    public static $sortable = ['sort', 'name_chi', 'name_eng'];

    //Resource Relationships
    public function prefectures(){
        return $this->hasMany(Prefecture::class, 'area_id', 'id')->where('isDeleted', false);
    }

    //Display data returned for GET
    public function displayData($request){
        $data = clone $this;
        //"from_selecter" -> Only essential fields for selecter
        if ($request->input("from_selecter")){
            $data = (object)[
                "id" => $data->id,
                "name_chi" => $data->name_chi,
                "name_eng" => $data->name_eng,
            ];
        }
        //"more" -> Get also prefectures as well
        if ($request->input('more')){
            $query = $this->prefectures()->orderBy('sort', 'asc');
            $data->prefectures = $query->get();
            foreach ($data->prefectures as $i => $item){
                $data->prefectures[$i]->name_chi_full = $item->name_chi . $item->name_chi_suffix;
                $data->prefectures[$i]->name_eng_full = $item->name_eng .' '. $item->name_eng_suffix;
                if ($request->input("from_selecter")){
                    $data->prefectures[$i] = (object)[
                        'id' => $data->prefectures[$i]->id,
                        'name_chi_full' => $data->prefectures[$i]->name_chi_full,
                        'name_eng_full' => $data->prefectures[$i]->name_eng_full,
                    ];
                }
            }
        }
        return $data;
    }

    /**
     * Custom Methods
     */


}
