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
    $clean = preg_replace('/[^A-Za-z0-9\.\-\_]/', '_', $filename);
    return $clean ?: 'image_' . time();
}

function handleImageUpload(array $file, ?string $existing = null): ?string
{
    if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
        return $existing;
    }

    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
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

    if ($existing && file_exists(UPLOAD_DIR . $existing) && $existing !== $filename) {
        @unlink(UPLOAD_DIR . $existing);
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
