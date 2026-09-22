<?php

declare(strict_types=1);

namespace FluffyDiscord\Honkers\Tests\Unit\Fixtures;

use FluffyDiscord\Honkers\Validator\ToolChoice;

class RegionListArguments
{
    /**
     * @param list<string> $regions
     */
    public function __construct(
        #[ToolChoice(loader: RegionChoiceLoader::class, multiple: true, min: 1, max: 2)]
        public readonly array $regions = [],
    ) {
    }
}
