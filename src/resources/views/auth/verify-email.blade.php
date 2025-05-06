<!-- resources/views/auth/verify-email.blade.php -->
<x-guest-layout>
    <h1>メールアドレスを確認してください</h1>
    <p>登録時に送信されたリンクをクリックして、メールアドレスを確認してください。</p>

    @if (session('message'))
        <p style="color: green;">{{ session('message') }}</p>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit">再送信する</button>
    </form>
</x-guest-layout>
