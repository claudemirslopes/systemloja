<?php
require_once __DIR__ . '/../config/config.php';
include __DIR__ . '/../templates/header.php';

$stmt = $pdo->query("SELECT * FROM perfumes ORDER BY id DESC");
$perfumes = $stmt->fetchAll();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Perfumes</h2>
    <a href="create.php" class="btn btn-primary"><i class="fa fa-plus"></i> Novo Perfume</a>
</div>
<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">Perfume cadastrado com sucesso!</div>
<?php elseif (isset($_GET['edited'])): ?>
    <div class="alert alert-success">Perfume editado com sucesso!</div>
<?php endif; ?>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th class="col-id">ID</th>
            <th>Título</th>
            <th class="text-center">Valor Compra</th>
            <th class="text-center">Quantidade</th>
            <th class="text-center">Data de Entrada</th>
            <th class="text-end">Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($perfumes as $p): ?>
    <tr>
        <td class="col-id text-center"><?= $p['id'] ?></td>
        <td><?= htmlspecialchars($p['titulo']) ?></td>
        <td class="text-center">R$ <?= number_format($p['valor_compra'],2,',','.') ?></td>
        <td class="text-center">
            <?php if ($p['quantidade'] <= 3): ?>
                <span class="badge badge-danger">Estoque baixo: <?= $p['quantidade'] ?></span>
            <?php elseif ($p['quantidade'] == 4 || $p['quantidade'] == 5): ?>
                <span class="badge badge-warning text-dark">Atenção: <?= $p['quantidade'] ?></span>
            <?php else: ?>
                <span class="badge badge-success">Estoque ok: <?= $p['quantidade'] ?></span>
            <?php endif; ?>
        </td>
        <td class="text-center"><?= date('d/m/Y', strtotime($p['data_entrada'])) ?></td>
        <td class="text-end">
            <a href="edit.php?id=<?= $p['id'] ?>" class="btn btn-info btn-action btn-sm" title="Editar"><i class="fa fa-pencil"></i></a>
            <a href="delete.php?id=<?= $p['id'] ?>" class="btn btn-danger btn-action btn-sm" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir?')"><i class="fa fa-trash"></i></a>
        </td>
    </tr>
<?php endforeach; ?>
    </tbody>
</table>
<?php include __DIR__ . '/../templates/footer.php'; ?>
