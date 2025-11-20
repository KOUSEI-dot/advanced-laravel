<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function メールアドレスが未入力の場合バリデーションエラーが出る()
    {
        $response = $this->post('/admin/login', [
            'email'    => '',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function パスワードが未入力の場合バリデーションエラーが出る()
    {
        $response = $this->post('/admin/login', [
            'email'    => 'admin@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function 認証失敗時にはログイン画面にリダイレクトされエラーが出る()
    {
        // 管理者ユーザーを作成（role カラム等で判別しているなら設定）
        User::factory()->create([
            'email'    => 'admin@example.com',
            'password' => bcrypt('correctpassword'),
            'role'     => 'admin',
        ]);

        $response = $this->from('/admin/login')->post('/admin/login', [
            'email'    => 'admin@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertRedirect('/admin/login');
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function 正しい認証情報の場合管理者ログインに成功する()
    {
        $admin = User::factory()->create([
            'email'              => 'admin@example.com',
            'password'           => bcrypt('password123'),
            'role'               => 'admin',
            'email_verified_at'  => now(),
        ]);

        $response = $this->post('/admin/login', [
            'email'    => 'admin@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/admin/attendance');
        $this->assertAuthenticatedAs($admin);
    }
}
