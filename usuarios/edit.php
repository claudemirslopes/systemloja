<?php
require_once __DIR__ . '/../config/config.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: list.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
$usuario = $stmt->fetch();
if (!$usuario) {
    header('Location: list.php');
    exit;
}

$nome = $usuario['nome'];
$email = $usuario['email'];
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    if ($nome && $email) {
        if ($senha) {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE usuarios SET nome=?, email=?, senha=? WHERE id=?");
            $stmt->execute([$nome, $email, $senhaHash, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE usuarios SET nome=?, email=? WHERE id=?");
            $stmt->execute([$nome, $email, $id]);
        }
        header('Location: list.php?edited=1');
        exit;
    } else {
        $erro = 'Preencha todos os campos obrigatórios!';
    }
}
include __DIR__ . '/../templates/header.php';
?>
<h2>Editar Usuário</h2>
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
            <label>Senha (deixe em branco para não alterar)</label>
            <input type="password" name="senha" class="form-control">
        </div>
    </div>
    <button type="submit" class="btn btn-danger">Salvar</button>
    <a href="list.php" class="btn btn-secondary">Voltar</a>
</form>
<?php include __DIR__ . '/../templates/footer.php'; ?>
