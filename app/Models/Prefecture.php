<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use App\Models\PrefectureArea;

class Prefecture extends Model{

    protected $table = 'prefectures';
    protected $primaryKey = 'id_auto';
    protected $hidden = ['created_at', 'updated_at', 'id_auto', 'isDeleted'];

    //Fields Modifiable by PATCH / POST
    protected $fillable = [
        'area_id', 'sort',
        'name_chi', 'name_chi_suffix', 'name_chi_short',
        'name_eng', 'name_eng_suffix', 'name_eng_short',
        'remarks', 'other_info',
    ];
    
    //JSON fields
    protected $casts = [
        'other_info' => 'object',
    ];

    //Data validations
    public static $validations_update = [
        'area_id' => 'exists:prefecture_area,id',
        'sort' => 'integer',
        'other_info' => 'json',
    ];
    public static $validations_new = [
        'area_id' => 'required|exists:prefecture_area,id',
        'sort' => 'integer',
        'name_chi' => 'required',
        'name_eng' => 'required',
        'other_info' => 'json',
    ];

    //Filters
    public static function filters($query, $param){
        switch ($query){
            case 'area_id':
            return ['query' => 'area_id = ?', 'params' => [$param]];
        }
    }
    
    //Sortings
    public static $sort_default = 'sort';
    public static $sortable = [
        'area_id', 'sort',
        'name_chi', 'name_chi_suffix', 'name_chi_short',
        'name_eng', 'name_eng_suffix', 'name_eng_short'
    ];

    //Resource Relationships
    public function prefectureArea(){
        return $this->belongsTo(PrefectureArea::class, 'area_id', 'id')->where('isDeleted', false);
    }

    //Display data returned for GET
    public function displayData($request){
        $data = clone $this;
        //Combine to name_chi_full, name_eng_full
        $data->name_chi_full = $data->name_chi . $data->name_chi_suffix;
        $data->name_eng_full = $data->name_eng .' '. $data->name_eng_suffix;
        //"from_selecter" -> Only essential fields for selecter
        if ($request->input("from_selecter")){
            $data = (object)[
                "id" => $data->id,
                "name_chi_full" => $data->name_chi_full,
                "name_eng_full" => $data->name_eng_full,
            ];
        }
        //"more" -> Get also prefecture area as well
        if ($request->input('more')){
            $data->prefectureArea = $this->prefectureArea;
        }
        return $data;
    }

    /**
     * Custom Methods
     */


}
