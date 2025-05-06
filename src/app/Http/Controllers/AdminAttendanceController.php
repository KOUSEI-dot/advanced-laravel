<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\AttendanceRecord;
use App\Models\AttendanceRequest;
use App\Models\Breaktime;
use Illuminate\Support\Facades\Response;


class AdminAttendanceController extends Controller
{
    public function index(Request $request)
    {
        // 日付の取得（デフォルトは今日）
        $date = $request->input('date', now()->toDateString());

        $targetMonth = Carbon::parse($date)->startOfMonth();

        // 指定した日付の勤怠データを取得
        $attendanceRecords = AttendanceRecord::where('date', $date)
            ->with('user')
            ->get();

        return view('admin.attendance.list', [
            'attendanceRecords' => $attendanceRecords,
            'date'              => $date,
            'currentMonth'      => $targetMonth,
            'previousMonth'     => $targetMonth->copy()->subMonth(),
            'nextMonth'         => $targetMonth->copy()->addMonth(),
        ]);
    }



    public function list(Request $request)
    {
        $targetMonth = $request->input('month')
            ? Carbon::parse($request->input('month'))
            : now();

        $startOfMonth = $targetMonth->copy()->startOfMonth();
        $endOfMonth   = $targetMonth->copy()->endOfMonth();

        // 勤怠＋休憩をまとめて取得
        $attendanceRecords = AttendanceRecord::with(['user', 'breakTimes'])
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get()
            ->map(function ($attendance) {
                $clockIn = $attendance->clock_in ? Carbon::parse($attendance->clock_in) : null;
                $clockOut = $attendance->clock_out ? Carbon::parse($attendance->clock_out) : null;

                $totalWorkSeconds = 0;
                $totalBreakSeconds = 0;

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

        return view('admin.attendance.list', [
            'attendanceRecords' => $attendanceRecords,
            'currentMonth'      => $targetMonth,
            'previousMonth'     => $targetMonth->copy()->subMonth(),
            'nextMonth'         => $targetMonth->copy()->addMonth(),
        ]);
    }



    public function detail($id)
    {
    $attendance = AttendanceRecord::with('user')->findOrFail($id);

    // 対象の修正申請データを取得（存在しない場合は null）
    $attendanceRequest = $attendance->attendanceRequest ?? null;

    return view('admin.detail', compact('attendance', 'attendanceRequest'));
    }

    public function update(Request $request, $id)
    {
        $attendance = AttendanceRecord::findOrFail($id);
        $attendance->clock_in = $request->input('clock_in');
        $attendance->clock_out = $request->input('clock_out');
        $attendance->break_start = $request->input('break_start');
        $attendance->break_end = $request->input('break_end');
        $attendance->save();
        return redirect()->route('admin.detail', ['id' => $id])
                        ->with('success', '勤怠情報を更新しました。');
    }

}

