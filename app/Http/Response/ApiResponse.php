<?php

namespace App\Http\Response;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ApiResponse extends JsonResponse
{

    public static function error($code = Response::HTTP_INTERNAL_SERVER_ERROR, $message = '系统繁忙', $data = null)
    {
        return (new static)->setData($data, $code, $message);
    }

    public static function success($data = [], $code = 0, $message = '')
    {
        return (new static)->setData($data, $code, $message);
    }

    /**
     * api返回数据格式
     *
     * @param mixed $data
     * @param integer $code
     * @param string $message
     * @return static
     */
    public function setData($data = [], $code = 0, $message = '')
    {
        return parent::setData(compact('code', 'message', 'data'));
    }

}