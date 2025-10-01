<?php
require_once __DIR__ . '/header.php';

$about = fetchOne('SELECT * FROM about_pilates LIMIT 1');
$history = fetchOne('SELECT * FROM history LIMIT 1');
$instructor = fetchOne('SELECT * FROM instructor LIMIT 1');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrf($_POST['csrf_token'] ?? '');
    $section = $_POST['section'] ?? '';

    switch ($section) {
        case 'about':
            $content = trim($_POST['content'] ?? '');
            if ($about) {
                executeQuery('UPDATE about_pilates SET content = :content WHERE id = :id', [':content' => $content, ':id' => $about['id']]);
            } else {
                executeQuery('INSERT INTO about_pilates (content) VALUES (:content)', [':content' => $content]);
            }
            setFlash('success', 'Pilates Nedir? metni güncellendi.');
            break;
        case 'history':
            $content = trim($_POST['content'] ?? '');
            if ($history) {
                executeQuery('UPDATE history SET content = :content WHERE id = :id', [':content' => $content, ':id' => $history['id']]);
            } else {
                executeQuery('INSERT INTO history (content) VALUES (:content)', [':content' => $content]);
            }
            setFlash('success', 'Tarihçe metni güncellendi.');
            break;
        case 'instructor':
            $bio = trim($_POST['bio'] ?? '');
            $highlights = trim($_POST['highlights'] ?? '');
            try {
                $photo = handleImageUpload($_FILES['photo'] ?? [], $instructor['photo'] ?? null);
            } catch (RuntimeException $e) {
                setFlash('error', $e->getMessage());
                header('Location: content.php');
                exit;
            }
            $params = [
                ':name' => 'Pınar Sarı Koçak',
                ':bio' => $bio,
                ':highlights' => $highlights,
            ];
            if ($photo) {
                $params[':photo'] = $photo;
            }
            if ($instructor) {
                $query = 'UPDATE instructor SET name = :name, bio = :bio, highlights = :highlights';
                if ($photo) {
                    $query .= ', photo = :photo';
                }
                $query .= ' WHERE id = :id';
                $params[':id'] = $instructor['id'];
            } else {
                $query = 'INSERT INTO instructor (name, bio, highlights, photo) VALUES (:name, :bio, :highlights, :photo)';
                if (!$photo) {
                    $params[':photo'] = null;
                }
            }
            executeQuery($query, $params);
            setFlash('success', 'Eğitmen bilgileri güncellendi.');
            break;
    }

    header('Location: content.php');
    exit;
}

$success = getFlash('success');
$error = getFlash('error');
?>
<header>
    <h1>Metin Yönetimi</h1>
</header>
<?php if ($success): ?><div class="flash"><?= htmlspecialchars($success); ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert error"><?= htmlspecialchars($error); ?></div><?php endif; ?>
<div class="card">
    <h3>Pilates Nedir?</h3>
    <form method="post">
        <textarea name="content" rows="6" required><?= htmlspecialchars($about['content'] ?? ''); ?></textarea>
        <input type="hidden" name="section" value="about">
        <input type="hidden" name="csrf_token" value="<?= csrfToken(); ?>">
        <button type="submit" class="btn primary">Kaydet</button>
    </form>
</div>

<div class="card">
    <h3>Kısaca Tarihçe</h3>
    <form method="post">
        <textarea name="content" rows="6" required><?= htmlspecialchars($history['content'] ?? ''); ?></textarea>
        <input type="hidden" name="section" value="history">
        <input type="hidden" name="csrf_token" value="<?= csrfToken(); ?>">
        <button type="submit" class="btn primary">Kaydet</button>
    </form>
</div>

<div class="card">
    <h3>Pınar Sarı Koçak</h3>
    <form method="post" enctype="multipart/form-data">
        <label for="bio">Özgeçmiş</label>
        <textarea name="bio" id="bio" rows="6" required><?= htmlspecialchars($instructor['bio'] ?? ''); ?></textarea>
        <label for="highlights">Uzmanlıklar (her satıra bir madde)</label>
        <textarea name="highlights" id="highlights" rows="5"><?= htmlspecialchars($instructor['highlights'] ?? ''); ?></textarea>
        <label for="photo">Fotoğraf</label>
        <input type="file" name="photo" id="photo" accept="image/*">
        <?php if (!empty($instructor['photo'])): ?>
            <p>Mevcut fotoğraf: <strong><?= htmlspecialchars($instructor['photo']); ?></strong></p>
        <?php endif; ?>
        <input type="hidden" name="section" value="instructor">
        <input type="hidden" name="csrf_token" value="<?= csrfToken(); ?>">
        <button type="submit" class="btn primary">Kaydet</button>
    </form>
</div>
<?php
require_once __DIR__ . '/layout-footer.php';
