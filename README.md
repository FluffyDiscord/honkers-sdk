# Honkers SDK

Framework-agnostic core of the Honkers.dev chatbot tool-server.

Used by:

- [`fluffydiscord/symfony-honkers-bundle`](https://github.com/FluffyDiscord/symfony-honkers-bundle) — Symfony wiring + the `/chatbot/v1` HTTP endpoints.
- [`fluffydiscord/sylius-honkers-bundle`](https://github.com/FluffyDiscord/sylius-honkers-bundle) — Sylius defaults (tools, data sources, shop widget).

## Examples

A tool — one class, one arguments DTO:

```php
use FluffyDiscord\Honkers\Contract\ChatbotToolInterface;
use FluffyDiscord\Honkers\DTO\ContentItem;
use FluffyDiscord\Honkers\DTO\ToolCallContext;
use FluffyDiscord\Honkers\DTO\ToolDefinition;
use FluffyDiscord\Honkers\DTO\ToolResult;

class GreetTool implements ChatbotToolInterface
{
    public function getDefinition(): ToolDefinition
    {
        return new ToolDefinition('greet', 'app.chatbot.greet.description');
    }

    public function getArgumentsClass(): string
    {
        return GreetArguments::class;
    }

    public function execute(object $arguments, ToolCallContext $context): ToolResult
    {
        return new ToolResult([new ContentItem('Hello ' . $arguments->name)]);
    }
}
```

Argument DTOs carry `symfony/validator` constraints; `ArgumentsSchemaGenerator` turns them into a
JSON Schema (with runtime-loaded choice enums):

```php
use Symfony\Component\Validator\Constraints as Assert;

class GreetArguments
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 64)]
        public string $name = '',
    ) {
    }
}

// $generator->generate(GreetArguments::class)
// => ['type' => 'object',
//     'properties' => ['name' => ['type' => 'string', 'maxLength' => 64]],
//     'required' => ['name'], 'additionalProperties' => false]
```

Registries wrap a plain iterable of tools/sources and key them by `getDefinition()->name`:

```php
$tool = $toolRegistry->get('greet');   // null when unknown
foreach ($toolRegistry->all() as $tool) { /* ... */ }
```

Also here: `ChatbotDataSourceInterface` (bulk documents), `ToolChoiceLoaderInterface` +
`#[ToolChoice]` (DB-backed enums), the `ChatbotLocaleContextInterface` port the host app implements,
result DTOs (`ToolResult`, `ToolDefinition`, `SourceDocument`, …) and helpers (`CursorCodec`,
`LocaleMatcher`, `HtmlToText`).

## Standalone setup (no framework)

The SDK owns the logic, not the transport. You wire the registries once, then map four HTTP routes
to them.

Wire the pieces — each registry takes a plain list of services, no container:

```php
use FluffyDiscord\Honkers\Registry\ToolRegistry;
use FluffyDiscord\Honkers\Registry\DataSourceRegistry;
use FluffyDiscord\Honkers\Registry\ToolChoiceLoaderRegistry;
use FluffyDiscord\Honkers\Schema\ArgumentsSchemaGenerator;
use Symfony\Component\Validator\Validation;

$tools   = [new GreetTool()];   // ChatbotToolInterface, keyed at runtime by getDefinition()->name
$sources = [];                  // ChatbotDataSourceInterface
$loaders = [];                  // ToolChoiceLoaderInterface, matched by class

$toolRegistry   = new ToolRegistry($tools);
$sourceRegistry = new DataSourceRegistry($sources);
$schema         = new ArgumentsSchemaGenerator(new ToolChoiceLoaderRegistry($loaders));
$validator      = Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator();
```

`GET /chatbot/v1/tools` — tool list with input schemas:

```php
$out = [];
foreach ($toolRegistry->all() as $tool) {
    $out[] = $tool->getDefinition()
        ->withInputSchema($schema->generate($tool->getArgumentsClass()))
        ->jsonSerialize();
}
echo json_encode(['tools' => $out]);
```

`POST /chatbot/v1/tools/{name}` — body `{ "arguments": {...}, "context": {...} }`:

```php
use FluffyDiscord\Honkers\DTO\ToolCallContext;
use FluffyDiscord\Honkers\DTO\Violation;
use FluffyDiscord\Honkers\Exception\ToolNotFoundException;
use FluffyDiscord\Honkers\Exception\ArgumentsValidationException;

$tool = $toolRegistry->get($name) ?? throw new ToolNotFoundException($name);
$body = json_decode($rawRequestBody, true);

// Turn the arguments array into the typed DTO. Any deserializer works;
// symfony/serializer is what the bundles use (not an SDK dependency).
$arguments = $serializer->denormalize($body['arguments'] ?? [], $tool->getArgumentsClass());

$violations = [];
foreach ($validator->validate($arguments) as $violation) {
    $violations[] = new Violation($violation->getPropertyPath(), (string) $violation->getMessage());
}
if ($violations !== []) {
    throw new ArgumentsValidationException($violations);   // -> 422 envelope
}

$context = new ToolCallContext(
    $body['context']['conversationId'],   // must be a UUID
    $body['context']['locale'],           // an ICU locale, e.g. cs_CZ
    $body['context']['channelCode'] ?? null,
);
echo json_encode($tool->execute($arguments, $context)->jsonSerialize());
```

`GET /chatbot/v1/sources` and `GET /chatbot/v1/sources/{name}`:

```php
use FluffyDiscord\Honkers\DTO\SourceQuery;
use FluffyDiscord\Honkers\Exception\SourceNotFoundException;

// list
$out = [];
foreach ($sourceRegistry->all() as $source) {
    $out[] = $source->getDefinition()->jsonSerialize();
}
echo json_encode(['sources' => $out]);

// read: ?locale=cs_CZ&channel=&cursor=&ids[]=
$source = $sourceRegistry->get($name) ?? throw new SourceNotFoundException($name);
$query = new SourceQuery(
    locale: $_GET['locale'] ?? '',
    channel: $_GET['channel'] ?? null,
    cursor: $_GET['cursor'] ?? null,
    ids: $_GET['ids'] ?? null,
);
echo json_encode($source->getDocuments($query)->jsonSerialize());
```

You provide, around the SDK: **auth**, **routing**, and **argument deserialization**. Locale
matching against a channel's served locales is optional — use `LocaleMatcher` and implement
`ChatbotLocaleContextInterface` if you have channels.

## HTTP contract (what the chatbot backend expects)

Base path is yours; the example uses `/chatbot/v1`. Tell the backend the full base URL.

| Method | Path | Request | Response |
|---|---|---|---|
| GET | `/tools` | `Accept-Language` (optional) | `{ "tools": [ {name, description, inputSchema, ui?} ] }` |
| POST | `/tools/{name}` | `{ "arguments": {...}, "context": { "conversationId", "locale", "channelCode"? } }` | `{ "content": [{type,text}], "blocks": [], "isError": bool }` |
| GET | `/sources` | — | `{ "sources": [ {name, description, locales} ] }` |
| GET | `/sources/{name}` | `?locale=&channel=&cursor=&ids[]=` (`ids[]` max 500; then `cursor` ignored, `nextCursor` null) | `{ "documents": [...], "nextCursor": string\|null }` |

- **Auth** — the backend sends `Authorization: Bearer <shared-secret>`. The SDK does no auth; verify
  the header yourself (`hash_equals`) or let the Symfony bundle's firewall do it.
- **Errors** — every failure returns `{ "error": { "code", "message", "violations" } }`. Throw a
  `ChatbotApiException` subclass; `getErrorCode()` gives the `code`, `getStatusCode()` the HTTP
  status (`tool_not_found`/`source_not_found` 404, `validation_failed` 422 with `violations`,
  `invalid_cursor`/`invalid_locale` 400).

## Tests

```bash
composer install
vendor/bin/phpunit
```
