# Model Railroad Locator

A free directory and member-blog platform for model railroad layouts. Members list their
railroads, publish build reports with photos and video embeds, and the community browses,
searches, comments, and reacts.

Built with **Laravel 13**, **Livewire 4**, **Filament 5**, **Tailwind CSS 4**, and **SQLite**
for local development (Postgres recommended in production).

## What is included

- **Layout directory** with search, scale filters, and "sort by distance" using a portable
  haversine query (no PostGIS required to run locally).
- **Member blogs** with a Markdown editor, live preview, draft/publish workflow, cover photos,
  and optional video embeds.
- **Clubs** with memberships, club pages, layouts, and stories.
- **Comments** with one level of replies, author/moderation deletion, and soft deletes.
- **Reactions** (like, love, haha, wow, sad, angry) with one reaction per member per item.
- **Profiles** at `/members/{username}` showing each member's layouts and stories.
- **Admin panel** at `/admin` (Filament) for users, layouts, posts, clubs, comments, and tags.
- **Demo seed data** with six layouts, eight stories, members, clubs, comments, and reactions.

## Requirements

- PHP 8.3+ with the usual Laravel extensions
- Composer
- Node.js 20+ and npm
- SQLite (default) or Postgres for production

## Setup

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate

touch database/database.sqlite
php artisan migrate --seed
php artisan storage:link

npm run build
php artisan serve
```

Then open <http://localhost:8000>.

### Demo accounts

| Role | Email | Password |
| --- | --- | --- |
| Admin | `admin@example.com` | `password` |
| Member | `marta@example.com` | `password` |
| Member | `dev@example.com` | `password` |

The admin account can access `/admin`. Every seeded member uses `password`.

## Routes

| Path | Purpose |
| --- | --- |
| `/` | Home, featured layouts, latest stories |
| `/layouts` | Searchable layout directory |
| `/layouts/{slug}` | Layout detail, build reports, reactions, comments |
| `/blog` | All published stories with search |
| `/blog/{slug}` | Story detail, reactions, comments |
| `/members/{username}` | Member profile, layouts, stories |
| `/clubs/{slug}` | Club page, members, layouts, stories |
| `/stories/new` | Write a story (auth) |
| `/stories/{post}/edit` | Edit a story (auth, owner or admin) |
| `/layouts/new` | Add a layout (auth) |
| `/layouts/{layout}/edit` | Edit a layout (auth, owner or admin) |
| `/admin` | Filament admin panel (admins only) |

## Data model

```
User ──< ClubMembership >── Club
 │                            │
 ├──< Layout >────────────────┘
 │      │
 │      └──< Post >──< Comment (morph, 1-level replies)
 │             │
 │             ├──< Media (morph: image or video_embed)
 │             ├──< Reaction (morph, unique per user)
 │             └──< Tag (morph to many)
 └──< Post
```

Key migrations live in `database/migrations`. Enums (`LayoutScale`, `LayoutVisibility`,
`PostStatus`, `ReactionType`, `MembershipRole`) live in `app/`.

## Architecture notes

### Search

MVP search uses `LIKE` queries scoped through model scopes (`Layout::search()`,
`Post::search()`). When the directory grows, install Laravel Scout with Meilisearch or
Typesense and move these scopes to `searchableAs()` / `toSearchableArray()`.

### Distance search

`Layout::scopeNear()` uses the haversine formula in SQL. It works on SQLite, MySQL, and
Postgres today. For large datasets, switch to PostGIS:

```sql
-- Add to the layouts migration on Postgres
ALTER TABLE layouts ADD COLUMN location geography(Point, 4326);
CREATE INDEX layouts_location_idx ON layouts USING GIST (location);
```

Then replace the scope with `ST_DWithin(location, ST_MakePoint(?, ?)::geography, ?)`.

### Media

- Images are uploaded to the `public` disk (`storage/app/public`) and served through
  `php artisan storage:link`.
- Video is intentionally **embed-only** (YouTube/Vimeo). Self-hosted video is the biggest
  cost risk for a free site; move to Cloudflare Stream or Mux only when there is revenue.
- For production, point the `public` disk at Cloudflare R2 or S3 and add queued image
  resizing (e.g. `intervention/image`) so uploads never block a request.

### Auth and permissions

- Session auth with registration, login (rate limited), and logout.
- `Gate::before()` grants admins all abilities for moderation.
- Policies: `PostPolicy`, `LayoutPolicy`, `CommentPolicy` (owners can edit/delete; admins
  can moderate).
- Filament access is restricted by `User::canAccessPanel()`, which requires `is_admin`.

## Testing

```bash
php artisan test
```

The suite covers public pages, layout browsing/filtering/distance, story creation,
draft visibility, comments, reactions, layout creation, authorization, and auth.

## Production checklist

- [ ] Switch to Postgres, add PostGIS if you want indexed geo queries.
- [ ] Move media to R2/S3 and process images in a queue.
- [ ] Run `php artisan queue:work` (or Horizon) for uploads, mail, and future jobs.
- [ ] Add email verification and password reset mail.
- [ ] Add a moderation queue (reports/flags), spam protection, and a DMCA agent.
- [ ] Add Terms of Service, privacy policy, and a content policy.
- [ ] Configure backups and monitoring.
- [ ] Add advertising/sponsorship inventory (Stripe + simple ad-slot tables) and, later,
      classifieds and swap-meet listings built on the same geo/media primitives.

## Not built yet

Email verification, password reset UI, profile editing, notifications, report/flag
workflows, native apps, ad serving, classifieds, swap meets, and an interactive map.
The schema and policies are designed so those can be added without reworking the core.
