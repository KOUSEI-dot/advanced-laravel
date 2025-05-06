<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>勤怠管理</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-100">

<header class="bg-black text-white p-4 flex justify-between items-center">
    <div class="header__inner">
        <img class="header__logo" src="/storage/logo.svg" alt="coachtechのロゴ">
    </div>
    <nav class="flex">
        <a href="{{ url('/attendance') }}" class="px-4">勤怠</a>
        <a href="{{ url('/attendance/list') }}" class="px-4">勤怠一覧</a>
        <a href="{{ url('/stamp_correction_request/list') }}" class="px-4">申請</a>
        <form action="{{ route('logout') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="px-4 bg-transparent text-white border-none cursor-pointer">ログアウト</button>
        </form>
    </nav>
</header>

<main class="flex flex-col items-center justify-center min-h-screen">
    <div class="bg-white shadow-md rounded-lg p-8 text-center w-96">
        <span id="status" class="text-gray-500 text-sm bg-gray-200 px-3 py-1 rounded-full">
            {{ $attendance->status ?? '勤務外' }}
        </span>
        <h2 class="text-xl mt-2" id="currentDate">
            {{ \Carbon\Carbon::now()->locale('ja')->isoFormat('YYYY年MM月DD日 (dd)') }}
        </h2>
        <p class="text-4xl font-bold mt-2" id="currentTime"></p>

        <div class="mt-4">
            @if (!$attendance)
                <button id="startWork" class="bg-black text-white px-6 py-3 rounded-md mt-4 hover:bg-gray-800">
                    出勤
                </button>
            @elseif ($attendance->status === '出勤中')
                <div class="flex justify-center gap-4 mt-4">
                    <button id="endWork" class="bg-black text-white px-4 py-2 rounded-md">退勤</button>
                    <button id="startBreak" class="bg-white text-black border border-black px-4 py-2 rounded-md">休憩入</button>
                </div>
            @elseif ($attendance->status === '休憩中')
                <button id="endBreak" class="bg-white text-black border border-black px-4 py-2 rounded-md mt-4">休憩戻</button>
            @elseif ($attendance->status === '退勤済み')
                <p class="text-lg font-bold text-gray-700 mt-4">お疲れ様でした。</p>
            @endif
        </div>
    </div>
</main>

<script>
$(document).ready(function() {
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('ja-JP', {
            hour: '2-digit',
            minute: '2-digit'
        });
        $('#currentTime').text(timeString);
    }
    setInterval(updateTime, 1000);
    updateTime();

    $('#startWork').click(function() {
        $.post("{{ route('start.work') }}", {_token: "{{ csrf_token() }}"}, function() {
            location.reload();
        });
    });

    $('#startBreak').click(function() {
        $.post("{{ route('start.break') }}", {_token: "{{ csrf_token() }}"}, function() {
            location.reload();
        });
    });

    $('#endBreak').click(function() {
        $.post("{{ route('end.break') }}", {_token: "{{ csrf_token() }}"}, function() {
            location.reload();
        });
    });

    $('#endWork').click(function() {
        $.post("{{ route('end.work') }}", {_token: "{{ csrf_token() }}"}, function() {
            location.reload();
        });
    });
});
</script>

</body>
</html>
