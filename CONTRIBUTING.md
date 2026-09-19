# Contributing

This repository is maintained as a compact engineering reference and portfolio project.

Contributions should preserve the project's main goals: clear domain boundaries, explicit data integrity, predictable API behavior and code that remains easy to inspect during technical review.

## Development setup

Requirements:

- PHP 8.3+
- Composer 2
- Docker with Docker Compose, or PostgreSQL 17 installed locally

Start PostgreSQL:

```bash
docker compose up -d
```

Prepare the application:

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
```

Run validation:

```bash
composer lint
composer test
```

## Change expectations

Changes should:

- keep controllers focused on HTTP concerns;
- keep domain rules outside transport code when they become non-trivial;
- preserve database constraints for integrity rules that belong in the data layer;
- add or update feature tests for behavior changes;
- update OpenAPI documentation when an endpoint contract changes;
- document architectural trade-offs when a decision materially affects concurrency, integrity, security or operability.

## Pull requests

A pull request should describe:

- the problem being solved;
- the design choice;
- the validation performed;
- API or schema impact;
- security or concurrency implications;
- documentation updated.

Large refactors should be separated from unrelated feature work.

## Commit style

Use concise conventional-style commit messages such as:

- `feat:`
- `fix:`
- `refactor:`
- `test:`
- `docs:`
- `ci:`
- `chore:`

## Architecture decisions

When a change introduces a meaningful trade-off, add or update an ADR under `docs/adr/`.
