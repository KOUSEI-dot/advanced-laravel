<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceRequest;

class AdminAttendanceRequestController extends Controller
{
    public function requestAttendanceFix(Request $request)
    {
        // 修正申請データを保存
        AttendanceRequest::create([
            'user_id' => $user_id,
            'attendance_id' => $attendance_id,
            'request_type' => $type,
            'requested_value' => $time,
            'request_reason' => $request->request_reason,
            'status' => 'pending',
        ]);
        return redirect()->back()->with('success', '修正申請を送信しました。');
    }

    public function index()
    {
        // 承認待ちの申請を取得
        $pendingRequests = AttendanceRequest::where('status', 'pending')->with(['user', 'attendanceRecord'])->get();

        // 承認済みの申請を取得
        $approvedRequests = AttendanceRequest::where('status', 'approved')->with(['user', 'attendanceRecord'])->get();

        return view('admin.requests.list', compact('pendingRequests', 'approvedRequests'));
    }

    public function approve($id)
    {
        $request = AttendanceRequest::findOrFail($id);
        $request->status = 'approved';
        $request->save();

        return redirect()->route('admin.requests.list')->with('success', '申請を承認しました。');

    }
    public function show($id)
    {
        $attendanceRequest = AttendanceRequest::with('user')->findOrFail($id);
        return view('admin.requests.detail', compact('attendanceRequest'));
    }


}
