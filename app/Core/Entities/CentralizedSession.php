<?php
namespace App\Core\Entities;

use Illuminate\Support\Facades\Redis;
class CentralizedSession {
    
    private $redis = null;
    private $error = null;
    private const PREFIX  = 'session:';
    
    public function __construct() {
        $redis = Redis::connection('centralized');
        return $this->redis = $redis;
    }
    public function getErrorMessage(){
        return $this->error;
    }
    
    /**
     * @return 
     *      error : accesstoken_falied      => AccessToken failed
     *      error : signature_failed        => Signature failed
     *      error : conect_redis_failed     => Conect redis failed
     *      data  : data|null               => data in db redis
     */
    public function validate($_accessToken, $_signatureData = null){
        
        $result = [
            'error' => 'accesstoken_falied',
            'data'  => null
        ];
        $jwt = explode('.', $_accessToken);
        if(isset($jwt[1]) == false){
            $this->error = 'AccessToken is not defined correctly';
            return $result;
        }
        
        
        $payload = strtr($jwt[1], '-_', '+/');
        $payload = json_decode(base64_decode($payload), true);
        if( isset($payload['jti']) == false ){
            $this->error = 'AccessToken is not defined correctly';
            return $result;
        }
        $session = $this->get($payload['jti']);
//        dd($_accessToken, $payload);
        if($session == -1 ){
            $result['error'] = 'conect_redis_failed';
            return $result;
        }else if($session == null){
            $result['error'] = 'success';
            return $result;
        }
        
        //check Signature
        if($_signatureData != null){
            $_signature = $_signatureData['signature'];
            $_params    = $_signatureData['params'];
            unset($_params['lang']);
            $signature = sha1($session['clientId'] . $session['codeVerifier'] . $_accessToken . json_encode((object)$_params));
            if($_signature != $signature){
                $this->error = $signature;
                $result['error'] = 'signature_failed';
                return $result;
            }
        }
        
        $result['error'] = 'success';
        $result['data']  = $session;
        return $result;
    }
    private function get($key){
        if($this->redis === null){
            return -1;
        }
        $keySession = self::PREFIX . $key;
        try {
            $value = $this->redis->executeRaw(['GET', $keySession]);
            if($value == null){
                return null;
            }
            return unserialize($value);
        } catch (\Exception $exc) {
            $this->error = $exc->getMessage();
        }
        return -1;  
    }
    
}
