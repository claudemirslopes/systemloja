<?php
require_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json');

$email = $_POST['email'] ?? '';
if (!$email) {
    echo json_encode(['success' => false, 'msg' => 'E-mail não informado.']);
    exit;
}
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
$stmt->execute([$email]);
$usuario = $stmt->fetch();
if (!$usuario) {
    // Não revela se o e-mail existe
    echo json_encode(['success' => true, 'msg' => 'Se o e-mail estiver cadastrado, você receberá instruções para redefinir sua senha.']);
    exit;
}
$token = bin2hex(random_bytes(32));
$expira = date('Y-m-d H:i:s', strtotime('+1 hour'));
$stmt = $pdo->prepare("INSERT INTO recuperacao_senha (usuario_id, token, expirado_em) VALUES (?, ?, ?)");
$stmt->execute([$usuario['id'], $token, $expira]);
$link = 'http://' . $_SERVER['HTTP_HOST'] . '/systemloja/usuarios/recuperar.php?token=' . $token;

// Função para carregar variáveis do .env
function env($key, $default = null) {
    static $env = null;
    if ($env === null) {
        $env = [];
        $envPath = __DIR__ . '/../.env';
        if (file_exists($envPath)) {
            foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                if (strpos(trim($line), '#') === 0) continue;
                [$k, $v] = array_map('trim', explode('=', $line, 2) + [1 => '']);
                $env[$k] = $v;
            }
        }
    }
    return $env[$key] ?? $default;
}

// Função para enviar e-mail via SMTP (MailerSend)
function enviarEmailSMTP($to, $subject, $bodyHtml, $bodyText = '') {
    require_once __DIR__ . '/../vendor/autoload.php';
    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = env('MAIL_HOST');
        $mail->SMTPAuth = true;
        $mail->Username = env('MAIL_USERNAME');
        $mail->Password = env('MAIL_PASSWORD');
        $mail->SMTPSecure = strtolower(env('MAIL_ENCRYPTION', 'ssl')) === 'ssl' ? PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS : PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = (int)env('MAIL_PORT', 465);
        $mail->setFrom(env('MAIL_FROM'), env('MAIL_FROM_NAME', 'System Loja'));
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $bodyHtml;
        $mail->AltBody = $bodyText ?: strip_tags($bodyHtml);
        $mail->CharSet = 'UTF-8';
        $mail->SMTPDebug = 0;
        $log = "\n==== ENVIO ====\nPara: $to\nAssunto: $subject\nBody: $bodyHtml\nAltBody: $mail->AltBody\n";
        file_put_contents(__DIR__.'/log_email.txt', $log, FILE_APPEND);
        $mail->send();
        file_put_contents(__DIR__.'/log_email.txt', "\n[OK] E-mail enviado com sucesso\n", FILE_APPEND);
        return true;
    } catch (Exception $e) {
        file_put_contents(__DIR__.'/log_email.txt', "\n[ERRO] ".$mail->ErrorInfo."\nException: ".$e->getMessage()."\n", FILE_APPEND);
        return false;
    }
}

$assunto = 'Recuperação de senha - System Loja';
$corpo = '<p>Olá,</p><p>Recebemos uma solicitação para redefinir sua senha. Clique no link abaixo para criar uma nova senha:</p><p><a href="'.$link.'">Redefinir senha</a></p><p>Se não foi você, ignore este e-mail.</p>';
$enviado = enviarEmailSMTP($email, $assunto, $corpo);
if ($enviado) {
    echo json_encode(['success' => true, 'msg' => 'Se o e-mail estiver cadastrado, você receberá instruções para redefinir sua senha.']);
} else {
    echo json_encode(['success' => false, 'msg' => 'Erro ao enviar e-mail. Tente novamente mais tarde.']);
}
exit;
?>
