# AGENTS.md

WeCourier v1.8 (CodeCanyon) — courier & logistics CMS on Laravel 12 (PHP ^8.3, MySQL). CodeCanyon product code; the root README is the stock Laravel one and contains no project info.

## Setup / install gate

- The app is installer-driven: `IsInstalledMiddleware` redirects to `/install` unless `APP_INSTALLED=yes` in `.env` AND `settings`, `general_settings`, `users` tables exist. The installer (routes/web.php:127) verifies a purchase code against the Envato API, writes `.env` via `geo-sot/laravel-env-editor`, then runs `migrate:refresh --seed`.
- Dev database: MySQL `courierdb` (see local `.env`, user `kamal`). Normal setup is `php artisan migrate` + `php artisan db:seed`. `database/database.sql` (2950 lines) is the full-schema dump used for manual/hosting imports — don't treat it as source of truth.
- Default seeded login: `admin@wemaxdevs.com` / `12345678` (database/seeders/UserSeeder.php).

## Global helpers

`app/Http/Helper/Helper.php` is autoloaded via composer `files` — plain functions usable anywhere: `settings()` (general_settings row id=1), `settingHelper($key)` (Config key/value table), `hasPermission()`, `active_theme()`, `formatPrice()`, `pluck()`. Add new global helpers there and run `composer dump-autoload` after editing it.

## Architecture

- **Settings are DB-driven**, not config files: `App\Models\Config` (key/value) and `App\Models\Backend\GeneralSettings` (id=1). Don't look for them in `config/`.
- **Controllers by panel**: `app/Http/Controllers/Backend/` (admin), `Backend/HubPanel/`, `Backend/MerchantPanel/`, `Frontend/`, `Api/V10/`. Models mirror this under `app/Models/Backend/...`. State enums live in `app/Enums/` and are used pervasively (e.g. `ParcelStatus`, `UserType`).
- **Permissions**: per-user permission arrays checked via `hasPermission()` helper + `PermissionCheckMiddleware`; seeded by `PermissionSeeder`.
- **REST API** (`routes/api.php`, ~177 lines): prefix `/api/v10`, wrapped in `CheckApiKeyMiddleware` which requires header `apiKey` equal to `config('rxcourier.api_key')` (config/rxcourier.php, default `123456rx-ecourier123456`). Sanctum auth for logged-in endpoints. The merchant/deliveryman mobile apps consume this — keep the API key contract intact.
- **Frontend themes**: `resources/views/frontend/theme-1`..`theme-6`. Active theme resolved by `active_theme()` which caches for 1h — run `php artisan cache:clear` after theme changes.
- **i18n**: translations in `lang/{ar,bn,en,es,fr,in,zh}` (PHP files + `en.json`); `LanguageManager` middleware sets locale from `session('locale')`.
- **Activity log**: spatie/laravel-activitylog trait on most models (admin `ActiveLog` views read it).
- **Addons**: `Backend/AddonController` + `resources/views/backend/addons` — zip-based addon system, don't break it.

## Frontend build

Vite with the React plugin; inputs are `resources/sass/app.scss` and `resources/js/app.js` (Bootstrap + React/Vue mix). `npm run dev` / `npm run build`. Compiled assets are committed under `public/build`, `public/css`, `public/js` — prod runs off those, so rebuild after JS/Sass changes.

## Payments / integrations

Many gateways wired in: Stripe, PayPal (config/paypal.php), Paytm, Razorpay, Skrill, SSLCommerz (app/Library/SslCommerz), bkash, Paymob, Aamarpay, Paystack, plus Twilio SMS and FCM push (both configured from `.env`). Excel import/export via maatwebsite/excel (`app/Exports`, `app/Imports`).

## Verification

- Tests are placeholders (stock Laravel ExampleTest only) — don't rely on `phpunit` for verification; it uses array cache and MySQL connection from `.env`.
- Code style: `vendor/bin/pint` (laravel/pint) is the configured formatter.
- `.env` and `.claude/settings.local.json` hold local secrets — never commit them.