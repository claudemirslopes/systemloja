<?php
require_once __DIR__ . '/../config/config.php';
include __DIR__ . '/../templates/header.php';

$stmt = $pdo->query("SELECT * FROM clientes ORDER BY id DESC");
$clientes = $stmt->fetchAll();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Clientes</h2>
    <a href="create.php" class="btn btn-success"><i class="fa fa-plus"></i> Novo Cliente</a>
</div>
<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">Cliente criado com sucesso!</div>
<?php elseif (isset($_GET['edited'])): ?>
    <div class="alert alert-success">Cliente editado com sucesso!</div>
<?php endif; ?>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th class="col-id">ID</th>
            <th>Nome</th>
            <th>Email</th>
            <th>Telefone</th>
            <th class="text-end">Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($clientes as $c): ?>
        <tr>
            <td class="col-id text-center"><?= $c['id'] ?></td>
            <td><?= htmlspecialchars($c['nome']) ?></td>
            <td><?= htmlspecialchars($c['email']) ?></td>
            <td class="text-center"><?= htmlspecialchars($c['telefone']) ?></td>
            <td class="text-end">
                <a href="edit.php?id=<?= $c['id'] ?>" class="btn btn-primary btn-action btn-sm" title="Editar"><i class="fa fa-pencil"></i></a>
                <a href="delete.php?id=<?= $c['id'] ?>" class="btn btn-danger btn-action btn-sm" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir?')"><i class="fa fa-trash"></i></a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php include __DIR__ . '/../templates/footer.php'; ?>
