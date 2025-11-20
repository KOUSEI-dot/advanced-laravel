@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/verify-email.css') }}">
@endsection

@section('content')
<div class="verify-form__content">
    <div class="verify-form__heading">
        <h2 class="verify-title">メール認証</h2>
    </div>

    <div class="verify-container">
        <p class="verify-message">
            登録していただいたメールアドレスに認証メールを送付しました。<br>
            メール認証を完了してください。
        </p>

        @if (session('status') == 'verification-link-sent')
            <p class="sent-message">新しい認証リンクをメールアドレスに送信しました。</p>
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="verify-button">認証はこちらから</button>
        </form>

        <div class="resend-link">
            <a href="{{ route('verification.send') }}">認証メールを再送する</a>
        </div>
    </div>
</div>
@endsection
