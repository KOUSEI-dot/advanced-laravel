<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AttendanceRecord;
use App\Models\BreakTime;
use App\Models\AttendanceRequest;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function list(Request $request)
    {
        $targetMonth = $request->input('month')
            ? Carbon::parse($request->input('month'))
            : now();

        $startOfMonth = $targetMonth->copy()->startOfMonth();
        $endOfMonth   = $targetMonth->copy()->endOfMonth();

        // 1ヶ月分の日付リスト
        $dates = collect();
        for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
            $dates->push($date->copy());
        }

        // 勤怠＋休憩をまとめて取得
        $attendances = AttendanceRecord::with('breakTimes')
            ->whereMonth('date', $targetMonth->month)
            ->whereYear('date',  $targetMonth->year)
            ->where('user_id',   auth()->id())
            ->orderBy('date')
            ->get()
            ->map(function($attendance) {
                if ($attendance->clock_in && $attendance->clock_out) {
                    // 出勤・退勤時間の差を分単位で計算
                    $workMinutes = Carbon::parse($attendance->clock_out)->diffInMinutes(Carbon::parse($attendance->clock_in));
                } else {
                    $workMinutes = null;
                }

                // 休憩時間を分単位で計算
                $breakMinutes = $attendance->breakTimes->sum(function($bt) {
                    if ($bt->break_start && $bt->break_end) {
                        return Carbon::parse($bt->break_end)->diffInMinutes(Carbon::parse($bt->break_start));
                    }
                    return 0;
                });

                // 勤務時間から休憩時間を引いて総勤務時間を計算
                $attendance->total_work_minutes = is_null($workMinutes)
                    ? null
                    : max(0, $workMinutes - $breakMinutes);

                return $attendance;
            });

        // ここで $records を定義してから dd
        $records = $attendances->keyBy(function($item) {
            return $item->date->format('Y-m-d');
        });

        return view('attendance.list', [
            'dates'        => $dates,
            'records'      => $records,
            'currentMonth' => $targetMonth,
            'previousMonth'=> $targetMonth->copy()->subMonth(),
            'nextMonth'    => $targetMonth->copy()->addMonth(),
        ]);
    }

    public function detail($id)
    {
        // AttendanceRecordとBreakTimeを一度に読み込む
        $attendance = AttendanceRecord::with(['user', 'breakTimes'])
            ->findOrFail($id);

        // 同一ユーザーで「承認待ちの申請」が存在するか確認
        $hasPendingRequest = AttendanceRequest::where('attendance_id', $attendance->id)
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->exists();

        return view('attendance.detail', compact('attendance', 'hasPendingRequest'));
    }

    // 新しい勤怠情報を保存するメソッド
    public function store(Request $request)
    {
        // 入力値のバリデーション
        $validated = $request->validate([
            'clock_in' => 'required|date_format:H:i',
            'clock_out' => 'required|date_format:H:i',
            'break_start' => 'array',
            'break_end' => 'array',
            'break_start.*' => 'required|date_format:H:i',
            'break_end.*' => 'required|date_format:H:i',
        ]);

        // 勤怠記録の保存
        $attendance = new AttendanceRecord();
        $attendance->user_id = Auth::id();
        $attendance->clock_in = $request->clock_in;
        $attendance->clock_out = $request->clock_out;
        $attendance->save();

        // 休憩時間の保存
        if ($request->has('break_start') && $request->has('break_end')) {
            foreach ($request->break_start as $key => $start) {
                $attendance->breakTimes()->create([
                    'break_start' => $start,
                    'break_end' => $request->break_end[$key],
                ]);
            }
        }

        // 成功メッセージを付けてリダイレクト
        return redirect()->route('attendance.list')->with('success', '勤怠が登録されました');
    }

    public function showAttendanceList()
    {
        $user = Auth::user();
        $attendances = AttendanceRecord::where('user_id', $user->id)
            ->with('breakTimes') // 休憩情報を取得
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($attendance) {
                $clockIn = $attendance->clock_in ? Carbon::parse($attendance->clock_in) : null;
                $clockOut = $attendance->clock_out ? Carbon::parse($attendance->clock_out) : null;

                // 初期化
                $totalWorkSeconds = 0;
                $totalBreakSeconds = 0;

                // 出勤・退勤の時間がある場合に勤務時間を計算
                if ($clockIn && $clockOut) {
                    $totalWorkSeconds = $clockOut->diffInSeconds($clockIn);
                } else {
                    // 出勤または退勤時間が不完全な場合
                    $attendance->status = '時間データ不完全';
                }

                // 休憩時間の計算
                foreach ($attendance->breakTimes as $break) {
                    if ($break->break_start && $break->break_end) {
                        $start = Carbon::parse($break->break_start);
                        $end = Carbon::parse($break->break_end);
                        $totalBreakSeconds += $end->diffInSeconds($start);
                    }
                }

                // 実際の勤務時間（休憩時間を差し引いた時間）
                $actualWorkSeconds = max(0, $totalWorkSeconds - $totalBreakSeconds);
                $attendance->break_time = gmdate('H:i', $totalBreakSeconds);
                $attendance->work_time = gmdate('H:i', $actualWorkSeconds);

                return $attendance;
            });

        return view('attendance_list', compact('attendances'));
    }

}
