<?php
require_once __DIR__ . '/config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        header('Location: index.php');
        exit;
    } else {
        $erro = "Usuário ou senha inválidos!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - System Loja</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/systemloja/assets/style/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: #333 !important;
            min-height: 100vh;
        }
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 32px rgba(0,0,0,0.18);
            padding: 2.5rem 2.2rem 2.2rem 2.2rem;
            max-width: 370px;
            width: 100%;
            margin: 0 auto;
        }
        .login-logo {
            display: block;
            margin: 0 auto 18px auto;
            width: 100%;
            height: auto;
            object-fit: contain;
        }
        .login-title {
            font-weight: 700;
            color: #22223b;
            font-size: 1.35rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .form-control {
            background: #fff !important;
            border-radius: 8px !important;
            border: 1px solid #d1d5db !important;
            font-size: 1rem;
            color: #22223b;
        }
        .form-control:focus {
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 2px #6366f133;
        }
        .btn-primary {
            background: #6366f1 !important;
            border: none !important;
            border-radius: 8px !important;
            font-weight: 600;
            font-size: 1.08rem;
            padding: 0.65rem 0;
            margin-top: 0.5rem;
        }
        .alert-danger {
            border-radius: 8px;
            font-size: 0.98rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <img src="/systemloja/assets/images/logo2.png" alt="Logo" class="login-logo">
            <div class="login-title">Acesso ao Sistema</div>
            <?php if (isset($erro)): ?>
                <div class="alert alert-danger text-center"><?= $erro ?></div>
            <?php endif; ?>
            <form method="post" class="user mt-3">
                <div class="form-group mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" required autofocus>
                </div>
                <div class="form-group mb-3">
                    <input type="password" name="senha" class="form-control" placeholder="Senha" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Entrar</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/js/sb-admin-2.min.js"></script>
</body>
</html>
