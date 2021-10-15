<?php
namespace App\Http\Middleware;

use Closure;

class MyCommon{
    
    public function handle($request, Closure $next){

        $response = $next($request);
        
        /**
         * Handle CORS
         */
        $headers = [
            'Access-Control-Allow-Origin'      => env('ACCESS_CONTROL_ALLOW_ORIGIN'),
            'Access-Control-Allow-Methods'     => 'POST, GET, OPTIONS, PUT, DELETE',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Max-Age'           => '86400',
            'Access-Control-Allow-Headers'     => 'Content-Type, Authorization, X-Requested-With',
            'Access-Control-Expose-Headers'    => 'Content-Disposition',
        ];

        if ($request->isMethod('OPTIONS')){
            return response()->json('{"method":"OPTIONS"}', 200, $headers);
        }
        foreach($headers as $key => $value){
            if (method_exists($response, 'header')){
                $response->header($key, $value);
            }
        }

        return $response;
    }
}