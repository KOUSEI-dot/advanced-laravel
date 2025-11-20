@extends('layouts.authenticated')

@section('title', '勤怠詳細')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
<div class="attendance-wrapper">

    <h1 class="page-title">勤怠詳細</h1>

    <div class="attendance-card">

        {{-- 承認待ちならフォーム非表示 --}}
        @if ($hasPendingRequest ?? false)

            {{-- 承認待ち表示 --}}
            <table>
                <tr><th>名前</th><td>{{ $attendance->user->name }}</td></tr>

                <tr><th>日付</th>
                    <td>{{ $attendance->date ? $attendance->date->format('Y年 n月j日') : '' }}</td>
                </tr>

                <tr>
                    <th>出勤・退勤</th>
                    <td>
                        {{ $attendance->clock_in ? $attendance->clock_in->format('H:i') : '-' }}
                        〜
                        {{ $attendance->clock_out ? $attendance->clock_out->format('H:i') : '-' }}
                    </td>
                </tr>

                @foreach ($attendance->breakTimes as $i => $break)
                <tr>
                    <th>休憩 {{ $i+1 }}</th>
                    <td>
                        {{ $break->start_time ? $break->start_time->format('H:i') : '--:--' }}
                        〜
                        {{ $break->end_time ? $break->end_time->format('H:i') : '--:--' }}
                    </td>
                </tr>
                @endforeach

                <tr><th>備考</th><td>{{ $attendanceRequest->request_reason }}</td></tr>
            </table>

            <p class="notice-text">＊承認待ちのため修正はできません。</p>

        @else

        {{-- ▼▼ 修正フォーム ▼▼ --}}
        <form action="{{ route('attendance.request') }}" method="POST">
            @csrf
            <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">

            <table>

                {{-- 名前 --}}
                <tr>
                    <th>名前</th>
                    <td>{{ $attendance->user->name }}</td>
                </tr>

                {{-- 日付 --}}
                <tr>
                    <th>日付</th>
                    <td>{{ $attendance->date ? $attendance->date->format('Y年 n月j日') : '' }}</td>
                </tr>

                {{-- 出勤・退勤 --}}
                <tr>
                    <th>出勤・退勤</th>
                    <td>
                        <input type="time"
                            name="requested_clock_in"
                            value="{{ $attendance->clock_in ? $attendance->clock_in->format('H:i') : '' }}">
                        〜
                        <input type="time"
                            name="requested_clock_out"
                            value="{{ $attendance->clock_out ? $attendance->clock_out->format('H:i') : '' }}">
                    </td>
                </tr>

                {{-- 休憩時間 --}}
                @foreach ($attendance->breakTimes as $i => $break)
                <tr>
                    <th>休憩 {{ $i+1 }}</th>
                    <td>
                        {{-- start --}}
                        <input type="time"
                            name="requested_breaks[{{ $i }}][start]"
                            value="{{ $break->start_time ? $break->start_time->format('H:i') : '' }}">

                        〜

                        {{-- end --}}
                        <input type="time"
                            name="requested_breaks[{{ $i }}][end]"
                            value="{{ $break->end_time ? $break->end_time->format('H:i') : '' }}">
                    </td>
                </tr>
                @endforeach

                {{-- 備考 --}}
                <tr>
                    <th>備考</th>
                    <td>
                        <textarea name="request_reason"
                                required
                                placeholder="理由を入力してください"></textarea>
                    </td>
                </tr>

            </table>

            <div class="button-area">
                <button type="submit" class="submit-btn">修正</button>
            </div>
        </form>
        {{-- ▲▲ 修正フォームここまで ▲▲ --}}


        @endif

    </div>
</div>
@endsection

