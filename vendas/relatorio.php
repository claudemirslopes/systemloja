<?php
require_once __DIR__ . '/../config/config.php';

// Filtros de datas
$data_inicio = $_GET['data_inicio'] ?? '';
$data_fim = $_GET['data_fim'] ?? '';

$where = '';
$params = [];
if ($data_inicio && $data_fim) {
    $where = 'WHERE v.data_venda BETWEEN ? AND ?';
    $params = [$data_inicio, $data_fim];
} elseif ($data_inicio) {
    $where = 'WHERE v.data_venda >= ?';
    $params = [$data_inicio];
} elseif ($data_fim) {
    $where = 'WHERE v.data_venda <= ?';
    $params = [$data_fim];
}

$sql = "SELECT v.*, c.nome as cliente, f.descricao as forma_pagamento FROM vendas v 
        JOIN clientes c ON v.cliente_id = c.id 
        JOIN formas_pagamento f ON v.forma_pagamento_id = f.id
        $where ORDER BY v.data_venda DESC, v.id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$vendas = $stmt->fetchAll();

// Totais do período
$totalVendas = 0;
$totalLucro = 0;
$totalCompras = 0;
foreach ($vendas as $v) {
    $totalVendas += $v['valor_venda'];
    $totalLucro += $v['valor_lucro'];
    $totalCompras += $v['valor_compra'];
}

include __DIR__ . '/../templates/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Relatório de Vendas</h2>
</div>
<form class="row g-3 mb-4" method="get">
    <div class="col-md-4">
        <label for="data_inicio" class="form-label">Data Inicial</label>
        <input type="date" class="form-control" id="data_inicio" name="data_inicio" value="<?= htmlspecialchars($data_inicio) ?>">
    </div>
    <div class="col-md-4">
        <label for="data_fim" class="form-label">Data Final</label>
        <input type="date" class="form-control" id="data_fim" name="data_fim" value="<?= htmlspecialchars($data_fim) ?>">
    </div>
    <div class="col-md-4 d-flex align-items-end">
        <button type="submit" class="btn btn-warning w-100"><i class="fa fa-search"></i> Filtrar</button>
    </div>
</form>
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card shadow h-100 py-2 border-left-info" style="border-left: 0.25rem solid #780e14 !important;">
            <div class="card-body d-flex align-items-center">
                <div class="mr-3">
                    <i class="fa fa-credit-card fa-2x text-danger"></i>
                </div>
                <div>
                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total de Compras</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">R$ <?= number_format($totalCompras, 2, ',', '.') ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card shadow h-100 py-2 border-left-primary" style="border-left: 0.25rem solid #6366f1 !important;">
            <div class="card-body d-flex align-items-center">
                <div class="mr-3">
                    <i class="fa fa-shopping-cart fa-2x text-primary"></i>
                </div>
                <div>
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total de Vendas</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">R$ <?= number_format($totalVendas, 2, ',', '.') ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card shadow h-100 py-2 border-left-success" style="border-left: 0.25rem solid #22c55e !important;">
            <div class="card-body d-flex align-items-center">
                <div class="mr-3">
                    <i class="fa fa-dollar fa-2x text-success"></i>
                </div>
                <div>
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Lucro Total</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">R$ <?= number_format($totalLucro, 2, ',', '.') ?></div>
                </div>
            </div>
        </div>
    </div>
</div>
<table class="table table-bordered table-striped" id="relatorioVendas">
    <thead>
        <tr>
            <th class="col-id">ID</th>
            <th>Data</th>
            <th>Cliente</th>
            <th>Forma de Pagamento</th>
            <th>Produtos</th>
            <th class="text-center">Valor Venda</th>
            <th class="text-center">Valor Compra</th>
            <th class="text-center">Valor Lucro</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($vendas)): ?>
            <?php foreach ($vendas as $v): ?>
            <tr>
                <td class="col-id text-center"><?= $v['id'] ?></td>
                <td><?= date('d/m/Y', strtotime($v['data_venda'])) ?></td>
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
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- DataTables exige pelo menos uma linha com o número exato de <td> -->
            <tr>
                <td class="col-id text-center">-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td class="text-center">-</td>
                <td class="text-center">-</td>
                <td class="text-center">Nenhuma venda encontrada para o período.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<!-- Corrige bug do DataTables ao não encontrar registros -->
<script>
$(document).ready(function() {
    var table = $('#relatorioVendas').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/pt-BR.json'
        },
        responsive: true,
        autoWidth: false
    });
    // Corrige erro de coluna ao não encontrar registros
    table.on('error.dt', function(e, settings, techNote, message) {
        if (techNote === 18) {
            // Remove todas as linhas e adiciona a linha de colspan manualmente
            var colCount = $('#relatorioVendas thead th').length;
            $('#relatorioVendas tbody').html('<tr><td colspan="'+colCount+'" class="text-center">Nenhuma venda encontrada para o período.</td></tr>');
        }
    });
});
</script>
<?php include __DIR__ . '/../templates/footer.php'; ?>
