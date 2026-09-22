<?php

declare(strict_types=1);

namespace FluffyDiscord\Honkers\DTO;

class ToolResult implements \JsonSerializable
{
    public function __construct(
        public readonly array $content,
        public readonly array $blocks = [],
        public readonly bool $isError = false,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'content' => array_map(
                fn (ContentItem $item): array => $item->jsonSerialize(),
                $this->content,
            ),
            'blocks' => array_map(
                fn (\JsonSerializable $block): array => $block->jsonSerialize(),
                $this->blocks,
            ),
            'isError' => $this->isError,
        ];
    }
}
