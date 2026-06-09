# Архитектура проекта Morf

> Этот документ описывает принятые архитектурные решения и правила организации кода. Если вы не помните, куда положить новый класс — загляните сюда.

---

## 1. Bounded Contexts (разделение по контекстам)

Код разбит на изолированные модули — **контексты**. Каждый контекст отвечает за одну бизнес-область и не знает о деталях других.

| Контекст | Ответственность |
|---|---|
| **Identity** | Пользователи, анонимная регистрация, токены устройств, recovery codes |
| **Content** | Категории референсов, изображения-референсы, наборы (`reference_sets`) |
| **Artworks** | Работы пользователей, модерация, soft delete |
| **Engagement** | Лайки, счетчики, комментарии (заготовка) |
| **Moderation** | Аудит действий администраторов |
| **Static** | Статический контент: описание проекта, ссылки на донаты и соцсети |

### Почему это важно
- Контексты можно выносить в отдельные сервисы (например, переписать `Engagement` на Go), не трогая остальной монолит.
- Изменения в одной области не ломают другую.

---

## 2. Четыре слоя внутри каждого контекста

Каждый контекст содержит 4 слоя. Зависимости строго однонаправлены: верхний слой знает о нижнем, но не наоборот.

```
Presentation  →  Application  →  Domain  ←  Infrastructure
     ↑                                              ↑
  HTTP/API                                     Eloquent/DB
```

### 2.1 Domain (ядро)

- **Что здесь:** модели (Eloquent, но без привязки к фреймворку), Value Objects, Domain Events, интерфейсы репозиториев.
- **Правило:** не зависит от Laravel, HTTP, БД. Можно скопировать в другой проект.
- **Пример:** `Artwork` как модель с методами `approve()`, `reject()`, `like()`. Интерфейс `ArtworkRepositoryInterface`.

### 2.2 Application (оркестрация)

- **Что здесь:** Use Cases / Services, DTO, команды.
- **Правило:** содержит бизнес-сценарии («загрузить работу», «поставить лайк»), но не знает, как именно данные сохраняются в БД.
- **Пример:** `UploadArtworkService` принимает `UploadArtworkDto`, создает модель `Artwork`, сохраняет через интерфейс репозитория, диспатчит событие.

### 2.3 Infrastructure (детали реализации)

- **Что здесь:** Eloquent-репозитории, миграции, конфигурации, работа с внешними API (CDN, S3).
- **Правило:** реализует интерфейсы, объявленные в Domain. Здесь «грязные» детали: SQL, Redis, файловая система.
- **Пример:** `EloquentArtworkRepository implements ArtworkRepositoryInterface`.

### 2.4 Presentation (интерфейс с внешним миром)

- **Что здесь:** контроллеры, Form Requests, API Resources, middleware.
- **Правило:** только HTTP-специфика: валидация входных данных, формирование ответа, авторизация. Не содержит бизнес-логики.
- **Пример:** `ArtworkController` вызывает `UploadArtworkService`, возвращает `ArtworkResource`.

---

## 3. Как общаются слои

### 3.1 Presentation → Application (DTO + UseCase)

Контроллер не передает `Request` напрямую в сервис. Он создает **DTO** — плоский объект с данными:

```php
$dto = new UploadArtworkDto(
    userId: $request->user_id,
    referenceSetId: $request->reference_set_id,
    file: $request->file('image'),
    caption: $request->caption,
);

$result = $uploadService->execute($dto);
```

**Почему:** сервис не должен зависеть от HTTP-запроса. Завтра вместо API может быть CLI-команда — DTO останется тем же.

### 3.2 Application → Domain (модели + события)

Service создает модели Domain и вызывает их методы:

```php
$artwork = Artwork::create(/* ... */);
$artwork->publish(); // внутри может породить Domain Event
```

Domain возвращает события, которые Service решает, что делать (синхронно или асинхронно).

### 3.3 Application → Infrastructure (интерфейс репозитория)

Service не вызывает `Artwork::create()` напрямую (чтобы не зависеть от Eloquent). Он использует интерфейс:

```php
interface ArtworkRepositoryInterface
{
    public function save(Artwork $artwork): void;
    public function findById(string $id): ?Artwork;
}
```

Реализация (`EloquentArtworkRepository`) живет в `Infrastructure/` и подключается через DI-контейнер Laravel.

**Почему:** можно заменить Postgres на MongoDB или внешний API, не меняя ни строчки в Application.

### 3.4 Domain → Application/World (Domain Events)

Когда внутри модели происходит значимое событие, она порождает **Domain Event**:

```php
class ArtworkLiked
{
    public function __construct(
        public string $artworkId,
        public string $userId,
        public bool $added, // true = лайк поставлен, false = убран
    ) {}
}
```

Application диспатчит событие:

```php
event(new ArtworkLiked($artworkId, $userId, true));
```

Другие контексты или слушатели обрабатывают его асинхронно (через очереди):
- `UpdateLikesCount` → обновляет денормализованный счетчик `likes_count`.
- `LogModerationAction` → пишет аудит, если лайк связан с модерацией.

**Почему:** контексты не обращаются напрямую друг к другу. Они общаются через события — это и есть «событийная консистентность» вместо распределенных транзакций.

---

## 4. Зависимости (стрелки)

```
Presentation
    ↓ (DTO)
Application
    ↓ (модели, интерфейсы репозиториев)
Domain
    ↑ (реализация интерфейсов)
Infrastructure
```

**Запрещено:**
- Domain зависит от Infrastructure (модель не должна знать о Eloquent/Postgres).
- Application зависит от Presentation (сервис не должен знать о HTTP).
- Контекст A напрямую импортирует классы Контекста B (только через события или общие интерфейсы в `Shared/`).

**Разрешено:**
- Presentation импортирует Application.
- Application импортирует Domain.
- Infrastructure импортирует Domain (для реализации интерфейсов).

---

## 5. Практический пример: загрузка работы

1. **Presentation:** `ArtworkController` получает `POST /api/artworks`, валидирует `UploadArtworkRequest`, создает `UploadArtworkDto`.
2. **Application:** `UploadArtworkService` принимает DTO, генерирует UUID, создает модель `Artwork` (Domain), сохраняет через `ArtworkRepositoryInterface` (Infrastructure), диспатчит `ArtworkCreated`.
3. **Domain:** модель `Artwork` проверяет бизнес-правила (например, «пользователь может загружать не более 5 работ в день» — если такое правило появится).
4. **Infrastructure:** `EloquentArtworkRepository` сохраняет модель в `artworks` (PostgreSQL), `S3ImageStorage` загружает файл в CDN.
5. **Событие:** `ArtworkCreated` обрабатывается слушателем `SendNotificationToAdmin` (Moderation), который ставит работу в очередь на модерацию.

---

## 6. Где что лежит (шпаргалка)

| Сущность | Путь |
|---|---|
| Модель | `app/Contexts/Artworks/Domain/Artwork.php` |
| Интерфейс репозитория | `app/Contexts/Artworks/Domain/ArtworkRepositoryInterface.php` |
| Реализация репозитория | `app/Contexts/Artworks/Infrastructure/Repositories/EloquentArtworkRepository.php` |
| DTO | `app/Contexts/Artworks/Application/Dto/UploadArtworkDto.php` |
| Service (UseCase) | `app/Contexts/Artworks/Application/Services/UploadArtworkService.php` |
| Domain Event | `app/Contexts/Artworks/Domain/Events/ArtworkCreated.php` |
| Controller | `app/Contexts/Artworks/Presentation/Controllers/ArtworkController.php` |
| Resource | `app/Contexts/Artworks/Presentation/Resources/ArtworkResource.php` |
| Form Request | `app/Contexts/Artworks/Presentation/Requests/UploadArtworkRequest.php` |
| Миграция | `app/Contexts/Artworks/Infrastructure/Migrations/...` |

---

## 7. UUID, логические связи и денормализация

- **UUID:** первичные ключи — строки UUID v7. Нет автоинкремента. UUID v7 содержит timestamp (в отличие от v4), что даёт монотонно возрастающие значения → лучше индексируются в PostgreSQL и позволяют сортировать по порядку создания.
- **Логические связи:** в миграциях нет `$table->foreign(...)`. Модели используют `belongsTo(..., 'category_id')`, но на уровне БД нет FOREIGN KEY.
- **Денормализация:** поля `author_nickname` и `likes_count` в `artworks` дублируют данные из других таблиц. Это намеренно: контекст `Artworks` остается автономным и не ходит в `Identity`/`Engagement` при каждом запросе ленты.

---

*Последнее обновление: 2026-06-04*
