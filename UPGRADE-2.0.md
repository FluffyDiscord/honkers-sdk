# Upgrade from 1.x to 2.0

## Credentials go in per call

**`CatalogIngestClient` and `TelemetryClient` no longer take the ingest secret.** Pass a
`SiteCredentials` to each call instead.

```diff
+use FluffyDiscord\Honkers\DTO\SiteCredentials;
+
 $client = new CatalogIngestClient(
     $psr18Client,
     $psr17Factory,
     $psr17Factory,
     'https://honkers.dev',
-    $ingestSecret,
 );
-$result = $client->send($siteKey, $change);
+$result = $client->send(new SiteCredentials($siteKey, $ingestSecret), $change);
```

```diff
-$telemetry = new TelemetryClient($psr18Client, $psr17Factory, $psr17Factory, 'https://honkers.dev', $ingestSecret);
-$telemetry->reportLinkVisit($siteKey, $clickId, $pageUrl);
-$telemetry->reportOrder($siteKey, $order);
+$telemetry = new TelemetryClient($psr18Client, $psr17Factory, $psr17Factory, 'https://honkers.dev');
+$credentials = new SiteCredentials($siteKey, $ingestSecret);
+$telemetry->reportLinkVisit($credentials, $clickId, $pageUrl);
+$telemetry->reportOrder($credentials, $order);
```

| 1.x | 2.0 |
|---|---|
| `CatalogIngestClient::__construct(…, string $backendUrl, string $ingestSecret)` | `CatalogIngestClient::__construct(…, string $backendUrl)` |
| `CatalogIngestClient::send(string $siteKey, CatalogChange $change)` | `CatalogIngestClient::send(SiteCredentials $credentials, CatalogChange $change)` |
| `TelemetryClient::__construct(…, string $backendUrl, string $ingestSecret)` | `TelemetryClient::__construct(…, string $backendUrl)` |
| `TelemetryClient::reportLinkVisit(string $siteKey, …)` | `TelemetryClient::reportLinkVisit(SiteCredentials $credentials, …)` |
| `TelemetryClient::reportOrder(string $siteKey, ChatOrder $order)` | `TelemetryClient::reportOrder(SiteCredentials $credentials, ChatOrder $order)` |

The request is the same: `Authorization: Bearer <siteKey>.<ingestSecret>`.

## Nothing else breaks

- `WidgetSnippet::render()` gained an optional `bool $defer = true` → same markup as 1.x by default.
- `PairingClient` is new; see [Pairing](README.md#pairing).
