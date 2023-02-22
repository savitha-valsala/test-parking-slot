<?php

namespace App\Http\Middleware;
use Illuminate\Http\Request;

use Closure;

class checkHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        
        if(strtolower($request->header('Content-Type'))!= "application/json"){
            $error = 'UnAuthorized Access - Header Failed';           
           $res =json_encode(["Message"=>$error  ,"status"=>"Error"]);
           return response($res );
         }
        return $next($request);
    }
}
