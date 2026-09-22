<?php

declare(strict_types=1);

namespace FluffyDiscord\Honkers\Registry;

use FluffyDiscord\Honkers\Contract\ChatbotDataSourceInterface;
use Psr\Container\ContainerInterface;

class DataSourceRegistry
{
    /**
     * @param ContainerInterface $sources data sources keyed by their definition name
     * @param list<string>       $names   every registered definition name
     */
    public function __construct(
        private readonly ContainerInterface $sources,
        private readonly array              $names = [],
    ) {
    }

    public function get(string $name): ?ChatbotDataSourceInterface
    {
        $isKnown = $this->sources->has($name);
        if (!$isKnown) {
            return null;
        }

        return $this->sources->get($name);
    }

    /**
     * @return iterable<ChatbotDataSourceInterface>
     */
    public function all(): iterable
    {
        foreach ($this->names as $name) {
            yield $this->sources->get($name);
        }
    }
}
