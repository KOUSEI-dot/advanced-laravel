@extends('layouts.admin')

@section('title', '勤怠詳細')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
<div class="attendance-wrapper">

    {{-- タイトル --}}
    <h1 class="page-title">勤怠詳細</h1>

    {{-- 白いカード --}}
    <div class="attendance-card">

        @if (isset($hasPendingRequest) && $hasPendingRequest)
            <table>
                <tr>
                    <th>名前</th>
                    <td>{{ $attendance->user->name }}</td>
                </tr>
                <tr>
                    <th>日付</th>
                    <td>{{ \Carbon\Carbon::parse($attendance->date)->format('Y年 n月j日') }}</td>
                </tr>
                <tr>
                    <th>出勤・退勤</th>
                    <td>
                        {{ $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '-' }}
                        <span>〜</span>
                        {{ $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '-' }}
                    </td>
                </tr>

                @foreach ($attendance->breakTimes as $index => $breakTime)
                <tr>
                    <th>休憩 {{ $index + 1 }}</th>
                    <td>
                        {{ $breakTime->break_start ? \Carbon\Carbon::parse($breakTime->break_start)->format('H:i') : '-' }}
                        <span>〜</span>
                        {{ $breakTime->break_end ? \Carbon\Carbon::parse($breakTime->break_end)->format('H:i') : '-' }}
                    </td>
                </tr>
                @endforeach

                <tr>
                    <th>備考</th>
                    <td>{{ $attendanceRequest->request_reason ?? '—' }}</td>

                </tr>
            </table>

            <p class="notice-text">＊承認待ちのため修正はできません。</p>
        @else
            {{-- ▼▼ 管理者 修正フォーム ▼▼ --}}
        <form action="{{ route('attendance.request') }}" method="POST">
            @csrf

            <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">

            <table>
                <tr>
                    <th>名前</th>
                    <td>{{ $attendance->user->name }}</td>
                </tr>

                <tr>
                    <th>日付</th>
                    <td>{{ \Carbon\Carbon::parse($attendance->date)->format('Y年 n月j日') }}</td>
                </tr>

                <tr>
                    <th>出勤・退勤</th>
                    <td>
                        <input type="time"
                            name="requested_clock_in"
                            value="{{ $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '' }}">
                        〜
                        <input type="time"
                            name="requested_clock_out"
                            value="{{ $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '' }}">
                    </td>
                </tr>

                @foreach ($attendance->breakTimes as $index => $breakTime)
                <tr>
                    <th>休憩 {{ $index + 1 }}</th>
                    <td>
                        <input type="time"
                            name="requested_breaks[{{ $index }}][start]"
                            value="{{ $breakTime->break_start ? \Carbon\Carbon::parse($breakTime->break_start)->format('H:i') : '' }}">
                        〜
                        <input type="time"
                            name="requested_breaks[{{ $index }}][end]"
                            value="{{ $breakTime->break_end ? \Carbon\Carbon::parse($breakTime->break_end)->format('H:i') : '' }}">
                    </td>
                </tr>
                @endforeach

                <tr>
                    <th>備考</th>
                    <td>
                        <textarea name="request_reason" required></textarea>
                    </td>
                </tr>
            </table>

            <div class="button-area">
                <button type="submit" class="submit-btn">修正</button>
            </div>
        </form>
        {{-- ▲▲ 管理者 修正フォームここまで ▲▲ --}}

        @endif
    </div>
</div>
@endsection
