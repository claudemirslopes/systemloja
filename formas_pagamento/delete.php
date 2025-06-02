<?php
require_once __DIR__ . '/../config/config.php';
$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $pdo->prepare("DELETE FROM formas_pagamento WHERE id = ?");
    $stmt->execute([$id]);
}
header('Location: list.php');
exit;
