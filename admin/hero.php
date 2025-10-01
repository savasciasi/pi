<?php
require_once __DIR__ . '/header.php';

$hero = fetchOne('SELECT * FROM hero LIMIT 1');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrf($_POST['csrf_token'] ?? '');

    $data = [
        ':title' => trim($_POST['title'] ?? ''),
        ':subtitle' => trim($_POST['subtitle'] ?? ''),
        ':cta_primary_text' => trim($_POST['cta_primary_text'] ?? ''),
        ':cta_secondary_text' => trim($_POST['cta_secondary_text'] ?? ''),
        ':cta_primary_link' => trim($_POST['cta_primary_link'] ?? ''),
        ':cta_secondary_link' => trim($_POST['cta_secondary_link'] ?? ''),
        ':address' => trim($_POST['address'] ?? ''),
        ':map_embed' => trim($_POST['map_embed'] ?? ''),
    ];

    try {
        $image = handleImageUpload($_FILES['background_media'] ?? [], $hero['background_media'] ?? null);
    } catch (RuntimeException $e) {
        setFlash('error', $e->getMessage());
        header('Location: hero.php');
        exit;
    }

    if ($image) {
        $data[':background_media'] = $image;
    }

    if ($hero) {
        $query = 'UPDATE hero SET title = :title, subtitle = :subtitle, cta_primary_text = :cta_primary_text, cta_secondary_text = :cta_secondary_text, cta_primary_link = :cta_primary_link, cta_secondary_link = :cta_secondary_link, address = :address, map_embed = :map_embed';
        if ($image) {
            $query .= ', background_media = :background_media';
        }
        $query .= ' WHERE id = :id';
        $data[':id'] = $hero['id'];
    } else {
        $query = 'INSERT INTO hero (title, subtitle, cta_primary_text, cta_secondary_text, cta_primary_link, cta_secondary_link, address, map_embed, background_media) VALUES (:title, :subtitle, :cta_primary_text, :cta_secondary_text, :cta_primary_link, :cta_secondary_link, :address, :map_embed, :background_media)';
        if (!$image) {
            $data[':background_media'] = null;
        }
    }

    executeQuery($query, $data);
    setFlash('success', 'Hero alanı güncellendi.');
    header('Location: hero.php');
    exit;
}

$success = getFlash('success');
$error = getFlash('error');
?>
<header>
    <h1>Hero Bölümü</h1>
</header>
<?php if ($success): ?><div class="flash"><?= htmlspecialchars($success); ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert error"><?= htmlspecialchars($error); ?></div><?php endif; ?>
<div class="card">
    <form method="post" enctype="multipart/form-data">
        <label for="title">Başlık</label>
        <input type="text" name="title" id="title" value="<?= htmlspecialchars($hero['title'] ?? ''); ?>" required>

        <label for="subtitle">Alt Başlık</label>
        <textarea name="subtitle" id="subtitle" rows="3"><?= htmlspecialchars($hero['subtitle'] ?? ''); ?></textarea>

        <label for="cta_primary_text">Birincil Buton Metni</label>
        <input type="text" name="cta_primary_text" id="cta_primary_text" value="<?= htmlspecialchars($hero['cta_primary_text'] ?? ''); ?>">

        <label for="cta_primary_link">Birincil Buton Linki</label>
        <input type="text" name="cta_primary_link" id="cta_primary_link" value="<?= htmlspecialchars($hero['cta_primary_link'] ?? ''); ?>">

        <label for="cta_secondary_text">İkincil Buton Metni</label>
        <input type="text" name="cta_secondary_text" id="cta_secondary_text" value="<?= htmlspecialchars($hero['cta_secondary_text'] ?? ''); ?>">

        <label for="cta_secondary_link">İkincil Buton Linki</label>
        <input type="text" name="cta_secondary_link" id="cta_secondary_link" value="<?= htmlspecialchars($hero['cta_secondary_link'] ?? ''); ?>">

        <label for="address">Adres</label>
        <textarea name="address" id="address" rows="2"><?= htmlspecialchars($hero['address'] ?? ''); ?></textarea>

        <label for="map_embed">Google Maps Embed (iframe kodu veya sadece src)</label>
        <textarea name="map_embed" id="map_embed" rows="4"><?= htmlspecialchars($hero['map_embed'] ?? ''); ?></textarea>

        <label for="background_media">Arka Plan Görseli</label>
        <input type="file" name="background_media" id="background_media" accept="image/*">
        <?php if (!empty($hero['background_media'])): ?>
            <p>Mevcut görsel: <strong><?= htmlspecialchars($hero['background_media']); ?></strong></p>
        <?php endif; ?>

        <input type="hidden" name="csrf_token" value="<?= csrfToken(); ?>">
        <button type="submit" class="btn primary">Kaydet</button>
    </form>
</div>
<?php
require_once __DIR__ . '/layout-footer.php';
