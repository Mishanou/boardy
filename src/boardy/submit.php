<?php
require_once __DIR__ . '/partials/session.php';

if (empty($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

require_once 'db.php';

$message = trim($_POST['message'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $message) {
    $stmt = $pdo->prepare(
        'INSERT INTO posts (title, body, author_id) VALUES (?, ?, ?)'
    );
    $stmt->execute(['Сообщение', $message, $_SESSION['user_id']]);

    header('Location: /messages.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Boardy — Добавить пост</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<?php include __DIR__ . '/partials/nav.php'; ?>

<div class="submit-container">
    <main class="submit-card">
        <h2>Новый пост</h2>

        <form class="submit-form" method="POST" action="/submit.php">
            <label for="message">Текст</label>
            <textarea
                id="message"
                name="message"
                rows="6"
                required
                placeholder="Напишите ваше объявление..."
            ><?= htmlspecialchars($message) ?></textarea>

            <div class="submit-actions">
                <button type="submit">Опубликовать</button>
                <a href="/messages.php">Отмена</a>
            </div>
        </form>
    </main>
</div>
</body>
</html>