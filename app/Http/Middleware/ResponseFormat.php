<?php

namespace App\Http\Middleware;

use App\Http\Response\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ResponseFormat
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, \Closure $next)
    {
        $response = $next($request);

        if (!$response instanceof ApiResponse) {
            if ($response instanceof JsonResponse) {
                return (new ApiResponse(
                    null,
                    $response->getStatusCode(),
                    $response->headers->all(),
                    $response->getEncodingOptions()
                ))
                ->setData($response->getData())
                ->withCallback($response->getCallback());
            } elseif ($response instanceof Response && !$response->getOriginalContent() instanceof View) {
                $contentType = strtolower(explode(';', $response->headers->get('Content-Type'))[0]);
                switch ($contentType) {
                    case 'application/json':
                    case 'text/html':
                        return (new ApiResponse(
                            null,
                            $response->getStatusCode(),
                            $response->header('Content-Type', 'application/json')->headers->all(),
                        ))->setData($response->getContent());
                        break;
                }
            }
        }

        return $response;
    }
}
