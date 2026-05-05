<?php
require_once __DIR__ . '/partials/session.php';
require_once __DIR__ . '/db.php';

$client_id = 'Ov23liReY3tEt5fbx7OJ';
$client_secret = '99c160c92e09b6e916057669f8a2fb29f730389b';

// 1. Проверка state (защита от CSRF)
if (!isset($_GET['state']) || $_GET['state'] !== $_SESSION['oauth_state']) {
    die('Invalid state');
}

// 2. Получаем code
$code = $_GET['code'] ?? null;
if (!$code) {
    die('No code');
}

// 3. Обмен code → access_token
$token_response = file_get_contents('https://github.com/login/oauth/access_token', false, stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/x-www-form-urlencoded\r\nAccept: application/json\r\n",
        'content' => http_build_query([
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'code' => $code
        ])
    ]
]));

$token_data = json_decode($token_response, true);
$access_token = $token_data['access_token'] ?? null;

if (!$access_token) {
    die('No access token');
}

// 4. Получаем пользователя
$user_response = file_get_contents('https://api.github.com/user', false, stream_context_create([
    'http' => [
        'header' => "User-Agent: Boardy\r\nAuthorization: Bearer $access_token\r\n"
    ]
]));

$user_data = json_decode($user_response, true);

$github_id = (string)$user_data['id'];
$name = $user_data['login'];

$stmt = $pdo->prepare('SELECT id FROM users WHERE github_id = ?');
$stmt->execute([$github_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $stmt = $pdo->prepare(
        'INSERT INTO users (`NAME`, email, `PASSWORD`, password_hash, github_id) VALUES (?, ?, ?, ?, ?)'
    );

    $email = 'github_' . $github_id . '@github.local';

    $password = 'github_oauth';
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt->execute([
        $name,
        $email,
        $password,
        $password_hash,
        $github_id
    ]);

    $user_id = $pdo->lastInsertId();
} else {
    $user_id = $user['id'];
}

$_SESSION['user_id'] = $user_id;
$_SESSION['user_name'] = $name;

header('Location: /messages.php');
exit;
