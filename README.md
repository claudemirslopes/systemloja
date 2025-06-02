# System Loja

Sistema de gestão de vendas de perfumes, clientes, usuários e formas de pagamento. Desenvolvido em PHP com Bootstrap, focado em controle de estoque, cadastro de vendas, relatórios e dashboard informativo.

## Funcionalidades

- Cadastro e edição de perfumes, clientes, usuários e formas de pagamento
- Controle de estoque com alerta de baixo estoque
- Registro e edição de vendas com cálculo automático de totais e lucro
- Dashboard com cards informativos (totais, top perfumes, últimas vendas)
- Relatórios de vendas por data
- Máscaras de moeda e campos otimizados para uso brasileiro
- Interface responsiva e moderna

## Instalação

1. Clone este repositório:

   ```sh
   git clone https://github.com/claudemirslopes/systemloja.git
   ```

   [Repositório no GitHub](https://github.com/claudemirslopes/systemloja.git)
2. Importe o banco de dados usando o arquivo `systemloja.sql` (não incluso no repositório, solicite ao desenvolvedor).
3. Configure o arquivo `config/config.php` com os dados do seu banco MySQL.
4. Certifique-se de que o Apache e o MySQL estão rodando (XAMPP recomendado).
5. Acesse `http://localhost/systemloja` no navegador.

## Requisitos

- PHP 7.4+
- MySQL/MariaDB
- Apache/Nginx
- Composer (opcional, para dependências)

## Estrutura

- `clientes/`, `perfumes/`, `vendas/`, `usuarios/`, `formas_pagamento/`: CRUDs
- `assets/`: CSS, JS e imagens
- `templates/`: Header e footer
- `config/`: Configuração do banco

## Licença

Este projeto está licenciado sob a Licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

---
Desenvolvido por Open Beta CTI.
