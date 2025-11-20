<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', '管理者 - 勤怠管理')</title>

    {{-- ▼ ユーザー側と同じ CSS 構成に統一 --}}
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">

    @yield('css')
</head>
<body>

{{-- ▼ ヘッダー（ユーザーと完全に同じ HTML 構造） --}}
<header>
    <div class="header__inner">
        <img class="header__logo" src="/storage/logo.svg" alt="coachtechのロゴ">
    </div>

    <nav>
        <a href="{{ route('admin.attendance') }}">勤怠一覧</a>
        <a href="{{ route('admin.staff.list') }}">スタッフ一覧</a>
        <a href="{{ route('admin.requests.list') }}">申請一覧</a>

        <form action="{{ route('admin.logout') }}" method="POST" class="logout-form">
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
