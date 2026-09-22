<?php

declare(strict_types=1);

namespace FluffyDiscord\Honkers\DTO;

class Violation implements \JsonSerializable
{
    public function __construct(
        public readonly string $path,
        public readonly string $message,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'path' => $this->path,
            'message' => $this->message,
        ];
    }
}
