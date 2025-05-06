@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div class="login-form__content">
  <div class="login-form__heading">
    <h2>管理者ログイン</h2>
  </div>

  {{-- ログイン認証失敗（credentialsエラー） --}}
  @if ($errors->has('login'))
      <div class="form__error">
          {{ $errors->first('login') }}
      </div>
  @endif

  <form class="form" action="{{ route('admin.login') }}" method="post">
    @csrf

    {{-- メールアドレス --}}
    <div class="form__group">
      <div class="form__group-title">
        <span class="form__label--item">メールアドレス</span>
      </div>
      <div class="form__group-content">
        <div class="form__input--text">
          <input type="text" name="email" value="{{ old('email') }}" />
        </div>
        {{-- バリデーションエラー（email.required / email.email） --}}
        <div class="form__error">
          @error('email')
              {{ $message }}
          @enderror
        </div>
      </div>
    </div>

    {{-- パスワード --}}
    <div class="form__group">
      <div class="form__group-title">
        <span class="form__label--item">パスワード</span>
      </div>
      <div class="form__group-content">
        <div class="form__input--text">
          <input type="password" name="password" />
        </div>
        {{-- バリデーションエラー（password.required） --}}
        <div class="form__error">
          @error('password')
              {{ $message }}
          @enderror
        </div>
      </div>
    </div>

    <div class="form__button">
      <button class="form__button-submit" type="submit">管理者ログインする</button>
    </div>
  </form>
</div>
@endsection
