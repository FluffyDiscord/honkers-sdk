<?php

declare(strict_types=1);

namespace FluffyDiscord\Honkers\DTO;

use Symfony\Contracts\Translation\TranslatorInterface;

class FormFieldOption implements \JsonSerializable
{
    public function __construct(
        public readonly string $value,
        public readonly string $label,
    ) {
    }

    public function translated(TranslatorInterface $translator, string $locale): self
    {
        return new self($this->value, $translator->trans($this->label, [], 'messages', $locale));
    }

    public function jsonSerialize(): array
    {
        return [
            'value' => $this->value,
            'label' => $this->label,
        ];
    }
}
