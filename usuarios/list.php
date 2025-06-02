<?php
require_once __DIR__ . '/../config/config.php';
include __DIR__ . '/../templates/header.php';

$stmt = $pdo->query("SELECT * FROM usuarios ORDER BY id DESC");
$usuarios = $stmt->fetchAll();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Usuários</h2>
    <a href="create.php" class="btn btn-danger"><i class="fa fa-plus"></i> Novo Usuário</a>
</div>
<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">Usuário criado com sucesso!</div>
<?php elseif (isset($_GET['edited'])): ?>
    <div class="alert alert-success">Usuário editado com sucesso!</div>
<?php endif; ?>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th class="col-id">ID</th>
            <th>Nome</th>
            <th>Email</th>
            <th class="text-end">Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($usuarios as $u): ?>
        <tr>
            <td class="col-id text-center"><?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['nome']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td class="text-end">
                <a href="edit.php?id=<?= $u['id'] ?>" class="btn btn-primary btn-action btn-sm" title="Editar"><i class="fa fa-pencil"></i></a>
                <a href="delete.php?id=<?= $u['id'] ?>" class="btn btn-warning btn-action btn-sm" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir?')"><i class="fa fa-trash"></i></a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php include __DIR__ . '/../templates/footer.php'; ?>
