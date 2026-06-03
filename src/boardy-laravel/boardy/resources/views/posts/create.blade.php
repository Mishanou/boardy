@extends('layouts.app')

@section('title', 'Boardy — Добавить пост')

@section('content')
    <div class="submit-container">
        <main class="submit-card">
            <h2>Новый пост</h2>

            <form class="submit-form" method="POST" action="{{ route('posts.store') }}">
                @csrf

                <label for="title">Заголовок</label>
                <input
                    id="title"
                    type="text"
                    name="title"
                    value="{{ old('title') }}"
                    required
                    placeholder="Например: Продам велосипед"
                >

                <label for="body" style="margin-top: 18px;">Текст</label>
                <textarea
                    id="body"
                    name="body"
                    rows="6"
                    required
                    placeholder="Напишите ваше объявление..."
                >{{ old('body') }}</textarea>

                <div class="submit-actions">
                    <button type="submit">Опубликовать</button>
                    <a href="{{ route('posts.index') }}">Отмена</a>
                </div>
            </form>
        </main>
    </div>
@endsection