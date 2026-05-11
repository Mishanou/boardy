@extends('layouts.app')

@section('title', 'Boardy — посты')

@section('content')
    <header>
        <h1>Все посты</h1>
    </header>

    @auth
        <div style="margin-bottom: 24px;">
            <a href="{{ route('posts.create') }}" class="button-link">
                Добавить пост
            </a>
        </div>
    @endauth

    @forelse ($posts as $post)
        @if ($loop->first)
            <div class="posts-list">
        @endif

        <div class="post-card">
            <div class="post-header">
                <span class="post-author">
                    {{ $post->author->name ?? 'Неизвестно' }}
                </span>

                <span class="post-date">
                    {{ $post->created_at->diffForHumans() }}
                </span>
            </div>

            <h2 style="margin-top: 0;">
                <a href="{{ route('posts.show', $post) }}">
                    {{ $post->title }}
                </a>
            </h2>

            <div class="post-body">
                {{ \Illuminate\Support\Str::limit($post->body, 300) }}
            </div>

            <div style="margin-top: 14px;">
                <a href="{{ route('posts.show', $post) }}">
                    Читать полностью
                </a>
            </div>
        </div>

        @if ($loop->last)
            </div>
        @endif
    @empty
        <p>Сообщений пока нет.</p>
    @endforelse

    @if ($posts->hasPages())
        <div style="margin-top: 30px; display: flex; gap: 14px; align-items: center;">
            @if ($posts->onFirstPage())
                <span style="color: #999;">← Назад</span>
            @else
                <a href="{{ $posts->previousPageUrl() }}">← Назад</a>
            @endif

            <span style="color: #777;">
                Страница {{ $posts->currentPage() }} из {{ $posts->lastPage() }}
            </span>

            @if ($posts->hasMorePages())
                <a href="{{ $posts->nextPageUrl() }}">Вперёд →</a>
            @else
                <span style="color: #999;">Вперёд →</span>
            @endif
        </div>
    @endif
@endsection