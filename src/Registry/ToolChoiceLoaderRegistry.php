<?php

declare(strict_types=1);

namespace FluffyDiscord\Honkers\Registry;

use FluffyDiscord\Honkers\Contract\ToolChoiceLoaderInterface;
use Psr\Container\ContainerInterface;

class ToolChoiceLoaderRegistry
{
    /**
     * @param ContainerInterface $loaders choice loaders keyed by their class name
     */
    public function __construct(
        private readonly ContainerInterface $loaders,
    ) {
    }

    /**
     * @param class-string<ToolChoiceLoaderInterface> $loaderClass
     *
     * @return list<string>
     */
    public function getChoices(string $loaderClass): array
    {
        $isRegistered = $this->loaders->has($loaderClass);
        if (!$isRegistered) {
            throw new \LogicException(sprintf(
                'The tool choice loader "%s" is not a service implementing %s.',
                $loaderClass,
                ToolChoiceLoaderInterface::class,
            ));
        }

        $loader = $this->loaders->get($loaderClass);
        $choices = $loader->loadChoices();

        return array_values(array_unique($choices));
    }
}
