<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/auth.php';

if (!isLoggedIn()) {
    echo json_encode(['status'=>false,'message'=>'Not authenticated']);
    exit;
}

$body = json_decode(file_get_contents('php://input'), true);
$score    = (int)($body['score'] ?? 0);
$total    = (int)($body['total'] ?? 10);
$category = htmlspecialchars($body['category'] ?? 'Random');

if ($total <= 0) { echo json_encode(['status'=>false,'message'=>'Invalid']); exit; }

DB::saveScore($_SESSION['user_id'], $score, $total, $category);
echo json_encode(['status'=>true,'message'=>'Score saved']);
