<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AttendanceRecord;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminStaffController extends Controller
{
    public function index()
    {
        $staffs = User::where('role', 'user')->get(); // 一般ユーザーのみ取得
        return view('admin.staff.list', compact('staffs'));
    }



    public function show(Request $request, $id)
    {
        $staff = User::findOrFail($id);

        // 表示対象月の取得（クエリパラメータがあればそれを使う）
        $monthParam = $request->query('month');
        $currentMonth = $monthParam ? Carbon::createFromFormat('Y-m', $monthParam) : Carbon::now()->startOfMonth();

        // 前月・翌月を計算
        $previousMonth = $currentMonth->copy()->subMonth();
        $nextMonth = $currentMonth->copy()->addMonth();

        // 現在月の出勤記録を取得（当該月の1日〜月末まで）
        $attendanceRecords = AttendanceRecord::where('user_id', $id)
            ->whereBetween('date', [
                $currentMonth->copy()->startOfMonth(),
                $currentMonth->copy()->endOfMonth()
            ])
            ->orderBy('date', 'asc')
            ->get();

        return view('admin.attendance.staff.detail', compact(
            'staff',
            'attendanceRecords',
            'previousMonth',
            'currentMonth',
            'nextMonth'
        ));
    }


}
