<?php

declare(strict_types=1);

namespace FluffyDiscord\Honkers\Tests\Unit\Registry;

use FluffyDiscord\Honkers\Registry\ToolRegistry;
use FluffyDiscord\Honkers\Tests\Unit\Fixtures\AlphaTool;
use PHPUnit\Framework\TestCase;

class ToolRegistryTest extends TestCase
{
    public function testUnknownNameReturnsNull(): void
    {
        $registry = new ToolRegistry([]);

        self::assertNull($registry->get('unknown'));
    }

    public function testKnownNameReturnsTool(): void
    {
        $tool = new AlphaTool();
        $registry = new ToolRegistry([$tool]);

        self::assertSame($tool, $registry->get('alpha_tool'));
        self::assertSame([$tool], iterator_to_array($registry->all(), false));
    }
}
