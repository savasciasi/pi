<?php
require_once __DIR__ . '/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrf($_POST['csrf_token'] ?? '');
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $equipment = fetchOne('SELECT * FROM equipments WHERE id = :id', [':id' => $id]);
        if ($equipment) {
            if (!empty($equipment['img']) && file_exists(UPLOAD_DIR . $equipment['img'])) {
                @unlink(UPLOAD_DIR . $equipment['img']);
            }
            executeQuery('DELETE FROM equipments WHERE id = :id', [':id' => $id]);
            setFlash('success', 'Ekipman silindi.');
        }
        header('Location: equipments.php');
        exit;
    }

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $moreInfo = trim($_POST['more_info'] ?? '');
    $sortOrder = (int)($_POST['sort_order'] ?? 0);
    $id = (int)($_POST['id'] ?? 0);
    $existingImage = null;

    if ($id) {
        $existing = fetchOne('SELECT * FROM equipments WHERE id = :id', [':id' => $id]);
        $existingImage = $existing['img'] ?? null;
    }

    try {
        $image = handleImageUpload($_FILES['img'] ?? [], $existingImage);
    } catch (RuntimeException $e) {
        setFlash('error', $e->getMessage());
        header('Location: equipments.php');
        exit;
    }

    $params = [
        ':title' => $title,
        ':description' => $description,
        ':more_info' => $moreInfo,
        ':sort_order' => $sortOrder,
    ];
    if ($image) {
        $params[':img'] = $image;
    }

    if ($id) {
        $query = 'UPDATE equipments SET title = :title, description = :description, more_info = :more_info, sort_order = :sort_order';
        if ($image) {
            $query .= ', img = :img';
        }
        $query .= ' WHERE id = :id';
        $params[':id'] = $id;
    } else {
        $query = 'INSERT INTO equipments (title, description, more_info, sort_order, img) VALUES (:title, :description, :more_info, :sort_order, :img)';
        if (!$image) {
            $params[':img'] = null;
        }
    }

    executeQuery($query, $params);
    setFlash('success', 'Ekipman kaydedildi.');
    header('Location: equipments.php');
    exit;
}

$equipments = fetchAll('SELECT * FROM equipments ORDER BY sort_order, id');
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$editEquipment = $editId ? fetchOne('SELECT * FROM equipments WHERE id = :id', [':id' => $editId]) : null;
$success = getFlash('success');
$error = getFlash('error');
?>
<header>
    <h1>Ekipmanlar</h1>
</header>
<?php if ($success): ?><div class="flash"><?= htmlspecialchars($success); ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert error"><?= htmlspecialchars($error); ?></div><?php endif; ?>
<div class="card">
    <h3><?= $editEquipment ? 'Ekipman Düzenle' : 'Yeni Ekipman'; ?></h3>
    <form method="post" enctype="multipart/form-data">
        <label for="title">Başlık</label>
        <input type="text" name="title" id="title" value="<?= htmlspecialchars($editEquipment['title'] ?? ''); ?>" required>

        <label for="description">Kısa Açıklama</label>
        <textarea name="description" id="description" rows="4" required><?= htmlspecialchars($editEquipment['description'] ?? ''); ?></textarea>

        <label for="more_info">Detaylı Bilgi</label>
        <textarea name="more_info" id="more_info" rows="5"><?= htmlspecialchars($editEquipment['more_info'] ?? ''); ?></textarea>

        <label for="sort_order">Sıra</label>
        <input type="number" name="sort_order" id="sort_order" value="<?= htmlspecialchars($editEquipment['sort_order'] ?? 0); ?>">

        <label for="img">Görsel</label>
        <input type="file" name="img" id="img" accept="image/*">
        <?php if (!empty($editEquipment['img'])): ?>
            <p>Mevcut görsel: <strong><?= htmlspecialchars($editEquipment['img']); ?></strong></p>
        <?php endif; ?>

        <input type="hidden" name="id" value="<?= (int)($editEquipment['id'] ?? 0); ?>">
        <input type="hidden" name="csrf_token" value="<?= csrfToken(); ?>">
        <button type="submit" class="btn primary">Kaydet</button>
        <?php if ($editEquipment): ?>
            <a class="btn secondary" href="equipments.php">İptal</a>
        <?php endif; ?>
    </form>
</div>

<div class="card">
    <h3>Mevcut Ekipmanlar</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Başlık</th>
                <th>Sıra</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($equipments as $equipment): ?>
                <tr>
                    <td><?= htmlspecialchars($equipment['title']); ?></td>
                    <td><?= (int)$equipment['sort_order']; ?></td>
                    <td>
                        <div class="table-actions">
                            <a class="btn secondary" href="equipments.php?edit=<?= (int)$equipment['id']; ?>">Düzenle</a>
                            <form method="post" onsubmit="return confirm('Ekipman silinsin mi?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int)$equipment['id']; ?>">
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
