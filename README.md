# 💧 Sistema de Controle de Consumo de Água

Sistema web para substituir o processo manual (anotações em papel + WhatsApp) usado por uma associação comunitária para gerenciar o abastecimento de água de um bairro: cadastro de consumidores, registro de leituras mensais com cálculo automático de consumo, geração de faturas e configuração das tarifas.

Desenvolvido como avaliação prática da disciplina **Programação Web I** — IFCE Campus Boa Viagem — ADS 2026.1.

- William Axel Da Silva Ribeiro

## Tecnologias usadas

- PHP 8.2+
- Laravel 11
- MySQL 8
- Blade (templates nativos do Laravel, sem frontend framework)
- Autenticação padrão do Laravel (`Auth::attempt`, sem Breeze/Jetstream)

## Funcionalidades

- **Login** simples com usuário/senha (tabela `users` do Laravel).
- **Cadastro de consumidores**: nome, endereço, número do medidor (único) e telefone. Listagem com busca por nome/medidor, cadastro e edição.
- **Registro de leitura mensal**: seleciona o consumidor, informa mês/ano e a leitura atual do medidor. O sistema calcula automaticamente o consumo (`leitura atual − leitura anterior`), valida que a leitura atual não seja menor que a anterior e impede mais de uma leitura por consumidor no mesmo mês.
- **Fatura**: gerada automaticamente após o registro da leitura, com o valor calculado pela regra de cobrança. Listagem das faturas do mês com nome, consumo e valor. O gestor pode marcar a fatura como paga (ou voltar para pendente).
- **Configuração de tarifas**: tela para o gestor alterar a taxa fixa, o limite de consumo sem excedente e o valor por 1.000 L excedentes. Todo cálculo de fatura usa sempre o valor mais atual cadastrado aqui.
- **Bônus — Link para WhatsApp**: cada fatura tem um botão que abre o WhatsApp (`wa.me`) com uma mensagem pré-formatada contendo nome, medidor, leituras, consumo e valor.

## Regra de cobrança

| Consumo mensal | Cobrança |
|---|---|
| Até 10.000 L (10 m³) | Taxa fixa — padrão R$ 25,00 (configurável) |
| Acima de 10.000 L | Taxa fixa + R$ 2,00 por cada 1.000 L excedentes |

Exemplo: consumo de 15.000 L → R$ 25,00 (fixa) + R$ 10,00 (5.000 L × R$ 2,00) = **R$ 35,00**.

## Estrutura do banco de dados

- `users` — autenticação do gestor.
- `consumidores` — nome, endereço, número do medidor (único), telefone, leitura inicial do medidor e status (ativo/inativo).
- `configuracoes_taxa` — taxa fixa, limite de litros sem excedente e valor do excedente por 1.000 L. Sempre existe um único registro ativo.
- `leituras` — leitura mensal de cada consumidor (mês, ano, leitura anterior, leitura atual e consumo calculado). Restrição única por `consumidor_id + mes_referencia + ano_referencia`.
- `faturas` — vinculada a uma leitura, com o valor total calculado e o status (`pendente`/`pago`).

## Como instalar e rodar o projeto localmente

### Pré-requisitos

- PHP >= 8.2 com as extensões `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`
- Composer
- MySQL 8 (ou MariaDB compatível) rodando localmente
- Git

### Passo a passo

```bash
# 1. Clone o repositório
git clone <URL_DO_SEU_REPOSITORIO>
cd controle-agua

# 2. Instale as dependências PHP
composer install

# 3. Copie o arquivo de variáveis de ambiente
cp .env.example .env

# 4. Gere a chave da aplicação
php artisan key:generate
```

### Configurando o `.env`

Abra o `.env` e ajuste os dados do seu MySQL local:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=controle_agua
DB_USERNAME=root
DB_PASSWORD=sua_senha_aqui
```

Crie o banco de dados (se ele ainda não existir):

```bash
mysql -u root -p -e "CREATE DATABASE controle_agua CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### Rodando as migrations e o seeder

```bash
php artisan migrate --seed
```

Isso vai criar todas as tabelas e popular o banco com:
- o usuário gestor padrão para login (veja credenciais abaixo);
- a configuração de tarifa inicial (taxa fixa R$ 25,00, limite 10.000 L, excedente R$ 2,00/1.000 L).

### Subindo o servidor local

```bash
php artisan serve
```

Acesse **http://localhost:8000** no navegador.

## Usuário e senha padrão para login

| Campo | Valor |
|---|---|
| E-mail | `gestor@associacao.com.br` |
| Senha | `senha123` |

## Estrutura de pastas relevante

```
app/Http/Controllers/   → AuthController, DashboardController, ConsumidorController,
                           LeituraController, FaturaController, ConfiguracaoTaxaController
app/Models/              → User, Consumidor, ConfiguracaoTaxa, Leitura, Fatura
database/migrations/     → create_users_table, create_consumidores_table,
                           create_configuracoes_taxa_table, create_leituras_table,
                           create_faturas_table
database/seeders/        → DatabaseSeeder (usuário gestor + tarifa padrão)
resources/views/          → layouts, auth, dashboard, consumidores, leituras,
                           faturas, configuracao
routes/web.php           → todas as rotas da aplicação
```

## Observações

- O cálculo do consumo e da fatura é feito inteiramente no backend (`LeituraController` + `ConfiguracaoTaxa::calcularFatura`); o preview que aparece na tela de registro de leitura é só uma simulação em JavaScript para dar feedback visual imediato ao usuário, mas o valor salvo é sempre recalculado e validado no servidor.
- Como o sistema usa um único papel de usuário (gestor), o mesmo login é usado tanto para o cadastro/configuração quanto para o registro de leituras (que na vida real seria feito pelo leiturista).


