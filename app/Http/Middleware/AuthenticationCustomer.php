<?php

namespace App\Http\Middleware;

use Closure;
use App\Core\Entities\CentralizedSession;
class AuthenticationCustomer {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle($request, Closure $next) {

        // $json = [
        //             'name'         => 'handle-api-report-create-v9',
        //             'contracNo'    => '',
        //             'function'     => 'render',
        //             'date_created' => date('Y-m-d'),
        //             'input'        => json_encode($request->all()),
        //             'output'        => ""
        // ];
        // $kafka = new Kafka();
        // $kafka->producer(env('KAFKA_TOPIC_NAME'), json_encode($json));


       // if(env('APP_ENV') == 'local'){
       //     return $next($request);
       // }
        
        //check AccessToken and Signature
        $arrErrorCode = [
            'conect_redis_failed'   => 66403,    //Conect redis failed
            'signature_failed'      => 10404,    //Signature failed
            'accesstoken_falied'    => 10403,    //AccessToken failed   => login lại
            'success'               => 10403,    //Redis exprired       => login lại
        ];
        $accessToken = $request->bearerToken();

        $signatureData = [
            'signature' => $request->headers->get('Signature'),
            'params'    => $request->all(),
        ];

        if (isset($signatureData['params']['appVersion'])) {
            $request->request->set('appVersion', $signatureData['params']['appVersion']);
            unset($signatureData['params']['appVersion']) ;
        }
        if (isset($signatureData['params']['lang'])) {
            $request->request->set('lang', $signatureData['params']['lang']);
            unset($signatureData['params']['lang']) ;
        }
        if (isset($signatureData['params']['apiVersion'])) {
            $request->request->set('apiVersion', $signatureData['params']['apiVersion']);
            unset($signatureData['params']['apiVersion']) ;
        }


        $centralizedSession = new CentralizedSession();
        $session = $centralizedSession->validate($accessToken);
        if($session['error'] != 'success'){
            $errorCode = $arrErrorCode[$session['error']];
            if(env('APP_ENV') != 'production'){
                return response()->json(['statusCode' => $errorCode, 'message' => $centralizedSession->getErrorMessage(), 'data' => null]);
            }
            return response()->json(['statusCode' => $errorCode, 'message' => 'unauthorized', 'data' => null]);
        }else if($session['data'] == null){
            $errorCode = $arrErrorCode['success'];
            return response()->json(['statusCode' => $errorCode, 'message' => $centralizedSession->getErrorMessage(), 'data' => null]);
        }

        $session = $session['data'];
        $appIp = $request->headers->get('X-Forwarded-App-Ip') ?? '127.0.0.1';
        $request->request->set('listContractHiFPT', $session['listContract']);
        $request->request->set('customerId',        $session['customerId']);
        $request->request->set('sessionPhone',      $session['phone']);
        $request->request->set('provider',          $session['provider']);
        $request->request->set('ipAddress',         $appIp);
        
        $route      = $request->route();
        
        $appVersion = $request->headers->get('App-Version') ?? '';
        $routeAs    = isset($route[1]['as']) ? $route[1]['as'] : '';
        $logAll     = $this->buildParamsLogs($session, $appIp, $appVersion, $request->fullUrl(), $routeAs, $request->all());
        $request->request->set('logAll', $logAll);
        $request->request->set('myActionName', '');
        //print_r($request->all());die;
        return $next($request);
        
    }

    function buildParamsLogs($session, $ipAaddress, $appVersion, $fullUrl, $routeAs, $body){
        //print_r($body);die;
        
        $functionName   = '';
        $arrRouteAs     = explode('.', $routeAs);
        $n = count($arrRouteAs) - 1;
        if($n >= 0){
            $functionName = $arrRouteAs[$n];
        }
        $infoContract = $this->getInfoContract($body);
        
        return [
            'phone'         => $session['phone']            ?? '',
            'customerId'    => $session['customerId']       ?? '',
            'ipAaddress'    => $ipAaddress,
            'isCanhTo'      => $session['isCanhTo']         ?? '',
            'isCustomer'    => $session['isCustomerFpt']    ?? '',
            'provider'      => $session['provider']         ?? '',
            'deviceId'      => $session['deviceId']         ?? '',
            'devicePlatform'=> $session['devicePlatform']   ?? '',
            'appVersion'    => $appVersion,
            'contractNo'    => $infoContract['contractNo']?? '',
            'locationZone'  => $infoContract['locationZone']?? '',
            'locationCode'  => $infoContract['locationCode']?? '',
            'branchName'    => $infoContract['locationCode']?? '',
            'lang'          => $body['lang']?? 'vi',
            'status'        => '',
            'note'          => '',
            'serviceName'   => 'hi-report',
            'functionName'  => $functionName,
            'actionName'    => '',
            'dateAction'    => date('Y-m-d H:i:s'),
            'url'           => $fullUrl,
            'positionIcon'  => '',
            'referer'       => '',
            'typeLog'       => 'Api',
            'processTime'   => microtime(true)
        ];
    }

    function getInfoContract($body){

        if(isset($body['contractId']) && !empty($body['contractId'])){
            foreach ($body['listContractHiFPT'] as $key => $value) {
               if($body['contractId'] == $value['contractId']){
                    return $value;
               }
            }
        }

        if(isset($body['contractNo']) && !empty($body['contractNo'])){
            foreach ($body['listContractHiFPT'] as $key => $value) {
               if($body['contractNo'] == $value['contractNo']){
                    return $value;
               }
            }
        }
        
        return [];        
        
    }
}
