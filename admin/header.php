<?php
require_once __DIR__ . '/../config.php';
requireLogin();

$section = basename($_SERVER['PHP_SELF']);
$menu = [
    'index.php' => 'Genel Bakış',
    'hero.php' => 'Hero',
    'content.php' => 'Metinler',
    'equipments.php' => 'Ekipmanlar',
    'trainings.php' => 'Eğitimler',
    'plans.php' => 'Planlar',
    'schedule.php' => 'Ders Programı',
    'faq.php' => 'S.S.S.',
    'messages.php' => 'Mesajlar',
    'footer-links.php' => 'Footer',
];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pi Studio Pilates | Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<div class="admin-layout">
    <aside class="sidebar">
        <div>
            <h2>Pi Studio</h2>
            <nav>
                <ul>
                    <?php foreach ($menu as $file => $label): ?>
                        <li><a href="<?= $file; ?>" class="<?= $file === $section ? 'active' : ''; ?>"><?= htmlspecialchars($label); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        </div>
        <div>
            <span class="badge"><?= htmlspecialchars($_SESSION['user_email'] ?? ''); ?></span>
            <div style="margin-top:1rem;">
                <a class="btn secondary" href="logout.php">Çıkış</a>
            </div>
        </div>
    </aside>
    <main class="admin-content">
