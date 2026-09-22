<?php

declare(strict_types=1);

namespace FluffyDiscord\Honkers\Tests\Unit\Fixtures;

use FluffyDiscord\Honkers\Contract\ToolChoiceLoaderInterface;

class RegionChoiceLoader implements ToolChoiceLoaderInterface
{
    /**
     * @param list<string> $choices
     */
    public function __construct(
        private readonly array $choices = ['Praha', 'Moravskoslezský kraj'],
    ) {
    }

    public function loadChoices(): array
    {
        return $this->choices;
    }
}
