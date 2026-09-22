<?php

declare(strict_types=1);

namespace FluffyDiscord\Honkers\Tests\Unit\Fixtures;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class ArrayContainer implements ContainerInterface
{
    /**
     * @param array<string, callable():object> $factories
     */
    public function __construct(
        private readonly array $factories,
    ) {
    }

    public function has(string $id): bool
    {
        return isset($this->factories[$id]);
    }

    public function get(string $id): mixed
    {
        $isRegistered = isset($this->factories[$id]);
        if (!$isRegistered) {
            throw new class ($id) extends \RuntimeException implements NotFoundExceptionInterface {
                public function __construct(string $id)
                {
                    parent::__construct(sprintf('Service "%s" is not registered.', $id));
                }
            };
        }

        return ($this->factories[$id])();
    }
}
