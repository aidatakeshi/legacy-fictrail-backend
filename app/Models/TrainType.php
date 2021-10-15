<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use App\Models\TrainName;

class TrainType extends Model{

    protected $table = 'train_types';
    protected $primaryKey = 'id_auto';
    protected $hidden = ['created_at', 'updated_at'];

    //Fields Modifiable by PATCH / POST
    protected $fillable = [
        'sort', 'operator_id',
        'name_chi', 'name_chi_short', 'name_eng', 'name_eng_short',
        'color', 'color_text', 'is_premium',
        'remarks', 'other_info',
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
    public function trainNames(){
        return $this->hasMany(TrainName::class, 'train_type_id', 'id');
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
