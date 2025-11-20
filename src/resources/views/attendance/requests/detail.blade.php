@extends('layouts.authenticated')

@section('title', '申請詳細')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
<div class="attendance-wrapper">

    <h1 class="page-title">申請詳細</h1>

    <div class="attendance-card">

        {{-- ▼▼ 基本情報 ▼▼ --}}
        <table class="detail-table">
            <tr>
                <th>名前</th>
                <td>{{ $requestData->user->name }}</td>
            </tr>

            <tr>
                <th>日付</th>
                <td>
                    {{ optional($requestData->attendanceRecord->date)->format('Y年n月j日') ?? '-' }}
                </td>
            </tr>

            {{-- 出勤・退勤 --}}
            <tr>
                <th>出勤・退勤</th>
                <td>
                    {{ $requestData->requested_clock_in 
                        ? \Carbon\Carbon::parse($requestData->requested_clock_in)->format('H:i')
                        : '-' }}

                    <span>〜</span>

                    {{ $requestData->requested_clock_out
                        ? \Carbon\Carbon::parse($requestData->requested_clock_out)->format('H:i')
                        : '-' }}
                </td>
            </tr>

            {{-- 休憩時間 --}}
            @php
                $breaks = $requestData->requested_breaks ?? [];
            @endphp

            @if(is_array($breaks) && count($breaks) > 0)
                @foreach ($breaks as $i => $break)
                <tr>
                    <th>休憩 {{ $i+1 }}</th>
                    <td>
                        {{ $break['start'] ?? '--:--' }}  
                        <span>〜</span>
                        {{ $break['end'] ?? '--:--' }}
                    </td>
                </tr>
                @endforeach
            @else
                <tr>
                    <th>休憩</th>
                    <td>-</td>
                </tr>
            @endif

            {{-- 備考 --}}
            <tr>
                <th>備考</th>
                <td>{{ $requestData->request_reason }}</td>
            </tr>
        </table>

        <div class="button-area">
            <a href="{{ route('attendance.request.userlist') }}" class="back-link">
                一覧に戻る
            </a>
        </div>

    </div>

</div>
@endsection
