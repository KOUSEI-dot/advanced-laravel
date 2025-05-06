<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceRecord;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\BreakTime;

class WorkController extends Controller
{
    // 勤怠ページの表示
    public function attendance()
    {
        $user = Auth::user();
        $attendance = AttendanceRecord::where('user_id', $user->id)
            ->whereDate('date', Carbon::today())
            ->first();

        return view('attendance', compact('attendance'));
    }

    // 出勤処理
    public function startWork()
    {
        $user = Auth::user();
        $now = Carbon::now();

        // 'clock_in' に日付と時間を含む形式を設定
        $attendance = AttendanceRecord::create([
            'user_id' => $user->id,
            'date' => $now->toDateString(), // 'YYYY-MM-DD'
            'clock_in' => $now->format('Y-m-d H:i:s'), // 'YYYY-MM-DD HH:MM:SS'
            'status' => '出勤中',
        ]);

        return response()->json($attendance);
    }

    // 休憩入り
    public function startBreak()
    {
        $user = Auth::user();
        $attendance = AttendanceRecord::where('user_id', $user->id)
            ->whereDate('date', Carbon::today())
            ->first();

        if ($attendance) {
            BreakTime::create([
                'attendance_record_id' => $attendance->id, // カラム名を修正
                'break_start' => Carbon::now()->format('Y-m-d H:i:s'), // 'YYYY-MM-DD HH:MM:SS'
            ]);

            $attendance->status = '休憩中';
            $attendance->save();
        }

        return response()->json($attendance);
    }

    // 休憩戻り
    public function endBreak()
    {
        $user = Auth::user();
        $attendance = AttendanceRecord::where('user_id', $user->id)
            ->whereDate('date', Carbon::today())
            ->first();

        if ($attendance) {
            $breakTime = BreakTime::where('attendance_record_id', $attendance->id)
                ->whereNull('break_end')
                ->latest()
                ->first();

            if ($breakTime) {
                $breakTime->break_end = Carbon::now()->format('Y-m-d H:i:s'); // 'YYYY-MM-DD HH:MM:SS'
                $breakTime->save();
            }

            $attendance->status = '出勤中';
            $attendance->save();
        }

        return response()->json($attendance);
    }

    // 退勤処理
    public function endWork()
    {
        $user = Auth::user();
        $attendance = AttendanceRecord::where('user_id', $user->id)
            ->whereDate('date', Carbon::today())
            ->first();

        if ($attendance) {
            $attendance->clock_out = Carbon::now()->format('Y-m-d H:i:s'); // 'YYYY-MM-DD HH:MM:SS'
            $attendance->status = '退勤済み';
            $attendance->save();
        }

        return response()->json($attendance);
    }

    // 勤怠一覧表示
        // 勤怠一覧表示
    public function showAttendanceList()
    {
        $user = Auth::user();
        $attendances = AttendanceRecord::where('user_id', $user->id)
            ->with('breakTimes') // 休憩情報をまとめて取得
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($attendance) {
                $clockIn = $attendance->clock_in ? Carbon::parse($attendance->clock_in) : null;
                $clockOut = $attendance->clock_out ? Carbon::parse($attendance->clock_out) : null;

                $totalWorkSeconds = 0;
                $totalBreakSeconds = 0;

                // 勤務時間計算
                if ($clockIn && $clockOut) {
                    $totalWorkSeconds = $clockOut->diffInSeconds($clockIn);
                }

                foreach ($attendance->breakTimes as $break) {
                    if ($break->break_start && $break->break_end) {
                        $start = Carbon::parse($break->break_start);
                        $end = Carbon::parse($break->break_end);
                        $totalBreakSeconds += $end->diffInSeconds($start);
                    }
                }

                $actualWorkSeconds = max(0, $totalWorkSeconds - $totalBreakSeconds);

                $attendance->break_time = gmdate('H:i', $totalBreakSeconds);
                $attendance->work_time = gmdate('H:i', $actualWorkSeconds);

                return $attendance;
            });

        return view('attendance_list', compact('attendances'));
    }

}
