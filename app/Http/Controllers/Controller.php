<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends MY_Controller
{
    //
    public function __construct()
    {
        parent::__construct();
    }

    public function test(Request $request){
        $result = [];
        $statusObject = null;
        try{
            $result = [
                'test' => date('d-m-Y h:i:s')
            ];
            $statusObject = $this->buildStatusObject('HTTP_OK');
            // throw new Exception('INVALID_INPUT');
        }catch(Exception $e){
            $statusObject = $this->buildStatusObject($e->getMessage());
        }
        return $this->printJson($result, $statusObject);
    }
}
