<?php
require_once __DIR__ . '/../config/config.php';
include __DIR__ . '/../templates/header.php';

$stmt = $pdo->query("SELECT * FROM formas_pagamento ORDER BY id DESC");
$formas = $stmt->fetchAll();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Formas de Pagamento</h2>
    <a href="create.php" class="btn btn-info"><i class="fa fa-plus"></i> Nova Forma de Pagamento</a>
</div>
<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">Forma de pagamento cadastrada com sucesso!</div>
<?php elseif (isset($_GET['edited'])): ?>
    <div class="alert alert-success">Forma de pagamento editada com sucesso!</div>
<?php endif; ?>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th class="col-id">ID</th>
            <th>Descrição</th>
            <th class="text-end">Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($formas as $f): ?>
        <tr>
            <td class="col-id text-center"><?= $f['id'] ?></td>
            <td><?= htmlspecialchars($f['descricao']) ?></td>
            <td class="text-end">
                <a href="edit.php?id=<?= $f['id'] ?>" class="btn btn-primary btn-action btn-sm" title="Editar"><i class="fa fa-pencil"></i></a>
                <a href="delete.php?id=<?= $f['id'] ?>" class="btn btn-danger btn-action btn-sm" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir?')"><i class="fa fa-trash"></i></a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php include __DIR__ . '/../templates/footer.php'; ?>
