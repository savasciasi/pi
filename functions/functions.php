<?php
function getPDO(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            http_response_code(500);
            echo 'Veritabanı bağlantı hatası: ' . htmlspecialchars($e->getMessage());
            exit;
        }
    }
    return $pdo;
}

function fetchOne(string $query, array $params = []): ?array
{
    $stmt = getPDO()->prepare($query);
    $stmt->execute($params);
    $result = $stmt->fetch();
    return $result !== false ? $result : null;
}

function fetchAll(string $query, array $params = []): array
{
    $stmt = getPDO()->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function executeQuery(string $query, array $params = []): bool
{
    $stmt = getPDO()->prepare($query);
    return $stmt->execute($params);
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function setFlash(string $key, $message): void
{
    $_SESSION['flash'][$key] = $message;
}

function getFlash(string $key)
{
    if (!empty($_SESSION['flash'][$key])) {
        $message = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $message;
    }
    return null;
}

function sanitizeFileName(string $filename): string
{
    $clean = preg_replace('/[^A-Za-z0-9\.\-_]/', '_', $filename);
    return $clean ?: 'image_' . time();
}

function handleImageUpload(array $file, ?string $existing = null): ?string
{
    if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
        return $existing;
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
        throw new RuntimeException('Desteklenmeyen dosya formatı.');
    }

    $filename = sanitizeFileName(pathinfo($file['name'], PATHINFO_FILENAME)) . '_' . time() . '.' . $ext;
    $destination = UPLOAD_DIR . $filename;
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new RuntimeException('Dosya yüklenemedi.');
    }

    if ($existing && $existing !== $filename) {
        $existingPath = UPLOAD_DIR . ltrim($existing, '/');
        if (file_exists($existingPath)) {
            @unlink($existingPath);
        }
    }

    return $filename;
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCsrf(string $token): void
{
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(400);
        exit('Geçersiz CSRF token.');
    }
}

function unsplash_placeholder(string $topic = 'pilates studio'): string
{
    $query = trim($topic) !== '' ? trim($topic) : 'pilates';
    return 'https://source.unsplash.com/featured/?' . rawurlencode($query);
}

function img_path_or_fallback(?string $path, string $topic = 'pilates studio'): string
{
    $placeholder = unsplash_placeholder($topic);

    if ($path === null || trim($path) === '') {
        return $placeholder;
    }

    if (preg_match('/^https?:\/\//i', $path)) {
        return $path;
    }

    $normalized = ltrim($path, '/');

    if ($normalized === '' || str_contains($normalized, '..')) {
        return $placeholder;
    }

    $baseDir = dirname(__DIR__) . '/';
    $candidates = [$normalized];

    if (!str_starts_with($normalized, 'assets/')) {
        $candidates[] = 'assets/' . $normalized;
        $candidates[] = 'assets/img/' . $normalized;
    }

    if (!str_starts_with($normalized, 'assets/img/uploads/')) {
        $candidates[] = 'assets/img/uploads/' . $normalized;
    }

    if (str_starts_with($normalized, 'img/uploads/')) {
        $candidates[] = 'assets/' . $normalized;
    }

    if (str_starts_with($normalized, 'uploads/')) {
        $candidates[] = 'assets/img/' . $normalized;
    }

    foreach (array_unique($candidates) as $candidate) {
        $fullPath = $baseDir . $candidate;
        if (is_file($fullPath)) {
            return $candidate;
        }
    }

    return $placeholder;
}

function media_url(?string $path, ?string $fallback = null, string $topic = 'pilates studio'): string
{
    $candidate = $path ?? $fallback;
    $resolved = img_path_or_fallback($candidate, $topic);

    if (preg_match('/^https?:\/\//i', $resolved)) {
        return $resolved;
    }

    $normalized = ltrim($resolved, '/');
    return BASE_URL . $normalized;
}

function get_settings(): array
{
    if (!array_key_exists('__settings_cache', $GLOBALS) || $GLOBALS['__settings_cache'] === null) {
        $GLOBALS['__settings_cache'] = fetchOne('SELECT * FROM settings LIMIT 1') ?: [];
    }

    return $GLOBALS['__settings_cache'];
}

function refresh_settings(): void
{
    $GLOBALS['__settings_cache'] = null;
}

function setting(string $key, $default = null)
{
    $settings = get_settings();
    return $settings[$key] ?? $default;
}

function format_time(string $time): string
{
    return substr($time, 0, 5);
}

function send_contact_mail(string $name, string $email, string $phone, string $preference, string $message): void
{
    $to = setting('contact_email', 'info@pistudiopilates.com');
    $subject = 'Pi Studio Pilates İletişim Formu';
    $body = "Ad Soyad: {$name}\nE-posta: {$email}\nTelefon: {$phone}\nDers Tercihi: {$preference}\n\nMesaj:\n{$message}";
    $headers = 'From: ' . $to . "\r\n" . 'Reply-To: ' . $email . "\r\n";
    @mail($to, $subject, $body, $headers);
}

function whatsapp_link(?string $number): ?string
{
    if (!$number) {
        return null;
    }

    $digits = preg_replace('/\D+/', '', $number);
    if ($digits === '') {
        return null;
    }

    if (str_starts_with($digits, '00')) {
        $digits = substr($digits, 2) ?: '';
    }

    if ($digits === '') {
        return null;
    }

    if ($digits[0] === '0') {
        $digits = ltrim($digits, '0');
        if ($digits === '') {
            return null;
        }
        $digits = '90' . $digits;
    }

    return 'https://wa.me/' . $digits;
}
