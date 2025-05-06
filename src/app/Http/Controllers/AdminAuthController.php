<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Requests\AdminLoginRequest;

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(AdminLoginRequest $request)
    {
    $credentials = $request->only('email', 'password');

    if (Auth::attempt(array_merge($credentials, ['role' => 'admin']))) {
        return redirect()->route('admin.attendance');
    }

    return back()->withErrors([
        'login' => 'ログイン情報が登録されていません。',
    ])->withInput();
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('admin.login.form');
    }

}
