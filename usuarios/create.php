<?php
require_once __DIR__ . '/../config/config.php';

$nome = $email = $senha = '';
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    if ($nome && $email && $senha) {
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
        $stmt->execute([$nome, $email, $hash]);
        header('Location: list.php?success=1');
        exit;
    } else {
        $erro = 'Preencha todos os campos!';
    }
}
include __DIR__ . '/../templates/header.php';
?>
<h2>Novo Usuário</h2>
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
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>" required>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label>Senha</label>
            <input type="password" name="senha" class="form-control" required>
        </div>
    </div>
    <button type="submit" class="btn btn-danger">Salvar</button>
    <a href="list.php" class="btn btn-secondary">Voltar</a>
</form>
<?php include __DIR__ . '/../templates/footer.php'; ?>
