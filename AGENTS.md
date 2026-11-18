# AGENTS.md

Laravel app for a model railroad layout directory and member blog.

## Stack and commands

- Laravel 13, Livewire 4, Filament 5, Tailwind CSS 4, Vite 8, PHPUnit 12 (not Pest).
- PHP 8.3+ required. Don't copy Laravel/Livewire/Filament patterns from older majors; check `vendor/` or current docs first.
- `composer dev` runs the app server, queue listener, Pail logs, and Vite together (`php artisan dev`).
- `php artisan test` runs the full suite. Tests use in-memory SQLite and `RefreshDatabase`; no external services are needed.
- Run one file with `php artisan test tests/Feature/LayoutBrowserTest.php`; narrow further with `--filter=test_name`.
- `composer test` clears config before running tests; use it after changing config or `.env`.
- `vendor/bin/pint --dirty` formats changed PHP. Pint is the only configured linter; there is no static analysis/typecheck.
- `npm run build` is required after Blade, CSS, or JS edits. `public/build` is gitignored, so a fresh checkout needs a build before serving.
- `php artisan migrate --seed` loads demo data. Admin: `admin@example.com`; member: `marta@example.com`; password: `password`.
- Fresh clone: run `touch database/database.sqlite` before `composer setup` or `migrate`; the DB file is gitignored. `composer setup` also does **not** seed demo data.

## Code map

- Page controllers in `app/Http/Controllers` are thin; dynamic UI lives in `app/Livewire` (layout browser, editors, comments, reactions).
- Admin is a Filament 5 panel at `/admin`. Resources live in `app/Filament/Resources/{Model}` with sibling `Pages/`, `Schemas/`, and `Tables/`. Only admins pass `User::canAccessPanel()`.
- Enums live in the `App` namespace at `app/*.php` (e.g. `App\LayoutScale`, `App\PostStatus`), not `app/Enums`.
- Models use Laravel 13 attributes such as `#[Fillable([...])]` and `#[Hidden([...])]`, not `$fillable`/`$hidden` properties.
- `Layout` and `Post` route keys are `slug`; `User` uses `username`. Slugs are generated on create by `App\Models\Concerns\HasUniqueSlug`.
- Comments, reactions, media, and tags are polymorphic (`commentable`, `reactionable`, `mediable`, `taggable`). A DB unique index allows one reaction per user per item.
- Authorization uses policies in `app/Policies`; `Gate::before` grants admins every ability in `AppServiceProvider`.
- Search uses `LIKE` scopes (`Layout::search`, `Post::search`). Distance uses a SQL haversine (`Layout::near`). Don't introduce Scout or PostGIS unless asked.

## Gotchas

- Render user Markdown with `Str::markdown($text, ['html_input' => 'strip', 'allow_unsafe_links' => false])`; keep those flags in new rendering paths.
- Uploads use the `public` disk (`storage/app/public`) and require `php artisan storage:link`. Use `Storage::fake('public')` in tests.
- Public layout/story queries use `Layout::visible()` and `Post::published()`; publishing through `PostEditor` sets both status and `published_at`.
- `LayoutBrowser` keeps `q`, `scale`, and `sort` in the URL via `#[Url]`. Tests set component properties or call `setCoordinates()` rather than hitting the page.
- Filament assets are generated and gitignored. If `/admin` is unstyled, run `php artisan filament:assets`.
- No CI is configured. Run `php artisan test` and `vendor/bin/pint --test` before finishing.
