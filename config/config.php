<?php

error_reporting(E_ALL);
ini_set('display_errors', $_ENV['APP_DEBUG'] ?? '0');

if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        if (!array_key_exists($name, $_ENV)) {
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
            putenv("$name=$value");
        }
    }
}

session_set_cookie_params([
    'lifetime' => 3600,
    'path' => '/',
    'domain' => '',
    'secure' => filter_var($_ENV['SESSION_SECURE'] ?? 'false', FILTER_VALIDATE_BOOLEAN),
    'httponly' => filter_var($_ENV['SESSION_HTTPONLY'] ?? 'true', FILTER_VALIDATE_BOOLEAN),
    'samesite' => $_ENV['SESSION_SAMESITE'] ?? 'Lax'
]);

session_start();

define('DB_HOST', $_ENV['PGHOST'] ?? 'localhost');
define('DB_PORT', $_ENV['PGPORT'] ?? '5432');
define('DB_NAME', $_ENV['PGDATABASE'] ?? 'postgres');
define('DB_USER', $_ENV['PGUSER'] ?? 'postgres');
define('DB_PASS', $_ENV['PGPASSWORD'] ?? '');
define('DB_DRIVER', 'pgsql');

define('APP_ENV', $_ENV['APP_ENV'] ?? 'production');
define('APP_DEBUG', filter_var($_ENV['APP_DEBUG'] ?? 'false', FILTER_VALIDATE_BOOLEAN));
define('APP_URL', rtrim($_ENV['APP_URL'] ?? 'http://localhost', '/'));

define('MAX_UPLOAD_SIZE', (int)($_ENV['MAX_UPLOAD_SIZE'] ?? 10485760));
define('ALLOWED_EXTENSIONS', explode(',', $_ENV['ALLOWED_EXTENSIONS'] ?? 'jpg,jpeg,png,webp,mp4'));

define('RATE_LIMIT_LOGIN', (int)($_ENV['RATE_LIMIT_LOGIN'] ?? 5));
define('RATE_LIMIT_CONTACT', (int)($_ENV['RATE_LIMIT_CONTACT'] ?? 3));

define('SMTP_HOST', $_ENV['SMTP_HOST'] ?? '');
define('SMTP_PORT', $_ENV['SMTP_PORT'] ?? '587');
define('SMTP_USERNAME', $_ENV['SMTP_USERNAME'] ?? '');
define('SMTP_PASSWORD', $_ENV['SMTP_PASSWORD'] ?? '');
define('SMTP_FROM_EMAIL', $_ENV['SMTP_FROM_EMAIL'] ?? 'info@pistudiopilates.com');
define('SMTP_FROM_NAME', $_ENV['SMTP_FROM_NAME'] ?? 'Pi Studio Pilates');

define('BASE_PATH', dirname(__DIR__));
define('PUBLIC_PATH', BASE_PATH . '/public');
define('STORAGE_PATH', BASE_PATH . '/storage');
define('UPLOAD_PATH', STORAGE_PATH . '/uploads');

if (!is_dir(STORAGE_PATH)) {
    mkdir(STORAGE_PATH, 0755, true);
}
if (!is_dir(UPLOAD_PATH)) {
    mkdir(UPLOAD_PATH, 0755, true);
}

function getDb() {
    static $pdo = null;

    if ($pdo === null) {
        try {
            $dsn = DB_DRIVER . ':host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';options=\'--client_encoding=UTF8\'';
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            if (APP_DEBUG) {
                die('Database connection failed: ' . $e->getMessage());
            } else {
                die('Database connection failed. Please check your configuration.');
            }
        }
    }

    return $pdo;
}

function generateCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function escape($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header('Location: ' . $url);
    exit;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect('/admin/login.php');
    }
}

function checkRateLimit($key, $maxAttempts = 5, $timeWindow = 900) {
    $now = time();
    $attemptsKey = 'rate_limit_' . $key;

    if (!isset($_SESSION[$attemptsKey])) {
        $_SESSION[$attemptsKey] = [];
    }

    $_SESSION[$attemptsKey] = array_filter($_SESSION[$attemptsKey], function($timestamp) use ($now, $timeWindow) {
        return ($now - $timestamp) < $timeWindow;
    });

    if (count($_SESSION[$attemptsKey]) >= $maxAttempts) {
        return false;
    }

    $_SESSION[$attemptsKey][] = $now;
    return true;
}

function getSetting($key, $default = '') {
    static $settings = null;

    if ($settings === null) {
        $db = getDb();
        $stmt = $db->query('SELECT key, value FROM settings');
        $settings = [];
        while ($row = $stmt->fetch()) {
            $settings[$row['key']] = $row['value'];
        }
    }

    return $settings[$key] ?? $default;
}

function getPage($slug, $lang = 'tr') {
    $db = getDb();
    $stmt = $db->prepare('
        SELECT p.id, p.slug, pt.lang, pt.title, pt.content, pt.meta_title, pt.meta_description
        FROM pages p
        JOIN page_translations pt ON p.id = pt.page_id
        WHERE p.slug = ? AND pt.lang = ?
    ');
    $stmt->execute([$slug, $lang]);
    return $stmt->fetch();
}

function getCurrentLang() {
    if (isset($_SESSION['lang']) && in_array($_SESSION['lang'], ['tr', 'en'])) {
        return $_SESSION['lang'];
    }
    return 'tr';
}

function setCurrentLang($lang) {
    if (in_array($lang, ['tr', 'en'])) {
        $_SESSION['lang'] = $lang;
        setcookie('lang', $lang, time() + (365 * 24 * 60 * 60), '/', '', false, false);
    }
}
