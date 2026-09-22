<?php

declare(strict_types=1);

namespace FluffyDiscord\Honkers\Tests\Unit\Fixtures;

use FluffyDiscord\Honkers\Validator\ToolChoice;

class RegionArguments
{
    public function __construct(
        #[ToolChoice(loader: RegionChoiceLoader::class)]
        public readonly ?string $region = null,
    ) {
    }
}
