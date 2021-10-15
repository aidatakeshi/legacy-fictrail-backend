<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\UserLoginSession;

class UserAccountController extends Controller{

    public function __construct(){}

    /**
     * POST /login (user, password)
     */
    public function login(Request $request){
        $username = $request->input('user');
        $password = $request->input('password');
        
        //Missing input -> 400
        if ($username == null || $password == null){
            return response()->json(["error" => "Credentials Missing"], 400);
        }

        //User not found -> 401
        $user = User::where('user', $username)->first();
        if ($user == null){
            return response()->json(["error" => "User Not Found"], 401);
        }

        //Wrong password -> 401
        if(!Hash::check($password, $user->password)){
            return response()->json(["error" => "Password Incorrect"], 401);
        }

        //Prepare token
        $bearer_token = base64_encode(Str::random(env('BEARER_TOKEN_LENGTH')));

        //Create session
        $session = new UserLoginSession;
        $session->user = $username;
        $session->bearer_token = $bearer_token;
        $session->login_time = time();
        $session->last_activity_time = time();
        $session->save();

        //Return 200
        return response()->json([
            'bearer_token' => $bearer_token,
        ]);
    }

    /**
     * POST /logout
     */
    public function logout(Request $request){
        $session = UserLoginSession::where('bearer_token', $request->bearer_token)->first();
        $session->delete();
        return response()->json((object)[]);
    }

    /**
     * POST /change-password (password_old, password_new)
     */
    public function changePassword(Request $request){

        //Check request
        $password_old = $request->input('password_old');
        $password_new = $request->input('password_new');

        if (!$password_old || !$password_new){
            return response()->json(["error" => "Password Missing"], 400);
        }

        //Check if old password correct
        $user = User::where('user', $request->user)->first();
        if (!Hash::check($password_old, $user->password)){
            return response()->json(["error" => "Old Password Incorrect"], 400);
        }

        //Proceed
        $user->password = Hash::make($password_new);
        $user->save();
        return response()->json((object)[]);
        
    }

    /**
     * GET /myself
     */
    public function getMyself(Request $request){

        $session = UserLoginSession::where('bearer_token', $request->bearer_token)->first();
        if (!$session) return response()->json((object)[], 400);
        $user = User::where('user', $session->user)->first();
        if (!$user) return response()->json((object)[], 400);
        return $user;
        
    }

}
