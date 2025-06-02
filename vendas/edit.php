<?php
require_once __DIR__ . '/../config/config.php';

function moedaParaFloat($valor) {
    $valor = str_replace('.', '', $valor); // Remove pontos de milhar
    $valor = str_replace(',', '.', $valor); // Substitui vírgula por ponto
    return number_format((float)$valor, 2, '.', ''); // Garante 2 casas decimais
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: list.php');
    exit;
}

$perfumes = $pdo->query("SELECT * FROM perfumes")->fetchAll();
$clientes = $pdo->query("SELECT * FROM clientes")->fetchAll();
$formas = $pdo->query("SELECT * FROM formas_pagamento")->fetchAll();

$stmt = $pdo->prepare("SELECT * FROM vendas WHERE id = ?");
$stmt->execute([$id]);
$venda = $stmt->fetch();

if (!$venda) {
    header('Location: list.php');
    exit;
}

// Buscar itens da venda
$stmtItens = $pdo->prepare("SELECT * FROM itens_venda WHERE venda_id = ?");
$stmtItens->execute([$id]);
$itens_venda = $stmtItens->fetchAll();

// Formata os valores dos itens para exibição
foreach ($itens_venda as $key => $item) {
    $itens_venda[$key]['valor_compra_unitario'] = number_format($item['valor_compra'] / max($item['quantidade'], 1), 2, ',', '.');
    $itens_venda[$key]['valor_venda_unitario'] = number_format($item['valor_venda'] / max($item['quantidade'], 1), 2, ',', '.');
    $itens_venda[$key]['valor_lucro'] = number_format($item['valor_lucro'], 2, ',', '.');
}

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id = $_POST['cliente_id'] ?? '';
    $forma_pagamento_id = $_POST['forma_pagamento_id'] ?? '';
    $data_venda = $_POST['data_venda'] ?? '';

    if ($cliente_id && $forma_pagamento_id && $data_venda) {
        $stmt = $pdo->prepare("UPDATE vendas SET cliente_id=?, forma_pagamento_id=?, data_venda=? WHERE id=?");
        $stmt->execute([$cliente_id, $forma_pagamento_id, $data_venda, $id]);

        // Atualiza itens da venda
        foreach ($_POST['itens'] as $item) {
            $perfume_id = $item['perfume_id'] ?? '';
            $quantidade = intval($item['quantidade'] ?? 1);
            $valor_venda = $item['valor_venda'] ?? '';
            $valor_compra = $item['valor_compra'] ?? '';
            $valor_lucro = $item['valor_lucro'] ?? '';

            $valor_venda = moedaParaFloat($valor_venda);
            $valor_compra = moedaParaFloat($valor_compra);
            $valor_lucro = moedaParaFloat($valor_lucro);

            $valor_venda_total = $valor_venda * $quantidade;
            $valor_compra_total = $valor_compra * $quantidade;
            $valor_lucro_total = $valor_venda_total - $valor_compra_total;

            if ($perfume_id && $quantidade > 0) {
                // Atualiza estoque do perfume
                $stmtEstoque = $pdo->prepare("UPDATE perfumes SET quantidade = quantidade - ? WHERE id = ? AND quantidade >= ?");
                $stmtEstoque->execute([$quantidade, $perfume_id, $quantidade]);
                if ($stmtEstoque->rowCount() === 0) {
                    $erro = 'Estoque insuficiente para a quantidade informada!';
                } else {
                    $stmtItem = $pdo->prepare("UPDATE itens_venda SET quantidade=?, valor_venda=?, valor_compra=?, valor_lucro=? WHERE id=? AND venda_id=?");
                    $stmtItem->execute([$quantidade, $valor_venda_total, $valor_compra_total, $valor_lucro_total, $item['id'], $id]);
                }
            } else {
                $erro = 'Preencha todos os campos obrigatórios!';
            }
        }

        if (empty($erro)) {
            header('Location: list.php?edited=1');
            exit;
        }
    } else {
        $erro = 'Preencha todos os campos obrigatórios!';
    }
}

include __DIR__ . '/../templates/header.php';
?>
<h2>Editar Venda</h2>
<?php if ($erro): ?>
    <div class="alert alert-danger"><?= $erro ?></div>
<?php endif; ?>
<form method="post" id="formVenda">
    <div class="form-row">
        <div class="form-group col-md-4">
            <label>Cliente</label>
            <select name="cliente_id" class="form-control" required>
                <option value="">Selecione</option>
                <?php foreach ($clientes as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $venda['cliente_id'] == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group col-md-4">
            <label>Forma de Pagamento</label>
            <select name="forma_pagamento_id" class="form-control" required>
                <option value="">Selecione</option>
                <?php foreach ($formas as $f): ?>
                    <option value="<?= $f['id'] ?>" <?= $venda['forma_pagamento_id'] == $f['id'] ? 'selected' : '' ?>><?= htmlspecialchars($f['descricao']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group col-md-4">
            <label>Data da Venda</label>
            <input type="date" name="data_venda" class="form-control" required value="<?= htmlspecialchars($venda['data_venda']) ?>">
        </div>
    </div>
    <hr>
    <h5>Produtos</h5>
    <div id="itensVenda">
        <?php foreach ($itens_venda as $idx => $item): ?>
        <div class="form-row align-items-end item-venda" data-index="<?= $idx ?>">
            <div class="form-group col-md-4">
                <label>Perfume</label>
                <select name="itens[<?= $idx ?>][perfume_id]" class="form-control perfume-select" required>
                    <option value="">Selecione</option>
                    <?php foreach ($perfumes as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= $item['perfume_id'] == $p['id'] ? 'selected' : '' ?>><?= htmlspecialchars($p['titulo']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group col-md-2">
                <label>Quantidade</label>
                <input type="number" name="itens[<?= $idx ?>][quantidade]" class="form-control quantidade-item" min="1" value="<?= $item['quantidade'] ?>" required>
            </div>
            <div class="form-group col-md-2">
                <label>Valor Compra</label>
                <input type="text" name="itens[<?= $idx ?>][valor_compra]" class="form-control valor-compra-item" required readonly value="<?= $item['valor_compra_unitario'] ?>">
            </div>
            <div class="form-group col-md-2">
                <label>Valor Venda</label>
                <input type="text" name="itens[<?= $idx ?>][valor_venda]" class="form-control valor-venda-item" required value="<?= $item['valor_venda_unitario'] ?>">
            </div>
            <div class="form-group col-md-2">
                <label>Lucro</label>
                <input type="text" name="itens[<?= $idx ?>][valor_lucro]" class="form-control valor-lucro-item" readonly value="<?= $item['valor_lucro'] ?>">
            </div>
            <!-- <div class="form-group col-md-1">
                <button type="button" class="btn btn-danger btn-remove-item"><i class="fa fa-trash"></i></button>
            </div> -->
        </div>
        <?php endforeach; ?>
    </div>
    <!-- <button type="button" class="btn btn-info mb-3" id="addItem">Adicionar Produto</button> -->
    <div class="form-row mb-3">
        <div class="form-group col-md-4">
            <label><b>Total Venda</b></label>
            <input type="text" id="total_venda" class="form-control" readonly>
        </div>
        <div class="form-group col-md-4">
            <label><b>Total Compra</b></label>
            <input type="text" id="total_compra" class="form-control" readonly>
        </div>
        <div class="form-group col-md-4">
            <label><b>Total Lucro</b></label>
            <input type="text" id="total_lucro" class="form-control" readonly>
        </div>
    </div>
    <!-- <button type="submit" class="btn btn-success">Salvar</button> -->
    <a href="list.php" class="btn btn-secondary">Voltar</a>
</form>
<?php include __DIR__ . '/../templates/footer.php'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
$(function() {
    // Máscara para valores
    $('input[name="valor_compra"], input[name="valor_venda"], input[name="valor_lucro"]').mask('000.000.000,00', {reverse: true});

    // Preenche valor de compra ao selecionar perfume
    var perfumes = <?php echo json_encode($perfumes, JSON_UNESCAPED_UNICODE); ?>;
    $("select[name='perfume_id']").on('change', function() {
        var id = $(this).val();
        var perfume = perfumes.find(function(p) { return p.id == id; });
        if (perfume) {
            var valor = parseFloat(perfume.valor_compra).toFixed(2).replace('.', ',');
            valor = valor.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
            $("input[name='valor_compra']").val(valor).trigger('input');
        } else {
            $("input[name='valor_compra']").val('');
        }
    });
});

function parseMoeda(valor) {
    if (!valor) return 0;
    valor = valor.replace(/\./g, '').replace(',', '.');
    return parseFloat(valor) || 0;
}

function formatarMoedaBrasil(numero) {
    if (typeof numero !== 'number') numero = 0;
    return numero.toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function atualizarTotais() {
    var totalVenda = 0, totalCompra = 0, totalLucro = 0;
    
    $('#itensVenda .item-venda').each(function() {
        var idx = $(this).data('index');
        var quantidade = parseInt($(`input[name='itens[${idx}][quantidade]']`).val()) || 0;
        var valorVenda = parseMoeda($(`input[name='itens[${idx}][valor_venda]']`).val());
        var valorCompra = parseMoeda($(`input[name='itens[${idx}][valor_compra]']`).val());
        
        totalVenda += valorVenda * quantidade;
        totalCompra += valorCompra * quantidade;
    });
    
    totalLucro = totalVenda - totalCompra;
    
    $('#total_venda').val(formatarMoedaBrasil(totalVenda));
    $('#total_compra').val(formatarMoedaBrasil(totalCompra));
    $('#total_lucro').val(formatarMoedaBrasil(totalLucro));
}

function atualizarLucro() {
    setTimeout(function() {
        var vendaStr = document.querySelector("input[name='valor_venda']").value;
        var compraStr = document.querySelector("input[name='valor_compra']").value;
        var quantidade = parseInt(document.querySelector("input[name='quantidade']").value) || 1;
        var venda = parseMoeda(vendaStr);
        var compra = parseMoeda(compraStr);
        var lucroInput = document.querySelector("input[name='valor_lucro']");
        if (!isNaN(venda) && !isNaN(compra) && quantidade > 0) {
            var lucro = (venda - compra) * quantidade;
            lucroInput.value = formatarMoedaBrasil(lucro);
        } else {
            lucroInput.value = '';
        }
    }, 100);
}

document.addEventListener('DOMContentLoaded', function() {
    var venda = document.querySelector("input[name='valor_venda']");
    var compra = document.querySelector("input[name='valor_compra']");
    var quantidade = document.querySelector("input[name='quantidade']");
    if (venda && compra && quantidade) {
        venda.addEventListener('input', atualizarLucro);
        compra.addEventListener('input', atualizarLucro);
        quantidade.addEventListener('input', atualizarLucro);
    }

    // Atualiza totais ao carregar a página
    atualizarTotais();

    // Adicionar novo item
    var idx = <?= count($itens_venda) ?>;
    $('#addItem').on('click', function() {
        var newItem = `
        <div class="form-row align-items-end item-venda" data-index="` + idx + `">
            <div class="form-group col-md-4">
                <label>Perfume</label>
                <select name="itens[` + idx + `][perfume_id]" class="form-control perfume-select" required>
                    <option value="">Selecione</option>
                    <?php foreach ($perfumes as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['titulo']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group col-md-2">
                <label>Quantidade</label>
                <input type="number" name="itens[` + idx + `][quantidade]" class="form-control quantidade-item" min="1" value="1" required>
            </div>
            <div class="form-group col-md-2">
                <label>Valor Compra</label>
                <input type="text" name="itens[` + idx + `][valor_compra]" class="form-control valor-compra-item" required readonly>
            </div>
            <div class="form-group col-md-2">
                <label>Valor Venda</label>
                <input type="text" name="itens[` + idx + `][valor_venda]" class="form-control valor-venda-item" required>
            </div>
            <div class="form-group col-md-2">
                <label>Lucro</label>
                <input type="text" name="itens[` + idx + `][valor_lucro]" class="form-control valor-lucro-item" readonly>
            </div>
            <div class="form-group col-md-1">
                <button type="button" class="btn btn-danger btn-remove-item"><i class="fa fa-trash"></i></button>
            </div>
        </div>`;
        $('#itensVenda').append(newItem);
        idx++;
    });

    // Remover item
    $(document).on('click', '.btn-remove-item', function() {
        $(this).closest('.item-venda').remove();
        atualizarTotais();
    });

    // Atualizar totais ao mudar quantidade ou valores
    $(document).on('input', '.quantidade-item, .valor-venda-item, .valor-compra-item', function() {
        var $item = $(this).closest('.item-venda');
        var idx = $item.data('index');
        var quantidade = parseInt($("input[name='itens[" + idx + "][quantidade]']").val()) || 0;
        var valorVenda = parseMoeda($("input[name='itens[" + idx + "][valor_venda]']").val());
        var valorCompra = parseMoeda($("input[name='itens[" + idx + "][valor_compra]']").val());
        var valorLucro = valorVenda - valorCompra;
        $("input[name='itens[" + idx + "][valor_lucro]']").val(formatarMoedaBrasil(valorLucro));

        atualizarTotais();
    });
});
</script>