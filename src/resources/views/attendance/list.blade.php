@extends('layouts.authenticated')

@section('title', '勤怠一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('css/list.css') }}">
@endsection

@section('content')
<div class="attendance-wrapper">
    {{-- タイトル --}}
    <h1 class="attendance-title">勤怠一覧</h1>

    {{-- 上部カード（カレンダー） --}}
    <div class="attendance-card calendar-card">
        <div class="calendar-header">
            <a href="{{ route('attendance.list', ['month' => $previousMonth->format('Y-m')]) }}" class="calendar-nav">← 前月</a>
            <div class="calendar-current">
                <svg xmlns="http://www.w3.org/2000/svg" class="calendar-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10m1 5H6a2 2 0 01-2-2V7a2 2 0 012-2h12a2 2 0 012 2v11a2 2 0 01-2 2z"/>
                </svg>
                {{ $currentMonth->format('Y/m') }}
            </div>
            <a href="{{ route('attendance.list', ['month' => $nextMonth->format('Y-m')]) }}" class="calendar-nav">翌月 →</a>
        </div>
    </div>

    {{-- 下部カード（勤怠一覧テーブル） --}}
    <div class="attendance-card">
        <div class="table-wrapper">
            <table class="attendance-table">
                <thead>
                    <tr>
                        <th>日付</th>
                        <th>出勤</th>
                        <th>退勤</th>
                        <th>休憩</th>
                        <th>合計</th>
                        <th>詳細</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $dateStr => $record)
                        @php
                            $date = \Carbon\Carbon::parse($dateStr);
                            $weekdayIndex = $date->dayOfWeek;
                            $weekdayText = ['日','月','火','水','木','金','土'][$weekdayIndex];
                            $weekdayColor = $weekdayIndex === 0 ? 'red' : ($weekdayIndex === 6 ? 'black' : '');

                            $totalBreakMinutes = 0;
                            foreach ($record->breakTimes as $break) {
                                if ($break->break_start && $break->break_end) {
                                    $totalBreakMinutes += \Carbon\Carbon::parse($break->break_start)
                                        ->diffInMinutes(\Carbon\Carbon::parse($break->break_end));
                                }
                            }

                            $formattedBreakTime = sprintf("%02d:%02d", floor($totalBreakMinutes / 60), $totalBreakMinutes % 60);
                            $formattedWorkTime = $record->total_work_minutes !== null
                                ? sprintf("%02d:%02d", floor($record->total_work_minutes / 60), $record->total_work_minutes % 60)
                                : '-';
                        @endphp
                        <tr>
                            <td class="{{ $weekdayColor }}">{{ $date->format('m/d') }}({{ $weekdayText }})</td>
                            <td>{{ $record->clock_in ? \Carbon\Carbon::parse($record->clock_in)->format('H:i') : '-' }}</td>
                            <td>{{ $record->clock_out ? \Carbon\Carbon::parse($record->clock_out)->format('H:i') : '-' }}</td>
                            <td>{{ $formattedBreakTime }}</td>
                            <td>{{ $formattedWorkTime }}</td>
                            <td><a href="{{ route('attendance.detail', ['id' => $record->id]) }}" class="detail-link">詳細</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
