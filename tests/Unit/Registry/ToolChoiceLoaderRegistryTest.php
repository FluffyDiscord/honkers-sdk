<?php

declare(strict_types=1);

namespace FluffyDiscord\Honkers\Tests\Unit\Registry;

use FluffyDiscord\Honkers\Contract\ToolChoiceLoaderInterface;
use FluffyDiscord\Honkers\Registry\ToolChoiceLoaderRegistry;
use FluffyDiscord\Honkers\Tests\Unit\Fixtures\RegionChoiceLoader;
use PHPUnit\Framework\TestCase;
use FluffyDiscord\Honkers\Tests\Unit\Fixtures\ArrayContainer;

class ToolChoiceLoaderRegistryTest extends TestCase
{
    public function testReturnsTheChoicesOfTheRegisteredLoader(): void
    {
        $registry = new ToolChoiceLoaderRegistry(new ArrayContainer([
            RegionChoiceLoader::class => static fn (): RegionChoiceLoader => new RegionChoiceLoader(),
        ]));

        self::assertSame(['Praha', 'Moravskoslezský kraj'], $registry->getChoices(RegionChoiceLoader::class));
    }

    public function testReturnsEachChoiceOnceAsAList(): void
    {
        $loader = $this->createStub(ToolChoiceLoaderInterface::class);
        $loader->method('loadChoices')->willReturn([3 => 'Praha', 5 => 'Praha', 7 => 'Vysočina']);
        $registry = new ToolChoiceLoaderRegistry(new ArrayContainer([
            'keyed' => static fn (): ToolChoiceLoaderInterface => $loader,
        ]));

        self::assertSame(['Praha', 'Vysočina'], $registry->getChoices('keyed'));
    }

    public function testRefusesALoaderThatIsNotRegistered(): void
    {
        $registry = new ToolChoiceLoaderRegistry(new ArrayContainer([]));

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(RegionChoiceLoader::class);

        $registry->getChoices(RegionChoiceLoader::class);
    }
}
