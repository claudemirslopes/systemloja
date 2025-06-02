<?php
require_once __DIR__ . '/../config/config.php';
if (!isset($_SESSION['usuario_id'])) {
    header('Location: /systemloja/login.php');
    exit;
}
// Detecta a página atual para destacar o menu
$currentPage = basename($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>System Loja</title>
    <!-- SB Admin 2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/systemloja/assets/style/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .bg-gradient-primary {
            background-color: #333 !important;
            background-image: none !important;
        }
        .sidebar {
            background-color: #333 !important;
        }
        .sidebar .sidebar-brand {
            padding: 1.5rem 1rem;
        }
        .sidebar .sidebar-brand img {
            width: 120px;
            height: 120px;
            margin: 0 auto 0 0;
            display: block;
        }
        .sidebar .nav-item .nav-link {
            color: #fff !important;
        }
        .sidebar .nav-item .nav-link.active, .sidebar .nav-item .nav-link:hover {
            background-color: #444 !important;
            color: #fff !important;
        }
        .container-fluid {
            padding-top: 32px !important;
            padding-bottom: 32px !important;
            padding-left: 32px !important;
            padding-right: 32px !important;
        }
        h1, h2, h3, .h1, .h2, .h3 {
            font-size: 1.1rem !important;
            font-weight: 600 !important;
        }
        .card-title, .page-title {
            font-size: 1rem !important;
            font-weight: 500 !important;
        }
        /* DEBUG OVERLAY */
        #debug-overlay-info {
          position: fixed;
          top: 0; left: 0;
          z-index: 99999;
          background: rgba(255,255,0,0.95);
          color: #333;
          font-size: 14px;
          padding: 6px 12px;
          border-bottom: 2px solid #333;
          border-right: 2px solid #333;
          pointer-events: none;
        }
        #debug-overlay-highlight {
          position: fixed;
          border: 2px solid red;
          z-index: 99998;
          pointer-events: none;
        }
    </style>
</head>
<body id="page-top">
<!-- BLOCO DE TESTE INÍCIO -->
<!-- <div style="position:fixed;top:10px;right:10px;z-index:9999;background:#fff;padding:10px;border:2px solid #333;">
  <span id="jquery-test"></span>
  <i class="fa fa-user" style="font-size:24px;color:#780e14;"></i>
  <button class="btn btn-primary" id="abrirModalTeste">Abrir Modal Teste</button>
</div> -->
<!-- <div class="modal fade" id="modalTeste" tabindex="-1" aria-labelledby="modalTesteLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTesteLabel">Modal Teste Bootstrap 5</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Se você está vendo isso, Bootstrap 5 está funcionando!
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div> -->
<!-- BLOCO DE TESTE FIM -->
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav sidebar sidebar-dark accordion bg-gradient-primary" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-start" href="/systemloja/index.php">
                <img src="/systemloja/assets/images/logo.png" alt="Logo" style="width:90%;height:120px;object-fit:contain;">
            </a>
            <hr class="sidebar-divider my-0">
            <li class="nav-item <?php if($currentPage == 'index.php') echo 'active'; ?>">
                <a class="nav-link" href="/systemloja/index.php"><i class="fa fa-tachometer"></i> <span>Dashboard</span></a>
            </li>
            <li class="nav-item <?php if(strpos($_SERVER['SCRIPT_NAME'], '/perfumes/') !== false) echo 'active'; ?>">
                <a class="nav-link" href="/systemloja/perfumes/list.php"><i class="fa fa-flask"></i> <span>Perfumes</span></a>
            </li>
            <li class="nav-item <?php if(strpos($_SERVER['SCRIPT_NAME'], '/clientes/') !== false) echo 'active'; ?>">
                <a class="nav-link" href="/systemloja/clientes/list.php"><i class="fa fa-users"></i> <span>Clientes</span></a>
            </li>
            <li class="nav-item <?php if(strpos($_SERVER['SCRIPT_NAME'], '/vendas/') !== false) echo 'active'; ?>">
                <a class="nav-link collapsed d-flex justify-content-between align-items-center" href="#" data-bs-toggle="collapse" data-bs-target="#collapseVendas" aria-expanded="false" aria-controls="collapseVendas">
                    <span><i class="fa fa-shopping-cart"></i> <span>Vendas</span></span>
                    <i class="fa fa-chevron-down ms-2" id="iconVendas"></i>
                </a>
                <div id="collapseVendas" class="collapse<?php if(strpos($_SERVER['SCRIPT_NAME'], '/vendas/') !== false) echo ' show'; ?>" data-bs-parent="#accordionSidebar" style="background:#444444; border-radius:0 0 10px 10px; box-shadow:0 4px 12px rgba(0,0,0,0.10); margin-left:0; margin-right:0; position:relative; z-index:1; overflow:hidden;">
                    <div class="py-2 collapse-inner rounded" style="padding:0;">
                        <a class="collapse-item nav-link" href="/systemloja/vendas/list.php" style="color:#fff; padding-left:2.5rem; padding-top:0.7rem; padding-bottom:0.7rem; border-bottom:1px solid #444; background:none;">
                            <i class="fa fa-list"></i> Todas as Vendas
                        </a>
                        <a class="collapse-item nav-link" href="/systemloja/vendas/relatorio.php" style="color:#fff; padding-left:2.5rem; padding-top:0.7rem; padding-bottom:0.7rem; background:none;">
                            <i class="fa fa-bar-chart"></i> Relatório
                        </a>
                    </div>
                </div>
            </li>
            <li class="nav-item <?php if(strpos($_SERVER['SCRIPT_NAME'], '/formas_pagamento/') !== false) echo 'active'; ?>">
                <a class="nav-link" href="/systemloja/formas_pagamento/list.php"><i class="fa fa-credit-card"></i> <span>Formas de Pagamento</span></a>
            </li>
            <li class="nav-item <?php if(strpos($_SERVER['SCRIPT_NAME'], '/usuarios/') !== false) echo 'active'; ?>">
                <a class="nav-link" href="/systemloja/usuarios/list.php"><i class="fa fa-user"></i> <span>Usuários</span></a>
            </li>
            <hr class="sidebar-divider d-none d-md-block">
            <li class="nav-item" style="margin-top: -20px;"><a class="nav-link" href="/systemloja/logout.php"><i class="fa fa-sign-out"></i> <span>Sair</span></a></li>
            <hr class="sidebar-divider d-none d-md-block">
        </ul>
        <!-- End of Sidebar -->
        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar static-top shadow" style="margin-left:0;margin-right:0;border-radius:0;">
                    <!-- Notificação de estoque baixo -->
                    <div class="d-flex align-items-center" style="padding-left:18px;">
                        <?php
                        // Consulta quantidade de perfumes com estoque baixo (exemplo: <= 3)
                        $qtdEstoqueBaixo = 0;
                        try {
                            $stmtEstoque = $pdo->query("SELECT COUNT(*) as qtd FROM perfumes WHERE quantidade IS NOT NULL AND quantidade <= 3");
                            $rowEstoque = $stmtEstoque->fetch(PDO::FETCH_ASSOC);
                            $qtdEstoqueBaixo = (int)($rowEstoque['qtd'] ?? 0);
                        } catch (Exception $e) {
                            $qtdEstoqueBaixo = 0;
                        }
                        ?>
                        <a href="/systemloja/perfumes/list.php" class="position-relative d-flex align-items-center" title="Perfumes com estoque baixo" style="min-width:36px; text-decoration: none !important;">
                            <i class="fa fa-bell fa-lg <?= $qtdEstoqueBaixo > 0 ? 'text-danger' : 'text-dark' ?>"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill <?= $qtdEstoqueBaixo > 0 ? 'bg-danger' : 'bg-secondary' ?>" style="font-size:0.75rem;"> <?= $qtdEstoqueBaixo ?> </span>
                        </a>
                    </div>
                    <!-- Header user info -->
                    <div class="d-flex align-items-center justify-content-end" style="width:100%;padding-right:32px;">
                        <span class="mr-2 d-none d-lg-inline text-gray-800" style="padding-left:10px;padding-right:10px;font-size:1.25rem;font-weight:600;">
                            Olá, <?php
                                // Exibe o nome do usuário logado corretamente
                                if (isset($_SESSION['usuario_id'])) {
                                    $usuarioNome = $_SESSION['usuario_nome'] ?? '';
                                    echo htmlspecialchars($usuarioNome ? $usuarioNome : 'Usuário');
                                } else {
                                    echo 'Usuário';
                                }
                            ?>
                        </span>
                        <a href="/systemloja/usuarios/edit.php?id=<?= $_SESSION['usuario_id'] ?>" class="btn btn-link p-0 ml-2" title="Editar Perfil" style="padding-left:10px;padding-right:10px;color:#9752da;">
                            <i class="fa fa-user-edit" style="font-size:1.5rem;"></i>
                        </a>
                        <!-- <a href="/systemloja/usuarios/view.php?id=<?= $_SESSION['usuario_id'] ?>" class="btn btn-link p-0 ml-2" title="Ver Perfil" style="padding-left:10px;padding-right:10px;">
                            <i class="fa fa-user" style="font-size:1.5rem;"></i>
                        </a> -->
                    </div>
                </nav>
                <!-- End of Topbar -->
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col-12">
                            <div class="card shadow mb-4">
                                <div class="card-body">