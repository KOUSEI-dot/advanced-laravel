<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>勤怠詳細</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
</head>
<body class="bg-gray-100">

<header class="bg-black text-white p-4 flex justify-between items-center">
    <div class="header__inner">
        <img class="header__logo" src="/storage/logo.svg" alt="coachtechのロゴ">
    </div>
    <nav class="flex space-x-4">
        <a href="{{ url('/admin/attendance/list') }}" class="px-4">勤怠一覧</a>
        <a href="{{ url('/admin/staff/list') }}" class="px-4">スタッフ一覧</a>
        <a href="{{ route('admin.requests.list') }}" class="px-4">申請一覧</a>
        <form action="{{ route('admin.logout') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="text-white">ログアウト</button>
        </form>
    </nav>
</header>


<main class="container mx-auto mt-8">
    <h2 class="text-xl font-bold mb-4 text-center">勤怠詳細</h2>

    @if(session('success'))
        <div class="bg-green-500 text-white p-3 rounded-md text-center mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg p-6 mx-auto w-2/3">
    <form action="{{ route('admin.attendance.request') }}" method="POST">
    @csrf
    <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">
    <input type="hidden" name="user_id" value="{{ $attendance->user->id }}">

    <table class="w-full border border-gray-400">
        <tr class="border-b border-gray-400">
            <th class="py-2 px-4 border-r border-gray-400">名前</th>
            <td class="py-2 px-4">{{ $attendance->user->name }}</td>
        </tr>
        <tr class="border-b border-gray-400">
            <th class="py-2 px-4 border-r border-gray-400">日付</th>
            <td class="py-2 px-4">{{ \Carbon\Carbon::parse($attendance->date)->format('Y年 m月d日') }}</td>
        </tr>
        <tr class="border-b border-gray-400">
            <th class="py-2 px-4 border-r border-gray-400">出勤・退勤</th>
            <td class="py-2 px-4">
                    <input type="time" name="clock_in" value="<?php echo e($attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : ''); ?>" class="border px-2 py-1">
                    分 〜
                    <input type="time" name="clock_out" value="<?php echo e($attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : ''); ?>" class="border px-2 py-1">
                    分
                </td>
        </tr>
        <tr class="border-b border-gray-400">
            <th class="py-2 px-4 border-r border-gray-400">休憩</th>
            <td class="py-2 px-4">
            @foreach($attendance->breakTimes as $breakTime)
                <div class="break-input mb-2">
                    <input type="time" name="break_start[]" value="{{ $breakTime->break_start ? \Carbon\Carbon::parse($breakTime->break_start)->format('H:i') : '' }}" class="border px-2 py-1">
                    分 〜
                    <input type="time" name="break_end[]" value="{{ $breakTime->break_end ? \Carbon\Carbon::parse($breakTime->break_end)->format('H:i') : '' }}" class="border px-2 py-1">
                </div>
            @endforeach
            </td>
            </tr>

        <tr>
            <th class="py-2 px-4 border-r border-gray-400">備考</th>
            <td class="py-2 px-4">
                <textarea name="reason" class="border w-full px-2 py-1" required></textarea>
            </td>
        </tr>
    </table>

    <div class="text-center mt-6">
        <button type="submit" class="bg-black text-white px-6 py-2 rounded-md">修正</button>
    </div>
    </form>

    </div>
</main>

</body>
</html>
