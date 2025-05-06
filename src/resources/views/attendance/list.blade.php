<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>勤怠一覧</title>
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

<main class="container mx-auto p-6">

    <div class="calendar-header flex justify-between items-center mb-6">
        <a href="{{ route('attendance.list', ['month' => $previousMonth->format('Y-m')]) }}">← 前月</a>

        <h2 class="flex items-center text-lg font-semibold calendar-title">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon-calendar w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10m1 5H6a2 2 0 01-2-2V7a2 2 0 012-2h12a2 2 0 012 2v11a2 2 0 01-2 2z"/>
            </svg>
            {{ $currentMonth->format('Y年n月') }}
        </h2>

        <a href="{{ route('attendance.list', ['month' => $nextMonth->format('Y-m')]) }}">翌月 →</a>
    </div>

    <table class="table-auto w-full bg-white shadow-md rounded text-sm">
        <thead class="bg-gray-200">
            <tr>
                <th class="py-2 px-3">日付</th>
                <th class="py-2 px-3">曜日</th>
                <th class="py-2 px-3">出勤</th>
                <th class="py-2 px-3">退勤</th>
                <th class="py-2 px-3">休憩</th>
                <th class="py-2 px-3">合計</th>
                <th class="py-2 px-3">詳細</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($records as $dateStr => $record)
            @php
                $date = \Carbon\Carbon::parse($dateStr);
                $weekdayIndex = $date->dayOfWeek;
                $weekdayText = ['日', '月', '火', '水', '木', '金', '土'][$weekdayIndex];
                $weekdayColor = $weekdayIndex === 0 ? 'text-red-500' : ($weekdayIndex === 6 ? 'text-blue-500' : '');

                // 休憩時間の合計を計算
                $totalBreakMinutes = 0;
                foreach ($record->breakTimes as $break) {
                    if ($break->break_start && $break->break_end) {
                        $breakStart = \Carbon\Carbon::parse($break->break_start);
                        $breakEnd = \Carbon\Carbon::parse($break->break_end);
                        $totalBreakMinutes += $breakStart->diffInMinutes($breakEnd);
                    }
                }

                // 合計休憩時間を「H:i」形式に変換
                $breakHours = floor($totalBreakMinutes / 60);
                $breakMinutes = $totalBreakMinutes % 60;
                $formattedBreakTime = sprintf("%02d:%02d", $breakHours, $breakMinutes);
            @endphp
            <tr>
                <td class="py-2 px-3">{{ $date->format('n/j') }}</td>
                <td class="py-2 px-3 {{ $weekdayColor }}">{{ $weekdayText }}</td>
                <td>{{ $record->clock_in ? \Carbon\Carbon::parse($record->clock_in)->format('H:i') : '-' }}</td>
                <td>{{ $record->clock_out ? \Carbon\Carbon::parse($record->clock_out)->format('H:i') : '-' }}</td>
                <td>{{ $formattedBreakTime }}</td>
                <td>
                    @if ($record->total_work_minutes !== null)
                        @php
                            $workHours = floor($record->total_work_minutes / 60);
                            $workMinutes = $record->total_work_minutes % 60;
                            $formattedWorkTime = sprintf("%02d:%02d", $workHours, $workMinutes);
                        @endphp
                        {{ $formattedWorkTime }}
                    @else
                        -
                    @endif
                </td>
                <td>
                    <a href="{{ route('attendance.detail', ['id' => $record->id]) }}">詳細</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

</main>

</body>
</html>
