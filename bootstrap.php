<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__);
}

if (!defined('BASE_VIEW_PATH')) {
    define('BASE_VIEW_PATH', BASE_PATH . '/resources/views');
}

if (!defined('BASE_URL')) {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $dir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
    $baseUrl = $scheme . '://' . $host . ($dir ? '/' . ltrim($dir, '/') : '');
    define('BASE_URL', $baseUrl);
}

require_once BASE_PATH . '/config/env_loader.php';
require_once BASE_PATH . '/app/autoload.php';

// Load environment variables and register the autoloader
EnvLoader::load(BASE_PATH);
AppAutoloader::register([
    BASE_PATH . '/app',
    BASE_PATH . '/models',
    BASE_PATH . '/config',
    BASE_PATH . '/security',
]);

// Define security constants
require_once BASE_PATH . '/config/security.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/app/helpers/session/SessionManager.php';
SessionManager::startSecureSession();
