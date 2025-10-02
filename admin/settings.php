<?php
require_once __DIR__ . '/header.php';

$settings = get_settings();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrf($_POST['csrf_token'] ?? '');

    $siteName = trim($_POST['site_name'] ?? '');
    $language = trim($_POST['language'] ?? 'tr');
    $contactEmail = trim($_POST['contact_email'] ?? '');
    $contactPhone = trim($_POST['contact_phone'] ?? '');
    $whatsappNumber = trim($_POST['whatsapp_number'] ?? '');
    $instagramUrl = trim($_POST['instagram_url'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if ($siteName === '') {
        setFlash('error', 'Site adı boş bırakılamaz.');
        header('Location: settings.php');
        exit;
    }

    try {
        $logo = handleImageUpload($_FILES['logo'] ?? [], $settings['logo'] ?? null);
    } catch (RuntimeException $e) {
        setFlash('error', $e->getMessage());
        header('Location: settings.php');
        exit;
    }

    $payload = [
        ':site_name' => $siteName,
        ':language' => $language ?: 'tr',
        ':contact_email' => $contactEmail,
        ':contact_phone' => $contactPhone,
        ':whatsapp_number' => $whatsappNumber,
        ':instagram_url' => $instagramUrl,
        ':address' => $address,
    ];

    if ($logo) {
        $payload[':logo'] = $logo;
    }

    if ($settings) {
        $query = 'UPDATE settings SET site_name = :site_name, language = :language, contact_email = :contact_email, contact_phone = :contact_phone, whatsapp_number = :whatsapp_number, instagram_url = :instagram_url, address = :address';
        if ($logo) {
            $query .= ', logo = :logo';
        }
        $query .= ' WHERE id = :id';
        $payload[':id'] = $settings['id'];
    } else {
        $query = 'INSERT INTO settings (site_name, language, contact_email, contact_phone, whatsapp_number, instagram_url, address, logo) VALUES (:site_name, :language, :contact_email, :contact_phone, :whatsapp_number, :instagram_url, :address, :logo)';
        if (!$logo) {
            $payload[':logo'] = null;
        }
    }

    executeQuery($query, $payload);
    refresh_settings();

    setFlash('success', 'Genel ayarlar kaydedildi.');
    header('Location: settings.php');
    exit;
}

$success = getFlash('success');
$error = getFlash('error');
$settings = get_settings();
?>
<header>
    <h1>Site Ayarları</h1>
</header>
<?php if ($success): ?><div class="flash"><?= htmlspecialchars($success); ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert error"><?= htmlspecialchars($error); ?></div><?php endif; ?>
<div class="card">
    <form method="post" enctype="multipart/form-data">
        <label for="site_name">Site Adı</label>
        <input type="text" id="site_name" name="site_name" value="<?= htmlspecialchars($settings['site_name'] ?? 'Pi Studio Pilates'); ?>" required>

        <label for="language">Dil Kodu (ör. tr, en)</label>
        <input type="text" id="language" name="language" value="<?= htmlspecialchars($settings['language'] ?? 'tr'); ?>" maxlength="5">

        <label for="contact_email">İletişim E-postası</label>
        <input type="email" id="contact_email" name="contact_email" value="<?= htmlspecialchars($settings['contact_email'] ?? 'info@pistudiopilates.com'); ?>">

        <label for="contact_phone">İletişim Telefonu</label>
        <input type="text" id="contact_phone" name="contact_phone" value="<?= htmlspecialchars($settings['contact_phone'] ?? '+90 530 111 22 33'); ?>">

        <label for="whatsapp_number">WhatsApp Numarası</label>
        <input type="text" id="whatsapp_number" name="whatsapp_number" value="<?= htmlspecialchars($settings['whatsapp_number'] ?? '+90 530 111 22 33'); ?>">

        <label for="instagram_url">Instagram URL</label>
        <input type="url" id="instagram_url" name="instagram_url" value="<?= htmlspecialchars($settings['instagram_url'] ?? 'https://www.instagram.com'); ?>">

        <label for="address">Adres</label>
        <textarea id="address" name="address" rows="3"><?= htmlspecialchars($settings['address'] ?? 'Bağdat Caddesi No:123, İstanbul'); ?></textarea>

        <label for="logo">Logo</label>
        <input type="file" id="logo" name="logo" accept="image/*">
        <?php if (!empty($settings['logo'])): ?>
            <p>Mevcut logo: <strong><?= htmlspecialchars($settings['logo']); ?></strong></p>
        <?php endif; ?>

        <input type="hidden" name="csrf_token" value="<?= csrfToken(); ?>">
        <button type="submit" class="btn primary">Kaydet</button>
    </form>
</div>
<?php
require_once __DIR__ . '/layout-footer.php';
