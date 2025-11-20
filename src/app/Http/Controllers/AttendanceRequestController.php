<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceRequest;
use App\Models\AttendanceRecord;
use Illuminate\Support\Facades\Auth;

class AttendanceRequestController extends Controller
{
    /**
     * 修正申請を保存（出勤／退勤／休憩を統合）
     */
    public function store(Request $request)
    {
        $request->validate([
            'attendance_id' => 'required|exists:attendance_records,id',

            // 出退勤（任意）
            'requested_clock_in'  => 'nullable|date_format:H:i',
            'requested_clock_out' => 'nullable|date_format:H:i',

            // 休憩（複数）
            'requested_breaks' => 'nullable|array',
            'requested_breaks.*.start' => 'required_with:requested_breaks|date_format:H:i',
            'requested_breaks.*.end'   => 'required_with:requested_breaks|date_format:H:i',

            // 理由
            'request_reason' => 'required|string|max:255',
        ]);

        if (
            !$request->requested_clock_in &&
            !$request->requested_clock_out &&
            empty($request->requested_breaks)
        ) {
            return back()->withErrors(['error' => '修正内容がありません。'])->withInput();
        }

        $attendance = AttendanceRecord::findOrFail($request->attendance_id);

        // 既に pending があるか判定
        $existing = AttendanceRequest::where('attendance_id', $attendance->id)
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return back()->withErrors([
                'error' => 'この日の申請は既に送信済みです（承認待ち）。'
            ]);
        }

        // 保存
        $attendanceRequest = new AttendanceRequest();
        $attendanceRequest->user_id = Auth::id();
        $attendanceRequest->attendance_id = $attendance->id;
        $attendanceRequest->requested_clock_in  = $request->requested_clock_in;
        $attendanceRequest->requested_clock_out = $request->requested_clock_out;
        $attendanceRequest->requested_breaks = $request->requested_breaks
            ? array_values($request->requested_breaks)
            : null;
        $attendanceRequest->request_reason = $request->request_reason;
        $attendanceRequest->status = 'pending';
        $attendanceRequest->save();

        return redirect()->back()->with('success', '修正申請を送信しました。');
    }


    /**
     * 自分の申請一覧
     */
    public function userList()
    {
        $userId = Auth::id();

        // 承認待ち
        $pendingRequests = AttendanceRequest::where('user_id', $userId)
            ->where('status', 'pending')
            ->with('attendanceRecord', 'user')
            ->orderBy('created_at', 'desc')
            ->get();

        // 承認済み
        $approvedRequests = AttendanceRequest::where('user_id', $userId)
            ->whereIn('status', ['approved', 'rejected'])
            ->with('attendanceRecord', 'user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('attendance.requests.list', compact(
            'pendingRequests',
            'approvedRequests'
        ));
    }



    /**
     * 自分の申請詳細
     */
    public function userShow($id)
    {
        $requestData = AttendanceRequest::with('attendanceRecord')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('attendance.requests.detail', compact('requestData'));
    }
}
