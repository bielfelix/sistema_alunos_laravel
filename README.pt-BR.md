# API de Gestão de Alunos

Este repositório é uma reconstrução moderna de um projeto antigo de gestão de alunos que já existia no meu GitHub.

Criei esta versão em setembro de 2026 em vez de tentar fazer o código PHP legado parecer atual. O repositório antigo continua disponível como parte do histórico, enquanto este projeto mostra como eu estruturaria o backend hoje.

A aplicação é uma API em Laravel 13 com PostgreSQL, endpoints versionados, identificadores ULID, constraints no banco, regras transacionais de matrícula e testes de integração.

## Tecnologias

- PHP 8.3+
- Laravel 13
- PostgreSQL
- PHPUnit
- Laravel Pint
- GitHub Actions

## Integridade das matrículas

A criação de uma matrícula acontece dentro de uma transação.

A linha do curso é bloqueada antes da verificação de capacidade. Isso evita que duas requisições concorrentes ocupem simultaneamente a última vaga disponível.

O banco também mantém uma constraint única para o par aluno/curso.

## Execução local

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## Validação

```bash
composer lint
composer test
```

O GitHub Actions executa migrations e testes usando PostgreSQL.

## Arquitetura

Veja [docs/architecture.md](docs/architecture.md).

## Projeto histórico

A versão antiga continua disponível em:

https://github.com/bielfelix/sistema_alunos

Ela permanece como evidência histórica e não foi maquiada para parecer código moderno.

## English

[README.md](README.md)
