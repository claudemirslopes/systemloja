<?php
require_once __DIR__ . '/../config/config.php';
$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $pdo->prepare("DELETE FROM vendas WHERE id = ?");
    $stmt->execute([$id]);
}
header('Location: list.php');
exit;
