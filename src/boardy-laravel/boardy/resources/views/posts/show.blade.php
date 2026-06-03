@extends('layouts.app')

@section('title', 'Boardy — ' . $post->title)

@section('content')
    <div style="margin-bottom: 24px;">
        <a href="{{ route('posts.index') }}">← Назад к ленте</a>
    </div>

    <article class="post-card">
        <div class="post-header">
            <span class="post-author">
                {{ $post->author->name ?? 'Неизвестно' }}
            </span>

            <span class="post-date">
                {{ $post->created_at->diffForHumans() }}
            </span>
        </div>

        <h2 style="margin-top: 0;">
            {{ $post->title }}
        </h2>

        <div class="post-body">
            {!! nl2br(e($post->body)) !!}
        </div>

        <div style="margin-top: 20px; display: flex; gap: 12px; flex-wrap: wrap;">
            @can('update', $post)
                <a href="{{ route('posts.edit', $post) }}" class="button-link secondary">
                    Редактировать
                </a>
            @endcan

            @can('delete', $post)
                <form method="POST"
                      action="{{ route('posts.destroy', $post) }}"
                      style="margin: 0; padding: 0; box-shadow: none;"
                      onsubmit="return confirm('Удалить этот пост?');">
                    @csrf
                    @method('DELETE')

                    <button type="submit" style="margin: 0; background: #b00020;">
                        Удалить
                    </button>
                </form>
            @endcan
        </div>
    </article>

    <section style="margin-top: 36px;">
        <h2>Комментарии</h2>

        @forelse ($post->comments as $comment)
            <div class="post-card" style="margin-bottom: 14px;">
                <div class="post-header">
                    <span class="post-author">
                        {{ $comment->author->name ?? 'Неизвестно' }}
                    </span>

                    <span class="post-date">
                        {{ $comment->created_at->diffForHumans() }}
                    </span>
                </div>

                <div class="post-body">
                    {!! nl2br(e($comment->body)) !!}
                </div>
            </div>
        @empty
            <p>Комментариев пока нет.</p>
        @endforelse
    </section>

    @auth
        <section class="submit-card" style="margin-top: 30px;">
            <h2>Добавить комментарий</h2>

            <form class="submit-form" method="POST" action="{{ route('comments.store') }}">
                @csrf

                <input type="hidden" name="post_id" value="{{ $post->id }}">

                <label for="body">Текст комментария</label>
                <textarea
                    id="body"
                    name="body"
                    rows="5"
                    required
                    placeholder="Напишите комментарий..."
                >{{ old('body') }}</textarea>

                <div class="submit-actions">
                    <button type="submit">Отправить</button>
                </div>
            </form>
        </section>
    @else
        <div class="info-card" style="margin-top: 30px;">
            Чтобы оставить комментарий, нужно <a href="{{ url('/login') }}">войти</a>.
        </div>
    @endauth
@endsection