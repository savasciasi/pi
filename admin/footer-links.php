<?php
require_once __DIR__ . '/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrf($_POST['csrf_token'] ?? '');
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        executeQuery('DELETE FROM footer_links WHERE id = :id', [':id' => $id]);
        setFlash('success', 'Footer bağlantısı silindi.');
        header('Location: footer-links.php');
        exit;
    }

    $label = trim($_POST['label'] ?? '');
    $url = trim($_POST['url'] ?? '');
    $target = $_POST['target'] ?? '_self';
    $position = (int)($_POST['position'] ?? 0);
    $id = (int)($_POST['id'] ?? 0);

    $params = [
        ':label' => $label,
        ':url' => $url,
        ':target' => $target === '_blank' ? '_blank' : '_self',
        ':position' => $position,
    ];

    if ($id) {
        $params[':id'] = $id;
        executeQuery('UPDATE footer_links SET label = :label, url = :url, target = :target, position = :position WHERE id = :id', $params);
    } else {
        executeQuery('INSERT INTO footer_links (label, url, target, position) VALUES (:label, :url, :target, :position)', $params);
    }

    setFlash('success', 'Footer bağlantısı kaydedildi.');
    header('Location: footer-links.php');
    exit;
}

$links = fetchAll('SELECT * FROM footer_links ORDER BY position ASC');
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$editLink = $editId ? fetchOne('SELECT * FROM footer_links WHERE id = :id', [':id' => $editId]) : null;
$success = getFlash('success');
?>
<header>
    <h1>Footer Bağlantıları</h1>
</header>
<?php if ($success): ?><div class="flash"><?= htmlspecialchars($success); ?></div><?php endif; ?>
<div class="card">
    <h3><?= $editLink ? 'Bağlantıyı Düzenle' : 'Yeni Bağlantı'; ?></h3>
    <form method="post">
        <label for="label">Başlık</label>
        <input type="text" name="label" id="label" value="<?= htmlspecialchars($editLink['label'] ?? ''); ?>" required>

        <label for="url">URL</label>
        <input type="url" name="url" id="url" value="<?= htmlspecialchars($editLink['url'] ?? ''); ?>" required>

        <label for="target">Hedef</label>
        <select name="target" id="target">
            <option value="_self" <?= ($editLink['target'] ?? '_self') === '_self' ? 'selected' : ''; ?>>Aynı sayfa</option>
            <option value="_blank" <?= ($editLink['target'] ?? '_self') === '_blank' ? 'selected' : ''; ?>>Yeni sekme</option>
        </select>

        <label for="position">Sıra</label>
        <input type="number" name="position" id="position" value="<?= htmlspecialchars($editLink['position'] ?? 0); ?>">

        <input type="hidden" name="id" value="<?= (int)($editLink['id'] ?? 0); ?>">
        <input type="hidden" name="csrf_token" value="<?= csrfToken(); ?>">
        <button type="submit" class="btn primary">Kaydet</button>
        <?php if ($editLink): ?>
            <a class="btn secondary" href="footer-links.php">İptal</a>
        <?php endif; ?>
    </form>
</div>

<div class="card">
    <h3>Mevcut Bağlantılar</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Başlık</th>
                <th>URL</th>
                <th>Sıra</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($links as $link): ?>
                <tr>
                    <td><?= htmlspecialchars($link['label']); ?></td>
                    <td><?= htmlspecialchars($link['url']); ?></td>
                    <td><?= (int)$link['position']; ?></td>
                    <td>
                        <div class="table-actions">
                            <a class="btn secondary" href="footer-links.php?edit=<?= (int)$link['id']; ?>">Düzenle</a>
                            <form method="post" onsubmit="return confirm('Bağlantı silinsin mi?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int)$link['id']; ?>">
                                <input type="hidden" name="csrf_token" value="<?= csrfToken(); ?>">
                                <button type="submit" class="btn danger">Sil</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
require_once __DIR__ . '/layout-footer.php';
