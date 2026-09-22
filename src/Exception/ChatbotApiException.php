<?php

declare(strict_types=1);

namespace FluffyDiscord\Honkers\Exception;

use FluffyDiscord\Honkers\Enum\ApiErrorCode;

abstract class ChatbotApiException extends \RuntimeException
{
    abstract public function getErrorCode(): ApiErrorCode;

    abstract public function getStatusCode(): int;

    public function getViolations(): array
    {
        return [];
    }
}
