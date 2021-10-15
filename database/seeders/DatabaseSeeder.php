<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\MapEditorSettings;

class DatabaseSeeder extends Seeder{

    public function run(){

        //Map Editor Settings
        $me_settings = new MapEditorSettings;
        $me_settings->id = 'main';
        $me_settings->save();

        //Create default admin user
        $my_admin = new User;
        $my_admin->user = 'admin';
        $my_admin->password = Hash::make('Password123');
        $my_admin->save();
    }

}
