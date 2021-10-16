<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class User extends Model{

    protected $table = 'users';
    protected $primaryKey = 'id_auto';

    protected $fillable = [];
    
    protected $hidden = ['password', 'created_at', 'updated_at', 'id_auto', 'isDeleted'];

}
