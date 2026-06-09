# Morf Backend

Community platform for artists. Weekly reference image sets, artwork uploads, likes, and moderation.

## Architecture

**Bounded Contexts** (`app/Contexts/`):
- `Identity` — anonymous registration, device tokens, roles
- `Content` — reference categories, images, weekly sets
- `Artworks` — upload, moderation status, soft deletes
- `Engagement` — likes
- `Moderation` — admin actions log
- `Static` — project info key/value store

Each context: `Domain/`, `Application/`, `Infrastructure/`, `Presentation/`.

**UUID v7** primary keys, no FOREIGN KEY constraints (logical relations via code).

## Tech Stack

- PHP 8.3 + Laravel 11
- PostgreSQL 16
- Redis (queues, cache)
- Filament v3 (admin panel)
- Swagger/OpenAPI 3.0 (l5-swagger)
- Docker + Docker Compose

## Quick Start

```bash
# 1. Start containers
docker compose up -d

# 2. Install dependencies
docker compose exec app composer install

# 3. Run migrations + seeders
docker compose exec app php artisan migrate:fresh --seed

# 4. Generate Swagger docs
docker compose exec app php artisan l5-swagger:generate

# 5. Start queue worker (new terminal)
docker compose exec app php artisan queue:work
```

## API

| Endpoint | Method | Auth | Description |
|----------|--------|------|-------------|
| `/api/auth/register` | POST | — | Anonymous registration |
| `/api/auth/recover` | POST | — | Account recovery |
| `/api/reference-sets` | GET | — | List published sets |
| `/api/reference-sets/{id}` | GET | — | Get set details |
| `/api/reference-images` | GET | — | List reference images |
| `/api/artworks` | GET | — | Approved artworks feed |
| `/api/artworks/{id}` | GET | — | Single artwork |
| `/api/artworks` | POST | Device token | Upload artwork |
| `/api/artworks/{id}` | DELETE | Device token | Soft delete |
| `/api/artworks/{id}/likes` | POST | Device token | Toggle like |
| `/api/admin/artworks/{id}/approve` | POST | Admin token | Approve |
| `/api/admin/artworks/{id}/reject` | POST | Admin token | Reject |
| `/api/admin/moderation-actions` | GET | Admin token | Moderation log |
| `/api/project-info/{key}` | GET | — | Static info |
| `/api/admin/project-info/{key}` | PUT | Admin token | Update info |

**Swagger UI**: `http://localhost:8080/api/documentation`

## Admin Panel

Filament admin at `/admin`. Resources: Users, Categories, Images, Sets, Artworks, Moderation, Project Info.

## Emulated CDN

No real CDN is configured. `PlaceholderImageGenerator` creates colored PNGs in `storage/app/public/` during seeding. Nginx serves them via `/storage/`. For production, switch the `s3` disk to a real CDN.

## Testing

```bash
# Run all tests
docker compose exec app php artisan test

# Run specific suite
docker compose exec app php artisan test --filter=AuthTest
```

## Events & Queues

| Event | Listeners |
|-------|-----------|
| `ArtworkSubmitted` | `LogArtworkSubmission` |
| `ArtworkModerated` | `LogModerationDecision` |
| `LikeToggled` | `LogLikeActivity` |
| `ReferenceSetPublished` | `ClearPublishedSetsCache` |

All listeners implement `ShouldQueue` and run on Redis.

## Project Structure

```
morf_back/
├── app/Contexts/           # Bounded contexts
│   ├── Artworks/
│   ├── Content/
│   ├── Engagement/
│   ├── Identity/
│   ├── Moderation/
│   ├── Static/
│   └── Presentation/     # Swagger spec
├── app/Filament/         # Admin resources
├── app/Http/Middleware/  # DeviceTokenAuth, AdminRole
├── app/Providers/
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── docker/               # PHP-FPM + Nginx Dockerfiles
├── routes/
│   └── api.php
└── tests/
    └── Feature/Api/
```

## License

MIT
