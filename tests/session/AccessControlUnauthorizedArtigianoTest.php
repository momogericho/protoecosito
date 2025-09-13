<?php
require_once __DIR__ . '/../../app/helpers/session/SessionManager.php';
require_once __DIR__ . '/../../app/helpers/session/AccessControl.php';

session_save_path(sys_get_temp_dir());
SessionManager::startSecureSession();

$_SESSION['user_id'] = 123;
$_SESSION['artigiano'] = 0;
AccessControl::requireArtigiano(['mode' => 'json']);