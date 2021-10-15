<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use App\Models\OperatorType;

class Operator extends Model{

    protected $table = 'operators';
    protected $primaryKey = 'id_auto';
    protected $hidden = ['created_at', 'updated_at'];

    //Fields Modifiable by PATCH / POST
    protected $fillable = [
        'operator_type_id',
        'name_chi', 'name_chi_short', 'name_eng', 'name_eng_short',
        'color', 'color_text', 'is_passenger_hr', 'logo_fileid',
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
    public function operatorType(){
        return $this->belongsTo(OperatorType::class, 'operator_type_id', 'id');
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
