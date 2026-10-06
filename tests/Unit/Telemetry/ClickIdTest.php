<?php

declare(strict_types=1);

namespace FluffyDiscord\Honkers\Tests\Unit\Telemetry;

use FluffyDiscord\Honkers\Telemetry\ClickId;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ClickIdTest extends TestCase
{
    public function testTheQueryParameterIsGooseclid(): void
    {
        self::assertSame('gooseclid', (new ClickId())->getQueryParameterName());
    }

    public function testAClickIdOfUpToSixtyFourCharactersIsFound(): void
    {
        $clickId = str_repeat('a', 64);

        self::assertSame($clickId, (new ClickId())->find(['gooseclid' => $clickId, 'page' => '2']));
    }

    /**
     * @return iterable<string, array{array<string, mixed>}>
     */
    public static function provideQueriesWithoutAUsableClickId(): iterable
    {
        yield 'missing' => [['page' => '2']];
        yield 'empty' => [['gooseclid' => '']];
        yield 'too long' => [['gooseclid' => str_repeat('a', 65)]];
        yield 'array' => [['gooseclid' => ['abc']]];
        yield 'null' => [['gooseclid' => null]];
    }

    /**
     * @param array<string, mixed> $query
     */
    #[DataProvider('provideQueriesWithoutAUsableClickId')]
    public function testNoUsableClickIdIsNull(array $query): void
    {
        self::assertNull((new ClickId())->find($query));
    }
}
