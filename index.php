<?php
require_once __DIR__ . '/config/config.php';

// Consulta total de vendas, lucro total e total de compras
$stmt = $pdo->query("SELECT SUM(valor_venda) AS total_vendas, SUM(valor_lucro) AS total_lucro, SUM(valor_compra) AS total_compras FROM vendas");
$resumo = $stmt->fetch(PDO::FETCH_ASSOC);
$totalVendas = $resumo['total_vendas'] ?? 0;
$totalLucro = $resumo['total_lucro'] ?? 0;
$totalCompras = $resumo['total_compras'] ?? 0;

// Consulta os 3 perfumes mais vendidos
$topPerfumes = $pdo->query("SELECT p.titulo, SUM(i.quantidade) as total_vendida FROM itens_venda i JOIN perfumes p ON i.perfume_id = p.id GROUP BY i.perfume_id ORDER BY total_vendida DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
// Consulta as 3 últimas vendas
$ultimasVendas = $pdo->query("SELECT v.id, v.data_venda, v.valor_venda, c.nome as cliente FROM vendas v JOIN clientes c ON v.cliente_id = c.id ORDER BY v.data_venda DESC, v.id DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/templates/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="mb-0">Bem-vindo ao System Loja</h1>
    <div class="d-flex gap-2">
        <a href="/systemloja/vendas/create.php" class="btn btn-warning me-2" style="min-width:140px;"><i class="fa fa-plus"></i> Nova Venda</a>
        <a href="/systemloja/perfumes/create.php" class="btn btn-primary me-2" style="min-width:140px;"><i class="fa fa-flask"></i> Novo Perfume</a>
        <a href="/systemloja/clientes/create.php" class="btn btn-success" style="min-width:140px;"><i class="fa fa-user-plus"></i> Novo Cliente</a>
    </div>
</div>
<p>Utilize o menu acima para navegar entre as funcionalidades do sistema.</p>
<hr class="hr-gradient">
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
                    <i class="fa fa-money fa-2x text-success"></i>
                </div>
                <div>
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Lucro Total</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">R$ <?= number_format($totalLucro, 2, ',', '.') ?></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card shadow h-100 py-2 border-left-warning" style="border-left: 0.25rem solid #f59e42 !important;">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <i class="fa fa-star fa-2x text-warning me-3"></i>
                    <div>
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Top 3 Perfumes Mais Vendidos</div>
                    </div>
                    <a href="/systemloja/vendas/relatorio.php" class="btn btn-sm btn-warning ms-auto"><i class="fa fa-file-alt"></i> Relatório por data</a>
                </div>
                <ol class="mb-0" style="font-size:1.1rem;">
                    <?php if (empty($topPerfumes)): ?>
                        <li class="text-muted">Nenhuma venda registrada ainda.</li>
                    <?php else: ?>
                        <?php foreach ($topPerfumes as $p): ?>
                            <li><b><?= htmlspecialchars($p['titulo']) ?></b> <span class="badge bg-warning text-dark ms-2">Qtd: <?= (int)$p['total_vendida'] ?></span></li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ol>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow h-100 py-2 border-left-info" style="border-left: 0.25rem solid #17a2b8 !important;">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <i class="fa fa-clock fa-2x text-info me-3"></i>
                    <div>
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">3 Últimas Vendas</div>
                    </div>
                    <a href="/systemloja/vendas/list.php" class="btn btn-sm btn-info ms-auto"><i class="fa fa-list"></i> Todas as Vendas</a>
                </div>
                <ol class="mb-0" style="font-size:1.1rem;">
                    <?php if (empty($ultimasVendas)): ?>
                        <li class="text-muted">Nenhuma venda registrada ainda.</li>
                    <?php else: ?>
                        <?php foreach ($ultimasVendas as $v): ?>
                            <li>
                                <b><?= htmlspecialchars($v['cliente']) ?></b> <span class="badge bg-info text-dark ms-2">R$ <?= number_format($v['valor_venda'], 2, ',', '.') ?></span>
                                <span class="text-muted ms-2">(<?= date('d/m/Y', strtotime($v['data_venda'])) ?>)</span>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ol>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/templates/footer.php'; ?>

<!-- Rodapé fixo do dashboard -->
<style>
    #footer-dashboard {
        position: fixed;
        left: 0;
        bottom: 0;
        width: 100vw;
        background: transparent;
        color: #333;
        text-align: center;
        padding: 8px 0 6px 0;
        font-size: 0.95rem;
        z-index: 9999;
        letter-spacing: 0.5px;
        box-shadow: 0 -2px 8px rgba(0,0,0,0.08);
    }
    @media (max-width: 768px) {
        #footer-dashboard { font-size: 0.85rem; padding: 10px 0 8px 0; }
    }
</style>
<div id="footer-dashboard">
    &copy; Open Beta CTI - <?php echo date('Y'); ?> | Todos os direitos reservados
</div>
