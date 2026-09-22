<?php

declare(strict_types=1);

namespace FluffyDiscord\Honkers\Registry;

use FluffyDiscord\Honkers\Contract\ChatbotToolInterface;
use Psr\Container\ContainerInterface;

class ToolRegistry
{
    /**
     * @param ContainerInterface $tools tools keyed by their definition name
     * @param list<string>       $names every registered definition name
     */
    public function __construct(
        private readonly ContainerInterface $tools,
        private readonly array              $names = [],
    ) {
    }

    public function get(string $name): ?ChatbotToolInterface
    {
        $isKnown = $this->tools->has($name);
        if (!$isKnown) {
            return null;
        }

        return $this->tools->get($name);
    }

    /**
     * @return iterable<ChatbotToolInterface>
     */
    public function all(): iterable
    {
        foreach ($this->names as $name) {
            yield $this->tools->get($name);
        }
    }
}
