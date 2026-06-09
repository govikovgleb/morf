# AGENTS.md — Morf Repository

This document helps AI coding agents work effectively in the Morf codebase.

## Project Overview

Morf is a Dockerized Laravel 13 (PHP 8.3) backend for an anonymous artist community platform. It uses a **Bounded Context / Clean Architecture** pattern with UUID v7 primary keys and no database foreign key constraints.

## Build / Lint / Test Commands

All backend commands run inside the `app` container. From the repository root:

```bash
# Start services
docker compose up -d

# Install dependencies
docker compose exec app composer install

# Run the full test suite
docker compose exec app php artisan test
# or
docker compose exec app vendor/bin/phpunit

# Run a single test / class / method
docker compose exec app php artisan test --filter=AuthTest
docker compose exec app vendor/bin/phpunit --filter=AuthTest

# Lint / fix PHP style (Laravel Pint)
docker compose exec app vendor/bin/pint
docker compose exec app vendor/bin/pint --test   # dry-run

# Run dev server + queue + logs + Vite (concurrently)
docker compose exec app composer dev

# Laravel CLI helpers
docker compose exec app php artisan migrate:fresh --seed
docker compose exec app php artisan l5-swagger:generate
docker compose exec app php artisan queue:work
```

## Architecture

Code lives under `morf_back/`. The `app/Contexts/` directory contains **Bounded Contexts** (e.g., `Artworks`, `Identity`, `Engagement`, `Content`, `Moderation`, `Static`).

Each context has four layers with strict dependency direction:

```
Presentation  →  Application  →  Domain  ←  Infrastructure
```

- **Domain** — Models, Value Objects, Events, Repository interfaces. Must not depend on Laravel or DB. Use `declare(strict_types=1);`.
- **Application** — Services (Use Cases), DTOs, Listeners. Orchestrates business logic.
- **Infrastructure** — Eloquent repositories, migrations, external APIs. Implements Domain interfaces.
- **Presentation** — Controllers, Form Requests, API Resources, middleware. No business logic here.

## Code Style Guidelines

### Formatting
- **EditorConfig:** 4 spaces, `space` indent, `lf`, `utf-8`, `insert_final_newline=true`. YAML uses 2 spaces (except Docker Compose: 4).
- **PHP:** Follow Laravel Pint default preset. No custom config exists.

### Imports & Namespaces
- **PSR-4 autoloading:** `App\` → `app/`, `Database\Factories\` → `database/factories/`, `Tests\` → `tests/`.
- Use explicit `use` statements; no string class references in routes.
- **Never import classes from another Bounded Context directly.** Contexts communicate only via Domain Events or shared interfaces in `App\Contexts\Shared\`.

### Naming Conventions
| Concept | Pattern |
|---|---|
| Domain Models | PascalCase noun: `Artwork`, `User` |
| DTOs | PascalCase + `Dto` suffix: `UploadArtworkDto` |
| Services / Use Cases | PascalCase + `Service` suffix: `UploadArtworkService` |
| Controllers | PascalCase + `Controller` suffix: `ArtworkController` |
| Domain Events | PascalCase past tense: `ArtworkSubmitted`, `LikeToggled` |
| Listeners | PascalCase descriptive: `UpdateLikesCount` |
| Database tables | snake_case plural: `artworks`, `reference_sets` |
| Migrations | Laravel timestamp prefix: `2026_06_04_095626_create_users_table.php` |
| Primary keys | UUID v7 strings (no auto-increment) via `HasUuid` trait |

### Types & Error Handling
- Use PHP 8.3 typed properties and return types everywhere.
- Domain layer files must declare `strict_types=1`.
- DTOs are plain data objects passed from Presentation to Application (never pass `Request` into a Service).
- Services receive DTOs, create Domain models, and persist via repository interfaces.
- Domain Events decouple contexts; dispatch with `event(new ArtworkCreated(...))`.
- No physical `FOREIGN KEY` constraints in migrations — only logical Eloquent relations.
- Intentional denormalization is allowed for autonomy (e.g., `author_nickname`, `likes_count` in `artworks`).

## Testing

- **Framework:** PHPUnit 12.5
- **Config:** `morf_back/phpunit.xml`
- **Suites:** `Unit` (`tests/Unit`) and `Feature` (`tests/Feature`)
- **Environment:** SQLite in-memory (`:memory:`), `QUEUE_CONNECTION=sync`, `CACHE_STORE=array`
- Base class: `Tests\TestCase`

### Single-test examples
```bash
docker compose exec app php artisan test --filter=AuthTest
docker compose exec app vendor/bin/phpunit --filter=AuthTest
docker compose exec app vendor/bin/phpunit tests/Feature/Api/AuthTest.php
```

## Existing Rules Files

- **No `.cursorrules` or `.cursor/rules/` found.**
- **No `.github/copilot-instructions.md` found.**

## Quick Reference

| Task | Command |
|---|---|
| Start everything | `docker compose up -d` |
| Run all tests | `docker compose exec app php artisan test` |
| Run one test | `docker compose exec app php artisan test --filter=AuthTest` |
| Lint PHP | `docker compose exec app vendor/bin/pint` |
| Fresh DB + seed | `docker compose exec app php artisan migrate:fresh --seed` |
| Generate Swagger | `docker compose exec app php artisan l5-swagger:generate` |

When creating new code, always place it in the correct Bounded Context layer (`Domain`, `Application`, `Infrastructure`, `Presentation`) and follow the dependency arrows above.
