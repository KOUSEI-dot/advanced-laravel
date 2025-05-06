<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BreakTimeRequestController extends Controller
{
    public function store(Request $request)
    {
    $request->validate([
        'break_time_id' => 'required|exists:break_times,id',
        'requested_break_start' => 'nullable|date_format:H:i',
        'requested_break_end' => 'nullable|date_format:H:i',
        'reason' => 'required|string|max:255',
    ]);

    if (!$request->requested_break_start && !$request->requested_break_end) {
        return back()->withErrors(['休憩開始または終了時刻のいずれかを入力してください。']);
    }

    BreakTimeRequest::create([
        'user_id' => Auth::id(),
        'break_time_id' => $request->break_time_id,
        'requested_break_start' => $request->requested_break_start,
        'requested_break_end' => $request->requested_break_end,
        'reason' => $request->reason,
        'status' => 'pending',
    ]);

    return back()->with('success', '休憩時間の修正申請を送信しました。');
    }

}
