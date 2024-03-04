<?php

namespace App\Api;

use App\Exceptions\Auth\AccountOrPassWordInvalidException;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        $input = $request->only(['mobile', 'password']);

        if ($token = auth()->attempt($input)) {
            return $this->loginInfo($token);
        }

        throw new AccountOrPassWordInvalidException();
    }

    public function refresh()
    {
        return $this->loginInfo(auth()->refresh(), time() + auth()->factory()->getTTL() * 60);
    }

    public function logout()
    {
        auth()->logout();
        return 'ok';
    }

    private function loginInfo($token, $expire = null)
    {
        return [
            'token' => $token,
            'expire' => $expire ?? auth()->getClaim('exp')
        ];
    }

}
