@extends('layouts.admin')

@section('title', $staff->name . 'さんの勤怠')

@section('css')
<link rel="stylesheet" href="{{ asset('css/staff_attendance_detail.css') }}">
@endsection

@section('content')

<div class="attendance-wrapper">

    {{-- タイトル --}}
    <h1 class="page-title">{{ $staff->name }}さんの勤怠</h1>

    {{-- 月移動部分 --}}
    <div class="month-nav">

        <a class="nav-btn" href="{{ url()->current() . '?month=' . $previousMonth->format('Y-m') }}">
            ← 前月
        </a>

        <div class="month-display">
            <img src="/storage/calendar.svg" alt="" class="calendar-icon">
            <span>{{ $currentMonth->format('Y年m月') }}</span>
        </div>

        <a class="nav-btn" href="{{ url()->current() . '?month=' . $nextMonth->format('Y-m') }}">
            翌月 →
        </a>

    </div>

    {{-- 白カード --}}
    <div class="attendance-card">

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
                @foreach ($attendanceRecords as $record)
                <tr>
                    <td>
                        {{ \Carbon\Carbon::parse($record->date)->format('m/d') }}
                        ({{ ['日','月','火','水','木','金','土'][\Carbon\Carbon::parse($record->date)->dayOfWeek] }})
                    </td>
                    <td>{{ \Carbon\Carbon::parse($record->clock_in)->format('H:i') }}</td>
                    <td>{{ \Carbon\Carbon::parse($record->clock_out)->format('H:i') }}</td>
                    <td>{{ formatMinutes($record->break_minutes) }}</td>
                    <td>{{ formatMinutes($record->total_minutes) }}</td>
                    <td class="detail-link">
                        <a href="{{ route('admin.attendance.detail', $record->id) }}">詳細</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="csv-area">
            <form action="{{ route('admin.attendance.export', $staff->id) }}" method="POST">
                @csrf
                <button type="submit" class="csv-btn">CSV出力</button>
            </form>
        </div>

    </div>

</div>


{{-- 時間表示 --}}
@php
function formatMinutes($minutes) {
    return sprintf('%02d:%02d', floor($minutes / 60), $minutes % 60);
}
@endphp

@endsection
