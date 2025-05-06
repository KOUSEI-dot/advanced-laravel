<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者 - 勤怠一覧</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/list.css') }}">
</head>
<body class="bg-gray-100">

<header class="bg-black text-white p-4 flex justify-between items-center">
    <div class="header__inner">
        <img class="header__logo" src="/storage/logo.svg" alt="coachtechのロゴ">
    </div>
    <nav class="flex space-x-4">
        <a href="{{ url('/admin/attendance/list') }}" class="px-4 hover:underline hover:text-gray-300">勤怠一覧</a>
        <a href="{{ url('/admin/staff/list') }}" class="px-4 hover:underline hover:text-gray-300">スタッフ一覧</a>
        <a href="{{ route('admin.requests.list') }}" class="px-4 hover:underline hover:text-gray-300">申請一覧</a>
        <form action="{{ route('admin.logout') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="text-white hover:underline hover:text-gray-300">ログアウト</button>
        </form>
    </nav>
</header>

<main class="container mx-auto mt-8">
    <h2 class="text-xl font-bold mb-4 text-center">勤怠一覧</h2>

    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="calendar-header flex justify-between items-center mb-6">
            <a href="{{ route('admin.attendance.list', ['month' => $previousMonth->format('Y-m')]) }}" class="hover:underline">← 前月</a>
            <h2 class="flex items-center text-lg font-semibold calendar-title">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon-calendar w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10m1 5H6a2 2 0 01-2-2V7a2 2 0 012-2h12a2 2 0 012 2v11a2 2 0 01-2 2z"/>
                </svg>
                {{ $currentMonth->format('Y年n月') }}
            </h2>
            <a href="{{ route('admin.attendance.list', ['month' => $nextMonth->format('Y-m')]) }}" class="hover:underline">翌月 →</a>
        </div>

        <table class="w-full border border-gray-400">
            <thead>
                <tr class="bg-gray-200">
                    <th class="py-2 px-4 border">名前</th>
                    <th class="py-2 px-4 border">出勤</th>
                    <th class="py-2 px-4 border">退勤</th>
                    <th class="py-2 px-4 border">休憩</th>
                    <th class="py-2 px-4 border">合計</th>
                    <th class="py-2 px-4 border">詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendanceRecords as $record)
                <tr>
                    <td class="py-2 px-4 border">{{ $record->user->name }}</td>
                    <td class="py-2 px-4 border">
                        {{ $record->clock_in ? \Carbon\Carbon::parse($record->clock_in)->format('H:i') : '-' }}
                    </td>
                    <td class="py-2 px-4 border">
                        {{ $record->clock_out ? \Carbon\Carbon::parse($record->clock_out)->format('H:i') : '-' }}
                    </td>
                    <td class="py-2 px-4 border">
                        @php
                            $totalBreakTime = 0;
                            foreach ($record->breakTimes as $breakTime) {
                                if ($breakTime->break_start && $breakTime->break_end) {
                                    $start = \Carbon\Carbon::parse($breakTime->break_start);
                                    $end = \Carbon\Carbon::parse($breakTime->break_end);
                                    $totalBreakTime += $start->diffInMinutes($end);
                                }
                            }
                        @endphp
                        {{ $totalBreakTime > 0 ? sprintf('%d:%02d', floor($totalBreakTime / 60), $totalBreakTime % 60) : '0:00' }}
                    </td>
                    <td class="py-2 px-4 border">
                        @php
                            $workMinutes = 0;
                            if ($record->clock_in && $record->clock_out) {
                                $clockIn = \Carbon\Carbon::parse($record->clock_in);
                                $clockOut = \Carbon\Carbon::parse($record->clock_out);
                                $workMinutes = $clockIn->diffInMinutes($clockOut);
                            }
                            $netWorkMinutes = max(0, $workMinutes - $totalBreakTime);
                        @endphp
                        {{ sprintf('%d:%02d', floor($netWorkMinutes / 60), $netWorkMinutes % 60) }}
                    </td>
                    <td class="py-2 px-4 border text-center">
                        <a href="{{ route('admin.attendance.detail', ['id' => $record->id]) }}" class="text-black hover:underline">詳細</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</main>

</body>
</html>
