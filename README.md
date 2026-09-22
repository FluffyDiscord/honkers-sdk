# Honkers SDK

Framework-agnostic core of the Honkers chatbot tool-server. Plain PHP 8.1+, no HTTP kernel, no DI
container, no Sylius.

Depends only on these as libraries: `symfony/validator`, `symfony/intl`,
`symfony/translation-contracts`, `psr/container`, `ext-intl`.

## Provides

- **Contracts** — `ChatbotToolInterface`, `ChatbotDataSourceInterface`, `ToolChoiceLoaderInterface`,
  and the `ChatbotLocaleContextInterface` port the host app implements.
- **Registries** — `ToolRegistry`, `DataSourceRegistry`, `ToolChoiceLoaderRegistry`, each backed by
  a PSR-11 container so lookups stay lazy.
- **`ArgumentsSchemaGenerator`** — turns a tool's argument DTO (`symfony/validator` constraints)
  into a JSON Schema, including runtime-loaded choice enums.
- **DTOs** — the tool/source result vocabulary (`ToolResult`, `ToolDefinition`, `SourceDocument`, …).
- **Helpers** — `CursorCodec`, `LocaleMatcher`, `HtmlToText`.

## Usage

- Symfony app: `fluffydiscord/symfony-honkers-bundle`.
- Sylius shop: `fluffydiscord/sylius-honkers-bundle`.

## Tests

```bash
composer install
vendor/bin/phpunit
```
