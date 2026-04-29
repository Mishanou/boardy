<?php
require_once __DIR__ . '/partials/session.php';
require_once __DIR__ . '/db.php';

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Заполните все поля';
    } else {
        $stmt = $pdo->prepare(
            'SELECT id, name, password_hash FROM users WHERE email = ?'
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || empty($user['password_hash']) || !password_verify($password, $user['password_hash'])) {
            $error = 'Неверный email или пароль';
        } else {
            session_regenerate_id(true);
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];

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
    <title>Boardy — Вход</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<?php include __DIR__ . '/partials/nav.php'; ?>

<div class="auth-container">
    <div class="auth-card">
        <h2>Вход</h2>

        <?php if ($error): ?>
            <div class="auth-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="/login.php">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required value="<?= htmlspecialchars($email) ?>">

            <label for="password">Пароль</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Войти</button>
        </form>

        <div class="auth-link">
            Нет аккаунта? <a href="/register.php">Регистрация</a>
        </div>
    </div>
</div>
</body>
</html>