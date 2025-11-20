<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\AttendanceRecord;
use App\Models\Breaktime;
use App\Models\AttendanceRequest;

class AdminAttendanceController extends Controller
{
    /**
     * ▼ 日別（前日・翌日）
     */
    public function index(Request $request)
    {
        // 表示する日付（デフォルト：今日）
        $date = $request->input('date', now()->toDateString());
        $targetDate = Carbon::parse($date);

        // 全ユーザーの当日の勤怠
        $attendanceRecords = AttendanceRecord::with(['user', 'breakTimes'])
            ->where('date', $targetDate->toDateString())
            ->orderBy('user_id')
            ->get()
            ->map(function ($record) {

                $clockIn  = $record->clock_in ? Carbon::parse($record->clock_in) : null;
                $clockOut = $record->clock_out ? Carbon::parse($record->clock_out) : null;

                $workSeconds = ($clockIn && $clockOut) ? $clockOut->diffInSeconds($clockIn) : 0;

                $breakSeconds = 0;
                foreach ($record->breakTimes as $break) {
                    if ($break->break_start && $break->break_end) {
                        $start = Carbon::parse($break->break_start);
                        $end   = Carbon::parse($break->break_end);
                        $breakSeconds += $end->diffInSeconds($start);
                    }
                }

                $record->break_time = gmdate('H:i', $breakSeconds);
                $record->work_time  = gmdate('H:i', max(0, $workSeconds - $breakSeconds));

                return $record;
            });

        return view('admin.attendance.list', [
            'attendanceRecords' => $attendanceRecords,

            // ▼ Blade が必要とする変数
            'date'         => $targetDate->toDateString(),
            'previousDate' => $targetDate->copy()->subDay()->toDateString(),
            'nextDate'     => $targetDate->copy()->addDay()->toDateString(),
        ]);
    }


    /**
     * ▼ 月別（前月・翌月）
     */
    public function list(Request $request)
    {
        $targetMonth = $request->month
            ? Carbon::parse($request->month)
            : now();

        $start = $targetMonth->copy()->startOfMonth();
        $end   = $targetMonth->copy()->endOfMonth();

        $attendanceRecords = AttendanceRecord::with(['user', 'breakTimes'])
            ->whereBetween('date', [$start, $end])
            ->orderBy('date')
            ->orderBy('user_id')
            ->get()
            ->map(function ($record) {

                $clockIn  = $record->clock_in ? Carbon::parse($record->clock_in) : null;
                $clockOut = $record->clock_out ? Carbon::parse($record->clock_out) : null;

                $workSeconds = ($clockIn && $clockOut) ? $clockOut->diffInSeconds($clockIn) : 0;

                $breakSeconds = 0;
                foreach ($record->breakTimes as $break) {
                    if ($break->break_start && $break->break_end) {
                        $start = Carbon::parse($break->break_start);
                        $end   = Carbon::parse($break->break_end);
                        $breakSeconds += $end->diffInSeconds($start);
                    }
                }

                $record->break_time = gmdate('H:i', $breakSeconds);
                $record->work_time  = gmdate('H:i', max(0, $workSeconds - $breakSeconds));

                return $record;
            });

        return view('admin.attendance.list', [
            'attendanceRecords' => $attendanceRecords,

            // ▼ Blade が必要とする月別変数
            'currentMonth'  => $targetMonth,
            'previousMonth' => $targetMonth->copy()->subMonth(),
            'nextMonth'     => $targetMonth->copy()->addMonth(),
        ]);
    }


    /**
     * ▼ 詳細
     */
    public function detail($id)
    {
        // 勤怠データ
        $attendance = AttendanceRecord::with(['user', 'breakTimes'])->findOrFail($id);

        // その勤怠に紐づく修正申請（1件だけ想定）
        $attendanceRequest = \App\Models\AttendanceRequest::where('attendance_id', $id)->first();

        // 承認待ちかどうか
        $hasPendingRequest = $attendanceRequest && $attendanceRequest->status === 'pending';

        return view('admin.attendance.detail', compact('attendance', 'attendanceRequest', 'hasPendingRequest'));

    }

}