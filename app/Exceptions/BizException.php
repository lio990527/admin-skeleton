<?php

namespace App\Exceptions;

use App\Http\Response\ApiResponse;
use Illuminate\Http\Request;

class BizException extends \Exception
{

    protected $code = 1000;

    protected $message = '服务异常';

    protected $data;

    /**
     * @param mixed $data
     */
    public function __construct($data = null)
    {
        $this->data = $data;

        parent::__construct($this->message, $this->code);
    }

    /**
     * @param Request $request
     * @return ApiResponse
     */
    public function render(Request $request)
    {
        return ApiResponse::error($this->getCode(), $this->getMessage(), $this->data);
    }

    /**
     * @return void
     */
    public function report()
    {
        return ;
    }

}