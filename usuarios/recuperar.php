<?php
require_once __DIR__ . '/../config/config.php';
$token = $_GET['token'] ?? '';
$erro = '';
$sucesso = '';
if ($token) {
    $stmt = $pdo->prepare("SELECT r.*, u.email FROM recuperacao_senha r JOIN usuarios u ON r.usuario_id = u.id WHERE r.token = ? AND r.utilizado = 0 AND r.expirado_em > NOW()");
    $stmt->execute([$token]);
    $rec = $stmt->fetch();
    if (!$rec) {
        $erro = 'Token inválido ou expirado.';
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $senha = $_POST['senha'] ?? '';
        $senha2 = $_POST['senha2'] ?? '';
        if (!$senha || strlen($senha) < 4) {
            $erro = 'A senha deve ter pelo menos 4 caracteres.';
        } elseif ($senha !== $senha2) {
            $erro = 'As senhas não coincidem.';
        } else {
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE usuarios SET senha=? WHERE id=?")->execute([$hash, $rec['usuario_id']]);
            $pdo->prepare("UPDATE recuperacao_senha SET utilizado=1 WHERE id=?")->execute([$rec['id']]);
            $sucesso = 'Senha redefinida com sucesso!';
        }
    }
} else {
    $erro = 'Token não informado.';
}
?><!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Redefinir Senha</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/systemloja/assets/style/style.css">
    <link rel="icon" type="image/png" href="/systemloja/assets/images/favicon.png">
    <style>
        body { background: #333; min-height: 100vh; }
        .redefinir-card { background: #fff; border-radius: 16px; box-shadow: 0 4px 32px rgba(0,0,0,0.18); padding: 2.5rem 2.2rem; max-width: 370px; margin: 60px auto; }
    </style>
</head>
<body>
<div class="redefinir-card">
    <div class="text-center mb-3">
        <img src="/systemloja/assets/images/logo2.png" alt="Logo" style="width:85%;height:auto;">
    </div>
    <h2 class="mb-3 text-center">Redefinir Senha</h2>
    <?php if ($erro): ?><div class="alert alert-danger text-center"><?= $erro ?></div><?php endif; ?>
    <?php if ($sucesso): ?><div class="alert alert-success text-center"><?= $sucesso ?></div><div class="text-center mt-3"><a href="/systemloja/login.php" class="btn btn-primary">Ir para o login</a></div><?php elseif (!$erro): ?>
    <form method="post">
        <div class="mb-3">
            <label for="senha" class="form-label">Nova senha</label>
            <input type="password" name="senha" id="senha" class="form-control" required autofocus>
        </div>
        <div class="mb-3">
            <label for="senha2" class="form-label">Confirme a nova senha</label>
            <input type="password" name="senha2" id="senha2" class="form-control" required>
        </div>
        <button type="submit" class="btn w-100 py-2" style="font-size:1.1rem;background:#6366F1;border:none;color:#fff;">Redefinir senha</button>
    </form>
    <?php endif; ?>
</div>
</body>
</html>
