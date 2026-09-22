<?php

declare(strict_types=1);

namespace FluffyDiscord\Honkers\Exception;

use FluffyDiscord\Honkers\Enum\ApiErrorCode;

class InvalidLocaleException extends ChatbotApiException
{
    public function __construct(string $locale)
    {
        parent::__construct(sprintf('Locale "%s" is not served.', $locale));
    }

    public function getErrorCode(): ApiErrorCode
    {
        return ApiErrorCode::InvalidLocale;
    }

    public function getStatusCode(): int
    {
        return 400;
    }
}
