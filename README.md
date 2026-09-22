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

Registries resolve tools/sources lazily through a PSR-11 container:

```php
$tool = $toolRegistry->get('greet');   // null when unknown
foreach ($toolRegistry->all() as $tool) { /* ... */ }
```

Also here: `ChatbotDataSourceInterface` (bulk documents), `ToolChoiceLoaderInterface` +
`#[ToolChoice]` (DB-backed enums), the `ChatbotLocaleContextInterface` port the host app implements,
result DTOs (`ToolResult`, `ToolDefinition`, `SourceDocument`, …) and helpers (`CursorCodec`,
`LocaleMatcher`, `HtmlToText`).

## Tests

```bash
composer install
vendor/bin/phpunit
```
