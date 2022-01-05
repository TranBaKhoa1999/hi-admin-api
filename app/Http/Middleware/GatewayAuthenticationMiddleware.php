<?php

namespace App\Http\Middleware;

use App\MyCore\Services\Api;
use Closure;
use Illuminate\Support\Facades\Redis;

class GatewayAuthenticationMiddleware {
    private $_secretKey = 'e063d2833da02c8dac4cac106b825535';    //md5('hi-admin-api')

    public function handle($request, Closure $next) {
        $uri = $_SERVER['REQUEST_URI'];
        $uri = explode('/', $uri);

        $clientsKey = array(
            'PNC'        =>'d1t2nc92nwerh9u4oy11evol020i3107',
            'LinhNH5'    =>'4c9ce4a661601e9c6ac6a5d5627f582b', //md5('Authorization::LinhNH5')
            'CMSHiFPT'   =>'9895ee2f7616a73ab8be47e5df5a8924', // cấp cho Tô Ni
            'TuyenVTT3'  =>'ff26f4761111bd210cde4336eae8cbf4', // 
        );
        $clientType = array(
            'TuyenVTT3'  => 'superClient', 
            'CMSHiFPT'   => 'superClient', 
            'LinhNH5'    => 'superClient',
            'PNC'        => 'client'
        );
        $dataResponse = null;
        $clientKey = $request->header("clientKey");
        $key = array_search($clientKey, $clientsKey);
        if($key == false){
            return printJson(null, buildStatusObject('UNAUTHORIZED'));
        }        
        $request->request->add(['clientKey' => $key]);
        if(array_key_exists($key,$clientType)){
            $request->request->add(['clientType' => $clientType[$key]]);
        }else{
            $request->request->add(['clientType' => 'client']);
        }

        $accessToken    = md5($clientKey . '::' . $this->_secretKey .date('Y-d-m'));

        $token = $request->header("Authorization");
        if (isset($token) && $accessToken == $token ){
            return $next($request);
        }
        if(env("APP_ENV")=="local" || env("APP_ENV")=="staging"){
            $dataResponse = $accessToken;
        }
        return response()->json($dataResponse, buildStatusObject('UNAUTHORIZED'));
    }
}
