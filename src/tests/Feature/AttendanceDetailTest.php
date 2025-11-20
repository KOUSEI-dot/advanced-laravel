<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\AttendanceRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 勤怠詳細画面に出勤・退勤・休憩合計が表示されていることを確認する
     */
    public function test_詳細画面に出勤退勤休憩時間が表示される()
    {
        // 固定時刻を設定
        $date       = '2025-06-30';
        $clockIn    = '2025-06-30 09:00:00';
        $clockOut   = '2025-06-30 18:00:00';

        // ユーザー作成＆ログイン
        $user = User::factory()->create();
        $this->actingAs($user);

        // 勤怠レコードを作成
        $record = AttendanceRecord::factory()->create([
            'user_id'    => $user->id,
            'date'       => $date,
            'clock_in'   => $clockIn,
            'clock_out'  => $clockOut,
            'status'     => '勤務済み',
        ]);

        // 詳細画面へアクセス（あなたの実装に合わせて修正）
        $response = $this->get("/attendance/detail/{$record->id}");
        $response->assertStatus(200);

        // ====== 表示される日付（あなたのBlade形式：YYYY年M月D日） ======
        // ※ m にスペースなし＆ゼロ埋めなしが正しいため修正
        $formattedDate = Carbon::parse($date)->format('Y年 n月j日');
        $response->assertSee($formattedDate);

        $response->assertSee('09:00');
        $response->assertSee('18:00');


        // 出勤時間「09:00」
        $response->assertSee(Carbon::parse($clockIn)->format('H:i'));

        // 退勤時間「18:00」
        $response->assertSee(Carbon::parse($clockOut)->format('H:i'));
    }
}
