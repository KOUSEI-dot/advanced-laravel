<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>申請詳細</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
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
    <h2 class="text-xl font-bold mb-4 text-center">申請詳細</h2>

    <div class="bg-white shadow-md rounded-lg p-6 mx-auto w-2/3">
        <table class="w-full border border-gray-300">
            <tr class="border-b">
                <th class="py-2 px-4 border-r">名前</th>
                <td class="py-2 px-4">{{ $attendanceRequest->user->name }}</td>
            </tr>
            <tr class="border-b">
                <th class="py-2 px-4 border-r">日付</th>
                <td class="py-2 px-4">{{ \Carbon\Carbon::parse($attendanceRequest->date)->format('Y年 m月d日') }}</td>
            </tr>
            <tr class="border-b">
                <th class="py-2 px-4 border-r">修正内容</th>
                <td class="py-2 px-4">
                    @if($attendanceRequest->clock_in)
                        出勤時刻: {{ $attendanceRequest->clock_in }}
                    @elseif($attendanceRequest->clock_out)
                        退勤時刻: {{ $attendanceRequest->clock_out }}
                    @elseif($attendanceRequest->break_start)
                        休憩開始時刻: {{ $attendanceRequest->break_start }}
                    @elseif($attendanceRequest->break_end)
                        休憩終了時刻: {{ $attendanceRequest->break_end }}
                    @endif
                </td>
            </tr>
            <tr class="border-b">
                <th class="py-2 px-4 border-r">修正理由</th>
                <td class="py-2 px-4">{{ $attendanceRequest->request_reason }}</td>
            </tr>
        </table>

        <div class="text-center mt-6">
            <form action="{{ route('attendance.approve', $attendanceRequest->id) }}" method="POST">
                @csrf
                <button type="submit" class="bg-black text-white px-6 py-2 rounded-md">承認</button>
            </form>
        </div>
    </div>
</main>

</body>
</html>
