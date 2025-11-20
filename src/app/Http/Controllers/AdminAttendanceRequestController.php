<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceRequest;
use App\Models\AttendanceRecord;
use App\Models\BreakTime;
use Illuminate\Support\Facades\Auth;

class AdminAttendanceRequestController extends Controller
{
    /* ▼▼ 管理者：修正申請一覧 ▼▼ */
    public function index()
    {
        // 承認待ち
        $pendingRequests = AttendanceRequest::with('user', 'attendanceRecord')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        // 承認済み
        $approvedRequests = AttendanceRequest::with('user', 'attendanceRecord')
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.requests.list', compact('pendingRequests', 'approvedRequests'));
    }

    /* ▼▼ 修正申請詳細 ▼▼ */
    public function show($id)
    {
        $attendanceRequest = AttendanceRequest::with('user', 'attendanceRecord')
            ->findOrFail($id);

        return view('admin.requests.detail', compact('attendanceRequest'));
    }

    /* ▼▼ 修正申請の承認 ▼▼ */
    public function approve($id)
    {
        $requestData = AttendanceRequest::findOrFail($id);
        $attendance  = AttendanceRecord::findOrFail($requestData->attendance_id);

        /* ---------------------------
            出勤・退勤の修正
        --------------------------- */
        if ($requestData->requested_clock_in) {
            $attendance->clock_in = $requestData->requested_clock_in;
        }

        if ($requestData->requested_clock_out) {
            $attendance->clock_out = $requestData->requested_clock_out;
        }

        /* ---------------------------
            休憩の修正（JSON/配列どちらでもOK）
        --------------------------- */
        $breaksRaw = $requestData->requested_breaks;

        if (is_array($breaksRaw)) {
            // 配列で保存されているパターン
            $breaks = $breaksRaw;
        } else {
            // JSON文字列で保存されているパターン
            $breaks = json_decode($breaksRaw, true) ?? [];
        }

        if (!empty($breaks)) {
            // 現在の休憩情報を削除して上書き
            BreakTime::where('attendance_id', $attendance->id)->delete();

            foreach ($breaks as $break) {
                BreakTime::create([
                    'attendance_id' => $attendance->id,
                    'start_time'    => $break['start'],
                    'end_time'      => $break['end'],
                ]);
            }
        }

        /* ---------------------------
            最終保存
        --------------------------- */
        $attendance->save();

        $requestData->status   = 'approved';
        $requestData->admin_id = Auth::id();
        $requestData->save();

        return redirect()->route('admin.requests.list')
            ->with('success', '申請を承認しました。');
    }
}
