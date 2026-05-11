@extends('layouts.app')

@section('title', 'Boardy — Редактировать пост')

@section('content')
    <div class="submit-container">
        <main class="submit-card">
            <h2>Редактировать пост</h2>

            <form class="submit-form" method="POST" action="{{ route('posts.update', $post) }}">
                @csrf
                @method('PUT')

                <label for="title">Заголовок</label>
                <input
                    id="title"
                    type="text"
                    name="title"
                    value="{{ old('title', $post->title) }}"
                    required
                >

                <label for="body" style="margin-top: 18px;">Текст</label>
                <textarea
                    id="body"
                    name="body"
                    rows="6"
                    required
                >{{ old('body', $post->body) }}</textarea>

                <div class="submit-actions">
                    <button type="submit">Сохранить</button>
                    <a href="{{ route('posts.show', $post) }}">Отмена</a>
                </div>
            </form>
        </main>
    </div>
@endsection