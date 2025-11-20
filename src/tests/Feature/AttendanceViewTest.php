<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class AttendanceViewTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 初期状態は「勤務外」が表示されるテスト
     */
    public function test_初期状態は勤務外が表示される()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('勤務外');
    }

    /**
     * 出勤中のステータスが表示されるテスト
     */
    public function test_出勤中のステータスが表示される()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $now = Carbon::now();

        // 勤怠レコード作成：出勤中（clock_inはあるがclock_outはnull）
        \DB::table('attendance_records')->insert([
            'user_id' => $user->id,
            'date' => $now->format('Y-m-d'),
            'clock_in' => $now,
            'clock_out' => null,
            'status' => '出勤中',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('出勤中');
    }

    /**
     * 休憩中のステータスが表示されるテスト
     */
    public function test_休憩中のステータスが表示される()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $now = Carbon::now();

        // 勤怠レコード作成：休憩中（statusに「休憩中」をセット）
        \DB::table('attendance_records')->insert([
            'user_id' => $user->id,
            'date' => $now->format('Y-m-d'),
            'clock_in' => $now->copy()->subHour(),
            'clock_out' => null,
            'status' => '休憩中',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('休憩中');
    }
}
