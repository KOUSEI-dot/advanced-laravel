<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COACHTECH</title>

    {{-- Tailwind --}}
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    {{-- 共通CSS --}}
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    @yield('css')
</head>
<body class="bg-gray-100">

    {{-- ▼ ヘッダー（勤怠ページと同じ） --}}
    <header class="bg-black text-white p-4 flex justify-between items-center">
        <div class="header__inner">
            <img class="header__logo" src="{{ asset('storage/logo.svg') }}" alt="COACHTECHロゴ">
        </div>
    </header>

    {{-- ▼ メインコンテンツ（白カードなし・中央寄せ） --}}
    <main class="min-h-[calc(100vh-80px)] flex items-center justify-center py-16">
        <div class="w-full max-w-lg px-6">
            @yield('content')
        </div>
    </main>

</body>
</html>
