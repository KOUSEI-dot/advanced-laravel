<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Laravel\Fortify\Fortify;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // ログイン・新規登録ビューの指定
        Fortify::loginView(fn () => view('auth.login'));
        Fortify::registerView(fn () => view('auth.register'));

        // 新規登録処理のハンドラ
        Fortify::createUsersUsing(CreateNewUser::class);

        // ログイン時の認証処理（メール未認証ならログインさせない）
        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->email)->first();

            if (
                $user &&
                Hash::check($request->password, $user->password) &&
                $user->hasVerifiedEmail()
            ) {
                return $user;
            }

            throw ValidationException::withMessages([
                'email' => 'メールアドレスが確認されていません。' // メール未認証または認証失敗
            ]);
        });

        // ログイン制限（ブルートフォース攻撃防止）
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(10)->by($request->email . $request->ip());
        });
    }
}
