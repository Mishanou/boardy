<?php
require_once __DIR__ . '/partials/session.php';
require_once __DIR__ . '/db.php';

$error = '';
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '' || $email === '' || $password === '') {
        $error = 'Заполните все поля';
    } elseif (strlen($password) < 6) {
        $error = 'Пароль должен быть не короче 6 символов';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $error = 'Email уже занят';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $pdo->prepare(
                'INSERT INTO users (name, email, password, password_hash) VALUES (?, ?, ?, ?)'
            );
            $stmt->execute([$name, $email, $hash, $hash]);

            session_regenerate_id(true);
            
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['user_name'] = $name;

            header('Location: /messages.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Boardy — Регистрация</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<?php include __DIR__ . '/partials/nav.php'; ?>

<div class="auth-container">
    <div class="auth-card">
        <h2>Регистрация</h2>

        <?php if ($error): ?>
            <div class="auth-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="/register.php">
            <label for="name">Имя</label>
            <input type="text" id="name" name="name" required value="<?= htmlspecialchars($name) ?>">

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required value="<?= htmlspecialchars($email) ?>">

            <label for="password">Пароль</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Зарегистрироваться</button>
        </form>

        <div class="auth-link">
            Уже есть аккаунт? <a href="/login.php">Войти</a>
        </div>
    </div>
</div>
</body>
</html>