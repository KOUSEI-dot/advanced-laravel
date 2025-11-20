@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div class="login-form__content">
    <div class="login-form__heading">
        <h2>ログイン</h2>
    </div>

    {{-- ログイン失敗メッセージ --}}
    @if ($errors->has('login'))
        <div class="form__error text-center">
            {{ $errors->first('login') }}
        </div>
    @endif

    <form class="form" action="/login" method="post">
        @csrf
        {{-- メールアドレス --}}
        <div class="form__group">
            <label class="form__label--item">メールアドレス</label>
            <div class="form__input--text">
                <input type="email" name="email" value="{{ old('email') }}">
            </div>
            <div class="form__error">
                @error('email')
                    {{ $message }}
                @enderror
            </div>
        </div>

        {{-- パスワード --}}
        <div class="form__group">
            <label class="form__label--item">パスワード</label>
            <div class="form__input--text">
                <input type="password" name="password">
            </div>
            <div class="form__error">
                @error('password')
                    {{ $message }}
                @enderror
            </div>
        </div>

        {{-- ログインボタン --}}
        <div class="form__button">
            <button class="form__button-submit" type="submit">ログインする</button>
        </div>
    </form>

    {{-- 会員登録リンク --}}
    <div class="register__link">
        <a href="/register">会員登録はこちら</a>
    </div>
</div>
@endsection
