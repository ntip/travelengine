Release v0.3.0 - TravelEngine

Tag: v0.3.0
Date: 2025-11-26

Summary
- Introduces the Oxylabs realtime scraping pipeline, provider extraction, retries, and developer tooling for debugging and validation.

Key files added/modified
- app/Jobs/RunScrape.php (UA rotation, logging, retry dispatch)
- app/Services/OxylabsClient.php (Oxylabs HTTP client)
- app/Services/ProviderGrabberFactory.php
- app/Services/Grabbers/VaGrabber.php
- app/Services/ProviderResponseValidator.php
- app/Console/Commands/DebugVaExtractor.php
- database/migrations/*_create_scrapes_table.php
- database/migrations/*_create_scrape_logs_table.php
- database/migrations/2025_11_26_110000_add_attempt_to_scrapes.php
- app/Models/Scrape.php (attempt column)
- app/Models/ScrapeLog.php
- config/scrapes.php
- CHANGELOG.md, README.md (release notes)

Upgrade / Deployment notes
- Run `php artisan migrate` to add the new `scrapes`/`scrape_logs` tables and the `attempt` column.
- Set Oxylabs config env vars in `.env` before running:
  - `OXYLABS_API_KEY` or `OXYLABS_USERNAME` + `OXYLABS_PASSWORD`
  - `OXYLABS_VERIFY` (true|false), `OXYLABS_TIMEOUT`, `OXYLABS_CONNECT_TIMEOUT`
- Use `php artisan debug:va-extract` to test extractor against saved Oxylabs JSONs.

Known follow-ups
- Add exponential backoff for retries instead of immediate re-dispatch.
- Add admin UI pages to inspect retries and attempt history.
- Expand provider grabbers for other providers.
- Add PHPUnit tests for extractor and validator.
