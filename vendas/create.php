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
                array_sum(array_map(function ($item) {
                    return moedaParaFloat($item['valor_venda']) * intval($item['quantidade']);
                }, $itens)),
                array_sum(array_map(function ($item) {
                    return moedaParaFloat($item['valor_compra']) * intval($item['quantidade']);
                }, $itens)),
                array_sum(array_map(function ($item) {
                    return moedaParaFloat($item['valor_lucro']);
                }, $itens)),
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

function moedaParaFloat($valor)
{
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
            <label for="busca_cliente">Cliente</label>
            <div class="input-group">
                <input type="text" id="busca_cliente" class="form-control" placeholder="Buscar cliente..." autocomplete="off">
                <input type="hidden" name="cliente_id" id="cliente_id" required>
                <div class="input-group-append">
                    <button type="button" class="btn btn-success" id="addCliente" title="Adicionar novo cliente">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
            </div>
            <div id="resultados_busca" class="position-absolute bg-white border rounded shadow-sm" style="z-index: 1000; display: none; width: 100%;"></div>
        </div>
        <div class="form-group col-md-4">
            <label for="forma_pagamento_id">Forma de Pagamento</label>
            <select name="forma_pagamento_id" id="forma_pagamento_id" class="form-control" required>
                <option value="">Selecione</option>
                <?php foreach ($formas as $f): ?>
                    <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['descricao']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group col-md-4">
            <label for="data_venda">Data da Venda</label>
            <input type="date" name="data_venda" id="data_venda" class="form-control" required>
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
    <button type="submit" class="btn btn-warning">Salvar</button>
    <a href="list.php" class="btn btn-secondary">Voltar</a>
</form>

<!-- Modal Adicionar Cliente -->
<div class="modal fade" id="modalCliente" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Novo Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formNovoCliente">
                    <div class="form-group">
                        <label for="nome">Nome</label>
                        <input type="text" class="form-control" id="nome" name="nome">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                    <div class="form-group">
                        <label for="telefone">Telefone</label>
                        <input type="text" class="form-control" id="telefone" name="telefone">
                    </div>
                </form>
                <div id="modalClienteAlert" style="display: none; margin-top: 15px;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-success" id="salvarCliente">Salvar</button>
            </div>
        </div>
    </div>
</div>

<style>
    .input-group {
        display: flex;
        align-items: stretch;
    }

    .input-group .form-control {
        flex: 1;
        border-radius: 4px 0 0 4px;
    }

    .input-group .btn {
        border-radius: 0 4px 4px 0;
        padding: 0.375rem 0.75rem;
    }

    #resultados_busca {
        max-height: 300px;
        overflow-y: auto;
        margin-top: 5px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .cliente-resultado {
        padding: 10px 15px;
        border-bottom: 1px solid #eee;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .cliente-resultado:last-child {
        border-bottom: none;
    }

    .cliente-resultado:hover {
        background-color: #f8f9fa;
    }

    .cliente-resultado strong {
        display: block;
        color: #333;
    }

    .cliente-resultado small {
        color: #666;
    }

    #modalCliente .modal-content {
        border-radius: 8px;
    }

    #modalCliente .modal-header {
        border-radius: 8px 8px 0 0;
        background-color: #f8f9fa;
    }

    #modalCliente .btn-close {
        font-size: 0.8rem;
    }

    .input-group select.form-control {
        flex: 1;
    }

    #addCliente {
        width: 40px;
        padding: 0.375rem;
    }
</style>

<!-- Defina o array perfumes ANTES do script -->
<script>
    const perfumes = <?php echo json_encode($perfumes, JSON_UNESCAPED_UNICODE); ?>;
</script>
<?php include __DIR__ . '/../templates/footer.php'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
    function parseMoedaBR(valor) {
        if (!valor) return 0;
        valor = valor.toString().trim();
        valor = valor.replace(/\./g, '').replace(',', '.');
        return isNaN(valor) ? 0 : parseFloat(valor);
    }

    function formatarMoedaBR(numero) {
        if (typeof numero !== 'number') numero = 0;
        return numero.toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }
    $(document).ready(function() {
        // Máscara de telefone
        $('#telefone').mask('(00) 00000-0000');
        // Máscara de valores
        function aplicarMascaraValor() {
            // Máscara até 7 dígitos antes da vírgula
            $("input[name*='valor'], input.valor").mask('000.000.000,00', {
                reverse: true
            });
        }
        aplicarMascaraValor();
        // Reaplica máscara ao adicionar produto
        $(document).on('focus', "input[name*='valor'], input.valor", function() {
            $(this).mask('000.000.000,00', {
                reverse: true
            });
        });
        // Cálculo automático do campo Lucro por item
        $(document).on('input', ".valor-venda-item, .valor-compra-item, .quantidade-item", function() {
            var $item = $(this).closest('.item-venda');
            var index = $item.data('index');
            var quantidade = parseInt($(`input[name='itens[${index}][quantidade]']`).val()) || 0;
            var valorVenda = parseMoedaBR($(`input[name='itens[${index}][valor_venda]']`).val());
            var valorCompra = parseMoedaBR($(`input[name='itens[${index}][valor_compra]']`).val());
            var lucro = (valorVenda - valorCompra) * quantidade;
            $(`input[name='itens[${index}][valor_lucro]']`).val(formatarMoedaBR(lucro));
            atualizarTotais();
        });
        $(document).ready(function() {
            console.log('jQuery e DOM prontos!');
            // Máscara de telefone
            $('#telefone').mask('(00) 00000-0000');

            // Adiciona spinner ao campo de busca
            let timeoutBusca;
            let spinnerHtml = '<div id="spinner-busca" style="display: none;"><div class="text-center p-2"><i class="fa fa-spinner fa-spin"></i> Buscando...</div></div>';
            $('#resultados_busca').before(spinnerHtml);

            // Busca de clientes
            $('#busca_cliente').on('input', function() {
                clearTimeout(timeoutBusca);
                const termo = $(this).val().trim();
                if (termo.length < 2) {
                    $('#resultados_busca, #spinner-busca').hide();
                    return;
                }
                $('#spinner-busca').show();
                $('#resultados_busca').hide();
                timeoutBusca = setTimeout(() => {
                    $.get('/systemloja/clientes/buscar.php', {
                        termo: termo
                    }, function(data) {
                        $('#spinner-busca').hide();
                        console.log('Busca clientes:', data);
                        if (Array.isArray(data) && data.length > 0) {
                            const html = data.map(cliente => `
                            <div class="p-2 border-bottom cliente-resultado" 
                                 data-id="${cliente.id}" 
                                 data-nome="${cliente.nome}">
                                <strong>${cliente.nome}</strong>
                                <small class="text-muted">${cliente.email} - ${cliente.telefone}</small>
                            </div>
                        `).join('');
                            $('#resultados_busca').html(html).show();
                        } else {
                            $('#resultados_busca').html('<div class="p-2">Nenhum cliente encontrado</div>').show();
                        }
                    }, 'json').fail(function() {
                        $('#spinner-busca').hide();
                        $('#resultados_busca').html('<div class="p-2 text-danger">Erro ao buscar clientes</div>').show();
                    });
                }, 300);
            });
            // Selecionar cliente da busca
            $(document).on('click', '.cliente-resultado', function() {
                const id = $(this).data('id');
                const nome = $(this).data('nome');
                $('input[name="cliente_id"]').val(id);
                $('#busca_cliente').val(nome);
                $('#resultados_busca').hide();
            });
            // Esconder resultados ao clicar fora
            $(document).mouseup(function(e) {
                var container = $("#resultados_busca");
                var input = $("#busca_cliente");
                if (!container.is(e.target) && !input.is(e.target) && container.has(e.target).length === 0) {
                    container.hide();
                }
            });
            // Modal novo cliente (Bootstrap 5)
            $('#addCliente').on('click', function(e) {
                e.preventDefault();
                $('#formNovoCliente')[0].reset();
                $('#modalClienteAlert').hide();
                var myModal = new bootstrap.Modal(document.getElementById('modalCliente'));
                myModal.show();
            });
            // Salvar novo cliente
            $('#salvarCliente').off('click').on('click', function() {
                var nome = $('#nome').val().trim();
                var email = $('#email').val().trim();
                var telefone = $('#telefone').val().trim();
                if (!nome || !email || !telefone) {
                    $('#modalClienteAlert').html('<div class="alert alert-danger">Preencha todos os campos!</div>').show();
                    return;
                }
                var $btn = $(this).prop('disabled', true);
                $.ajax({
                    url: '/systemloja/clientes/salvar_ajax.php',
                    method: 'POST',
                    data: {
                        nome,
                        email,
                        telefone
                    },
                    dataType: 'json'
                }).done(function(response) {
                    if (response.success) {
                        $('input[name="cliente_id"]').val(response.id);
                        $('#busca_cliente').val(nome);
                        var modalInstance = bootstrap.Modal.getInstance(document.getElementById('modalCliente'));
                        modalInstance.hide();
                        alert('Cliente cadastrado com sucesso!');
                    } else {
                        $('#modalClienteAlert').html('<div class="alert alert-danger">' + (response.message || 'Erro ao salvar cliente!') + '</div>').show();
                    }
                }).fail(function() {
                    $('#modalClienteAlert').html('<div class="alert alert-danger">Erro ao salvar cliente!</div>').show();
                }).always(function() {
                    $btn.prop('disabled', false);
                });
            });
            // Adicionar produto
            let itemIndex = 0;

            function novaLinhaItem(index) {
                return '<div class="form-row align-items-end item-venda" data-index="' + index + '">' +
                    '<div class="form-group col-md-4">' +
                    '<label for="perfume_' + index + '">Perfume</label>' +
                    '<select name="itens[' + index + '][perfume_id]" id="perfume_' + index + '" class="form-control perfume-select" required>' +
                    '<option value="">Selecione</option>' +
                    perfumes.map(function(p) {
                        return '<option value="' + p.id + '">' + p.titulo + '</option>';
                    }).join('') +
                    '</select>' +
                    '</div>' +
                    '<div class="form-group col-md-2">' +
                    '<label for="quantidade_' + index + '">Quantidade</label>' +
                    '<input type="number" name="itens[' + index + '][quantidade]" id="quantidade_' + index + '" class="form-control quantidade-item" min="1" value="1" required>' +
                    '</div>' +
                    '<div class="form-group col-md-2">' +
                    '<label for="valor_compra_' + index + '">Valor Compra</label>' +
                    '<input type="text" name="itens[' + index + '][valor_compra]" id="valor_compra_' + index + '" class="form-control valor-compra-item" required readonly>' +
                    '</div>' +
                    '<div class="form-group col-md-2">' +
                    '<label for="valor_venda_' + index + '">Valor Venda</label>' +
                    '<input type="text" name="itens[' + index + '][valor_venda]" id="valor_venda_' + index + '" class="form-control valor-venda-item" required>' +
                    '</div>' +
                    '<div class="form-group col-md-2">' +
                    '<label for="valor_lucro_' + index + '">Lucro</label>' +
                    '<input type="text" name="itens[' + index + '][valor_lucro]" id="valor_lucro_' + index + '" class="form-control valor-lucro-item" readonly>' +
                    '</div>' +
                    '<div class="form-group col-md-1">' +
                    '<button type="button" class="btn btn-danger btn-remove-item"><i class="fa fa-trash"></i></button>' +
                    '</div>' +
                    '</div>';
            }
            

            function eventosLinha(index) {
                var valorVendaInput = $(`input[name='itens[${index}][valor_venda]']`);
                valorVendaInput.off('input.calc').on('input.calc', atualizarTotais);
                $(`input[name='itens[${index}][quantidade]'], input[name='itens[${index}][valor_compra]']`).off('input').on('input', atualizarTotais);
                $(`select[name='itens[${index}][perfume_id]']`).off('change').on('change', function() {
                    var id = $(this).val();
                    var perfume = perfumes.find(function(p) {
                        return p.id == id;
                    });
                    if (perfume) {
                        var valor = parseFloat(perfume.valor_compra).toFixed(2).replace('.', ',');
                        $(`input[name='itens[${index}][valor_compra]']`).val(valor);
                        // Não preenche valor_venda automaticamente!
                        $(`input[name='itens[${index}][valor_venda]']`).val("");
                        $(`input[name='itens[${index}][quantidade]']`).trigger('input');
                    }
                });
            }
            $('#addItem').on('click', function() {
                var index = itemIndex++;
                $('#itensVenda').append(novaLinhaItem(index));
                eventosLinha(index);
                atualizarTotais();
            });
            $(document).on('click', '.btn-remove-item', function() {
                $(this).closest('.item-venda').remove();
                atualizarTotais();
            });

            
        });
        function atualizarTotais() {
                var totalVenda = 0,
                    totalCompra = 0,
                    totalLucro = 0;
                var produtosAgrupados = {};

                $(".item-venda").each(function() {
                    var idx = $(this).data('index');
                    var perfumeId = $(`select[name='itens[${idx}][perfume_id]']`).val();
                    var q = parseInt($(`input[name='itens[${idx}][quantidade]']`).val()) || 0;
                    var vVendaRaw = $(`input[name='itens[${idx}][valor_venda]']`).val();
                    var vCompraRaw = $(`input[name='itens[${idx}][valor_compra]']`).val();
                    var vVenda = parseMoedaBR(vVendaRaw);
                    var vCompra = parseMoedaBR(vCompraRaw);

                    console.log(`Item ${idx} => Qtd: ${q}, Venda: ${vVendaRaw} → ${vVenda}, Compra: ${vCompraRaw} → ${vCompra}`);

                    if (!perfumeId) return;

                    if (!produtosAgrupados[perfumeId]) {
                        produtosAgrupados[perfumeId] = {
                            quantidade: 0,
                            valor_venda: 0,
                            valor_compra: 0
                        };
                    }

                    produtosAgrupados[perfumeId].quantidade += q;
                    produtosAgrupados[perfumeId].valor_venda = vVenda; // Sempre pega o último valor inserido
                    produtosAgrupados[perfumeId].valor_compra = vCompra;
                });

                Object.keys(produtosAgrupados).forEach(function(perfumeId) {
                    var item = produtosAgrupados[perfumeId];
                    var lucro = (item.valor_venda - item.valor_compra) * item.quantidade;
                    var subtotalVenda = item.valor_venda * item.quantidade;
                    var subtotalCompra = item.valor_compra * item.quantidade;

                    console.log(`→ Perfume ${perfumeId}: ${item.quantidade} un × Venda ${item.valor_venda} = ${subtotalVenda}`);

                    totalVenda += subtotalVenda;
                    totalCompra += subtotalCompra;
                    totalLucro += lucro;
                });

                console.log(`📌 Totais: Venda ${totalVenda}, Compra ${totalCompra}, Lucro ${totalLucro}`);

                $('#total_venda').val(formatarMoedaBR(totalVenda));
                $('#total_compra').val(formatarMoedaBR(totalCompra));
                $('#total_lucro').val(formatarMoedaBR(totalLucro));
            }
    });
</script>