<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>スタッフ勤怠詳細</title>
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
        <a href="{{ url('/stamp_correction_request/list') }}" class="px-4">申請一覧</a>
        <form action="{{ route('admin.logout') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="text-white">ログアウト</button>
        </form>
    </nav>
</header>

        <div class="container">
            <h2>{{ $staff->name }}さんの勤怠</h2>

            <div class="calendar-header flex justify-between items-center mb-6">
                <a href="{{ url()->current() . '?month=' . $previousMonth->format('Y-m') }}">← 前月</a>

                    <h2 class="flex items-center text-lg font-semibold calendar-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon-calendar w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10m1 5H6a2 2 0 01-2-2V7a2 2 0 012-2h12a2 2 0 012 2v11a2 2 0 01-2 2z"/>
                        </svg>
                        {{ $currentMonth->format('Y年n月') }}
                    </h2>
                <a href="{{ url()->current() . '?month=' . $nextMonth->format('Y-m') }}">翌月 →</a>
            </div>

            <table class="attendance-table">
                <thead>
                    <tr>
                        <th>日付</th>
                        <th>出勤</th>
                        <th>退勤</th>
                        <th>休憩</th>
                        <th>合計</th>
                        <th>詳細</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($attendanceRecords as $record)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($record->date)->format('m/d') }}({{ ['日','月','火','水','木','金','土'][\Carbon\Carbon::parse($record->date)->dayOfWeek] }})</td>
                            <td>{{ \Carbon\Carbon::parse($record->clock_in)->format('H:i') }}</td>
                            <td>{{ \Carbon\Carbon::parse($record->clock_out)->format('H:i') }}</td>
                            <td>{{ formatMinutes($record->break_minutes) }}</td>
                            <td>{{ formatMinutes($record->total_minutes) }}</td>
                            <td>
                                <a href="{{ route('admin.attendance.detail', ['id' => $record->id]) }}">詳細</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="flex justify-end mt-4">
            <form action="{{ route('admin.attendance.export', ['staff_id' => $staff->id]) }}" method="POST">
                @csrf
                <button type="submit" class="csv-button">CSV出力</button>
            </form>
            </div>
        </div>


        @php
        function formatMinutes($minutes) {
            return sprintf('%02d:%02d', floor($minutes / 60), $minutes % 60);
        }
        @endphp

        <style>
        .container {
            background: #f5f5f5;
            padding: 40px;
            max-width: 800px;
            margin: auto;
            border-radius: 10px;
        }
        h2 {
            font-size: 20px;
            margin-bottom: 20px;
        }
        .filter-form {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }
        .attendance-table th,
        .attendance-table td {
            padding: 10px;
            border-bottom: 1px solid #ccc;
            text-align: center;
        }
        .attendance-table th {
            font-weight: bold;
            background: #eee;
        }
        .csv-button {
            margin-top: 20px;
            padding: 10px 20px;
            background: #000;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        .csv-button:hover {
            background: #333;
        }
        </style>

</body>
</html>
