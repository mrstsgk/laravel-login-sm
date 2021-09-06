<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginFormRequest;

class AuthController extends Controller
{
    public function showLogin(){
        return view('login.login_form');
    }

    public function login(LoginFormRequest $request)
    {

    }
}
