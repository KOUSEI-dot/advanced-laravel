<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\AttendanceRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class AttendanceListTest extends TestCase
{
    public function test_勤怠一覧画面で特定の月のデータが表示される()
{
    $user = User::factory()->create();

    AttendanceRecord::factory()->create([
        'user_id' => $user->id,
        'date' => '2025-06-15',
        'clock_in' => '2025-06-15 09:00:00',
        'clock_out' => '2025-06-15 18:00:00',
        'status' => '勤務済み',
    ]);
    AttendanceRecord::factory()->create([
        'user_id' => $user->id,
        'date' => '2025-06-20',
        'clock_in' => '2025-06-20 09:15:00',
        'clock_out' => '2025-06-20 18:10:00',
        'status' => '勤務済み',
    ]);

    AttendanceRecord::factory()->create([
        'user_id' => $user->id,
        'date' => '2025-07-05',
        'clock_in' => '2025-07-05 09:10:00',
        'clock_out' => '2025-07-05 17:50:00',
        'status' => '勤務済み',
    ]);

    $this->actingAs($user);

    $response = $this->get('/attendance/list?month=2025-06');

    $response->assertStatus(200);

    $response->assertSee('6/15');
    $response->assertSee('6/20');

    $response->assertDontSee('7/5');
}

public function test_デフォルトでは今月の勤怠が表示される()
{
    $user = User::factory()->create();

    $now = Carbon::now()->format('Y-m');

    AttendanceRecord::factory()->create([
        'user_id' => $user->id,
        'date' => $now . '-10',
        'clock_in' => $now . '-10 09:00:00',
        'clock_out' => $now . '-10 18:00:00',
        'status' => '勤務済み',
    ]);

    $this->actingAs($user);

    $response = $this->get('/attendance/list');

    $response->assertStatus(200);

    // "6/10"のように表示されることを想定しているならこちらに修正
    $response->assertSee(date('n/j', strtotime($now . '-10')));
}

}
