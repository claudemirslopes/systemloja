<?php
require_once __DIR__ . '/../config/config.php';

// Função para converter valor em formato brasileiro para float
function moedaParaFloat($valor) {
    $valor = str_replace('.', '', $valor); // Remove pontos de milhar
    $valor = str_replace(',', '.', $valor); // Substitui vírgula por ponto
    return number_format((float)$valor, 2, '.', ''); // Garante 2 casas decimais
}

$titulo = $valor_compra = $quantidade = '';
// Preenche data_entrada com a data atual se não for POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data_entrada = $_POST['data_entrada'] ?? '';
} else {
    $data_entrada = date('Y-m-d');
}
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';
    $valor_compra = $_POST['valor_compra'] ?? '';
    $quantidade = $_POST['quantidade'] ?? '';
    $data_entrada = $_POST['data_entrada'] ?? '';

    if ($titulo && $valor_compra && $quantidade && $data_entrada) {
        // Converte o valor de compra para o formato correto
        $valor_compra = moedaParaFloat($valor_compra);
        
        $stmt = $pdo->prepare("INSERT INTO perfumes (titulo, valor_compra, quantidade, data_entrada) VALUES (?, ?, ?, ?)");
        $stmt->execute([$titulo, $valor_compra, $quantidade, $data_entrada]);
        header('Location: list.php?success=1');
        exit;
    } else {
        $erro = 'Preencha todos os campos obrigatórios!';
    }
}
include __DIR__ . '/../templates/header.php';
?>
<h2>Novo Perfume</h2>
<?php if ($erro): ?>
    <div class="alert alert-danger"><?= $erro ?></div>
<?php endif; ?>
<form method="post">
    <div class="form-row">
        <div class="form-group">
            <label>Título</label>
            <input type="text" name="titulo" class="form-control" value="<?= htmlspecialchars($titulo) ?>" required>
        </div>
        <div class="form-group">
            <label>Valor de Compra</label>
            <input type="text" name="valor_compra" class="form-control mascara-valor" required placeholder="Ex: 1.000,00" value="<?= htmlspecialchars($valor_compra) ?>">
        </div>
        <div class="form-group">
            <label>Quantidade</label>
            <input type="number" name="quantidade" class="form-control" value="<?= htmlspecialchars($quantidade) ?>" required>
        </div>
        <div class="form-group">
            <label>Data de Entrada</label>
            <input type="date" name="data_entrada" class="form-control" value="<?= htmlspecialchars($data_entrada) ?>" required>
        </div>
    </div>
    <div class="mt-4">
        <button type="submit" class="btn btn-primary">Salvar</button>
        <a href="list.php" class="btn btn-secondary">Voltar</a>
    </div>
</form>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
$(function() {
    $('input.mascara-valor').mask('000.000.000,00', {reverse: true});
});
</script>
<?php include __DIR__ . '/../templates/footer.php'; ?>
