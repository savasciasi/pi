<?php
require_once __DIR__ . '/header.php';

$messages = fetchAll('SELECT * FROM messages ORDER BY created_at DESC');
?>
<header>
    <h1>İletişim Mesajları</h1>
</header>
<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Ad Soyad</th>
                <th>E-posta</th>
                <th>Telefon</th>
                <th>Tercih</th>
                <th>Mesaj</th>
                <th>Tarih</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($messages as $message): ?>
                <tr>
                    <td><?= htmlspecialchars($message['name']); ?></td>
                    <td><a href="mailto:<?= htmlspecialchars($message['email']); ?>"><?= htmlspecialchars($message['email']); ?></a></td>
                    <td><?= htmlspecialchars($message['phone']); ?></td>
                    <td><?= htmlspecialchars($message['preference']); ?></td>
                    <td><?= nl2br(htmlspecialchars($message['message'])); ?></td>
                    <td><?= htmlspecialchars($message['created_at']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
require_once __DIR__ . '/layout-footer.php';
