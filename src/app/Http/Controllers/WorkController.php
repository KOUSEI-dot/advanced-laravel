<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AttendanceRecord;
use Carbon\Carbon;

class WorkController extends Controller
{
    public function attendance()
    {
        $user = Auth::user();

        $attendance = AttendanceRecord::where('user_id', $user->id)
            ->whereDate('date', Carbon::today())
            ->first();

        return view('attendance', compact('attendance'));
    }

    public function startWork()
    {
        $user = Auth::user();
        $now = Carbon::now();

        AttendanceRecord::create([
            'user_id'  => $user->id,
            'date'     => $now->toDateString(),
            'clock_in' => $now->format('H:i'),
            'status'   => '出勤中',
        ]);

        return redirect()->route('attendance');
    }

    public function endWork()
    {
        $attendance = AttendanceRecord::where('user_id', Auth::id())
            ->whereDate('date', Carbon::today())
            ->first();

        if ($attendance) {
            $attendance->clock_out = Carbon::now()->format('H:i');
            $attendance->status = '退勤済み';
            $attendance->save();
        }

        return redirect()->route('attendance');
    }
}
