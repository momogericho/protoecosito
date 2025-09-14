<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__);
}

if (!defined('BASE_VIEW_PATH')) {
    define('BASE_VIEW_PATH', BASE_PATH . '/resources/views');
}

require_once BASE_PATH . '/vendor/autoload.php';
// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->safeLoad();
// Define security constants
require_once BASE_PATH . '/config/security.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/app/helpers/session/SessionManager.php';
SessionManager::startSecureSession();
