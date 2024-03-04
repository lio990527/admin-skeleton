<?php

use App\Api\AuthController;
use App\Api\TestController;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'namespace' => '\App\Api',
    'middleware' => 'auth:api'
], function(Router $router) {

    $router->any('test', function() {
        return 'test';
    });
    $router->any('api2', 'TestController@api');

    $router->group([
        'prefix' => 'auth'
    ], function (Router $router) {
        $router->post('refresh', [AuthController::class, 'refresh'])->name('refresh');
        $router->post('logout', [AuthController::class, 'logout'])->name('logout');
    });
});

$router->any('api', [TestController::class, 'api']);

$router->post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
