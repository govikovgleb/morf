# Morf — План реализации (декомпозиция)

> **Контекст:** На хост-машине нет PHP/Composer. Все команды Laravel/Composer выполняются только через терминал контейнера: `docker compose exec app <команда>`. Каждый следующий пункт зависит от предыдущего.

---

## Фаза 1. Инфраструктура Docker

Цель: получить работающие контейнеры (`app`, `web`, `db`, `redis`) до появления кода Laravel.

### 1.1 Подготовка директорий
- [ ] Создать директорию проекта `docker/`
- [ ] Создать поддиректории `docker/php/` и `docker/nginx/`
- [ ] Создать пустую директорию `morf_back/` (монтируется в `/var/www/html`)

### 1.2 Сборочный файл PHP (`docker/php/Dockerfile`)
- [ ] Выбрать базовый образ `php:8.3-fpm`
- [ ] Установить системные зависимостей: `apt-get update && apt-get install -y libpq-dev libzip-dev zip unzip git libpng-dev libjpeg-dev libfreetype6-dev`
- [ ] Установить PHP-расширения: `pdo_pgsql`, `pgsql`, `redis`, `zip`, `gd`, `exif`
- [ ] Скопировать скрипт установки Composer (`COPY --from=composer:latest /usr/bin/composer /usr/bin/composer`)
- [ ] Установить рабочую директорию `WORKDIR /var/www/html`
- [ ] Указать пользователя/группу `www-data`

### 1.3 Конфигурация Nginx (`docker/nginx/default.conf`)
- [ ] Настроить server-блок на порт `80`
- [ ] Указать `root /var/www/html/public`
- [ ] Настроить `index index.php`
- [ ] Настроить `location /` с `try_files $uri $uri/ /index.php?$query_string`
- [ ] Настроить `location ~ \.php$` с проксированием на `app:9000` через fastcgi

### 1.4 Docker Compose (`docker-compose.yml`)
- [ ] Описать сервис `app`: build из `docker/php/Dockerfile`, volume `./morf:/var/www/html`, зависимость от `db` и `redis`
- [ ] Описать сервис `web`: image `nginx:alpine`, volume `./morf:/var/www/html`, volume `./docker/nginx:/etc/nginx/conf.d`, порт `80:80`, зависимость от `app`
- [ ] Описать сервис `db`: image `postgres:16-alpine`, переменные окружения (`POSTGRES_DB`, `POSTGRES_USER`, `POSTGRES_PASSWORD`), volume для данных `db_data`
- [ ] Описать сервис `redis`: image `redis:7-alpine`
- [ ] Объявить volume `db_data`

### 1.5 Запуск и проверка инфраструктуры
- [ ] Создать `.dockerignore` (исключить `.git`, `*.md`, `node_modules` и т.д.)
- [ ] Выполнить `docker compose build --no-cache`
- [ ] Выполнить `docker compose up -d`
- [ ] Проверить статус всех сервисов: `docker compose ps`
- [ ] Проверить логи PHP: `docker compose logs app`
- [ ] Проверить логи Nginx: `docker compose logs web`
- [ ] Проверить доступность Postgres изнутри: `docker compose exec db pg_isready`

---

## Фаза 2. Установка Laravel

Цель: Laravel установлен внутри контейнера, файлы проявились в хостовой `morf_back/`.

### 2.1 Установка фреймворка
- [ ] Войти в контейнер: `docker compose exec app bash`
- [ ] Внутри контейнера перейти в `/var/www/html`
- [ ] Проверить, что директория пуста (volume смонтирован)
- [ ] Установить Laravel: `composer create-project laravel/laravel . --prefer-dist`
- [ ] Дождаться окончания загрузки зависимостей
- [ ] Выйти из контейнера и проверить, что файлы появились в `./morf_back/` на хосте
- [ ] Выполнить `docker compose exec app php artisan --version`

### 2.2 Права доступа
- [ ] Установить владельца `www-data:www-data` на `storage/` и `bootstrap/cache/` внутри контейнера
- [ ] Установить права `775` на `storage/` и `bootstrap/cache/`

---

## Фаза 3. Настройка окружения Laravel

Цель: приложение видит БД, Redis, сгенерирован `APP_KEY`.

### 3.1 Переменные окружения
- [x] Внутри контейнера скопировать `.env.example` в `.env`
- [x] Установить `APP_NAME=Morf`
- [x] Сгенерировать `APP_KEY`: `php artisan key:generate`
- [x] Настроить `DB_CONNECTION=pgsql`
- [x] Настроить `DB_HOST=db`
- [x] Настроить `DB_PORT=5432`
- [x] Настроить `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` (совпадают с `docker-compose.yml`)
- [x] Настроить `REDIS_HOST=redis`
- [x] Настроить `QUEUE_CONNECTION=redis`
- [x] Настроить `CACHE_STORE=redis`
- [x] Настроить `SESSION_DRIVER=redis` (опционально)

### 3.2 Дополнительные пакеты
- [x] Установить `laravel/sanctum`: `docker compose exec app composer require laravel/sanctum`
- [x] Опубликовать конфиг Sanctum: `docker compose exec app php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"`
- [x] Установить `darkaonline/l5-swagger`: `docker compose exec app composer require darkaonline/l5-swagger`

### 3.3 Проверка подключений
- [x] Выполнить `docker compose exec app php artisan tinker`
- [x] В tinker выполнить `DB::connection()->getPdo()` и убедиться, что подключение к PostgreSQL установлено
- [x] В tinker выполнить `Redis::ping()` и убедиться, что Redis отвечает

---

## Фаза 4. Фундамент приложения (структура + UUID)

Цель: созданы директории Bounded Contexts, базовые трейты для UUID.

### 4.1 Директории Bounded Contexts
- [x] Создать `app/Contexts/Identity/Domain/`
- [x] Создать `app/Contexts/Identity/Application/`
- [x] Создать `app/Contexts/Identity/Infrastructure/`
- [x] Создать `app/Contexts/Identity/Presentation/`
- [x] Повторить создание тех же 4 поддиректорий для контекстов:
  - `Content`
  - `Artworks`
  - `Engagement`
  - `Moderation`
  - `Static`

### 4.2 Базовый трейт UUID
- [x] Создать `app/Contexts/Shared/Traits/HasUuid.php`
- [x] В трейте: задать `$keyType = 'string'` и `$incrementing = false` через `initializeHasUuid()`
- [x] В трейте: в `bootHasUuid()` callback автоматически генерировать UUID v7 для `id`, если оно не задано
- [x] Убедиться, что Laravel нативно поддерживает `$table->uuid('id')` как primary key (доступно без doctrine/dbal)
- [x] Протестировать: `keyType = string`, `incrementing = false`, UUID v7 генерируется при `save()`

### 4.3 Базовая абстракция модели
- [x] По требованию проекта используется трейт `HasUuid` напрямую в моделях (без абстрактного `BaseModel`)
- [x] Все будущие модели будут использовать `use HasUuid;`

---

## Фаза 5. Слой Infrastructure (миграции)

Цель: все таблицы созданы в PostgreSQL, без физических FOREIGN KEY.

### 5.1 Identity
- [x] Создать миграцию `create_users_table` (стандартная удалена, создана новая)
- [x] Поля: `id` (uuid, PK), `public_nickname` (string), `role` (string, default 'user'), `auth_hash` (string), `recovery_code_hash` (string, nullable), `timestamps`
- [x] Убраны стандартные поля `name`, `email`, `password` и т.д.

### 5.2 Content
- [x] Создать миграцию `create_reference_categories_table` (uuid PK, name, slug, sort_order, timestamps)
- [x] Создать миграцию `create_reference_images_table` (uuid PK, category_id uuid, cdn_url, storage_path, width, height, file_size_bytes, mime_type, uploaded_by uuid, timestamps)
- [x] Создать миграцию `create_reference_sets_table` (uuid PK, title, week_start_date, is_published, published_at, created_by uuid, timestamps)
- [x] Создать миграцию `create_reference_set_items_table` (uuid PK, set_id uuid, reference_image_id uuid, timestamps)
- [x] **Проверено:** ни в одной миграции нет `$table->foreign(...)`

### 5.3 Artworks
- [x] Создать миграцию `create_artworks_table` (все поля из схемы, включая `deleted_at` для soft deletes)
- [x] Добавить `$table->softDeletes()` в миграцию

### 5.4 Engagement
- [x] Создать миграцию `create_likes_table` (uuid PK, artwork_id uuid, user_id uuid, timestamps)
- [x] Добавить уникальный составной индекс: `$table->unique(['artwork_id', 'user_id'], 'unique_user_like')`
- [x] Создать миграцию `create_comments_table` (uuid PK, artwork_id uuid, user_id uuid, text, deleted_at, timestamps)

### 5.5 Moderation
- [x] Создать миграцию `create_moderation_actions_table` (uuid PK, target_type, target_id uuid, action, actor_id uuid, reason, timestamps)

### 5.6 Static
- [x] Создать миграцию `create_project_info_table` (uuid PK, key, value, updated_at)

### 5.7 Полная проверка миграций
- [x] Выполнить `docker compose exec app php artisan migrate` — все 10 миграций выполнены
- [x] Выполнить `docker compose exec db psql -U morf_user -d morf -c "\dt"` — 11 таблиц (10 + migrations), все соответствуют схеме
- [x] Проверить структуру `users`, `artworks`, `likes` — UUID как PK, нет FOREIGN KEY, unique index на likes

---

## Фаза 6. Слой Domain (модели)

Цель: Eloquent-модели для всех таблиц, с UUID, casts, scopes, без foreign keys.

### 6.1 Identity
- [x] Создать модель `app/Contexts/Identity/Domain/User.php`
- [x] Подключить трейт `HasUuid`
- [x] Указать `$fillable`, `$casts`, `$hidden` (auth_hash, recovery_code_hash)
- [x] Создать scope `admin()` для фильтрации по роли

### 6.2 Content
- [x] Создать модель `ReferenceCategory` (fillable: name, slug, sort_order; casts: sort_order → integer)
- [x] Создать модель `ReferenceImage` (fillable: все поля; casts: width, height, file_size_bytes → integer)
- [x] Создать модель `ReferenceSet` (fillable: title, week_start_date, is_published, published_at, created_by; casts: is_published → boolean, week_start_date → date, published_at → datetime)
- [x] Создать модель `ReferenceSetItem` (fillable: set_id, reference_image_id)
- [x] Добавить логические связи:
  - `ReferenceImage -> belongsTo(ReferenceCategory::class, 'category_id')`
  - `ReferenceImage -> belongsTo(User::class, 'uploaded_by')`
  - `ReferenceSet -> belongsTo(User::class, 'created_by')`
  - `ReferenceSet -> hasMany(ReferenceSetItem::class, 'set_id')`
  - `ReferenceSetItem -> belongsTo(ReferenceSet::class, 'set_id')`
  - `ReferenceSetItem -> belongsTo(ReferenceImage::class, 'reference_image_id')`

### 6.3 Artworks
- [x] Создать модель `Artwork` (fillable: все поля)
- [x] Добавить `SoftDeletes` trait
- [x] Casts: `status` → string, `likes_count` → integer, `width`, `height`, `file_size_bytes` → integer, `moderated_at` → datetime
- [x] Добавить scopes: `approved()`, `rejected()`, `pending()`
- [x] Добавить логические связи:
  - `belongsTo(User::class, 'user_id')`
  - `belongsTo(ReferenceSet::class, 'reference_set_id')`

### 6.4 Engagement
- [x] Создать модель `Like` (fillable: artwork_id, user_id)
- [x] Создать модель `Comment` (fillable: artwork_id, user_id, text; trait `SoftDeletes`)
- [x] Добавить логические связи для Like и Comment (artwork, user)

### 6.5 Moderation
- [x] Создать модель `ModerationAction` (fillable: target_type, target_id, action, actor_id, reason)
- [x] Добавить связь `belongsTo(User::class, 'actor_id')`

### 6.6 Static
- [x] Создать модель `ProjectInfo` (fillable: key, value, custom `$table`)
- [x] Cast: `value` → array/json, `$timestamps = false`

### 6.7 Проверка моделей
- [x] Созданы тестовые записи: User, Artwork, ProjectInfo, Like
- [x] UUID генерируется как UUID v7 (проверено regex)
- [x] Default likes_count = 0 работает
- [x] JSON value в ProjectInfo сериализуется/десериализуется как array
- [x] Unique constraint на likes (artwork_id + user_id) работает — дубликат блокируется

---

## Фаза 7. Слой Application (бизнес-логика)

Цель: сервисы и DTO для каждого контекста. Рекомендуется идти строго по порядку контекстов.

### 7.1 Identity
- [x] Создать DTO `RegisterAnonymousUserDto` (nickname)
- [x] Создать сервис `RegisterAnonymousUserService`:
  - Принимает DTO, генерирует токен, хеш sha256, создает User, возвращает токен
- [x] Создать сервис `AuthenticateUserService`:
  - Ищет пользователя по sha256 токена, возвращает User или null
- [x] Создать сервис `GenerateRecoveryCodeService`:
  - Генерирует 12-символьный код, хеширует, сохраняет в recovery_code_hash
- [x] Создать сервис `RecoverAccountService`:
  - Принимает recovery code, ищет по хешу, генерирует новый токен, очищает код

### 7.2 Content
- [x] Создать сервис `CreateReferenceCategoryService`
- [x] Создать сервис `UploadReferenceImageService`:
  - Сохраняет в S3 (через Storage::disk('s3')), собирает метаданные, создает ReferenceImage
- [x] Создать сервис `CreateReferenceSetService`
- [x] Создать сервис `AddImageToSetService` (связь через ReferenceSetItem)
- [x] Создать сервис `PublishReferenceSetService`:
  - Устанавливает is_published = true, published_at = now()

### 7.3 Artworks
- [x] Создать DTO `UploadArtworkDto` (user_id, reference_set_id, file, caption)
- [x] Создать сервис `UploadArtworkService`:
  - Сохраняет в S3, создает Artwork, денормализует author_nickname
- [x] Создать сервис `ModerateArtworkService`:
  - Обновляет status, moderated_by, moderated_at
- [x] Создать сервис `SoftDeleteArtworkService`

### 7.4 Engagement
- [x] Создать сервис `ToggleLikeService`:
  - Toggle like (создать/удалить), обновляет денормализованный likes_count
- [x] Комментарии — заготовка, API не реализуется в MVP

### 7.5 Moderation
- [x] Создать сервис `LogModerationActionService`:
  - Создает запись ModerationAction

### 7.6 Static
- [x] Создать сервис `GetProjectInfoService` (по key)
- [x] Создать сервис `UpdateProjectInfoService` (по key, value JSON)

---

## Фаза 8. Слой Presentation (API-эндпоинты)

Цель: контроллеры, FormRequests, Resources, роуты.

### 8.1 Identity
- [x] Создать `AuthController`:
  - `POST /api/auth/register` — валидация nickname, возвращает токен
  - `POST /api/auth/recover` — восстановление по recovery code
- [x] Создать middleware `DeviceTokenAuth`:
  - Читает `X-Device-Token`, ищет пользователя по sha256 хешу
  - Кладет `auth_user_id` и `auth_user` в request attributes
- [x] Зарегистрировать middleware в `bootstrap/app.php`

### 8.2 Content (публичные)
- [x] Создать `ReferenceSetController`:
  - `GET /api/reference-sets` — список опубликованных наборов с пагинацией
  - `GET /api/reference-sets/{id}` — детали набора с картинками
- [x] Создать `ReferenceImageController`:
  - `GET /api/reference-images` — список с фильтром по category_id

### 8.3 Artworks
- [x] Создать `ArtworkController`:
  - `GET /api/artworks` — глобальная лента (фильтр reference_set_id, только approved)
  - `GET /api/artworks/{id}` — детали работы
  - `POST /api/artworks` — загрузка (middleware auth, validation: image max 20MB)
  - `DELETE /api/artworks/{id}` — soft delete (middleware auth)

### 8.4 Engagement
- [x] Создать `LikeController`:
  - `POST /api/artworks/{artwork_id}/likes` — toggle like (middleware auth)

### 8.5 Moderation (админ)
- [x] Создать middleware `AdminRole` (проверка role === 'admin')
- [x] Создать `ModerationController`:
  - `POST /api/admin/artworks/{id}/approve` — approve + логирование
  - `POST /api/admin/artworks/{id}/reject` — reject + reason + логирование
  - `GET /api/admin/moderation-actions` — лог действий (пагинация)

### 8.6 Static
- [x] Создать `ProjectInfoController`:
  - `GET /api/project-info/{key}` — публичный
  - `PUT /api/admin/project-info/{key}` — админ (обновление value JSON)

### 8.7 Роуты
- [x] Создан `routes/api.php`:
  - Публичные эндпоинты без middleware
  - Аутентифицированные через `device.auth`
  - Админ через `device.auth` + `admin`
- [x] Проверены роуты: 17 маршрутов (включая Swagger)

---

## Фаза 9. Админ-панель

Цель: готовая админка для управления контентом.

### 9.1 Выбор и установка
- [ ] Определить выбор: Laravel Nova (платное) vs Filament (бесплатное, open-source)
- [ ] Установить выбранный пакет через Composer внутри контейнера
- [ ] Опубликовать ресурсы и настройки
- [ ] Создать пользователя-админа через tinker / seeder

### 9.2 Ресурсы админки
- [ ] Создать ресурс `UserResource` (Filament/Nova) — просмотр, поиск по nickname
- [ ] Создать ресурс `ReferenceCategoryResource` — CRUD
- [ ] Создать ресурс `ReferenceImageResource` — CRUD + preview cdn_url
- [ ] Создать ресурс `ReferenceSetResource` — CRUD + добавление картинок в набор
- [ ] Создать ресурс `ArtworkResource` — просмотр, модерация (кнопки approve/reject), фильтр по статусу
- [ ] Создать ресурс `ModerationActionResource` — только просмотр (аудит)
- [ ] Создать ресурс `ProjectInfoResource` — редактирование key-value

---

## Фаза 10. Документирование API (Swagger)

Цель: интерактивная документация API доступна по `/api/documentation`.

### 10.1 Установка и настройка
- [ ] Убедиться, что `l5-swagger` установлен (Фаза 3.2)
- [ ] Опубликовать конфиг: `docker compose exec app php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"`
- [ ] Создать `app/Contexts/Presentation/Swagger/OpenApiSpec.php`
- [ ] Добавить в него `@OA\Info(title="Morf API", version="1.0.0")`
- [ ] Добавить `@OA\Server(url="/api")`
- [ ] Добавить `@OA\SecurityScheme` для `X-Device-Token`

### 10.2 Аннотации в контроллерах
- [ ] Аннотировать `AuthController` (register, recover)
- [ ] Аннотировать `ReferenceSetController` (list, show)
- [ ] Аннотировать `ArtworkController` (list, show, store, destroy)
- [ ] Аннотировать `LikeController` (toggle)
- [ ] Аннотировать `ModerationController` (approve, reject)
- [ ] Аннотировать `ProjectInfoController` (show, update)

### 10.3 Генерация и проверка
- [ ] Выполнить `docker compose exec app php artisan l5-swagger:generate`
- [ ] Проверить, что файл `storage/api-docs/api-docs.json` создался
- [ ] Открыть в браузере `http://localhost/api/documentation`
- [ ] Проверить отображение эндпоинтов и возможность отправки тестовых запросов

---

## Фаза 11. Событийная консистентность и очереди

Цель: тяжелые операции и межконтекстные обновления работают через очереди.

### 11.1 Настройка драйвера
- [ ] Убедиться, что `QUEUE_CONNECTION=redis` и `REDIS_HOST=redis`
- [ ] Убедиться, что worker запускается (вручную или через Supervisor в контейнере)
- [ ] Для ручного запуска: `docker compose exec app php artisan queue:work` (можно добавить в README как команду)

### 11.2 События и слушатели
- [ ] Создать Event `ArtworkLiked`
- [ ] Создать Listener `UpdateLikesCount`:
  - При `added=true` — `increment likes_count`
  - При `added=false` — `decrement likes_count`
- [ ] Создать Event `ArtworkApproved`
- [ ] Создать Event `ArtworkRejected`
- [ ] Создать Listener `LogArtworkModeration` (создает `ModerationAction`)
- [ ] Создать Event `ReferenceSetPublished`
- [ ] Создать Listener `NotifySetPublished` (опционально, можно просто залогировать)
- [ ] Зарегистрировать события и слушатели в `app/Providers/EventServiceProvider.php` (или через `Event::listen` в `AppServiceProvider`)

### 11.3 Job'ы (опционально)
- [ ] Создать Job `SyncImageToCdnJob` (если CDN требует асинхронной синхронизации)
- [ ] Создать Job `DeleteImageFromCdnJob` (при soft delete или reject удалять файл из CDN)

---

## Фаза 12. Тестирование и финальная проверка

Цель: приложение работает целиком, данные создаются, API отвечает.

### 12.1 Базовые сидеры
- [ ] Создать `DatabaseSeeder`:
  - Создать 1 администратора
  - Создать 2-3 обычных пользователя
  - Создать 3-4 категории референсов
  - Создать 5-10 референсов (с placeholder URL для cdn_url)
  - Создать 1-2 набора референсов, опубликовать 1 из них
  - Создать 2-3 работы
  - Создать 5-10 лайков
- [ ] Выполнить `docker compose exec app php artisan db:seed`

### 12.2 Ручное end-to-end тестирование (через Swagger или curl)
- [ ] Зарегистрировать нового пользователя → получить токен
- [ ] Получить список наборов референсов → убедиться, что данные есть
- [ ] Загрузить работу (с реальным файлом или mock) → убедиться, что работа появилась в `GET /api/artworks`
- [ ] Поставить лайк → проверить, что `likes_count` увеличился
- [ ] Убрать лайк → проверить, что `likes_count` уменьшился
- [ ] Зайти в админку → одобрить/отклонить работу
- [ ] Проверить `GET /api/admin/moderation-actions` — запись о действии есть
- [ ] Проверить `GET /api/project-info/welcome_text` → возвращает value

### 12.3 Проверка инфраструктуры
- [ ] Перезапустить контейнеры: `docker compose down && docker compose up -d`
- [ ] Убедиться, что данные в PostgreSQL сохранились (volume работает)
- [ ] Проверить логи на отсутствие критических ошибок

---

## Фаза 13. Документирование проекта

Цель: README позволяет любому разработчику развернуть проект за 10 минут.

### 13.1 README.md
- [ ] Описание проекта (цель, стек)
- [ ] Архитектурные решения:
  - Почему UUID
  - Почему логические связи без FOREIGN KEY
  - Почему денормализация
  - Bounded Contexts и их границы
  - Событийная консистентность
- [ ] Инструкция по запуску:
  - `docker compose up -d --build`
  - `docker compose exec app composer install`
  - `docker compose exec app php artisan migrate`
  - `docker compose exec app php artisan db:seed`
  - `docker compose exec app php artisan l5-swagger:generate`
  - Ссылка на Swagger UI
  - Ссылка на админку
- [ ] Примеры API-запросов (curl) или ссылка на Swagger
- [ ] Описание переменных окружения (`.env`)

### 13.2 Комментарии в коде
- [ ] Добавить PHPDoc к сервисам (что делает, какие события диспатчит)
- [ ] Добавить комментарии к денормализованным полям (`author_nickname`, `likes_count`) — почему они есть
- [ ] Добавить комментарии к логическим связям (`category_id` без FK)
- [ ] Добавить `@see` ссылки на схему `morf.dbml` в заголовках моделей

---

**Статус:** Декомпозиция завершена. Готов к выполнению по порядку.
