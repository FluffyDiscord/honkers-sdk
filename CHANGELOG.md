# Changelog

## v2.0.1

### Changed

- `CatalogIngestClient::send()` reports a `503` as throttled, like a `429`: nothing was queued, send the same
  ids again after `retryAfterSeconds`.
- A throttle without `Retry-After` waits 1 s instead of 300 s. The backend's rate limit sends none.

### Removed

- `CatalogJobStatus::Processing`, `Done`, `Failed` and `Cancelled`. The backend answers only `queued` or
  `rejected`; anything else is `Unknown`.

## v2.0.0

See [UPGRADE-2.0.md](UPGRADE-2.0.md).

### Added

- Pairing. `PairingClient::complete($code, $apiSecret, $ingestSecret)` redeems the code a shop receives on
  `/chatbot/pair` when the admin clicks **Connect** in the dashboard, and returns the site key, the connection's
  base URL, its channel codes, the website's verified domains and the URL to send the admin back to. Failures
  throw `PairingException` with the backend's error code.
- `SiteCredentials` — site key + ingest secret for one outbound call. `hasIngestSecret()` is `false` for a
  widget-only site (empty ingest secret); skip outbound calls then.
- `WidgetSnippet::render(..., defer: false)` prints the loader `<script>` without `defer`.

### Changed

- `CatalogIngestClient::send()`, `TelemetryClient::reportLinkVisit()` and `reportOrder()` take `SiteCredentials`
  instead of a site key; their constructors no longer take the ingest secret. A re-paired secret works on the
  next call, also in long-running workers.
- License is MIT.
