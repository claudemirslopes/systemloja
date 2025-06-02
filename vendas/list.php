<?php
require_once __DIR__ . '/../config/config.php';
include __DIR__ . '/../templates/header.php';

$stmt = $pdo->query("SELECT v.*, c.nome as cliente, f.descricao as forma_pagamento FROM vendas v JOIN clientes c ON v.cliente_id = c.id JOIN formas_pagamento f ON v.forma_pagamento_id = f.id ORDER BY v.id DESC");
$vendas = $stmt->fetchAll();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Vendas</h2>
    <a href="create.php" class="btn btn-warning"><i class="fa fa-plus"></i> Nova Venda</a>
</div>
<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">Venda cadastrada com sucesso!</div>
<?php elseif (isset($_GET['edited'])): ?>
    <div class="alert alert-success">Venda editada com sucesso!</div>
<?php endif; ?>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th class="col-id">ID</th>
            <th>Cliente</th>
            <th>Forma de Pagamento</th>
            <th class="text-center">Produtos</th>
            <th class="text-center">Valor Venda</th>
            <th class="text-center">Valor Compra</th>
            <th class="text-center">Valor Lucro</th>
            <th class="text-center">Data Venda</th>
            <th class="text-end">Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($vendas as $v): ?>
        <tr>
            <td class="col-id text-center"><?= $v['id'] ?></td>
            <td><?= htmlspecialchars($v['cliente']) ?></td>
            <td><?= htmlspecialchars($v['forma_pagamento']) ?></td>
            <td class="text-center">
                <?php
                $stmtItens = $pdo->prepare("SELECT i.*, p.titulo FROM itens_venda i JOIN perfumes p ON i.perfume_id = p.id WHERE i.venda_id = ?");
                $stmtItens->execute([$v['id']]);
                $itens = $stmtItens->fetchAll();
                foreach ($itens as $item) {
                    echo htmlspecialchars($item['titulo']) . ' (Qtd: ' . $item['quantidade'] . ')<br>';
                }
                ?>
            </td>
            <td class="text-center">R$ <?= number_format($v['valor_venda'],2,',','.') ?></td>
            <td class="text-center">R$ <?= number_format($v['valor_compra'],2,',','.') ?></td>
            <td class="text-center">R$ <?= number_format($v['valor_lucro'],2,',','.') ?></td>
            <td class="text-center"><?= date('d/m/Y', strtotime($v['data_venda'])) ?></td>
            <td class="text-end">
                <a href="edit.php?id=<?= $v['id'] ?>" class="btn btn-primary btn-action btn-sm" title="Ver"><i class="fa fa-eye"></i></a>
                <a href="delete.php?id=<?= $v['id'] ?>" class="btn btn-danger btn-action btn-sm" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir?')"><i class="fa fa-trash"></i></a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php include __DIR__ . '/../templates/footer.php'; ?>
