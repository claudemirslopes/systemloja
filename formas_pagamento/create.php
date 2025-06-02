<?php
require_once __DIR__ . '/../config/config.php';

$descricao = '';
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descricao = $_POST['descricao'] ?? '';
    if ($descricao) {
        $stmt = $pdo->prepare("INSERT INTO formas_pagamento (descricao) VALUES (?)");
        $stmt->execute([$descricao]);
        header('Location: list.php?success=1');
        exit;
    } else {
        $erro = 'Preencha a descrição!';
    }
}
include __DIR__ . '/../templates/header.php';
?>
<h2>Nova Forma de Pagamento</h2>
<?php if ($erro): ?>
    <div class="alert alert-danger"><?= $erro ?></div>
<?php endif; ?>
<form method="post">
    <div class="mb-3">
        <label>Descrição</label>
        <input type="text" name="descricao" class="form-control" value="<?= htmlspecialchars($descricao) ?>" required>
    </div>
    <button type="submit" class="btn btn-info">Salvar</button>
    <a href="list.php" class="btn btn-secondary">Voltar</a>
</form>
<?php include __DIR__ . '/../templates/footer.php'; ?>
