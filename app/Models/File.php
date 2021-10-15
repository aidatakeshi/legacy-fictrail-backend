<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class File extends Model{

    protected $table = 'files';
    protected $primaryKey = 'id_auto';

    protected $fillable = [];
    
    protected $hidden = ['created_at', 'updated_at'];

}
