# Repository Guidelines

WeCourier v1.8 (CodeCanyon) — courier & logistics CMS on Laravel 12 (PHP ^8.3, MySQL). The root README is the stock Laravel one and contains no project info.

## Project Structure & Module Organization

- **Controllers by panel**: `app/Http/Controllers/Backend/` (admin), `Backend/HubPanel/`, `Backend/MerchantPanel/`, `Frontend/`, `Api/V10/`. Models mirror this under `app/Models/Backend/`. State enums in `app/Enums/` (e.g. `ParcelStatus`, `UserType`).
- **Repository pattern**: `app/Repositories/` holds `*Interface` + `*Repository` pairs (e.g. `MerchantPanel/Shops/ShopsInterface.php` + `ShopsRepository.php`). Controllers delegate data access to these — follow this pattern for new modules.
- **Settings are DB-driven**, not config files: `App\Models\Config` (key/value) and `App\Models\Backend\GeneralSettings` (id=1).
- **Assets**: `resources/` (views, sass, js), compiled into `public/build`, `public/css`, `public/js`. Tests live in `tests/`.
- **i18n**: `lang/{ar,bn,en,es,fr,in,zh}` (PHP files + `en.json`); `LanguageManager` middleware sets locale from `session('locale')`.

## Build, Test, and Development Commands

- `composer install` — install PHP dependencies.
- `php artisan migrate` + `php artisan db:seed` — provision the MySQL schema/seed data.
- `npm run dev` / `npm run build` — Vite dev server / production build of Sass + JS (React/Vue mix).
- `php artisan cache:clear` — required after theme or helper changes (settings/theme are cached).
- `composer dump-autoload` — required after editing `app/Http/Helper/Helper.php` (autoloaded via `files`).
- `vendor/bin/pint` — run the Laravel Pint code formatter.

## Coding Style & Naming Conventions

- PHP follows PSR-12 via Laravel Pint (`vendor/bin/pint`).
- Repositories: `{Name}Interface` contract + `{Name}Repository` implementation; methods named by intent (`getDashboardData`, `filterByDateRange`).
- Global helper functions are defined in `app/Http/Helper/Helper.php` (e.g. `settings()`, `settingHelper()`, `hasPermission()`, `formatPrice()`, `active_theme()`).

## Testing Guidelines

- PHPUnit (PHP ^8.3); tests extend `Tests\TestCase` and use `DatabaseTransactions`.
- Feature tests assert view data via `$response->viewData('data')` (see `tests/Feature/MerchantDashboardTest.php`).
- Run a single test: `php artisan test --filter=MerchantDashboardTest`.

## Commit & Pull Request Guidelines

- Conventional Commits, French descriptions: `feat(merchant): ...`, `fix(merchant): ...`, `perf(layout): ...`.

## Security & Configuration

- Installer-driven: `IsInstalledMiddleware` redirects to `/install` unless `APP_INSTALLED=yes` and required tables exist. The installer verifies a purchase code via the Envato API.
- REST API (`routes/api.php`, prefix `/api/v10`) requires the `apiKey` header matching `config('rxcourier.api_key')`; Sanctum auth protects logged-in endpoints. Keep the mobile-app API contract intact.
- `.env` and `.claude/settings.local.json` hold local secrets — never commit them.
