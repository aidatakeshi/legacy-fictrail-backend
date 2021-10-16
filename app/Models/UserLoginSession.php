<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class UserLoginSession extends Model{

    protected $table = 'user_login_sessions';
    protected $primaryKey = 'id_auto';

    protected $fillable = [];
    
    protected $hidden = ['bearer_token', 'created_at', 'updated_at', 'id_auto', 'isDeleted'];

}
