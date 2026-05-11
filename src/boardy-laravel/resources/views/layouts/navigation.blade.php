<nav>
    <div class="nav-left">
        <a href="{{ route('posts.index') }}" class="brand">Boardy</a>

        <a href="{{ route('posts.index') }}"
           class="{{ request()->routeIs('posts.index') ? 'active' : '' }}">
            Все посты
        </a>

        @auth
            <a href="{{ route('posts.create') }}"
               class="{{ request()->routeIs('posts.create') ? 'active' : '' }}">
                Добавить пост
            </a>
        @endauth
    </div>

    <div class="nav-right">
        @auth
            <span>Привет, {{ Auth::user()->name }}!</span>

            <form method="POST"
                  action="{{ route('logout') }}"
                  style="margin: 0; padding: 0; box-shadow: none; background: transparent;">
                @csrf

                <button type="submit"
                        style="margin: 0; padding: 0; background: transparent; border: none; color: white; font-weight: 500; cursor: pointer;">
                    Выйти
                </button>
            </form>
        @else
            <a href="{{ route('login') }}"
               class="{{ request()->routeIs('login') ? 'active' : '' }}">
                Вход
            </a>

            <a href="{{ route('register') }}"
               class="{{ request()->routeIs('register') ? 'active' : '' }}">
                Регистрация
            </a>
        @endauth
    </div>
</nav>