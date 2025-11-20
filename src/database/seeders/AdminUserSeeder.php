<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => '管理者太郎',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'), // 任意の安全なパスワードに
            'role' => 'admin', // 管理者権限
            'email_verified_at' => now(), // メール認証済みにしておく
        ]);
    }
}
