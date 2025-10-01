<?php
session_start();

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'pistudio');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

define('BASE_URL', getenv('BASE_URL') ?: '/');

define('ASSET_PATH', BASE_URL . 'assets/');

define('UPLOAD_DIR', __DIR__ . '/assets/img/');

require_once __DIR__ . '/functions.php';
