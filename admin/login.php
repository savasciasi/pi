<?php
require_once __DIR__ . '/../config/config.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrf($_POST['csrf_token'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $user = fetchOne('SELECT * FROM users WHERE email = :email', [':email' => $email]);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        header('Location: index.php');
        exit;
    }

    $error = 'Geçersiz giriş bilgileri.';
}

?><!DOCTYPE html>
<html lang="<?= htmlspecialchars(setting('language', 'tr')); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(setting('site_name', 'Pi Studio Pilates')); ?> | Admin Giriş</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="auth">
    <form class="auth-card" method="post">
        <h1>Admin Giriş</h1>
        <?php if ($error): ?>
            <div class="alert error"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <label for="email">E-posta</label>
        <input type="email" name="email" id="email" required>
        <label for="password">Şifre</label>
        <input type="password" name="password" id="password" required>
        <input type="hidden" name="csrf_token" value="<?= csrfToken(); ?>">
        <button type="submit" class="btn primary">Giriş Yap</button>
    </form>
</body>
</html>
