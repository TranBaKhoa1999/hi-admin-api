<?php
namespace App\Core;
class StatusCodeObject{

    const HTTP_OK = [
        'code' => 200,
        'message' => 'Thành Công',
    ];

    const INTERNAL_SERVER_ERROR = [
        'code' => 500,
        'message' => 'Thất Bại',
    ];

    const SERVICE_UNAVAILABLE = [
        'code' => 503,
        'message' => 'Hệ thống đang bảo trì'
    ];

    const INVALID_INPUT = [
        'code' => -1,
        'message' => 'Đầu vào không hợp lệ',
    ];

    const PAGE_NOT_FOUND = [
        'code' => 404,
        'message' => 'Trang không tồn tại'
    ];

    const UNAUTHORIZED = [
        'code' => 401,
        'message' => 'xác thực không thành công'
    ];
    
    const FORBIDDEN = [
        'code'  => 403,
        'message'  => 'Không có quyền truy cập'
    ];

    const METHOD_NOT_ALLOWED = [
        'code'  => 405,
        'message' => 'phương thức không hỗ trợ'
    ];

    const REQUEST_TIMEOUT = [
        'code' => 408,
        'message'   => 'thời gian phản hồi quá lâu'
    ];

    const GATEWAY_TIMEOUT =[
        'code' => 504,
        'message' => 'Gateway timeout'
    ];

    public static function getObject($status){
        return (object)constant("self::".$status);
    }
}