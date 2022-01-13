<?php

namespace App\Http\Controllers;
use Exception;
use Illuminate\Support\Facades\Request;
class ExampleController extends MY_Controller

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
        }catch(Exception $e){
            $statusObject = buildStatusObject($e->getMessage());
        }
        return printJson($result, $statusObject);
    }
}
