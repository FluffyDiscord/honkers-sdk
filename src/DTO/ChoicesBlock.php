<?php

declare(strict_types=1);

namespace FluffyDiscord\Honkers\DTO;

class ChoicesBlock implements \JsonSerializable
{
    public function __construct(
        public readonly array $choices,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'type' => 'choices',
            'choices' => array_map(
                fn (ChoiceItem $choice): array => $choice->jsonSerialize(),
                $this->choices,
            ),
        ];
    }
}
