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

include __DIR__ . '/../templates/header.php';
?>
<div class="container" style="max-width:500px;">
    <div class="card shadow mb-4 mt-4">
        <div class="card-body">
            <h2 class="mb-4"><i class="fa fa-user"></i> Perfil do Usuário</h2>
            <dl class="row">
                <dt class="col-sm-4">Nome</dt>
                <dd class="col-sm-8"><?= htmlspecialchars($usuario['nome']) ?></dd>
                <dt class="col-sm-4">Email</dt>
                <dd class="col-sm-8"><?= htmlspecialchars($usuario['email']) ?></dd>
            </dl>
            <div class="d-flex justify-content-end">
                <a href="edit.php?id=<?= $usuario['id'] ?>" class="btn btn-primary mr-2"><i class="fa fa-user-edit"></i> Editar</a>
                <a href="list.php" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Voltar</a>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../templates/footer.php'; ?>
