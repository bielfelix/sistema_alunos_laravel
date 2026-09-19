# Student Management API

A modern rebuild of an older student-management project in this GitHub account.

I created this version in September 2026 instead of trying to make the original legacy PHP code look modern. The old repository remains available as historical context, while this repository shows how I would structure the backend today.

The application is an API-first Laravel 13 service using PostgreSQL, versioned endpoints, ULID identifiers, database constraints, transactional enrollment rules and integration tests.

## Why this repository exists

The original `sistema_alunos` project reflects an earlier stage of my PHP work.

This repository is intentionally new. It is not rewritten history and it is not presented as old production code. It is a current implementation of the same domain used to demonstrate modern Laravel and backend practices.

## Stack

- PHP 8.3+
- Laravel 13
- PostgreSQL 17
- PHPUnit
- Laravel Pint
- GitHub Actions

Laravel 13 is used deliberately so the rebuild represents the current framework generation rather than freezing an old stack.

## Domain

The API manages students, courses and enrollments.

Students and courses use soft deletion. Enrollment cancellation preserves the record instead of physically deleting it.

## API

```text
GET    /api/v1/students
POST   /api/v1/students
GET    /api/v1/students/{student}
PUT    /api/v1/students/{student}
PATCH  /api/v1/students/{student}
DELETE /api/v1/students/{student}

GET    /api/v1/courses
POST   /api/v1/courses
GET    /api/v1/courses/{course}
PUT    /api/v1/courses/{course}
PATCH  /api/v1/courses/{course}
DELETE /api/v1/courses/{course}

POST   /api/v1/enrollments
GET    /api/v1/enrollments/{enrollment}
DELETE /api/v1/enrollments/{enrollment}
```

For ready-to-run requests, see [requests.http](requests.http).

The OpenAPI contract is available at [docs/openapi.yaml](docs/openapi.yaml).

## Enrollment consistency

Enrollment creation runs inside a database transaction.

The course row is locked before capacity is checked. This prevents two concurrent requests from both observing the final available seat.

The database also enforces a unique student/course pair. Application validation improves the API response while the database keeps an independent integrity boundary.

## Running locally

Requirements:

- PHP 8.3 or newer
- Composer 2
- Docker with Docker Compose, or a local PostgreSQL 17 instance

Start PostgreSQL:

```bash
docker compose up -d
```

Prepare and run the application:

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## Validation

```bash
composer lint
composer test
```

GitHub Actions installs dependencies, checks formatting, runs migrations and executes the test suite against PostgreSQL 17.

## Architecture

See [docs/architecture.md](docs/architecture.md).

## Historical project

The earlier implementation remains at:

https://github.com/bielfelix/sistema_alunos

That repository is intentionally kept as historical code rather than being silently rewritten to look newer than it is.

## Portuguese

[README.pt-BR.md](README.pt-BR.md)
