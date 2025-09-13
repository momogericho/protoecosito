<?php
require_once __DIR__ . '/../app/init.php';
AppInitializer::init();
require_once __DIR__ . '/../models/UserRepository.php';

header('Content-Type: application/json; charset=utf-8');

$nick = $_GET['nick'] ?? '';

$userRepo = new UserRepository();
$exists = false;
if ($nick !== '') {
    $exists = $userRepo->findByNick($nick) !== null;
}

echo json_encode(['exists' => $exists]);