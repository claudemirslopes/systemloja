<?php
require_once __DIR__ . '/../config/config.php';

$perfumes = $pdo->query("SELECT * FROM perfumes")->fetchAll();
$clientes = $pdo->query("SELECT * FROM clientes")->fetchAll();
$formas = $pdo->query("SELECT * FROM formas_pagamento")->fetchAll();

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id = $_POST['cliente_id'] ?? '';
    $forma_pagamento_id = $_POST['forma_pagamento_id'] ?? '';
    $data_venda = $_POST['data_venda'] ?? '';

    if ($cliente_id && $forma_pagamento_id && $data_venda) {
        // Inicia transação
        $pdo->beginTransaction();
        try {
            // Insere a venda
            $stmt = $pdo->prepare("INSERT INTO vendas (cliente_id, forma_pagamento_id, data_venda) VALUES (?, ?, ?)");
            $stmt->execute([$cliente_id, $forma_pagamento_id, $data_venda]);
            $venda_id = $pdo->lastInsertId();

            $itens = $_POST['itens'] ?? [];
            foreach ($itens as $item) {
                $perfume_id = $item['perfume_id'] ?? '';
                $quantidade = intval($item['quantidade'] ?? 1);
                $valor_venda = $item['valor_venda'] ?? '';
                $valor_compra = $item['valor_compra'] ?? '';
                $valor_lucro = $item['valor_lucro'] ?? '';

                $valor_venda = moedaParaFloat($valor_venda);
                $valor_compra = moedaParaFloat($valor_compra);
                $valor_lucro = moedaParaFloat($valor_lucro);

                // Multiplica valores pela quantidade
                $valor_venda_total = $valor_venda * $quantidade;
                $valor_compra_total = $valor_compra * $quantidade;
                $valor_lucro_total = $valor_venda_total - $valor_compra_total;

                // Atualiza estoque do perfume
                $stmtEstoque = $pdo->prepare("UPDATE perfumes SET quantidade = quantidade - ? WHERE id = ? AND quantidade >= ?");
                $stmtEstoque->execute([$quantidade, $perfume_id, $quantidade]);
                if ($stmtEstoque->rowCount() === 0) {
                    $erro = 'Estoque insuficiente para a quantidade informada!';
                    throw new Exception($erro);
                }

                // Insere o item da venda
                $stmtItem = $pdo->prepare("INSERT INTO itens_venda (venda_id, perfume_id, quantidade, valor_venda, valor_compra, valor_lucro) VALUES (?, ?, ?, ?, ?, ?)");
                $stmtItem->execute([$venda_id, $perfume_id, $quantidade, $valor_venda_total, $valor_compra_total, $valor_lucro_total]);
            }

            // Remove campos antigos de perfume_id, quantidade, valor_venda, valor_compra, valor_lucro da tabela vendas
            // Adicione os campos de totais
            $stmt = $pdo->prepare("UPDATE vendas SET valor_venda=?, valor_compra=?, valor_lucro=? WHERE id=?");
            $stmt->execute([
                array_sum(array_map(function($item){ return moedaParaFloat($item['valor_venda']) * intval($item['quantidade']); }, $itens)),
                array_sum(array_map(function($item){ return moedaParaFloat($item['valor_compra']) * intval($item['quantidade']); }, $itens)),
                array_sum(array_map(function($item){ return moedaParaFloat($item['valor_lucro']); }, $itens)),
                $venda_id
            ]);

            // Commit na transação
            $pdo->commit();
            header('Location: list.php?success=1');
            exit;
        } catch (Exception $e) {
            // Rollback em caso de erro
            $pdo->rollBack();
            $erro = 'Erro ao processar a venda: ' . $e->getMessage();
        }
    } else {
        $erro = 'Preencha todos os campos obrigatórios!';
    }
}

function moedaParaFloat($valor) {
    $valor = preg_replace('/\./', '', $valor);
    $valor = str_replace(',', '.', $valor);
    return floatval($valor);
}
// SÓ AGORA INCLUA O HEADER!
include __DIR__ . '/../templates/header.php';
?>
<h2>Nova Venda</h2>
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
                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group col-md-4">
            <label>Forma de Pagamento</label>
            <select name="forma_pagamento_id" class="form-control" required>
                <option value="">Selecione</option>
                <?php foreach ($formas as $f): ?>
                    <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['descricao']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group col-md-4">
            <label>Data da Venda</label>
            <input type="date" name="data_venda" class="form-control" required>
        </div>
    </div>
    <hr>
    <h5>Produtos</h5>
    <div id="itensVenda"></div>
    <button type="button" class="btn btn-info mb-3" id="addItem">Adicionar Produto</button>
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
    <button type="submit" class="btn btn-success">Salvar</button>
    <a href="list.php" class="btn btn-secondary">Voltar</a>
</form>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
$(document).ready(function() {
    // Garante que a máscara de valor seja aplicada ao carregar a página
    setTimeout(function() {
        if (typeof aplicarMascaraValor === 'function') {
            aplicarMascaraValor();
        }
    }, 0);

    // Aplica a máscara ao adicionar novos itens
    $('#addItem').on('click', function() {
        setTimeout(function() {
            if (typeof aplicarMascaraValor === 'function') {
                aplicarMascaraValor();
            }
        }, 100); // Aguarda a renderização do novo item
    });

    // Verifica se o jQuery está carregado
    if (typeof jQuery === 'undefined') {
        console.error('jQuery não está carregado. Verifique o carregamento da biblioteca.');
    } else {
        console.log('jQuery carregado com sucesso. Versão:', jQuery.fn.jquery);
    }

    // Verifica se o plugin .mask() está disponível
    if (typeof $.fn.mask === 'undefined') {
        console.error('O plugin jQuery Mask não está carregado. Verifique o carregamento da biblioteca.');
    } else {
        console.log('Plugin jQuery Mask carregado com sucesso.');
    }
});

const perfumes = <?php echo json_encode($perfumes, JSON_UNESCAPED_UNICODE); ?>;
let itemIndex = 0;

function novaLinhaItem(index) {
    return '<div class="form-row align-items-end item-venda" data-index="'+index+'">' +
        '<div class="form-group col-md-4">'+
            '<label>Perfume</label>'+
            '<select name="itens['+index+'][perfume_id]" class="form-control perfume-select" required>'+
                '<option value="">Selecione</option>'+
                perfumes.map(function(p){return '<option value="'+p.id+'">'+p.titulo+'</option>';}).join('')+
            '</select>'+
        '</div>'+
        '<div class="form-group col-md-2">'+
            '<label>Quantidade</label>'+
            '<input type="number" name="itens['+index+'][quantidade]" class="form-control quantidade-item" min="1" value="1" required>'+
        '</div>'+
        '<div class="form-group col-md-2">'+
            '<label>Valor Compra</label>'+
            '<input type="text" name="itens['+index+'][valor_compra]" class="form-control valor-compra-item" required readonly>'+
        '</div>'+
        '<div class="form-group col-md-2">'+
            '<label>Valor Venda</label>'+
            '<input type="text" name="itens['+index+'][valor_venda]" class="form-control valor-venda-item" required>'+
        '</div>'+
        '<div class="form-group col-md-2">'+
            '<label>Lucro</label>'+
            '<input type="text" name="itens['+index+'][valor_lucro]" class="form-control valor-lucro-item" readonly>'+
        '</div>'+
        '<div class="form-group col-md-1">'+
            '<button type="button" class="btn btn-danger btn-remove-item"><i class="fa fa-trash"></i></button>'+
        '</div>'+
    '</div>';
}

function atualizarTotais() {
    var totalVenda = 0, totalCompra = 0, totalLucro = 0;
    var produtosAgrupados = {};
    $(".item-venda").each(function() {
        var idx = $(this).data('index');
        var perfumeId = $(`select[name='itens[${idx}][perfume_id]']`).val();
        var q = parseInt($(`input[name='itens[${idx}][quantidade]']`).val()) || 0;
        var vVenda = parseMoeda($(`input[name='itens[${idx}][valor_venda]']`).val());
        var vCompra = parseMoeda($(`input[name='itens[${idx}][valor_compra]']`).val());
        if (!perfumeId) return;
        if (!produtosAgrupados[perfumeId]) {
            produtosAgrupados[perfumeId] = {quantidade: 0, valor_venda: 0, valor_compra: 0};
        }
        produtosAgrupados[perfumeId].quantidade += q;
        produtosAgrupados[perfumeId].valor_venda = vVenda; // sempre pega o último valor informado
        produtosAgrupados[perfumeId].valor_compra = vCompra;
    });
    // Atualiza os campos de lucro e totais agrupando por produto
    Object.keys(produtosAgrupados).forEach(function(perfumeId) {
        var item = produtosAgrupados[perfumeId];
        var lucro = (item.valor_venda - item.valor_compra) * item.quantidade;
        totalVenda += item.valor_venda * item.quantidade;
        totalCompra += item.valor_compra * item.quantidade;
        totalLucro += lucro;
        // Atualiza todos os campos de lucro dos itens desse produto
        $(`select[name^='itens'][name$='[perfume_id]']`).each(function() {
            if ($(this).val() == perfumeId) {
                var idx = $(this).closest('.item-venda').data('index');
                $(`input[name='itens[${idx}][valor_lucro]']`).val(formatarMoedaBrasil(lucro));
            }
        });
    });
    $('#total_venda').val(formatarMoedaBrasil(totalVenda));
    $('#total_compra').val(formatarMoedaBrasil(totalCompra));
    $('#total_lucro').val(formatarMoedaBrasil(totalLucro));
}

function parseMoeda(valor) {
    valor = valor ? valor.replace(/\./g, '').replace(',', '.') : '0';
    return parseFloat(valor) || 0;
}

function formatarMoedaBrasil(numero) {
    return numero.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

$(function() {
    // Função específica para aplicar máscara no valor de venda
    function aplicarMascaraVenda(input) {
        $(input).off('input.mask').on('input.mask', function(e) {
            var value = $(this).val().replace(/\D/g, '');
            value = (parseInt(value) / 100).toFixed(2);
            value = value.replace('.', ',');
            value = value.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
            $(this).val(value);
        });
    }

    function eventosLinha(index) {
        // Aplicar máscara assim que o campo é criado
        var valorVendaInput = $(`input[name='itens[${index}][valor_venda]']`);
        aplicarMascaraVenda(valorVendaInput);

        $(`select[name='itens[${index}][perfume_id]']`).off('change').on('change', function() {
            var id = $(this).val();
            var perfume = perfumes.find(function(p){ return p.id == id; });
            if (perfume) {
                var valor = parseFloat(perfume.valor_compra).toFixed(2).replace('.', ',');
                valor = valor.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
                $(`input[name='itens[${index}][valor_compra]']`).val(valor).trigger('input');
            } else {
                $(`input[name='itens[${index}][valor_compra]']`).val('');
            }
            atualizarTotais();
        });

        // Eventos para atualizar totais
        valorVendaInput.off('input.calc').on('input.calc', atualizarTotais);
        $(`input[name='itens[${index}][quantidade]'], input[name='itens[${index}][valor_compra]']`).off('input').on('input', atualizarTotais);
    }

    function addItem() {
        $('#itensVenda').append(novaLinhaItem(itemIndex));
        eventosLinha(itemIndex);
        itemIndex++;
    }

    $('#addItem').off('click').on('click', function() {
        addItem();
    });

    $('#itensVenda').off('click', '.btn-remove-item').on('click', '.btn-remove-item', function() {
        $(this).closest('.item-venda').remove();
        atualizarTotais();
    });

    // Aplica máscara nos campos de totais
    $('#total_venda, #total_compra, #total_lucro').mask('#.##0,00', {reverse: true});
});
</script>
<?php include __DIR__ . '/../templates/footer.php'; ?>
