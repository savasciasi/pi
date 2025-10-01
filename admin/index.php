<?php
require_once __DIR__ . '/header.php';

$counts = [
    'equipments' => fetchOne('SELECT COUNT(*) AS total FROM equipments')['total'] ?? 0,
    'trainings' => fetchOne('SELECT COUNT(*) AS total FROM trainings')['total'] ?? 0,
    'plans' => fetchOne('SELECT COUNT(*) AS total FROM plans')['total'] ?? 0,
    'faq' => fetchOne('SELECT COUNT(*) AS total FROM faq')['total'] ?? 0,
    'messages' => fetchOne('SELECT COUNT(*) AS total FROM messages')['total'] ?? 0,
];
?>
<header>
    <h1>Kontrol Paneli</h1>
</header>
<div class="card">
    <h3>Özet</h3>
    <ul>
        <li>Ekipmanlar: <?= (int)$counts['equipments']; ?></li>
        <li>Eğitimler: <?= (int)$counts['trainings']; ?></li>
        <li>Planlar: <?= (int)$counts['plans']; ?></li>
        <li>S.S.S.: <?= (int)$counts['faq']; ?></li>
        <li>Mesajlar: <?= (int)$counts['messages']; ?></li>
    </ul>
</div>
<?php
require_once __DIR__ . '/layout-footer.php';
