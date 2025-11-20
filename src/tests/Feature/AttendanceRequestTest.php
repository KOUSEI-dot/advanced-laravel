<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\AttendanceRecord;
use App\Models\AttendanceRequest;
use Carbon\Carbon;

class AttendanceRequestTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    /** @test */
    public function 修正申請が保存される()
    {
        // ユーザー作成
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        // 勤怠レコード（出勤・退勤を持つ）
        $record = AttendanceRecord::factory()->create([
            'user_id'   => $user->id,
            'date'      => Carbon::today()->toDateString(),
            'clock_in'  => now()->subHours(8),
            'clock_out' => now(),
            'status'    => '勤務済み',
        ]);

        $this->actingAs($user);

        // +1分して指定
        $requestedIn  = Carbon::parse($record->clock_in)->copy()->addMinute()->format('H:i');
        $requestedOut = Carbon::parse($record->clock_out)->copy()->addMinute()->format('H:i');

        // 実装と一致する POST 先 & name 属性
        $response = $this->post('/attendance/request', [
            'attendance_id'        => $record->id,
            'requested_clock_in'   => $requestedIn,
            'requested_clock_out'  => $requestedOut,
            'request_reason'       => 'テスト理由',
        ]);

        // 秒付きで保存されるので整形
        $expectedIn  = Carbon::parse($requestedIn)->format('H:i:s');
        $expectedOut = Carbon::parse($requestedOut)->format('H:i:s');

        // DB 反映チェック
        $this->assertDatabaseHas('attendance_requests', [
            'user_id'             => $user->id,
            'attendance_id'       => $record->id,
            'requested_clock_in'  => $expectedIn,
            'requested_clock_out' => $expectedOut,
            'request_reason'      => 'テスト理由',
            'status'              => 'pending',
        ]);

        // 正常：redirect
        $response->assertStatus(302);
    }

    /** @test */
    public function 承認待ち申請が一覧に表示される()
    {
        // ユーザー作成
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        // 勤怠レコード
        $attendance = AttendanceRecord::factory()->create([
            'user_id'   => $user->id,
            'date'      => Carbon::today()->toDateString(),
            'clock_in'  => now()->subHours(8),
            'clock_out' => now(),
            'status'    => '勤務済み',
        ]);

        // テスト用申請レコード
        AttendanceRequest::factory()->create([
            'user_id'               => $user->id,
            'attendance_id'         => $attendance->id,
            'requested_clock_in'    => now()->subHour()->format('H:i:s'),
            'requested_clock_out'   => now()->format('H:i:s'),
            'request_reason'        => 'テスト理由',
            'status'                => 'pending',
        ]);

        $this->actingAs($user);

        // 正しい URL
        $response = $this->get('/attendance/request/list');

        $response->assertStatus(200);
        $response->assertSee('テスト理由');
        $response->assertSee('承認待ち');
    }
}

