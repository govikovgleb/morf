# Todo Frontend — Morf (MVP)

Последовательный план реализации фронтенда для Morf. Каждый этап выполняется полностью перед переходом к следующему. Задачи выполняются внутри Docker-контейнера `frontend`.

---

## Как я проверяю работу (технические ограничения)

**Я не могу:**
- Открывать браузер или видеть GUI
- Запускать Chrome DevTools
- Проверять pixel-perfect визуально
- Проверять hover/touch эффекты вручную

**Я могу и делаю:**
- `curl` — проверяю, что страница отдает HTML 200 OK, проверяю наличие классов/текста
- `docker compose logs` — проверяю, что сервер запускается без ошибок компиляции
- `pnpm build` — проверяю, что production сборка проходит
- `pnpm tsc --noEmit` — проверяю TypeScript типы (strict mode)
- `grep` — проверяю, что в HTML/CSS есть нужные классы, цвета, шрифты
- `cat` / `ls` — проверяю структуру файлов и содержимое кода

**Ваши ручные проверки:**
- Все проверки, помеченные комментарием `# Вы проверяете визуально:` — выполняете в своем браузере
- Pixel-perfect, hover-эффекты, touch targets, responsive — проверяете вы через DevTools

---

## Phase 0: Подготовка и инфраструктура

### 0.1. Проверка бэкенда
**Что проверяю:** API бэкенда доступен и возвращает корректные данные. Swagger UI работает по `/api/documentation`.
**Как проверяю:** `curl` к `/api/reference-sets`, `/api/artworks`, `/api/auth/register` (тестовый запрос).
**Результат:** Убеждаюсь, что базовые эндпоинты отвечают 200 и структура JSON совпадает с FRONTEND.md.

### 0.2. Создание Docker-инфраструктуры фронтенда
**Что делаю:**
- Создаю `Dockerfile` для Next.js (Node.js 20+ alpine, pnpm)
- Создаю `docker-compose.yml` (или дописываю в существующий) сервис `frontend` с:
  - build context: `./morf_front`
  - сетью `morf_network` (driver: bridge)
  - портами `3000:3000`
  - volumes: `./morf_front:/app` и `/app/node_modules` (hot reload)
  - командой `pnpm dev`
- Создаю `.dockerignore`

**Как проверяю:**
```bash
docker compose up -d --build
docker compose exec frontend pnpm --version
```

### 0.3. Инициализация Next.js проекта
**Что делаю:** Внутри контейнера создаю Next.js 15 проект с TypeScript, Tailwind CSS, App Router. Настраиваю `next.config.js` (output: standalone для Docker, если нужно). Устанавливаю pnpm-зависимости.

**Структура директорий:**
```
morf_front/
  src/
    app/
    components/
    lib/
    hooks/
    types/
    stores/
  public/
  .env.local
```

**Как проверяю:**
```bash
# Проверяю, что сервер запустился без ошибок
docker compose logs frontend | tail -20
# Проверяю, что страница отдает HTML 200 OK
curl -s http://localhost:3000 | head -50
# Вы проверяете визуально: открываете http://localhost:3000 — видите стандартную Next.js страницу
```

---

## Phase 1: Базовая тема и дизайн-система

### 1.1. Настройка Tailwind и глобальных стилей
**Что делаю:**
- Конфигурирую `tailwind.config.ts`:
  - Кастомные цвета (background, surface, accent, text, borders)
  - Шрифты (Cinzel для headings, Inter для body) — пока через Google Fonts
  - Breakpoints (sm, md, lg, xl)
- Создаю `globals.css` с CSS-переменными готической темы и базовыми сбросами
- Настраиваю `next/font/google` (Cinzel + Inter) — пока Google Fonts. Подготовливаю архитектуру под `next/font/local` для будущего перехода на кастомные шрифты из Figma

**Результат:** Страница отображается с тёмным фоном `#0a0a0f`, готическими шрифтами и базовой типографикой.

**Как проверяю:**
```bash
# Проверяю, что сервер запускается без ошибок
docker compose logs frontend | grep -i error
# Проверяю, что HTML содержит нужные CSS-переменные и классы
curl -s http://localhost:3000 | grep -E "(bg-\[\#0a0a0f\]|font-cinzel|font-inter)"
# Вы проверяете визуально: открываете http://localhost:3000 — видите тёмный фон и шрифты
```

### 1.2. Создание системы компонентов через cva + Radix UI
**Что делаю:**
- Устанавливаю `cva`, `tailwind-merge`, `clsx`
- Устанавливаю Radix UI primitives (`@radix-ui/react-dialog`, `@radix-ui/react-tabs`, `@radix-ui/react-dropdown-menu`, `@radix-ui/react-accordion`, `@radix-ui/react-slot`)
- Создаю базовые компоненты:
  - `Button` (variants: primary, secondary, ghost; sizes: sm, md, lg)
  - `Input` (variants: default, error, disabled)
  - `Card` (minimal radius 2px или острые углы)
  - `Dialog` (на основе Radix Dialog — модалка с overlay, focus trap, close on Escape)
  - `DropdownMenu` (для селектора недели)
- Все компоненты полностью кастомизированы под цвета и стили готики (нет neutral-gray-стилей shadcn)

**Как проверяю:**
```bash
# Проверяю, что /ui-test отдает HTML 200 OK
curl -s http://localhost:3000/ui-test | grep -E "(button|input|dialog|dropdown)" | head -10
# Проверяю, что в HTML есть наши Tailwind-классы (цвета, скругления)
curl -s http://localhost:3000/ui-test | grep -E "(bg-red-900|rounded-2px|border-2a2a3a)"
# Вы проверяете визуально: открываете http://localhost:3000/ui-test — видите кнопки, инпуты, модалку, дропдаун в готических стилях
```

---

## Phase 2: Layout и навигация

### 2.1. Создание Root Layout
**Что делаю:**
- `src/app/layout.tsx` — корневой layout:
  - Подключение шрифтов (Cinzel + Inter)
  - Глобальный тёмный фон
  - Декоративный SVG-силуэт готической архитектуры на фоне (opacity 0.15)
  - Провайдеры: TanStack Query, Zustand (хотя Zustand — standalone, не нужен провайдер)
  - Header

### 2.2. Header компонент
**Что делаю:**
- `Header`:
  - Лево: "MORF" (Cinzel, uppercase, tracking-[0.3em])
  - Право: кнопка профиля / "Войти"
  - Адаптив: на мобильных — только лого + иконка профиля
  - Все интерактивные элементы ≥ 44×44px

**Как проверяю:**
```bash
# Проверяю, что / отдает HTML 200 OK и содержит "MORF"
curl -s http://localhost:3000 | grep "MORF"
# Проверяю, что в HTML есть responsive-классы (md:, lg:)
curl -s http://localhost:3000 | grep -E "(md:|lg:|hidden.*md)" | head -5
# Вы проверяете визуально:
# - Открываете http://localhost:3000 — видите хедер с логотипом "MORF"
# - Меняете размер окна (mobile <768px) — хедер становится компактным
# - Проверяете в DevTools, что touch targets ≥ 44x44px
```

---

## Phase 3: Auth (регистрация и восстановление)

### 3.1. Zustand store для авторизации
**Что делаю:**
- Создаю `useAuthStore`:
  - `token: string | null`
  - `isAuthenticated: boolean`
  - `actions: login(token), logout(), setToken()`
- При инициализации читаем `auth_token` из `localStorage` (только на клиенте, в `useEffect` или с `typeof window !== 'undefined'`)

### 3.2. API-клиент
**Что делаю:**
- Создаю базовый `api` client (fetch-обёртка):
  - Базовый URL: `/api`
  - Интерцептор: добавляет `X-Device-Token` header если токен есть
  - Обработка 401 (редирект на auth модалку)

### 3.3. Auth модалка
**Что делаю:**
- Компонент `AuthModal` (Radix Dialog):
  - Вкладки (Radix Tabs): "Регистрация" / "Восстановить доступ"
  - **Регистрация:**
    - React Hook Form + Zod (nickname: min 3, max 50)
    - `POST /auth/register`
    - После успеха: показываю модалку с recovery_code (заголовок "СОХРАНИТЕ КОД ВОССТАНОВЛЕНИЯ", крупный monospace, кнопка "Копировать", чекбокс "Я сохранил код", кнопка "Продолжить" — disabled пока чекбокс не checked)
    - После "Продолжить": сохраняю token в store + localStorage, закрываю модалку
  - **Восстановление:**
    - React Hook Form + Zod (recovery_code: ровно 12 символов)
    - `POST /auth/recover`
    - После успеха: сохраняю новый token, закрываю модалку
  - Все формы с валидацией и ошибками

**Как проверяю:**
```bash
# Проверяю, что AuthModal рендерится в HTML (проверяю через API или клик)
# Проверяю, что POST /api/auth/register возвращает { token } (curl)
curl -X POST http://localhost:8080/api/auth/register -H "Content-Type: application/json" -d '{"nickname": "test"}'
# Проверяю, что POST /api/auth/recover возвращает { token } (curl)
curl -X POST http://localhost:8080/api/auth/recover -H "Content-Type: application/json" -d '{"recovery_code": "ABC123DEF456"}'
# Вы проверяете визуально:
# - Кликаете "Войти" в хедере — открывается модалка с двумя табами
# - Регистрация: вводите nickname → отправляется запрос → видите модалку с recovery_code
# - Recovery: вводите код → получаете новый token
# - Проверяете в DevTools: в localStorage есть auth_token, в запросах к /api/artworks есть X-Device-Token
```

---

## Phase 4: Главная страница ("/")

### 4.1. Получение данных о неделях (reference sets)
**Что делаю:**
- TanStack Query: `useReferenceSets` (`GET /api/reference-sets`)
- Отображаю селектор недели (DropdownMenu или кастомный select):
  - Выбор текущей недели (последняя опубликованная)
  - Готическая рамка вокруг селектора

### 4.2. Отображение референсов
**Что делаю:**
- TanStack Query: `useReferenceSet(id)` (`GET /api/reference-sets/{id}`)
- Карточки референсов: изображение + описание
- Desktop: ряд полноразмерных карточек
- Tablet: сетка 2×2 или горизонтальный скролл
- Mobile: вертикальный стек
- Кнопка "СКАЧАТЬ РЕФЕРЕНСЫ" (если есть endpoint, иначе пока disabled или скрыта)

### 4.3. Кнопка "УЧАСТВОВАТЬ" и модалка загрузки
**Что делаю:**
- Кнопка "УЧАСТВОВАТЬ" (красная рамка, glow эффект)
- Если не авторизован — открывает AuthModal
- Если авторизован — открывает UploadModal

### 4.4. Upload Modal (модалка загрузки)
**Что делаю:**
- Компонент `UploadModal` (Radix Dialog):
  - Dropzone (react-dropzone или кастомный):
    - Пунктирная рамка, glow при drag
    - Поддержка **одного изображения** (MVP — бэкенд пока принимает только single image)
    - Превью загруженного изображения с возможностью удалить/заменить
  - Поле `caption` (опционально, React Hook Form, max 1000 символов)
  - Кнопка "ОТПРАВИТЬ"
    - Multipart form-data: `reference_set_id`, `image` (single file), `caption`
    - `POST /api/artworks`
    - После успеха: toast "Работа отправлена на модерация", закрыть модалку, инвалидировать кэш ленты

**Как проверяю:**
```bash
# Проверяю, что GET /api/reference-sets возвращает данные (curl)
curl -s http://localhost:8080/api/reference-sets | head -20
# Проверяю, что страница / рендерится без ошибок (curl + grep)
curl -s http://localhost:3000 | grep -E "(reference|неделя|week)" | head -5
# Проверяю, что POST /api/artworks возвращает 201 (curl с multipart)
curl -X POST http://localhost:8080/api/artworks -H "X-Device-Token: test" -F "reference_set_id=test" -F "image=@test.png"
# Вы проверяете визуально:
# - Открываете /, выбираете неделю — видите референсы
# - Кликаете "УЧАСТВОВАТЬ" → открывается модалка загрузки
# - Перетаскиваете одну картинку в dropzone — видите превью
# - Заполняете caption, отправляете — видите toast, работа появляется в ленте
```

---

## Phase 5: Лента работ ("/feed")

### 5.1. TanStack Query + Infinite Scroll
**Что делаю:**
- `useInfiniteArtworks` — `useInfiniteQuery`:
  - `GET /api/artworks?reference_set_id={id}&page={page}`
  - `getNextPageParam` из Laravel paginate response (`next_page_url` или `current_page + 1`)
- Intersection Observer (клиентский хук) для infinite scroll:
  - Когда скроллим до конца — подгружаем следующую страницу
  - Loader-спиннер (акцентного цвета)
  - Обработка "больше нет работ"

### 5.2. Masonry Grid
**Что делаю:**
- Компонент `MasonryGrid`:
  - Desktop (≥1280px): 4 колонки
  - Tablet (768–1279px): 3 колонки (2 в портретной)
  - Mobile (<768px): 1-2 колонки
- Карточка `ArtworkCard`:
  - Превью изображение (Next/Image, `sizes` для responsive, lazy loading)
  - Автор (nickname)
  - ♥ + счётчик лайков
  - Все элементы ≥ 44×44px для тача

### 5.3. Фильтр по неделе
**Что делаю:**
- DropdownMenu (или селектор) для выбора reference_set_id
- При смене фильтра — сброс infinite scroll, новый запрос с `reference_set_id`

### 5.4. Модалка просмотра работы
**Что делаю:**
- Компонент `ArtworkModal` (Radix Dialog):
  - Полноразмерное изображение (tap-to-zoom на планшете)
  - Автор, caption
  - Кнопка лайка (см. Phase 5.5)
  - Кнопка закрыть
  - Desktop: centered, max-width 800px
  - Tablet: 90vw
  - Mobile: fullscreen или 95vw

### 5.5. Лайки (toggle)
**Что делаю:**
- `useToggleLike` mutation (TanStack Query):
  - `POST /api/artworks/{artwork_id}/likes` (с `X-Device-Token`)
  - Optimistic update: сразу меняю UI (liked + likes_count), потом синхронизация с сервером
  - Если не авторизован — открывается AuthModal

**Как проверяю:**
```bash
# Проверяю, что GET /api/artworks возвращает пагинированные данные (curl)
curl -s http://localhost:8080/api/artworks | head -30
# Проверяю, что /feed рендерит HTML с карточками (curl + grep)
curl -s http://localhost:3000/feed | grep -E "(artwork|card|likes)" | head -10
# Проверяю, что POST /api/artworks/{id}/likes возвращает { liked, likes_count } (curl)
curl -X POST http://localhost:8080/api/artworks/test-id/likes -H "X-Device-Token: test"
# Вы проверяете визуально:
# - Открываете /feed, видите masonry сетку работ
# - Скроллите вниз — подгружаются новые работы (смотрите Network tab)
# - Кликаете на работу — открывается модалка с полноразмерным изображением
# - Кликаете ♥ — счётчик меняется, запрос уходит на /api/artworks/{id}/likes
# - Перезагружаете страницу — лайк сохранился (если авторизован)
# - На мобильном разрешении — 1-2 колонки, всё удобно для пальца
```

---

## Phase 6: Защита роутов и UX

### 6.1. Middleware / защита страниц
**Что делаю:**
- `useRequireAuth` hook:
  - Если не авторизован — показывает AuthModal при попытке:
    - Открыть UploadModal
    - Поставить лайк
  - Пока не делаю: `/profile` страницу (Post-MVP)

### 6.2. Error handling и loading states
**Что делаю:**
- Skeleton screens для карточек (тёмные плейсхолдеры в цветах готики)
- Error boundaries (Next.js error.tsx)
- Toast notifications (react-hot-toast или sonner) для успешных/ошибочных действий:
  - "Работа отправлена на модерация"
  - "Ошибка загрузки" (сети)
  - "Неверный код восстановления"

### 6.3. Responsive и touch-UX
**Что делаю:**
- Проверяю все интерактивные элементы ≥ 44×44px
- Заменяю hover-эффекты на active-стейты (тач-устройства)
- Проверяю горизонтальный скролл только там, где нужен (галереи)
- Шрифты не меньше 16px
- Tap-to-zoom на изображениях в модалке (CSS или библиотека)

**Как проверяю:**
```bash
# Проверяю, что в CSS нет hover-only правил для критичных элементов
grep -r "hover:" src/components/ | grep -v "md:" | grep -v "lg:" | head -10
# Проверяю, что в HTML есть responsive-классы (min-width, touch targets)
curl -s http://localhost:3000/feed | grep -E "(min-w-44|min-h-44|text-base)" | head -5
# Вы проверяете визуально:
# - Открываете DevTools → Toggle Device Toolbar → iPad (768px) — проверяете touch targets
# - Проверяете, что нет hover-only эффектов (карточки не зависят от :hover)
# - На мобильном (iPhone SE) — проверяете, что текст читаемый (≥16px)
```

---

## Phase 7: Финализация и проверка

### 7.1. Линтинг и типы
**Что делаю:**
- Настраиваю ESLint + TypeScript (strict mode)
- Проверяю все `any` и неиспользуемые импорты
- Проверяю, что нет `console.log` в production

### 7.2. End-to-end проверка сценариев
**Мои технические проверки:**
```bash
# Проверяю, что все страницы отдают 200
curl -s -o /dev/null -w "%{http_code}" http://localhost:3000/
curl -s -o /dev/null -w "%{http_code}" http://localhost:3000/feed
# Проверяю, что production build проходит без ошибок
docker compose exec frontend pnpm build
# Проверяю, что TypeScript компилируется (strict mode)
docker compose exec frontend pnpm tsc --noEmit
# Проверяю, что нет console.log в production-коде
grep -r "console.log" src/app/ src/components/ || echo "No console.log found"
```

**Ваши ручные проверки (сценарии):**

**Сценарий 1 — Гость:**
- Открываете `/` — видите главную с референсами
- Открываете `/feed` — видите ленту, можете скроллить
- Пытаетесь поставить лайк — открывается AuthModal
- Пытаетесь нажать "УЧАСТВОВАТЬ" — открывается AuthModal

**Сценарий 2 — Регистрация:**
- Открываете AuthModal → Регистрация → вводите nickname → получаете recovery_code → сохраняете → продолжаете
- Теперь можете загружать работы и ставить лайки

**Сценарий 3 — Загрузка работы:**
- Выбираете неделю → "УЧАСТВОВАТЬ" → перетаскиваете одну картинку → добавляете caption → отправляете → toast → работа в ленте

**Сценарий 4 — Лайк:**
- Открываете ленту → кликаете на работу → кликаете ♥ → счётчик меняется → закрываете модалку → в ленте счётчик тоже обновился (optimistic update)

### 7.3. Документирование
**Что делаю:**
- Обновляю FRONTEND.md если появились изменения (новые компоненты, отклонения от плана)
- Добавляю README.md в `morf_front/` с инструкциями для запуска

---

## Post-MVP Backlog (не делать сейчас)

| Фича | Причина откладывания |
|---|---|
| **User Profile (/profile)** | Нет `/me` endpoint, нет nested layout приоритета |
| **Comments** | Нет API endpoint, backend schema только заложена |
| **Admin panel frontend** | Используется Filament backend |
| **Кастомные шрифты из Figma** | Дизайн ещё в разработке, сейчас Google Fonts |
| **Multi-image upload** | Бэкенд принимает только single image, drag-drop reordering deferred |
| **SEO / SSR для public pages** | MVP за auth wall, Next.js App Router позволит добавить позже |
| **PWA / Offline** | Вне скоупа MVP |

---

## Чек-лист перед каждой задачей

- [ ] Контейнер `frontend` запущен и `pnpm dev` работает
- [ ] Backend (`morf_app`) отвечает на `localhost:8080`
- [ ] Все изменения тестируются внутри контейнера через `docker compose exec frontend ...`
- [ ] После завершения каждой Phase — я демонстрирую результат и жду подтверждения перед переходом к следующей

---

*Last updated: 2026-06-09*
