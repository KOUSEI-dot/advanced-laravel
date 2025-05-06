<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceRequest;
use Illuminate\Support\Facades\Auth;

class StampCorrectionRequestController extends Controller
{
    public function index()
    {
    // 承認待ちリクエストを取得
    $pendingRequests = AttendanceRequest::with('attendanceRecord')  // attendanceRecordを一緒に取得
        ->where('user_id', Auth::id())
        ->where('status', 'pending')
        ->get();

    // 承認済みリクエストを取得
    $approvedRequests = AttendanceRequest::with('attendanceRecord')  // attendanceRecordを一緒に取得
        ->where('user_id', Auth::id())
        ->where('status', 'approved')
        ->get();

    // ビューにデータを渡す
    return view('attendance.stamp_correction_request_list', compact('pendingRequests', 'approvedRequests'));
    }

}
