<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class MY_Controller extends BaseController
{
    public function __construct()
    {

    }

    public function buildStatusObject($status){
        $statusCodeClass = new StatusCodeObject();
        $statusCodeObject = $statusCodeClass->getObject($status);
        return $statusCodeObject;

    }
    public function printJson($data, $statusObject){
        $response = [];
        $response['StatusCode'] = $statusObject->Code;
        $response['Message'] = $statusObject->Message;
        $response['Data'] = $data;
        return response()->json($response);
    }
}
