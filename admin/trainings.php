<?php
require_once __DIR__ . '/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrf($_POST['csrf_token'] ?? '');
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $training = fetchOne('SELECT * FROM trainings WHERE id = :id', [':id' => $id]);
        if ($training) {
            if (!empty($training['img']) && file_exists(UPLOAD_DIR . $training['img'])) {
                @unlink(UPLOAD_DIR . $training['img']);
            }
            executeQuery('DELETE FROM trainings WHERE id = :id', [':id' => $id]);
            setFlash('success', 'Eğitim silindi.');
        }
        header('Location: trainings.php');
        exit;
    }

    $title = trim($_POST['title'] ?? '');
    $year = trim($_POST['year'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $sortOrder = (int)($_POST['sort_order'] ?? 0);
    $id = (int)($_POST['id'] ?? 0);
    $existingImage = null;

    if ($id) {
        $existing = fetchOne('SELECT * FROM trainings WHERE id = :id', [':id' => $id]);
        $existingImage = $existing['img'] ?? null;
    }

    try {
        $image = handleImageUpload($_FILES['img'] ?? [], $existingImage);
    } catch (RuntimeException $e) {
        setFlash('error', $e->getMessage());
        header('Location: trainings.php');
        exit;
    }

    $params = [
        ':title' => $title,
        ':year' => $year,
        ':description' => $description,
        ':sort_order' => $sortOrder,
    ];
    if ($image) {
        $params[':img'] = $image;
    }

    if ($id) {
        $query = 'UPDATE trainings SET title = :title, year = :year, description = :description, sort_order = :sort_order';
        if ($image) {
            $query .= ', img = :img';
        }
        $query .= ' WHERE id = :id';
        $params[':id'] = $id;
    } else {
        $query = 'INSERT INTO trainings (title, year, description, sort_order, img) VALUES (:title, :year, :description, :sort_order, :img)';
        if (!$image) {
            $params[':img'] = null;
        }
    }

    executeQuery($query, $params);
    setFlash('success', 'Eğitim kaydedildi.');
    header('Location: trainings.php');
    exit;
}

$trainings = fetchAll('SELECT * FROM trainings ORDER BY sort_order, id');
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$editTraining = $editId ? fetchOne('SELECT * FROM trainings WHERE id = :id', [':id' => $editId]) : null;
$success = getFlash('success');
$error = getFlash('error');
?>
<header>
    <h1>Eğitimler</h1>
</header>
<?php if ($success): ?><div class="flash"><?= htmlspecialchars($success); ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert error"><?= htmlspecialchars($error); ?></div><?php endif; ?>
<div class="card">
    <h3><?= $editTraining ? 'Eğitim Düzenle' : 'Yeni Eğitim'; ?></h3>
    <form method="post" enctype="multipart/form-data">
        <label for="title">Başlık</label>
        <input type="text" name="title" id="title" value="<?= htmlspecialchars($editTraining['title'] ?? ''); ?>" required>

        <label for="year">Yıl / Durum</label>
        <input type="text" name="year" id="year" value="<?= htmlspecialchars($editTraining['year'] ?? ''); ?>">

        <label for="description">Açıklama</label>
        <textarea name="description" id="description" rows="5" required><?= htmlspecialchars($editTraining['description'] ?? ''); ?></textarea>

        <label for="sort_order">Sıra</label>
        <input type="number" name="sort_order" id="sort_order" value="<?= htmlspecialchars($editTraining['sort_order'] ?? 0); ?>">

        <label for="img">Görsel</label>
        <input type="file" name="img" id="img" accept="image/*">
        <?php if (!empty($editTraining['img'])): ?>
            <p>Mevcut görsel: <strong><?= htmlspecialchars($editTraining['img']); ?></strong></p>
        <?php endif; ?>

        <input type="hidden" name="id" value="<?= (int)($editTraining['id'] ?? 0); ?>">
        <input type="hidden" name="csrf_token" value="<?= csrfToken(); ?>">
        <button type="submit" class="btn primary">Kaydet</button>
        <?php if ($editTraining): ?>
            <a class="btn secondary" href="trainings.php">İptal</a>
        <?php endif; ?>
    </form>
</div>

<div class="card">
    <h3>Mevcut Eğitimler</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Başlık</th>
                <th>Yıl</th>
                <th>Sıra</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($trainings as $training): ?>
                <tr>
                    <td><?= htmlspecialchars($training['title']); ?></td>
                    <td><?= htmlspecialchars($training['year']); ?></td>
                    <td><?= (int)$training['sort_order']; ?></td>
                    <td>
                        <div class="table-actions">
                            <a class="btn secondary" href="trainings.php?edit=<?= (int)$training['id']; ?>">Düzenle</a>
                            <form method="post" onsubmit="return confirm('Eğitim silinsin mi?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int)$training['id']; ?>">
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
