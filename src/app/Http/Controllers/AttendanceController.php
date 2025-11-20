<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AttendanceRecord;
use App\Models\BreakTime;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * 休憩入
     */
    public function startBreak()
    {
        $attendance = AttendanceRecord::where('user_id', Auth::id())
            ->whereDate('date', Carbon::today())
            ->first();

        if (!$attendance) {
            return redirect()->route('attendance');
        }

        // すでに休憩中なら何もしない
        $ongoing = BreakTime::where('attendance_id', $attendance->id)
            ->whereNull('end_time')
            ->first();

        if (!$ongoing) {

            // 🔥 休憩開始
            BreakTime::create([
                'attendance_id' => $attendance->id,
                'start_time'    => now()->format('H:i'),
                'end_time'      => null,
            ]);

            // 🔥 ここが重要！！ status を変更する
            $attendance->status = '休憩中';
            $attendance->save();
        }

        return redirect()->route('attendance');
    }


    public function endBreak()
    {
        $attendance = AttendanceRecord::where('user_id', Auth::id())
            ->whereDate('date', Carbon::today())
            ->first();

        if (!$attendance) {
            return redirect()->route('attendance');
        }

        $break = BreakTime::where('attendance_id', $attendance->id)
            ->whereNull('end_time')
            ->first();

        if ($break) {

            // 🔙 休憩戻
            $break->end_time = now()->format('H:i');
            $break->save();

            // 🔥 ここも重要！！！ 戻ったらステータスを出勤中に戻す
            $attendance->status = '出勤中';
            $attendance->save();
        }

        return redirect()->route('attendance');
    }

        /**
     * 勤怠一覧
     */
    public function list(Request $request)
    {
        $targetMonth = $request->input('month')
            ? Carbon::parse($request->input('month'))
            : now();

        // Carbon オブジェクトのまま渡す（format しない！）
        $currentMonth  = $targetMonth->copy();
        $previousMonth = $targetMonth->copy()->subMonth();
        $nextMonth     = $targetMonth->copy()->addMonth();

        $startOfMonth = $targetMonth->copy()->startOfMonth();
        $endOfMonth   = $targetMonth->copy()->endOfMonth();

        // 日付リスト
        $records = AttendanceRecord::with('breakTimes')
            ->whereMonth('date', $targetMonth->month)
            ->whereYear('date', $targetMonth->year)
            ->where('user_id', Auth::id())
            ->get()
            ->keyBy('date');

        return view('attendance.list', compact(
            'records',
            'currentMonth',
            'previousMonth',
            'nextMonth'
        ));
    }

    public function detail($id)
    {
        // 出勤レコードを取得
        $attendance = AttendanceRecord::with(['breakTimes', 'user'])
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // すでに承認待ちの申請があるか？
        $attendanceRequest = \App\Models\AttendanceRequest::where('attendance_id', $attendance->id)
            ->where('status', 'pending')
            ->first();

        $hasPendingRequest = $attendanceRequest ? true : false;

        return view('attendance.detail', compact(
            'attendance',
            'attendanceRequest',
            'hasPendingRequest'
        ));
    }


}
