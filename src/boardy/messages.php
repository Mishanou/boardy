<?php
require_once __DIR__ . '/partials/session.php';
require_once 'db.php';
require_once 'functions.php';

$stmt = $pdo->query(
    'SELECT posts.body, users.name, posts.created_at
     FROM posts
     JOIN users ON posts.author_id = users.id
     ORDER BY posts.created_at DESC'
);
$messages = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Boardy — Сообщения</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<?php include __DIR__ . '/partials/nav.php'; ?>

<div class="container">
    <main>
        <h2>Все сообщения</h2>

        <?php if (empty($messages)): ?>
            <p>Сообщений пока нет.</p>
        <?php else: ?>
            <div class="posts-list">
                <?php foreach ($messages as $msg): ?>
                    <div class="post-card">
                        <div class="post-header">
                            <span class="post-author"><?= escape($msg['name']) ?></span>
                            <span class="post-date"><?= time_ago($msg['created_at']) ?></span>
                        </div>
                        <div class="post-body">
                            <?= nl2br(escape($msg['body'])) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2026 Boardy | Новокшонов М.Д.</p>
    </footer>
</div>
</body>
</html>