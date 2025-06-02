<?php
require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

try {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');

    if (!$nome || !$email || !$telefone) {
        throw new Exception('Todos os campos são obrigatórios');
    }

    // Verifica se já existe um cliente com este email
    $stmt = $pdo->prepare("SELECT id FROM clientes WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        throw new Exception('Já existe um cliente cadastrado com este email');
    }

    // Insere o novo cliente
    $stmt = $pdo->prepare("INSERT INTO clientes (nome, email, telefone) VALUES (?, ?, ?)");
    $stmt->execute([$nome, $email, $telefone]);
    
    $id = $pdo->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'id' => $id,
        'message' => 'Cliente cadastrado com sucesso!'
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
