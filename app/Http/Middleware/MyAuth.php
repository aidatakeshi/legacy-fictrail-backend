<?php

namespace App\Http\Middleware;
use Closure;

use App\Models\User;
use App\Models\UserLoginSession;

class MyAuth{

    public function handle($request, Closure $next){

        //If token not found -> 403
        $bearer_token = $request->bearerToken();
        if ($bearer_token == null){
            return response()->json(['error' => 'Token Not Found'], 403);
        }

        //If invalid token -> 403
        $session = UserLoginSession::where('bearer_token', $bearer_token)->first();
        if ($session == null){
            return response()->json(['error' => 'Token Incorrect'], 403);
        }

        //Check if last_activity_time exceeds
        if ((time() - $session->last_activity_time) >= intval(env('TOKEN_EXPIRATION_INACTIVITY'))){
            $session->delete();
            return response()->json(['error' => 'Token Expired'], 403);
        }

        //Update last_activity_time
        $session->last_activity_time = time();
        $session->save();

        //Pass data
        $request->bearer_token = $bearer_token;
        $request->user = $session->user;

        return $next($request);

    }
    
}
