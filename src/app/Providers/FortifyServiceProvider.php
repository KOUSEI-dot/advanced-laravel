<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Illuminate\Validation\ValidationException;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // フォーム表示
        Fortify::registerView(fn () => view('auth.register'));
        Fortify::loginView(fn () => view('auth.login'));

        // ✅ クラス名で登録
        Fortify::createUsersUsing(\App\Actions\Fortify\CreateNewUser::class);

        // ログイン処理のバリデーション（UserLoginRequest を使用）
        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->email)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                return $user;
            }

            throw ValidationException::withMessages([
                'login' => 'ログイン情報が正しくありません',
            ]);
        });

        RateLimiter::for('login', fn (Request $request) =>
            Limit::perMinute(10)->by($request->email . $request->ip())
        );
    }
}
