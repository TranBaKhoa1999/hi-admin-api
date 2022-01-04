<?php
namespace App\Http\Controllers;
class StatusCodeObject{

    const HTTP_OK = [
        'Code' => 0,
        'Message' => 'Thành Công',
    ];

    const HTTP_FAIL = [
        'Code' => -1,
        'Message' => 'Thất Bại',
    ];

    const INVALID_INPUT = [
        'Code' => -1,
        'Message' => 'Đầu vào không hợp lệ',
    ];

    public static function getObject($status){
        return (object)constant("self::".$status);
    }
}