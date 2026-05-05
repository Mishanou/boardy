<?php
require_once __DIR__ . '/partials/session.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Boardy</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<?php include __DIR__ . '/partials/nav.php'; ?>

<div class="container">
    <main>
        <section class="hero-card">
            <h1>Boardy</h1>
            <p class="hero-subtitle">Микро-доска объявлений</p>
            <p>Учебный проект курса «Архитектура веб-приложений».</p>
            <p>Публикуйте посты, комментируйте и получайте уведомления в реальном времени.</p>

            <div class="hero-actions">
                <a class="button-link" href="/messages.php">Смотреть посты</a>
                <a class="button-link secondary" href="/submit.php">Добавить пост</a>
            </div>
        </section>

        <section class="info-card">
            <h2>О проекте</h2>
            <p>Boardy постепенно развивается: от статических страниц до авторизации, базы данных, API и клиентского JavaScript.</p>
        </section>
    </main>

    <footer>
        <p>&copy; 2026 Boardy | Новокшонов М.Д.</p>
    </footer>
</div>
</body>
</html>