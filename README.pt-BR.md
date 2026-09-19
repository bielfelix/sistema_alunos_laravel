# API de Gestão de Alunos

Este repositório é uma reconstrução moderna de um projeto antigo de gestão de alunos que já existia no meu GitHub.

Criei esta versão em setembro de 2026 em vez de tentar fazer o código PHP legado parecer atual. O repositório antigo continua disponível como parte do histórico, enquanto este projeto mostra como eu estruturaria o backend hoje.

A aplicação é uma API em Laravel 13 com PostgreSQL, endpoints versionados, identificadores ULID, constraints no banco, regras transacionais de matrícula e testes de integração.

## Tecnologias

- PHP 8.3+
- Laravel 13
- PostgreSQL 17
- PHPUnit
- Laravel Pint
- GitHub Actions

## Domínio

A API gerencia alunos, cursos e matrículas.

Alunos e cursos usam soft delete. O cancelamento de uma matrícula preserva o registro em vez de removê-lo fisicamente.

## Integridade das matrículas

A criação de uma matrícula acontece dentro de uma transação.

A linha do curso é bloqueada antes da verificação de capacidade. Isso evita que duas requisições concorrentes ocupem simultaneamente a última vaga disponível.

O banco também mantém uma constraint única para o par aluno/curso.

As regras de matrícula ficam em um pequeno serviço de aplicação. O controller permanece responsável apenas pelo fluxo HTTP.

## Execução local

Requisitos:

- PHP 8.3 ou superior
- Composer 2
- Docker com Docker Compose, ou PostgreSQL 17 instalado localmente

Suba o PostgreSQL:

```bash
docker compose up -d
```

Prepare e inicie a aplicação:

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## Testando a API

O arquivo [requests.http](requests.http) contém requisições prontas para criação de aluno, curso, matrícula, consulta e cancelamento.

A especificação OpenAPI está em [docs/openapi.yaml](docs/openapi.yaml).

## Validação

```bash
composer lint
composer test
```

O GitHub Actions instala as dependências, valida o padrão de código, executa as migrations e roda a suíte de testes com PostgreSQL 17.

## Arquitetura

Veja [docs/architecture.md](docs/architecture.md).

## Projeto histórico

A versão antiga continua disponível em:

https://github.com/bielfelix/sistema_alunos

Ela permanece como evidência histórica e não foi maquiada para parecer código moderno.

## English

[README.md](README.md)
