# Frontend Specification — Morf (MVP)

## Overview

Frontend for Morf — an anonymous artist community platform for weekly character-design challenges.

**Status:** MVP scope only. Several features are intentionally simplified or deferred for future iterations.

## Tech Stack

| Technology | Purpose |
|---|---|
| **Next.js 15** (App Router) | Routing, nested layouts, interception routes |
| **TypeScript** | Type safety |
| **Tailwind CSS** | Styling (pixel-perfect for Figma-to-code) |
| **Radix UI** | Headless accessible primitives (Dialog, Tabs, Dropdown, Accordion) |
| **cva** + **tailwind-merge** | Typed component variants |
| **TanStack Query** (React Query) | Server state, infinite scroll, optimistic updates |
| **Zustand** | Client state (auth, multi-upload drafts, UI) |
| **React Hook Form** + **Zod** | Form handling and validation |
| **next/font/local** | Self-hosted custom fonts (preparation for Figma design) |

### Why Radix UI instead of shadcn/ui?

shadcn/ui is a **pre-styled neutral design system** (gray buttons, default radius, standard inputs). For a unique custom design from Figma with bespoke fonts, colors, and elements, you would end up **rewriting 100% of the styles** of every shadcn component anyway.

Radix UI provides the same accessibility primitives (keyboard navigation, focus trapping, ARIA) — but **completely unstyled**. You build the visual layer with Tailwind from scratch, matching your Figma exactly. This is the same approach used by Vercel and Linear for their proprietary design systems.

### Component Variants (cva)

With a custom Figma design, you will have many component variants: Button (primary, secondary, ghost), Card (compact, full), Input (error, disabled). `cva` (class-variance-authority) provides typed variant management without a design-token pipeline:

```typescript
import { cva } from "class-variance-authority";

const button = cva("base-classes", {
  variants: {
    intent: { primary: "bg-red-900", secondary: "border border-red-900" },
    size: { sm: "px-3 py-1", md: "px-4 py-2" }
  }
});
```

## Docker Setup

The frontend runs inside a Docker container. No Node.js/pnpm on the host.

```yaml
services:
  frontend:
    build: .
    container_name: morf-frontend
    working_dir: /app
    networks:
      - morf_network
    ports:
      - "3000:3000"
    volumes:
      - ./morf_front:/app
      - /app/node_modules
    command: pnpm dev

networks:
  morf_network:
    driver: bridge
```

### Commands
```bash
docker compose up --build -d
docker compose exec frontend pnpm install
docker compose exec frontend pnpm dev
docker compose exec frontend pnpm add [package]
```

## API Configuration

- **Base URL:** `/api`
- **Auth:** `X-Device-Token` header
- **Storage:** `auth_token` in `localStorage`; `recovery_code` is shown **once** and never stored

## Auth Flow

### Registration (POST /auth/register)
```json
{ "nickname": "artist_01" }
```
Response:
```json
{ "token": "..." }
```

After successful registration, the **recovery code** is displayed in a modal with:
- Title: "СОХРАНИТЕ КОД ВОССТАНОВЛЕНИЯ"
- Large monospace code + copy button
- Checkbox "Я сохранил код" → enables "Продолжить"

### Recovery (POST /auth/recover)
```json
{ "recovery_code": "ABC123DEF456" }
```
Response:
```json
{ "token": "..." }
```

### Auth Modal
- **Tab 1:** "Регистрация" — nickname input
- **Tab 2:** "Восстановить доступ" — recovery code input
- **No "Login" tab** — authentication is entirely token-based via device token

## Endpoints

### Public (no auth)
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/auth/register` | Create anonymous user |
| POST | `/auth/recover` | Recover account by recovery code |
| GET | `/reference-sets` | List published weekly sets |
| GET | `/reference-sets/{id}` | Single set with images (includes `items.referenceImage`) |
| GET | `/artworks` | Artwork feed (approved only), supports `?reference_set_id=` |
| GET | `/project-info/{key}` | Static content (welcome text, donations, social links) |

### Authenticated (X-Device-Token)
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/artworks` | Multipart upload (image + reference_set_id + caption) |
| DELETE | `/artworks/{id}` | Soft delete own artwork |
| POST | `/artworks/{artwork_id}/likes` | Toggle like |

### Admin only (device.auth + admin)
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/admin/artworks/{id}/approve` | Approve artwork |
| POST | `/admin/artworks/{id}/reject` | Reject artwork |
| GET | `/admin/moderation-actions` | Audit log |
| PUT | `/admin/project-info/{key}` | Update static content |

## Pages

### 1. Layout
- Header: "MORF" (Cinzel, uppercase, tracking-[0.3em])
- Right side: profile icon / auth button
- Background: #0a0a0f + decorative SVG gothic architecture silhouette (opacity 0.15)

### 2. Home ("/")
- Week selector dropdown (gothic frame style)
- Reference cards: image + description
- Buttons:
  - "УЧАСТВОВАТЬ" (red border, glow) → opens upload modal
  - "СКАЧАТЬ РЕФЕРЕНСЫ" (if applicable)

### 3. Upload Modal
- Dropzone with dashed border, glow on drag
- Support for **multiple images** (sketch → final artwork)
- Caption field (optional)
- Reorder images (drag-and-drop)
- "ОТПРАВИТЬ" button → toast "Работа отправлена на модерацию"

### 4. Feed ("/feed")
- Masonry grid: 4 cols desktop, 3 cols tablet, 1-2 cols mobile
- Week filter dropdown
- Card: preview image, author, ♥ + counter
- Click → modal with full-size image, like button, close

### 5. User Profile ("Post-MVP")
- Nested layout under `/profile`
- User's own artworks
- Settings (recovery code, etc.)

### 6. Comments ("Post-MVP")
- Interception route (modal over feed)
- Comment list + form
- Optimistic updates

## Responsive Design

### Desktop (≥1280px)
- 4-column masonry feed
- Full navigation header
- Centered modals, max-width 800px

### Tablet (768–1279px) — **Priority**
- 3-column masonry (2 in portrait)
- Compact header, larger touch targets (min 44x44px)
- Buttons: min-height 48px
- Larger dropzone for finger interaction
- Active states instead of hover effects
- Tap-to-zoom on images

### Mobile (<768px)
- 1-2 column feed
- Full-width cards on home
- Fullscreen or 95vw modals
- Minimal header (logo + profile icon)

## Color Scheme

- Background: `#0a0a0f`
- Surfaces: `#14141f`, `#1e1e2e`
- Accent: `#8b0000` (dark red)
- Primary text: `#e2e2e2`
- Secondary text: `#8b8b9a`
- Borders: `1px solid #2a2a3a`
- Glow effects using accent color

## Typography

- **Headings:** Cinzel (Google Fonts for now), uppercase, tracking-wider
- **Body:** Inter
- **Buttons & nav:** uppercase

**Note:** When the custom Figma design is ready, switch to `next/font/local` with self-hosted woff2 files to avoid FOUT/FOIT and CDN dependency.

## Data Fetching

- **TanStack Query** for all server state (artworks, reference sets, likes, comments)
- **Infinite scroll** for artwork feed (`useInfiniteQuery`, backend uses `paginate(20)`)
- **Optimistic updates** for likes and comments
- **Zustand** for client state:
  - Auth (token, user presence)
  - Multi-upload draft (image order, preview, caption)
  - UI modals

## Forms

- **React Hook Form** for all forms (upload, comments, profile settings)
- **Zod** for schema validation (shared with backend types where possible)
- Example: upload form validates `reference_set_id` (UUID), `image` (file array, max 20MB), `caption` (optional, max 1000 chars)

## Post-MVP / Attention Needed

The following are simplified or deferred:

1. **User Profile** — no profile page or `/me` endpoint in MVP
2. **Comments** — backend schema exists, no frontend or API yet
3. **Thumbnail generation** — originals only; no CDN optimization
4. **Advanced moderation** — admin panel is via Filament, not integrated into frontend
5. **Device management** — no UI for managing multiple devices or revoking tokens
6. **Image download** — "СКАЧАТЬ РЕФЕРЕНСЫ" button may be non-functional in MVP if no download endpoint exists
7. **Share functionality** — no social sharing or deep linking
8. **Search & advanced filtering** — only basic week filter
9. **Artwork detail page** — no dedicated `/artworks/{id}` route; only modal view
10. **Image optimization** — currently using plain `<img>` tags instead of Next.js `<Image>` due to Docker networking complexities. When a CDN is connected, switch back to `<Image>` for automatic optimization, lazy loading, and responsive sizing

## Architecture Notes

### Image Handling
Currently using plain `<img>` tags to avoid Next.js Image optimization issues with Docker networking. The images are proxied through Next.js rewrites (`/storage/:path* → http://host.docker.internal:8080/storage/:path*`).

**When CDN is connected:**
- Switch from `<img>` to Next.js `<Image>` component
- Configure `images.domains` or `images.remotePatterns` in `next.config.ts`
- Benefits: automatic WebP conversion, lazy loading, responsive sizing, blur placeholders
- Remove the `/storage/*` proxy rewrite from `next.config.ts`

### Next.js App Router Benefits
- **Nested layouts** — `/profile` layout with sidebar for user cabinet
- **Interception routes** — open comments as modal over feed (URL changes, feed stays)
- **Streaming** — Suspense boundaries for artwork feed sections

### Interactive Components
- Everything interactive (upload, likes, comments, drag-drop) is marked `'use client'`
- **Never** put `localStorage` or `X-Device-Token` logic in Server Components

### Backend Notes
- Backend uses **Bounded Contexts** (Identity, Content, Artworks, Engagement, Moderation, Static)
- No physical `FOREIGN KEY` constraints — only logical relations
- UUID v7 primary keys everywhere
- Event-driven consistency between contexts
- Laravel Pint for code style
- PHPUnit for testing

## Quick Reference

| Task | Command |
|------|---------|
| Start containers | `docker compose up -d` |
| Install deps | `docker compose exec frontend pnpm install` |
| Dev server | `docker compose exec frontend pnpm dev` |
| Add package | `docker compose exec frontend pnpm add [package]` |
| Lint | `docker compose exec frontend pnpm lint` (if configured) |

---

*Last updated: 2026-06-09*
