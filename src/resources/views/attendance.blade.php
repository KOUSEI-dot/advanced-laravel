@extends('layouts.authenticated')

@section('title', '勤怠管理')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
<div class="attendance-container">

    {{-- 勤務ステータス --}}
    <span id="status" class="status-badge">
        {{ $attendance->status ?? '勤務外' }}
    </span>

    {{-- 日付 --}}
    <h2 class="current-date">
        {{ \Carbon\Carbon::now()->locale('ja')->isoFormat('YYYY年M月D日(dd)') }}
    </h2>

    {{-- 現在時刻 --}}
    <p id="currentTime" class="current-time"></p>

    {{-- ボタンエリア --}}
    <div class="button-area">

        {{-- ▼ 勤務外：出勤 --}}
        @if (!$attendance)
            <form id="startWorkForm" method="POST" action="{{ route('start.work') }}">
                @csrf
                <button type="button" id="startWork" class="btn btn-black">出勤</button>
            </form>

        {{-- ▼ 出勤中：退勤＋休憩入（横並び） --}}
        @elseif ($attendance->status === '出勤中')
            <div class="btn-group-horizontal">

                <form id="endWorkForm" method="POST" action="{{ route('end.work') }}">
                    @csrf
                    <button type="button" id="endWork" class="btn btn-black">退勤</button>
                </form>

                <form id="startBreakForm" method="POST" action="{{ route('start.break') }}">
                    @csrf
                    <button type="button" id="startBreak" class="btn btn-white">休憩入</button>
                </form>

            </div>

        {{-- ▼ 休憩中：休憩戻 --}}
        @elseif ($attendance->status === '休憩中')
            <form id="endBreakForm" method="POST" action="{{ route('end.break') }}">
                @csrf
                <button type="button" id="endBreak" class="btn btn-white">休憩戻</button>
            </form>

        {{-- ▼ 退勤済み --}}
        @elseif ($attendance->status === '退勤済み')
            <p class="finished-text">お疲れ様でした。</p>
        @endif

    </div>
</div>

{{-- JS（レイアウトに依存しないようローカルに直書き） --}}
<script>
document.addEventListener("DOMContentLoaded", function() {

    // === 現在時刻の更新 ===
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('ja-JP', {
            hour: '2-digit',
            minute: '2-digit'
        });
        document.getElementById('currentTime').textContent = timeString;
    }
    updateTime();
    setInterval(updateTime, 1000);

    // === ボタンクリックで form.submit() を発火 ===
    const buttonToForm = {
        startWork: "startWorkForm",
        endWork: "endWorkForm",
        startBreak: "startBreakForm",
        endBreak: "endBreakForm"
    };

    for (const [btnId, formId] of Object.entries(buttonToForm)) {
        const btn = document.getElementById(btnId);
        if (btn) {
            btn.addEventListener("click", function() {
                document.getElementById(formId).submit();
            });
        }
    }
});
</script>

@endsection
