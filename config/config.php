<?php
session_start();

// Database configuration
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'pistudiopilates');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

// Application paths
$baseUrl = rtrim(getenv('BASE_URL') ?: '/', '/') . '/';
define('BASE_URL', $baseUrl);
define('ASSET_URL', BASE_URL . 'assets/');
define('PUBLIC_PATH', dirname(__DIR__) . '/public/');
define('UPLOAD_DIR', dirname(__DIR__) . '/assets/img/uploads/');
define('UPLOAD_URL', ASSET_URL . 'img/uploads/');
define('PLACEHOLDER_IMG', ASSET_URL . 'img/placeholder.svg');

if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}

require_once dirname(__DIR__) . '/functions/helpers.php';
