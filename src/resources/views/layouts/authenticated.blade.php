<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', '勤怠管理')</title>

    {{-- 共通CSS --}}
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    @yield('css')
</head>
<body>

<header>
    <div class="header__inner">
        <img class="header__logo" src="/storage/logo.svg" alt="coachtechのロゴ">
    </div>

    <nav>
        {{-- 勤怠トップ --}}
        <a href="{{ route('attendance') }}">勤怠</a>

        {{-- 勤怠一覧 --}}
        <a href="{{ route('attendance.list') }}">勤怠一覧</a>

        {{-- 修正申請一覧（新仕様） --}}
        <a href="{{ route('attendance.request.userlist') }}">申請</a>

        {{-- ログアウト --}}
        <form action="{{ route('logout') }}" method="POST" class="logout-form">
            @csrf
            <button type="submit">ログアウト</button>
        </form>
    </nav>
</header>

<main>
    @yield('content')
</main>

</body>
</html>
