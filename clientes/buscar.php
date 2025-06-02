<?php
require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $termo = trim($_GET['termo'] ?? '');

    if (empty($termo)) {
        echo json_encode([]);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id, nome, email, telefone FROM clientes WHERE nome LIKE ? OR email LIKE ? OR telefone LIKE ? ORDER BY nome LIMIT 10");
    $termo = "%{$termo}%";
    $stmt->execute([$termo, $termo, $termo]);
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($clientes);
    exit;
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro interno do servidor.']);
    exit;
}
