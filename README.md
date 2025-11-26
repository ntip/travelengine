# TravelEngine – Post-Clone Setup

This guide covers everything you need to do after cloning the TravelEngine repository to get it running locally, including fixing common Laravel cache / `bootstrap/cache` issues.

## 1. Clone the repo

```bash
git clone <repo-url> travelengine
cd travelengine
```

## 2. Install PHP dependencies

Make sure you have PHP, Composer, and the required PHP extensions (including `pdo_sqlite` if using SQLite).

```bash
composer install
```

## 3. Create `.env` file and app key

```bash
copy .env.example .env
php artisan key:generate
```

## 4. Configure the database

### SQLite (simple local setup)

```powershell
mkdir database
New-Item -Path database\database.sqlite -ItemType File -Force
# Update .env: set DB_CONNECTION=sqlite and DB_DATABASE=database/database.sqlite
```

### MySQL/Postgres

Set the usual DB vars in `.env`.

## 5. Ensure `storage` and `bootstrap/cache` are writable

Create missing dirs and fix permissions (Windows example):

```powershell
mkdir bootstrap\cache -Force
icacls ".\bootstrap\cache" /grant "Everyone:(F)"
icacls ".\storage" /grant "Everyone:(F)"
```

## 6. Run migrations

```powershell
php artisan migrate
```

## 7. Clear Laravel caches

```powershell
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

Optionally rebuild:

```powershell
php artisan optimize
```

## 8. Install frontend deps

```powershell
npm install
npm run dev
```

## 9. Run the application

```powershell
php artisan serve
```

---

You can now start using TravelEngine locally.

## Oxylabs configuration (realtime scraping)

TravelEngine can use Oxylabs Realtime API to perform scrapes for each job/provider. Configure credentials in your `.env`.

Supported variables:

- `OXYLABS_API_KEY` — (optional) Bearer token for API access. If present, this will be used.
- `OXYLABS_USERNAME` and `OXYLABS_PASSWORD` — basic-auth credentials fallback if `OXYLABS_API_KEY` is not present.
- `OXYLABS_URL` — endpoint to call (defaults to `https://realtime.oxylabs.io/v1/queries`).

Example (do NOT commit credentials):

```dotenv
# Use either API key or username/password
OXYLABS_API_KEY=
OXYLABS_USERNAME=your_user
OXYLABS_PASSWORD=your_password
OXYLABS_URL=https://realtime.oxylabs.io/v1/queries
```

Notes:

- If you plan to process many scrapes in parallel, configure and run a queue worker (`php artisan queue:work`) and use a database/Redis queue. The default example uses `QUEUE_CONNECTION=database`.

	- If using the `database` queue driver, create the jobs table and migrate it:

		```powershell
		php artisan queue:table
		php artisan migrate
		```

### Oxylabs SSL / TLS verification

Some environments may encounter TLS verification errors when calling the Oxylabs realtime API (cURL error 60). To support those cases during development you can disable SSL verification — but only do this temporarily and never in production.

Set the following in your `.env` to control verification (defaults to `true`):

```dotenv
# true|false - whether to verify TLS certs for Oxylabs requests
OXYLABS_VERIFY=true
```

If you must disable verification for testing, set `OXYLABS_VERIFY=false`. The client will pass this option to the HTTP client.

### Oxylabs timeouts

If Oxylabs requests take longer than your environment allows, you can tune request timeouts via env vars:

- `OXYLABS_TIMEOUT` — overall request timeout in seconds (default `60`).
- `OXYLABS_CONNECT_TIMEOUT` — TCP connect timeout in seconds (default `10`).

Example:

```dotenv
OXYLABS_TIMEOUT=120
OXYLABS_CONNECT_TIMEOUT=20
```

These values are passed to the HTTP client used by the Oxylabs integration.
- Keep real credentials out of version control and use environment-specific secret management for production.

## Release v0.3.0 (Scraping pipeline)

This repository now ships an initial end-to-end scraping pipeline (v0.3.0). Highlights:

- Queued `RunScrape` job that calls Oxylabs Realtime API and records provider logs.
- Provider extraction via `ProviderGrabberFactory` and a `VaGrabber` for Virgin Australia.
- Strict VA GraphQL selection: extractor requires `/api/graphql` POST responses with HTTP 200 and `bookingAirSearch` present.
- Automatic retries for failed/no-data scrapes with per-scrape `attempt` counts and configurable `max_attempts`.
- Randomized Oxylabs `user_agent_type` rotation for each scrape.

Migration and run notes (after pulling this tag):

```powershell
php artisan migrate
php artisan scrapes:process-pending
php artisan queue:work --once
```

Use `php artisan debug:va-extract <file> VA` to inspect Oxylabs payloads locally and see which content entry the VA grabber would extract.

See `CHANGELOG.md` for the full list of changes in v0.3.0.

Provider-specific date format

Providers can supply a `date_format` config value (via the Provider Config UI) to control how the `{{date}}` placeholder is formatted when the scraper renders provider URLs. The default format is `Y-m-d`.

For example, to get `11-28-2025` set `date_format` to `m-d-Y` for the provider.

Example via Tinker or DB:

```php
// provider_code 'VA'
\App\Models\ProviderConfig::updateOrCreate([
	'provider_code' => 'VA',
	'name' => 'date_format',
], [
	'value' => 'm-d-Y'
]);
```
