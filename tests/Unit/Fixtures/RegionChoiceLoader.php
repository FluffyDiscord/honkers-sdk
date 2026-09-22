<?php

declare(strict_types=1);

namespace FluffyDiscord\Honkers\Tests\Unit\Fixtures;

use FluffyDiscord\Honkers\Contract\ToolChoiceLoaderInterface;

class RegionChoiceLoader implements ToolChoiceLoaderInterface
{
    public function loadChoices(): array
    {
        return ['Praha', 'Moravskoslezský kraj'];
    }
}
