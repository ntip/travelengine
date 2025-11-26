# Changelog

All notable changes to this project will be documented in this file.

## [0.3.0] - 2025-11-26
### Added
- End-to-end scraping pipeline using Oxylabs Realtime API integration:
  - `app/Services/OxylabsClient.php` — Oxylabs client supporting API key or username/password, configurable TLS verification and timeouts.
  - `app/Jobs/RunScrape.php` — queued job to perform scrapes, rotate user-agents, log responses, and dispatch retries.
  - `app/Console/Commands/ProcessPendingScrapes.php` — command to create scrapes for pending `RouteJob`s and dispatch `RunScrape` jobs.
  - `app/Models/Scrape.php`, `database/migrations/*_create_scrapes_table.php` — store per-provider scrape rows.
  - `app/Models/ScrapeLog.php`, `database/migrations/*_create_scrape_logs_table.php` — save provider response logs and raw provider payloads.
- Provider extraction architecture:
  - `app/Services/ProviderGrabberFactory.php` — factory to use provider-specific grabbers.
  - `app/Services/Grabbers/VaGrabber.php` — Virgin Australia-specific extractor with strict GraphQL POST/200 detection for `bookingAirSearch`.
- Response validation and retry policy:
  - `app/Services/ProviderResponseValidator.php` — centralizes provider-specific success rules and reasons.
  - Retry support with `attempt` counter and retries creation (migration `2025_11_26_110000_add_attempt_to_scrapes.php`).
  - `config/scrapes.php` — configuration for `max_attempts`.
- Debugging and developer tools:
  - `php artisan debug:va-extract {file} {provider}` — inspect Oxylabs payloads and see which content entries would be selected.
- Misc:
  - User-agent rotation list added to `RunScrape` (desktop, mobile, tablet variants).
  - `VaGrabber` updated to strictly require GraphQL POST 200 responses that include `bookingAirSearch` for VA provider.

### Changed
- `RunScrape` now records `scrape_response_raw` in `scrape_logs` and uses `ProviderResponseValidator` for outcome decisions.
- WAF/block detection improved: treat all-403 payloads as blocked; mixed responses are considered `failed` or `no-data` depending on extraction.

### Fixed
- TLS verification for Oxylabs client made configurable (env `OXYLABS_VERIFY`).

### Notes
- This release focuses on the base scraping feature set: extraction, strict VA GraphQL detection, retries, and user-agent rotation. Follow-up releases should add backoff/delays, more provider grabbers, admin UI for retries, and automated tests.


*** End of changelog for 0.3.0
