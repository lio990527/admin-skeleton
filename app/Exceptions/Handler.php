<?php

namespace App\Exceptions;

use App\Http\Response\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function(Throwable $e, Request $request) {
            if (strstr($request->getRequestUri(), '/api') || $request->expectsJson()) {
                if ($e instanceof NotFoundHttpException) {
                    if ($e->getPrevious() instanceof ModelNotFoundException) {
                        return ApiResponse::error(Response::HTTP_NOT_FOUND, '记录找不到了');
                    }
                    return ApiResponse::error(Response::HTTP_NOT_FOUND, '未知接口');
                } elseif ($e instanceof AuthenticationException) {
                    return ApiResponse::error(Response::HTTP_UNAUTHORIZED, '未授权的访问');
                } elseif ($e instanceof AuthorizationException) {
                    return ApiResponse::error(Response::HTTP_FORBIDDEN, '无权访问');
                } elseif ($e instanceof ValidationException) {
                    return ApiResponse::error(Response::HTTP_UNPROCESSABLE_ENTITY, '数据验证失败', $e->errors());
                } elseif ($e instanceof QueryException) {
                    return ApiResponse::error(Response::HTTP_INTERNAL_SERVER_ERROR, '数据错误');
                } else {
                    return ApiResponse::error();
                }
            }
        });

        $this->renderable(function(AuthenticationException $e, $request) {
            
        });

        $this->reportable(function (Throwable $e) {
            //

        });
    }
}
