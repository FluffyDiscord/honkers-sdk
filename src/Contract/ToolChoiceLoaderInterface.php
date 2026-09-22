<?php

declare(strict_types=1);

namespace FluffyDiscord\Honkers\Contract;

interface ToolChoiceLoaderInterface
{
    /**
     * @return list<string>
     */
    public function loadChoices(): array;
}
