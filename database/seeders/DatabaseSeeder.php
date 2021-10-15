<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

class DatabaseSeeder extends Seeder{

    public function run(){

        //Create default admin user
        $my_admin = new User;
        $my_admin->user = 'admin';
        $my_admin->password = Hash::make('Password123');
        $my_admin->save();
    }

}
