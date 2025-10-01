<?php
require_once __DIR__ . '/../config/config.php';
requireLogin();

$section = basename($_SERVER['PHP_SELF']);
$menu = [
    'index.php' => 'Genel Bakış',
    'settings.php' => 'Site Ayarları',
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

$siteName = setting('site_name', 'Pi Studio Pilates');
$adminLang = setting('language', 'tr');
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($adminLang); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($siteName); ?> | Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<div class="admin-layout">
    <aside class="sidebar">
        <div>
            <h2><?= htmlspecialchars($siteName); ?></h2>
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
