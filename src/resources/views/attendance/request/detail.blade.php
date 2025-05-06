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

<main class="container mx-auto mt-8">
    <h2 class="text-xl font-bold mb-4 text-center">勤怠詳細</h2>

    <div class="bg-white shadow-md rounded-lg p-6 mx-auto w-2/3">
        <form action="{{ route('attendance.request.detail', ['id' => $attendanceRequest->id]) }}" method="POST">
            @csrf
            <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">
            <input type="hidden" name="user_id" value="{{ $attendance->user->id }}">

            <table class="w-full border border-gray-300">
                <tr class="border-b">
                    <th class="py-2 px-4 border-r">名前</th>
                    <td class="py-2 px-4">{{ $attendance->user->name }}</td>
                </tr>
                <tr class="border-b">
                    <th class="py-2 px-4 border-r">日付</th>
                    <td class="py-2 px-4">{{ \Carbon\Carbon::parse($attendance->date)->format('Y年 m月d日') }}</td>
                </tr>
                <tr class="border-b">
                <th class="py-2 px-4 border-r">出勤・退勤</th>
                <td class="py-2 px-4">
                    <input type="time" name="clock_in" value="<?php echo e($attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : ''); ?>" class="border px-2 py-1">
                    分 〜
                    <input type="time" name="clock_out" value="<?php echo e($attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : ''); ?>" class="border px-2 py-1">
                    分
                </td>
                </tr>

                @foreach ($attendance->breakTimes as $index => $breakTime)
                <tr class="border-b">
                    <th class="py-2 px-4 border-r">休憩 {{ $index + 1 }}</th>
                    <td class="py-2 px-4">
                        <div class="break-input">
                            <input type="time" name="break_start[]" value="{{ $breakTime->break_start ? \Carbon\Carbon::parse($breakTime->break_start)->format('H:i') : '' }}" class="border px-2 py-1">
                            分 〜
                            <input type="time" name="break_end[]" value="{{ $breakTime->break_end ? \Carbon\Carbon::parse($breakTime->break_end)->format('H:i') : '' }}" class="border px-2 py-1">
                        </div>
                    </td>
                </tr>
                @endforeach




                <th class="py-2 px-4 border-r">備考</th>
                    <td class="py-2 px-4">
                        <textarea name="request_reason" class="border w-full px-2 py-1" required readonly>{{ $attendanceRequest->request_reason }}</textarea>
                    </td>
                </tr>
            </table>

            @if ($hasPendingRequest)
                <div class="text-center mt-6 text-red-500">
                    <p>承認待ちのため修正できません</p>
                </div>
        </form>
        @endif
    </div>
</main>

<script>
// 新しい休憩入力フィールドを動的に追加
document.getElementById('add-break').addEventListener('click', function() {
    const breaksContainer = document.getElementById('breaks-container');

    // 新しい休憩入力フィールドを作成
    const newBreakInput = document.createElement('div');
    newBreakInput.classList.add('break-input');

    newBreakInput.innerHTML =
        <input type="number" name="break_start[]" step="1" class="border px-2 py-1" required>
        分
        〜
        <input type="number" name="break_end[]" step="1" class="border px-2 py-1" required>
        分
    ;
    breaksContainer.appendChild(newBreakInput);
});
</script>

</body>
</html>


