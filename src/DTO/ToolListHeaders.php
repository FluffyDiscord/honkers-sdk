<?php

declare(strict_types=1);

namespace FluffyDiscord\Honkers\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ToolListHeaders
{
    public function __construct(
        #[Assert\Locale]
        public readonly ?string $locale = null,
    ) {
    }
}
