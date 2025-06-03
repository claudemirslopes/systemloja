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

// Função para enviar e-mail via SMTP (MailerSend)
function enviarEmailSMTP($to, $subject, $bodyHtml, $bodyText = '') {
    require_once __DIR__ . '/../vendor/autoload.php';
    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        // CONFIGURE AQUI OS DADOS DO SEU PROVEDOR DE E-MAIL
        $mail->Host = 'smtp.titan.email';
        $mail->SMTPAuth = true;
        $mail->Username = 'no-reply@rogerimports.com.br';
        $mail->Password = 'Eliane19@1';
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS; // SSL
        $mail->Port = 465; // Porta SSL
        $mail->setFrom('no-reply@rogerimports.com.br', 'Roger Imports');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $bodyHtml;
        $mail->AltBody = $bodyText ?: strip_tags($bodyHtml);
        $mail->CharSet = 'UTF-8';
        $mail->SMTPDebug = 0; // Desative para AJAX funcionar corretamente
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
