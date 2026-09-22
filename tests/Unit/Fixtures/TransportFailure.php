<?php

declare(strict_types=1);

namespace FluffyDiscord\Honkers\Tests\Unit\Fixtures;

use Psr\Http\Client\ClientExceptionInterface;

class TransportFailure extends \RuntimeException implements ClientExceptionInterface
{
}
