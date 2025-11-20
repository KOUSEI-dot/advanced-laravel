@extends('layouts.admin')

@section('title', '申請詳細')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')

<div class="attendance-wrapper">

    <h2 class="page-title">勤怠詳細</h2>

    <div class="attendance-card">

        <table class="detail-table">

            {{-- 名前 --}}
            <tr>
                <th>名前</th>
                <td>{{ $attendanceRequest->user->name }}</td>
            </tr>

            {{-- 日付 --}}
            <tr>
                <th>日付</th>
                <td>{{ \Carbon\Carbon::parse(optional($attendanceRequest->attendanceRecord)->date)->format('Y年n月j日') }}</td>
            </tr>

            {{-- 出勤・退勤 --}}
            <tr>
                <th>出勤・退勤</th>
                <td>
                    {{ $attendanceRequest->requested_clock_in 
                        ? \Carbon\Carbon::parse($attendanceRequest->requested_clock_in)->format('H:i')
                        : '-' }}

                    <span>〜</span>

                    {{ $attendanceRequest->requested_clock_out 
                        ? \Carbon\Carbon::parse($attendanceRequest->requested_clock_out)->format('H:i')
                        : '-' }}
                </td>
            </tr>

            {{-- 休憩 --}}
            <tr>
                <th>休憩</th>
                <td>
                    {{ $attendanceRequest->requested_break_start 
                        ? \Carbon\Carbon::parse($attendanceRequest->requested_break_start)->format('H:i')
                        : '-' }}
                    
                    <span>〜</span>

                    {{ $attendanceRequest->requested_break_end 
                        ? \Carbon\Carbon::parse($attendanceRequest->requested_break_end)->format('H:i')
                        : '-' }}
                </td>
            </tr>

            {{-- 備考 --}}
            <tr>
                <th>備考</th>
                <td>{{ $attendanceRequest->request_reason }}</td>
            </tr>

        </table>

       <div class="button-area">

        @if($attendanceRequest->status === 'approved')
            {{-- 承認済み（押せない） --}}
            <button class="submit-btn" style="background-color: #888; cursor: default;" disabled>
                承認済み
            </button>

        @else
            {{-- 承認前（承認ボタン） --}}
            <form action="{{ route('admin.requests.approve', $attendanceRequest->id) }}" method="POST">
                @csrf
                <button type="submit" class="submit-btn">承認</button>
            </form>


        @endif

</div>


    </div>

</div>

@endsection
