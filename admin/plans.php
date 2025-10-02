<?php
require_once __DIR__ . '/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrf($_POST['csrf_token'] ?? '');
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        executeQuery('DELETE FROM plans WHERE id = :id', [':id' => $id]);
        setFlash('success', 'Plan silindi.');
        header('Location: plans.php');
        exit;
    }

    $title = trim($_POST['title'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $sortOrder = (int)($_POST['sort_order'] ?? 0);
    $id = (int)($_POST['id'] ?? 0);

    $params = [
        ':title' => $title,
        ':price' => $price,
        ':description' => $description,
        ':sort_order' => $sortOrder,
    ];

    if ($id) {
        $params[':id'] = $id;
        executeQuery('UPDATE plans SET title = :title, price = :price, description = :description, sort_order = :sort_order WHERE id = :id', $params);
    } else {
        executeQuery('INSERT INTO plans (title, price, description, sort_order) VALUES (:title, :price, :description, :sort_order)', $params);
    }

    setFlash('success', 'Plan kaydedildi.');
    header('Location: plans.php');
    exit;
}

$plans = fetchAll('SELECT * FROM plans ORDER BY sort_order, id');
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$editPlan = $editId ? fetchOne('SELECT * FROM plans WHERE id = :id', [':id' => $editId]) : null;
$success = getFlash('success');
?>
<header>
    <h1>Ders Planları</h1>
</header>
<?php if ($success): ?><div class="flash"><?= htmlspecialchars($success); ?></div><?php endif; ?>
<div class="card">
    <h3><?= $editPlan ? 'Planı Düzenle' : 'Yeni Plan'; ?></h3>
    <form method="post">
        <label for="title">Plan Adı</label>
        <input type="text" name="title" id="title" value="<?= htmlspecialchars($editPlan['title'] ?? ''); ?>" required>

        <label for="price">Fiyat</label>
        <input type="text" name="price" id="price" value="<?= htmlspecialchars($editPlan['price'] ?? ''); ?>" required>

        <label for="description">Açıklama</label>
        <textarea name="description" id="description" rows="4" required><?= htmlspecialchars($editPlan['description'] ?? ''); ?></textarea>

        <label for="sort_order">Sıra</label>
        <input type="number" name="sort_order" id="sort_order" value="<?= htmlspecialchars($editPlan['sort_order'] ?? 0); ?>">

        <input type="hidden" name="id" value="<?= (int)($editPlan['id'] ?? 0); ?>">
        <input type="hidden" name="csrf_token" value="<?= csrfToken(); ?>">
        <button type="submit" class="btn primary">Kaydet</button>
        <?php if ($editPlan): ?>
            <a class="btn secondary" href="plans.php">İptal</a>
        <?php endif; ?>
    </form>
</div>

<div class="card">
    <h3>Mevcut Planlar</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Plan</th>
                <th>Fiyat</th>
                <th>Sıra</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($plans as $plan): ?>
                <tr>
                    <td><?= htmlspecialchars($plan['title']); ?></td>
                    <td><?= htmlspecialchars($plan['price']); ?></td>
                    <td><?= (int)$plan['sort_order']; ?></td>
                    <td>
                        <div class="table-actions">
                            <a class="btn secondary" href="plans.php?edit=<?= (int)$plan['id']; ?>">Düzenle</a>
                            <form method="post" onsubmit="return confirm('Plan silinsin mi?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int)$plan['id']; ?>">
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
