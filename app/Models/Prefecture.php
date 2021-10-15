<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use App\Models\PrefectureArea;

class Prefecture extends Model{

    protected $table = 'prefectures';
    protected $primaryKey = 'id_auto';
    protected $hidden = ['created_at', 'updated_at'];

    //Fields Modifiable by PATCH / POST
    protected $fillable = [
        'area_id', 'sort',
        'name_chi', 'name_chi_suffix', 'name_chi_short',
        'name_eng', 'name_eng_suffix', 'name_eng_short',
        'remarks', 'other_info',
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
    public static function filters($param){
        case 'area_id':
        return ['query' => 'area_id = ?', 'params' => []];
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
        return $this->belongsTo(PrefectureArea::class, 'area_id', 'id');
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
