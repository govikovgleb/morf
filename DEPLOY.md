# Деплой Morf на dockerhosting.ru

## Структура проекта

```
morf/
├── docker-compose.yml          # Продакшен конфиг
├── docker/nginx/default.conf    # Nginx конфиг
├── docker/php/Dockerfile        # PHP-FPM образ
├── .env.example                 # Шаблон переменных окружения
├── morf_back/                   # Laravel backend
├── morf_front/                  # Next.js frontend
└── morf_back/storage/real_art/ # Исходники артов (для сидеров)
```

---

## 1. Подготовка к деплою (локально)

### 1.1. Скопируй .env

```bash
cp .env.example .env
```

### 1.2. Сгенерируй APP_KEY

```bash
docker compose exec app php artisan key:generate --show
```

Скопируй вывод в `.env` в поле `APP_KEY`.

### 1.3. Установи пароль БД

В `.env` замени:

```env
DB_PASSWORD=change_this_password
```

на свой сильный пароль.

### 1.4. Обнови APP_URL

```env
APP_URL=https://your-domain.ru
```

---

## 2. GitHub

### 2.1. Создай репозиторий на GitHub

1. Иди на https://github.com/new
2. Назови репозиторий (например `morf`)
3. **НЕ** ставь галочку "Initialize this repository with a README"
4. Нажми **Create repository**

### 2.2. Запушь код

```bash
# Добавь удалённый репозиторий
git remote add origin https://github.com/ТВОЙ_НИК/morf.git

# Запушь
git branch -M main
git push -u origin main
```

---

## 3. Деплой на dockerhosting.ru

### 3.1. Что понадобится

- Аккаунт на [dockerhosting.ru](https://dockerhosting.ru)
- SSH-доступ (ключ или пароль)
- Домен (можно поддомен от dockerhosting)

### 3.2. Подключись по SSH

```bash
ssh root@IP_СЕРВЕРА
```

### 3.3. Клонируй репозиторий

```bash
cd /opt
git clone https://github.com/ТВОЙ_НИК/morf.git
cd morf
```

### 3.4. Создай .env

```bash
cp .env.example .env
nano .env
```

Заполни обязательные поля:

```env
APP_NAME=Morf
APP_ENV=production
APP_KEY=base64:xxx...xxx
APP_DEBUG=false
APP_URL=https://your-domain.ru

DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=morf
DB_USERNAME=morf_user
DB_PASSWORD=ТВОЙ_СИЛЬНЫЙ_ПАРОЛЬ

REDIS_HOST=redis
REDIS_PORT=6379

QUEUE_CONNECTION=redis
CACHE_STORE=redis
SESSION_DRIVER=redis

API_HOST=web
API_PORT=80
```

Сохрани: `Ctrl+O`, `Enter`, `Ctrl+X`.

### 3.5. Запусти контейнеры

```bash
docker compose up -d --build
```

Жди пока соберётся frontend (Next.js standalone билд).

### 3.6. Проверь что всё поднялось

```bash
docker compose ps
```

Должно быть 5 контейнеров: `morf_app`, `morf_web`, `morf_db`, `morf_redis`, `morf_frontend`.

### 3.7. Накати миграции и сидеры

```bash
# Миграции
docker compose exec app php artisan migrate --force

# Сидеры (создадут пользователей, недели, референсы и арты)
docker compose exec app php artisan db:seed --force
```

### 3.8. Создай storage symlink

```bash
docker compose exec app php artisan storage:link
```

### 3.9. Проверь порты

```bash
# Backend API (должен отвечать JSON)
curl http://localhost:8080/api/reference-sets

# Frontend (должен отвечать HTML)
curl http://localhost:3000
```

---

## 4. Настройка домена и SSL

### 4.1. На dockerhosting.ru

В панели управления:
1. Привяжи домен к серверу
2. Настрой reverse proxy:
   - `your-domain.ru` → `localhost:3000` (frontend)
   - `api.your-domain.ru` → `localhost:8080` (backend, опционально)

Или проще: настрой домен на порт 3000, а API будет проксироваться через Next.js rewrites.

### 4.2. SSL (Let's Encrypt)

На dockerhosting обычно есть авто-SSL в панели. Включи его.

Если деплоишь руками:

```bash
docker compose exec web apk add certbot
# ... настрой certbot с nginx
```

---

## 5. Обновление (новый деплой)

```bash
cd /opt/morf
git pull origin main

# Пересобрать frontend (если менялся код фронта)
docker compose up -d --build frontend

# Пересобрать backend (если менялись зависимости)
docker compose up -d --build app

# Миграции
docker compose exec app php artisan migrate --force

# Очистить кэши
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:clear
```

---

## 6. Важные замечания

### Безопасность
- **НЕ** коммить `.env` в git
- **НЕ** коммить `morf_back/.env` в git
- Пароль БД должен быть сложным
- APP_DEBUG всегда `false` в продакшене

### Объём
- Репозиторий содержит `storage/real_art/` (картинки для сидеров)
- Вес репозитория ~50-100 МБ из-за картинок

### Порты
- `8080` — Backend API (Nginx + PHP-FPM)
- `3000` — Frontend (Next.js standalone)
- `5432` — PostgreSQL (только внутри Docker network)
- `6379` — Redis (только внутри Docker network)

---

## 7. Чеклист перед первым деплоем

- [ ] `.env` создан и заполнен
- [ ] `APP_KEY` сгенерирован
- [ ] `DB_PASSWORD` изменён со стандартного
- [ ] `APP_URL` указывает на твой домен
- [ ] `.env` добавлен в `.gitignore` (уже есть)
- [ ] `vendor/` и `node_modules/` не в git (уже есть)
- [ ] Репозиторий запушен на GitHub
- [ ] dockerhosting сервер готов
- [ ] Домен привязан к серверу

---

## 8. Проблемы и решения

### Frontend не билдится

```bash
# Посмотри логи
docker compose logs frontend

# Обычно причина: не установлены зависимости
cd morf_front && npm ci
```

### Backend 500 ошибка

```bash
# Логи
docker compose logs app

# Очистить всё и пересоздать
docker compose down -v
docker compose up -d --build
```

### Картинки не грузятся

```bash
# Проверь symlink
docker compose exec app ls -la public/storage

# Пересоздай
docker compose exec app php artisan storage:link
```

---

## 9. Быстрая команда для полного сброса БД

**ВНИМАНИЕ:** Удалит все данные!

```bash
docker compose exec db psql -U morf_user -d morf -c "
TRUNCATE TABLE likes, moderation_actions, artworks, 
reference_set_items, reference_sets, reference_images, 
reference_categories, project_info 
RESTART IDENTITY CASCADE;
"

# Заново сидеры
docker compose exec app php artisan db:seed --force
```
