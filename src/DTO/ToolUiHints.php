<?php

declare(strict_types=1);

namespace FluffyDiscord\Honkers\DTO;

use Symfony\Contracts\Translation\TranslatorInterface;

class ToolUiHints implements \JsonSerializable
{
    public function __construct(
        public readonly FormDefinition $form,
    ) {
    }

    public function translated(TranslatorInterface $translator, string $locale): self
    {
        return new self($this->form->translated($translator, $locale));
    }

    public function jsonSerialize(): array
    {
        return [
            'form' => $this->form->jsonSerialize(),
        ];
    }
}
