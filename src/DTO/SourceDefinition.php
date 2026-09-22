<?php

declare(strict_types=1);

namespace FluffyDiscord\Honkers\DTO;

use Symfony\Contracts\Translation\TranslatorInterface;

class SourceDefinition implements \JsonSerializable
{
    /** @var ?list<string> */
    public readonly ?array $locales;

    /**
     * @param ?list<string> $locales
     */
    public function __construct(
        public readonly string $name,
        public readonly string $description,
        ?array $locales = null,
    ) {
        $this->locales = $locales === [] ? null : $locales;
    }

    public function withLocales(array $locales): self
    {
        return new self($this->name, $this->description, $locales);
    }

    public function translated(TranslatorInterface $translator, string $locale): self
    {
        return new self(
            $this->name,
            $translator->trans($this->description, [], 'messages', $locale),
            $this->locales,
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'locales' => $this->locales ?? [],
        ];
    }
}
