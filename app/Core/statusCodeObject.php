<?php
namespace App\Core;
class StatusCodeObject{

    const HTTP_OK = [
        'code' => 0,
        'message' => 'Thành Công',
        'message_en' => 'Success!',
    ];

    const INTERNAL_SERVER_ERROR = [
        'code' => 500,
        'message' => 'Lỗi hệ thống',
        'message_en' => 'Internal server error',
    ];

    const SERVICE_UNAVAILABLE = [
        'code' => 503,
        'message' => 'Hệ thống đang bảo trì',
        'message_en' => 'Service unavailable',
    ];

    const INVALID_INPUT = [
        'code' => -1,
        'message' => 'Đầu vào không hợp lệ',
        'message_en' => 'Invalid Input',
    ];

    const PAGE_NOT_FOUND = [
        'code' => 404,
        'message' => 'Trang không tồn tại',
        'message_en' => 'Page not found',
    ];

    const UNAUTHORIZED = [
        'code' => 401,
        'message' => 'xác thực không thành công',
        'message_en' => 'Unauthorized',
    ];
    
    const FORBIDDEN = [
        'code'  => 403,
        'message'  => 'Không có quyền truy cập',
        'message_en' => 'Forbiden',
    ];

    const METHOD_NOT_ALLOWED = [
        'code'  => 405,
        'message' => 'phương thức không hỗ trợ',
        'message_en' => 'Method not allowed',
    ];

    const REQUEST_TIMEOUT = [
        'code' => 408,
        'message'   => 'thời gian phản hồi quá lâu',
        'message_en' => 'Response Time out',
    ];

    const GATEWAY_TIMEOUT =[
        'code' => 504,
        'message' => 'Gateway timeout',
        'message_en' => 'Gateway timeout',
    ];

    public static function getObject($status){
        return (object)constant("self::".$status);
    }
}