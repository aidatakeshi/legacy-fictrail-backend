<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TestItem extends Model{

    protected $table = 'test_items';
    protected $primaryKey = 'id_auto';
    protected $hidden = ['created_at', 'updated_at', 'id_auto', 'isDeleted'];

    //Fields Modifiable by PATCH / POST
    protected $fillable = [
        'sort', 'my_text', 'my_int', 'my_float', 'my_json', 'my_bool',
    ];
    
    //JSON fields
    protected $casts = [
        'my_json' => 'object',
    ];

    //Data validations
    public static $validations_update = [
        'sort' => 'integer',
        'my_int' => 'integer',
        'my_float' => 'numeric',
        'my_bool'=> 'boolean',
        'my_json' => 'json',
    ];
    public static $validations_new = [
        'sort' => 'integer',
        'my_text' => 'required',
        'my_int' => 'required|integer',
        'my_float' => 'required|numeric',
        'my_bool'=> 'boolean',
        'my_json' => 'json',
    ];

    //Filters
    public static function filters($query, $param){
    }
    
    //Sortings
    public static $sort_default = 'sort';
    public static $sortable = [
        'sort', 'my_text', 'my_int', 'my_float', 'my_bool',
    ];

    //Resource Relationships

    //Display data returned for GET
    public function displayData($request){
        $data = clone $this;
        //$data->my_int_square = $data->my_int * $data->my_int;
        //$data->yinyin = 'WZ5535';
        return $data;
    }

    //Additional processing of data
    public function whenGet($request){
        $this->readCount = $this->readCount + 1;
        $this->save();
    }
    public function whenSet($request){
        $this->updateCount = $this->updateCount + 1;
        $this->save();
    }
    public function whenCreated($request){
        $this->updateCount = $this->updateCount + 1;
        $this->save();
    }
    public function whenRemoved($request){
        $this->updateCount = $this->updateCount + 1;
        $this->save();
    }
    public function whenDuplicated($request){
        $this->updateCount = $this->updateCount + 1;
        $this->save();
    }

    /**
     * Custom Methods
     */


}
