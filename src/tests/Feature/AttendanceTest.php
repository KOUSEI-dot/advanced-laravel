<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_出勤ボタンを押すと出勤記録が保存される()
    {
        // ユーザー作成
        $user = User::factory()->create();

        // ログイン
        $this->actingAs($user);

        // 出勤POST（実装に合わせて 302 を期待）
        $response = $this->post('/start-work');

        // 出勤レコードが保存されているか確認
        $this->assertDatabaseHas('attendance_records', [
            'user_id' => $user->id,
        ]);

        // 正しいレスポンス：redirect の 302
        $response->assertStatus(302);
    }
}
