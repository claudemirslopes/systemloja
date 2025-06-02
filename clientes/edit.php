<?php
require_once __DIR__ . '/../config/config.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: list.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
$stmt->execute([$id]);
$cliente = $stmt->fetch();
if (!$cliente) {
    header('Location: list.php');
    exit;
}

$nome = $cliente['nome'];
$email = $cliente['email'];
$telefone = $cliente['telefone'];
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    if ($nome) {
        $stmt = $pdo->prepare("UPDATE clientes SET nome=?, email=?, telefone=? WHERE id=?");
        $stmt->execute([$nome, $email, $telefone, $id]);
        header('Location: list.php?edited=1');
        exit;
    } else {
        $erro = 'Preencha o nome!';
    }
}
include __DIR__ . '/../templates/header.php';
?>
<h2>Editar Cliente</h2>
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
