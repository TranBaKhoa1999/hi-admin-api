<?php

namespace App\Http\Middleware;
use Closure;

class GatewayAuthenticationMiddleware {
    private $_secretKey = 'hiadminapi_2022'; 
    
    public function handle($request, Closure $next) {

        $lang = null;
        $dataResponse = null;
        $accessToken    = md5($this->_secretKey . '::' . $this->_secretKey .date('Y-d-m'));

        if($request->lang){
            $lang = $request->lang;
        }
        $token = $request->header("Authorization");
        if (isset($token) && $accessToken == $token ){
            return $next($request);
        }
        if(env("APP_ENV")=="local" || env("APP_ENV")=="staging"){
            $dataResponse = $accessToken;
        }
        return printJson($dataResponse, buildStatusObject('UNAUTHORIZED'), $lang);
    }
}
