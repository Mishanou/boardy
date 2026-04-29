<?php
$is_logged = !empty($_SESSION['user_id']);
$user_name = $_SESSION['user_name'] ?? '';
$current = basename($_SERVER['PHP_SELF']);
?>
<nav>
    <div class="nav-left">
        <a href="/" class="brand">Boardy</a>
        <a href="/messages.php" class="<?= $current === 'messages.php' ? 'active' : '' ?>">Все посты</a>

        <?php if ($is_logged): ?>
            <a href="/submit.php" class="<?= $current === 'submit.php' ? 'active' : '' ?>">Добавить пост</a>
        <?php endif; ?>
    </div>

    <div class="nav-right">
        <?php if ($is_logged): ?>
            <span>Привет, <?= htmlspecialchars($user_name) ?>!</span>
            <a href="/logout.php">Выйти</a>
        <?php else: ?>
            <a href="/login.php" class="<?= $current === 'login.php' ? 'active' : '' ?>">Вход</a>
            <a href="/register.php" class="<?= $current === 'register.php' ? 'active' : '' ?>">Регистрация</a>
        <?php endif; ?>
    </div>
</nav>