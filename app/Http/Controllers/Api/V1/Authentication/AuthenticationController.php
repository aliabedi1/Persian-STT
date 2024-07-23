<?php

namespace App\Http\Controllers\Api\V1\Authentication;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Authentication\LoginRequest;

class AuthenticationController extends Controller
{
    public function login(LoginRequest $request)
    {
        dd($request->all());
    }
    public function verify()
    {

    }
    public function logout()
    {

    }
}
