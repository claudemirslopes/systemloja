<?php
require_once __DIR__ . '/../config/config.php';

$nome = $email = $telefone = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefone = $_POST['telefone'] ?? '';

    if ($nome) {
        $stmt = $pdo->prepare("INSERT INTO clientes (nome, email, telefone) VALUES (?, ?, ?)");
        $stmt->execute([$nome, $email, $telefone]);
        header('Location: list.php?success=1');
        exit;
    } else {
        $erro = 'Preencha o nome do cliente!';
    }
}
include __DIR__ . '/../templates/header.php';
?>
<h2>Novo Cliente</h2>
<?php if ($erro): ?>
    <div class="alert alert-danger"><?= $erro ?></div>
<?php endif; ?>
<form method="post">
    <div class="form-row">
        <div class="form-group">
            <label>Nome</label>
            <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($nome) ?>" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label>Telefone</label>
            <input type="text" name="telefone" class="form-control" value="<?= htmlspecialchars($telefone) ?>">
        </div>
    </div>
    <button type="submit" class="btn btn-success">Salvar</button>
    <a href="list.php" class="btn btn-secondary">Voltar</a>
</form>
<?php include __DIR__ . '/../templates/footer.php'; ?>
