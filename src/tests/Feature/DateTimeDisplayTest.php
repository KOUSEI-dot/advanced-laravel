<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

class DateTimeDisplayTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 勤怠打刻画面に日付とステータスが表示されている()
    {
        // テスト用ユーザー作成
        $user = User::factory()->create();

        // URLへアクセス
        $response = $this->actingAs($user)->get('/attendance');

        // ====== 日付（Blade の実際の表示形式） ======
        // 例：2025年11月21日(金)
        $today = Carbon::now()->locale('ja')->isoFormat('YYYY年M月D日(dd)');

        $response->assertStatus(200);
        $response->assertSee($today);       // 日付が表示される
        $response->assertSee('勤務外');     // 初期ステータス
        $response->assertSee('出勤');       // 出勤ボタンが表示されている
    }
}
