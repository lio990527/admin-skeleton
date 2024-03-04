<?php

namespace App\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Overtrue\LaravelWeChat\Facade;

class TestController extends Controller
{
    public function api()
    {
        return 'test api';
        return 11111;
        return ['a' => 1];
        return User::find(1);
        return response()->file('./robots.txt');
        return response()->view('welcome');
        return response()->jsonp('test', ['test' => 1]);
        return response()
        ->json(['name' => 'Abigail', 'state' => 'CA']);
        return response('Hello World', 200)
                  ->header('Content-Type', 'application/json');
    }
}
