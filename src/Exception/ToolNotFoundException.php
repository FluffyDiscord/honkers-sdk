<?php

declare(strict_types=1);

namespace FluffyDiscord\Honkers\Exception;

use FluffyDiscord\Honkers\Enum\ApiErrorCode;

class ToolNotFoundException extends ChatbotApiException
{
    public function __construct(string $name)
    {
        parent::__construct(sprintf('Tool "%s" is not registered.', $name));
    }

    public function getErrorCode(): ApiErrorCode
    {
        return ApiErrorCode::ToolNotFound;
    }

    public function getStatusCode(): int
    {
        return 404;
    }
}
