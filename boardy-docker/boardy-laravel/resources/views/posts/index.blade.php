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

    <div id="posts-feed" class="posts-list">
        @forelse ($posts as $post)
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
        @empty
            <p id="empty-posts-message">Сообщений пока нет.</p>
        @endforelse
    </div>

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

    <script>
        const wsUrl = 'ws://boardy.localhost/ws';

        function connect() {
            const ws = new WebSocket(wsUrl);

            ws.onopen = () => {
                console.log('WS connected');
            };

            ws.onmessage = (event) => {
                const msg = JSON.parse(event.data);

                if (msg.type === 'new_post') {
                    prependPost(msg.post);
                }
            };

            ws.onclose = () => {
                console.log('WS closed, reconnecting...');
                setTimeout(connect, 3000);
            };
        }

        function prependPost(post) {
            const feed = document.getElementById('posts-feed');

            if (!feed) return;

            const emptyMessage = document.getElementById('empty-posts-message');

            if (emptyMessage) emptyMessage.remove();

            const el = document.createElement('div');
            el.className = 'post-card';

            el.innerHTML = `
                <div class="post-header">
                    <span class="post-author">
                        ${escapeHtml(post.author)}
                    </span>

                    <span class="post-date">
                        только что
                    </span>
                </div>

                <h2 style="margin-top: 0;">
                    <a href="/posts/${post.id}">
                        ${escapeHtml(post.title)}
                    </a>
                </h2>

                <div class="post-body">
                    ${escapeHtml(post.body)}
                </div>

                <div style="margin-top: 14px;">
                    <a href="/posts/${post.id}">
                        Читать полностью
                    </a>
                </div>
            `;

            feed.prepend(el);
        }

        function escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        connect();
    </script>
@endsection
