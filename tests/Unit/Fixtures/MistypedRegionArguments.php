<?php

declare(strict_types=1);

namespace FluffyDiscord\Honkers\Tests\Unit\Fixtures;

use FluffyDiscord\Honkers\Validator\ToolChoice;

class MistypedRegionArguments
{
    public function __construct(
        #[ToolChoice(loader: RegionChoiceLoader::class)]
        public readonly int $region = 0,
    ) {
    }
}
