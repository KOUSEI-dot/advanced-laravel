@extends('layouts.admin')

@section('title', '勤怠一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endsection

@section('content')

{{-- ▼ タイトル（自動切替） --}}
@if(isset($date))
    <h2 class="page-title">
        {{ \Carbon\Carbon::parse($date)->format('Y年n月j日の勤怠') }}
    </h2>
@else
    <h2 class="page-title">
        {{ $currentMonth->format('Y年n月') }}の勤怠
    </h2>
@endif


{{-- ▼ カレンダー移動UI（日別 or 月別） --}}
<div class="calendar-nav">

    {{-- 左側 --}}
    @if(isset($date))
        <a href="{{ route('admin.attendance', ['date' => $previousDate]) }}">← 前日</a>
    @else
        <a href="{{ route('admin.attendance.list', ['month' => $previousMonth->format('Y-m')]) }}">← 前月</a>
    @endif


    {{-- 中央 表示日付 --}}
    <div class="center-label">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon-calendar" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8 7V3m8 4V3m-9 8h10m1 5H6a2 2 0 01-2-2V7a2 2 0 012-2h12a2 2 0 012 2v11a2 2 0 01-2 2z"/>
        </svg>

        @if(isset($date))
            {{ \Carbon\Carbon::parse($date)->format('Y/m/d') }}
        @else
            {{ $currentMonth->format('Y/m') }}
        @endif
    </div>


    {{-- 右側 --}}
    @if(isset($date))
        <a href="{{ route('admin.attendance', ['date' => $nextDate]) }}">翌日 →</a>
    @else
        <a href="{{ route('admin.attendance.list', ['month' => $nextMonth->format('Y-m')]) }}">翌月 →</a>
    @endif

</div>


{{-- ▼ テーブル --}}
<div class="table-card">
    <table>
        <thead>
            <tr>
                <th>名前</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
        </thead>

        <tbody>
            @forelse($attendanceRecords as $record)
            <tr>
                <td>{{ $record->user->name }}</td>
                <td>{{ $record->clock_in ? \Carbon\Carbon::parse($record->clock_in)->format('H:i') : '-' }}</td>
                <td>{{ $record->clock_out ? \Carbon\Carbon::parse($record->clock_out)->format('H:i') : '-' }}</td>
                <td>{{ $record->break_time }}</td>
                <td>{{ $record->work_time }}</td>
                <td>
                    <a href="{{ route('admin.attendance.detail', $record->id) }}" class="detail-link">詳細</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6">データがありません</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
