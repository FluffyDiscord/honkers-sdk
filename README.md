# Honkers SDK

Framework-agnostic core of the Honkers chatbot tool-server. Plain PHP (8.1+) with no HTTP
kernel, no DI container and no Sylius — it only depends on `symfony/validator`,
`symfony/serializer`-free DTOs, `symfony/intl` and `symfony/translation-contracts` as libraries,
plus `psr/container`.

It provides the building blocks a chatbot backend calls over HTTP:

- **Contracts** — `ChatbotToolInterface`, `ChatbotDataSourceInterface`, `ToolChoiceLoaderInterface`,
  and the `ChatbotLocaleContextInterface` port a host application implements.
- **Registries** — `ToolRegistry`, `DataSourceRegistry`, `ToolChoiceLoaderRegistry`, each backed by
  a PSR-11 container so lookups stay lazy.
- **Schema** — `ArgumentsSchemaGenerator` turns a tool's argument DTO (annotated with
  `symfony/validator` constraints) into a JSON Schema, including runtime-loaded choice enums.
- **DTOs** — the tool/source result vocabulary (`ToolResult`, `ToolDefinition`, `SourceDocument`, …).
- **Cursor / locale / text helpers** — `CursorCodec`, `LocaleMatcher`, `HtmlToText`.

Wire it into Symfony with `fluffydiscord/symfony-honkers-bundle`. Sylius shops get plug-and-play
defaults from `fluffydiscord/sylius-honkers-bundle`.

## Tests

```
composer install
vendor/bin/phpunit
```
