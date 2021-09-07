<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginFormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin(){
        return view('login.login_form');
    }

    public function login(LoginFormRequest $request)
    {
        $credentials = $request->only(['email', 'password']);
        
        $user = User::where('email', '=', $credentials['email'])->first();

        if (!is_null($user)) {
            if ($user->locked_flg === 1) {
                return back()->withErrors([
                    'login_error' => 'アカウントがロックされています',
                ]);
            }
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                if ($user->error_count > 0) {
                    $user->error_count = 0;
                    $user->save();
                }
                return redirect()->route('home')->with('login_success', 'ログイン成功しました');
            }

            $user->error_count = $user->error_count + 1;
            if ($user->error_count > 5) {
                $user->locked_flg = 1;
                $user->save();

                return back()->withErrors([
                    'login_error' => 'アカウントがロックされました',
                ]);
            }
            $user->save();
        }


        return back()->withErrors([
            'login_error' => 'メールアドレスかパスワードが間違っています。',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('showLogin')->with('logout', 'ログアウトしました');
    }
}
