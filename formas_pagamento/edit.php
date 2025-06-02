<?php
require_once __DIR__ . '/../config/config.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: list.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM formas_pagamento WHERE id = ?");
$stmt->execute([$id]);
$forma = $stmt->fetch();
if (!$forma) {
    header('Location: list.php');
    exit;
}

$descricao = $forma['descricao'];
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descricao = $_POST['descricao'] ?? '';
    if ($descricao) {
        $stmt = $pdo->prepare("UPDATE formas_pagamento SET descricao=? WHERE id=?");
        $stmt->execute([$descricao, $id]);
        header('Location: list.php?edited=1');
        exit;
    } else {
        $erro = 'Preencha a descrição!';
    }
}
include __DIR__ . '/../templates/header.php';
?>
<h2>Editar Forma de Pagamento</h2>
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
