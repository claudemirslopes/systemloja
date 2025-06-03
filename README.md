# System Loja

Sistema de gestão de vendas de perfumes, clientes, usuários e formas de pagamento. Desenvolvido em PHP com Bootstrap, focado em controle de estoque, cadastro de vendas, relatórios e dashboard informativo.

## Funcionalidades

- Cadastro e edição de perfumes, clientes, usuários e formas de pagamento
- Controle de estoque com alerta de baixo estoque (ícone de sino com badge dinâmico)
- Registro e edição de vendas com cálculo automático de totais e lucro
- Dashboard com cards informativos (totais, top perfumes, últimas vendas)
- Relatórios de vendas por data
- Máscaras de moeda e campos otimizados para uso brasileiro
- Interface responsiva e moderna
- **Recuperação de senha por e-mail** (com token, expiração e redefinição segura)
- **Envio real de e-mail via SMTP** (compatível com Titan, MailerSend, SendGrid, etc)
- **Notificações visuais** e atalhos rápidos no dashboard
- **Favicon e logo** em todas as páginas
- **Documentação e .gitignore** prontos para GitHub

## Instalação

1. Clone este repositório:

   ```sh
   git clone https://github.com/claudemirslopes/systemloja.git
   ```

   [Repositório no GitHub](https://github.com/claudemirslopes/systemloja.git)

2. Importe o banco de dados usando o arquivo `systemloja.sql` (não incluso no repositório, solicite ao desenvolvedor).

3. Configure o arquivo `config/config.php` com os dados do seu banco MySQL.

4. Configure o envio de e-mail SMTP em `usuarios/recuperar_solicitar.php`:

   - Preencha com os dados do seu provedor (host, porta, usuário, senha, criptografia).
   - Exemplo para Titan:

     ```php
     $mail->Host = 'smtp.titan.email';
     $mail->Port = 465;
     $mail->Username = 'no-reply@seudominio.com.br';
     $mail->Password = 'SENHA_DO_EMAIL';
     $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
     ```

   - **Importante:** Para evitar bloqueio de envio, ajuste o SPF do domínio no DNS:
     - Adicione (ou edite) o registro TXT:

       ```txt
       v=spf1 include:spf.titan.email ~all
       ```

     - Aguarde a propagação DNS.

5. Certifique-se de que o Apache e o MySQL estão rodando (XAMPP recomendado).

6. Acesse `http://localhost/systemloja` no navegador.

## Requisitos

- PHP 7.4+
- MySQL/MariaDB
- Apache/Nginx
- Composer (opcional, para dependências)
- Conta de e-mail SMTP válida (Titan, Locaweb, Gmail, etc)

## Estrutura

- `clientes/`, `perfumes/`, `vendas/`, `usuarios/`, `formas_pagamento/`: CRUDs
- `assets/`: CSS, JS, imagens, favicon e logos
- `templates/`: Header e footer
- `config/`: Configuração do banco

## Recuperação de Senha

- Acesse a tela de login e clique em "Esqueceu a senha?"
- Informe seu e-mail cadastrado
- Você receberá um link para redefinir a senha (válido por 1 hora)
- O link leva à tela de redefinição, com validação de token e senha
- O envio de e-mail é feito via SMTP real, com log de erros em `usuarios/log_email.txt`

## Licença

Este projeto está licenciado sob a Licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

---
Desenvolvido por Open Beta CTI.
