<?php

namespace App\Exceptions\Auth;

use App\Exceptions\BizException;

class AccountOrPassWordInvalidException extends BizException
{

    protected $code = 2001;

    protected $message = '用户名或密码错误';

}