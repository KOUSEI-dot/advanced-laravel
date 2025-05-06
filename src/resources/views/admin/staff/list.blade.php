<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>スタッフ一覧</title>
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
    <h2 class="text-xl font-bold mb-4 text-center">スタッフ一覧</h2>

    <div class="bg-white shadow-md rounded-lg p-6 mx-auto w-2/3">
        <table class="w-full border border-gray-400">
            <thead>
                <tr class="bg-gray-200">
                    <th class="py-2 px-4 border border-gray-400">名前</th>
                    <th class="py-2 px-4 border border-gray-400">メールアドレス</th>
                    <th class="py-2 px-4 border border-gray-400">月次勤怠</th>
                </tr>
            </thead>
            <tbody>
                @foreach($staffs as $staff)
                <tr class="border-b border-gray-400">
                    <td class="py-2 px-4 border border-gray-400">{{ $staff->name }}</td>
                    <td class="py-2 px-4 border border-gray-400">{{ $staff->email }}</td>
                    <td class="py-2 px-4 border border-gray-400 text-center">
                        <a href="{{ route('admin.attendance.staff.detail', $staff->id) }}">詳細</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</main>

</body>
</html>
