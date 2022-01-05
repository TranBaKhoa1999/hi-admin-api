<?php
if (!function_exists('printJson')) {
     function printJson($data, $statusObject){
        $response = [];
        $response['statusCode'] = $statusObject->code;
        $response['message'] = $statusObject->message;
        $response['data'] = $data;
        return response()->json($response);
    }
}
if (!function_exists('buildStatusObject')) {
    function buildStatusObject($status){
        $statusCodeObject = app('statusCodeObjectClass')->getObject($status);
        return $statusCodeObject;
    }
}