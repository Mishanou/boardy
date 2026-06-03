<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Boardy')</title>

    <link rel="stylesheet" href="/css/style.css">
</head>
<body>

@include('layouts.navigation')

<div class="container">
    @if (session('success'))
        <div class="info-card" style="border-color: #badbcc; color: #0f5132; background: #d1e7dd;">
            {{ session('success') }}
        </div>
    @endif

    @if (session('status'))
        <div class="info-card" style="border-color: #badbcc; color: #0f5132; background: #d1e7dd;">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="info-card" style="border-color: #f5c2c7; color: #842029; background: #f8d7da;">
            <strong>Проверьте форму:</strong>

            <ul style="margin-top: 10px; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <main>
        @yield('content')
    </main>

    <footer>
        <p>&copy; 2026 Boardy | Новокшонов М.Д.</p>
    </footer>
</div>

</body>
</html>