@extends('layouts.app')

@section('title', 'Boardy — Вход')

@section('content')
    <div class="auth-container">
        <div class="auth-card">
            <h2>Вход</h2>

            @if (session('status'))
                <div class="auth-error" style="color: #0f5132;">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="auth-error">
                    Неверный email или пароль.
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <label for="email">Email</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                >

                @error('email')
                    <div class="auth-error" style="margin-top: 8px;">
                        {{ $message }}
                    </div>
                @enderror

                <label for="password">Пароль</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                >

                @error('password')
                    <div class="auth-error" style="margin-top: 8px;">
                        {{ $message }}
                    </div>
                @enderror

                <label style="display: flex; align-items: center; gap: 8px; margin-top: 18px; font-weight: 500;">
                    <input
                        id="remember_me"
                        type="checkbox"
                        name="remember"
                        style="width: auto; margin: 0;"
                    >
                    Запомнить меня
                </label>

                <button type="submit">
                    Войти
                </button>
            </form>

            <div class="oauth-divider">
                или
            </div>

            <a href="{{ route('auth.github') }}" class="github-login">
                <span class="github-icon">●</span>
                Войти через GitHub
            </a>

            <div class="auth-link">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">
                        Забыли пароль?
                    </a>
                    <br>
                @endif

                Нет аккаунта?
                <a href="{{ route('register') }}">
                    Зарегистрироваться
                </a>
            </div>
        </div>
    </div>
@endsection