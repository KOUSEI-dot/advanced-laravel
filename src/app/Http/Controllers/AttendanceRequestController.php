<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceRequest;
use App\Models\AttendanceRecord;
use Illuminate\Support\Facades\Auth;

class AttendanceRequestController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'clock_in' => 'nullable|date_format:H:i',
            'clock_out' => 'nullable|date_format:H:i',
            'reason' => 'required|string|max:255',
        ]);

        if (!$request->clock_in && !$request->clock_out) {
            return back()->withErrors(['message' => '出勤または退勤時刻を入力してください。']);
        }

        // 勤怠データ取得
        $attendance = AttendanceRecord::find($request->attendance_id);

        if (!$attendance) {
            return back()->withErrors(['attendance_id' => '該当する勤怠データが見つかりませんでした。'])->withInput();
        }

        // 出退勤時間の範囲チェック
        if ($attendance->clock_in && $request->clock_in) {
            $clockInTime = date('H:i', strtotime($attendance->clock_in));
            if ($request->clock_in < $clockInTime) {
                return back()->withErrors(['clock_in' => '出勤時刻は過去の出勤時刻より前にできません。'])->withInput();
            }
        }

        if ($attendance->clock_out && $request->clock_out) {
    $clockOutTime = date('H:i', strtotime($attendance->clock_out));
    if ($request->clock_out < $clockOutTime) {
        return back()->withErrors(['clock_out' => '退勤時刻は既存の退勤時刻より前にできません。'])->withInput();
    }


        }

        // 申請データ保存
        AttendanceRequest::create([
            'user_id' => Auth::id(),
            'attendance_id' => $request->attendance_id,
            'requested_clock_in' => $request->clock_in, // ★そのまま時刻だけ渡す！
            'requested_clock_out' => $request->clock_out, // ★そのまま時刻だけ渡す！
            'request_reason' => $request->reason,
            'status' => 'pending',
        ]);

        return back()->with('success', '修正申請を送信しました。');
    }

    public function show($id)
    {
        $attendanceRequest = AttendanceRequest::findOrFail($id);
        $attendance = $attendanceRequest->attendanceRecord;

        $hasPendingRequest = $attendanceRequest->status === 'pending'; // ★これ追加

        return view('attendance.request.detail', [
            'attendanceRequest' => $attendanceRequest,
            'attendance' => $attendance,
            'hasPendingRequest' => $hasPendingRequest, // ★渡す
        ]);
    }

}


