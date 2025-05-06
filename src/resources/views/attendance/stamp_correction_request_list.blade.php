<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>勤怠申請一覧</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/stamp_correction.css') }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-100 text-black">

<header class="bg-black text-white p-4 flex justify-between items-center">
    <div class="header__inner">
        <img class="header__logo" src="/storage/logo.svg" alt="coachtechのロゴ">
    </div>
    <nav class="flex space-x-4">
        <a href="{{ url('/attendance') }}" class="px-4 text-white">勤怠</a>
        <a href="{{ url('/attendance/list') }}" class="px-4 text-white">勤怠一覧</a>
        <a href="{{ url('/stamp_correction_request/list') }}" class="px-4 text-white">申請</a>
        <form action="{{ route('logout') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="text-white">ログアウト</button>
        </form>
    </nav>
</header>

<div class="container mx-auto p-6">
    <!-- タブ切り替え -->
    <div class="tab-buttons flex space-x-4 mb-4">
        <button class="tab-button active text-black" id="pending-tab" onclick="showTab('pending')">承認待ち</button>
        <button class="tab-button text-black" id="approved-tab" onclick="showTab('approved')">承認済み</button>
    </div>
    <div class="tab-border"></div>

    @php
        $isAdmin = Auth::user()->role === 'admin';
    @endphp

    <div class="tab-content">
        <!-- 承認待ち -->
        <div id="pending" class="tab-pane active">
            <table class="table-auto w-full bg-white shadow-md rounded text-black">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="p-2">状態</th>
                        <th class="p-2">名前</th>
                        <th class="p-2">対象日時</th>
                        <th class="p-2">申請理由</th>
                        <th class="p-2">申請日時</th>
                        <th class="p-2">詳細</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($pendingRequests as $request)
                    <tr class="border-b">
                        <td class="p-2 status">承認待ち</td>
                        <td class="p-2">{{ $request->user->name }}</td>
                        <td class="p-2">
                            @if ($request->attendanceRecord)
                                {{ $request->attendanceRecord->date->format('Y/m/d') }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="p-2">{{ $request->request_reason ?? 'N/A' }}</td>
                        <td class="p-2">{{ $request->created_at->format('Y/m/d') }}</td>
                        <td class="p-2">
                            <a href="{{ $isAdmin
                                ? route('admin.attendance.request.detail', $request->id)
                                : route('user.attendance.request.detail', $request->id) }}"
                                class="text-black hover:underline">詳細</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <!-- 承認済み -->
        <div id="approved" class="tab-pane">
            <table class="table-auto w-full bg-white shadow-md rounded text-black">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="p-2">状態</th>
                        <th class="p-2">名前</th>
                        <th class="p-2">対象日時</th>
                        <th class="p-2">申請理由</th>
                        <th class="p-2">申請日時</th>
                        <th class="p-2">詳細</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($approvedRequests as $request)
                    <tr class="border-b">
                        <td class="p-2 status">承認済み</td>
                        <td class="p-2">{{ $request->user->name }}</td>
                        <td class="p-2">
                            @if ($request->attendanceRecord)
                                {{ $request->attendanceRecord->date->format('Y/m/d') }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="p-2">{{ $request->request_reason ?? 'N/A' }}</td>
                        <td class="p-2">{{ $request->created_at->format('Y/m/d') }}</td>
                        <td class="p-2">
                            <a href="{{ $isAdmin
                                ? route('admin.attendance.request.detail', $request->id)
                                : route('user.attendance.request.detail', $request->id) }}"
                                class="text-black hover:underline">詳細</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function showTab(tabId) {
    document.querySelectorAll('.tab-pane').forEach(tab => tab.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');

    document.querySelectorAll('.tab-button').forEach(button => button.classList.remove('active'));
    document.getElementById(tabId + '-tab').classList.add('active');
}
</script>

</body>
</html>
