<?php

declare(strict_types=1);

namespace FluffyDiscord\Honkers\DTO;

use FluffyDiscord\Honkers\Enum\CatalogJobStatus;

class CatalogChangeJob
{
    public function __construct(
        public readonly string           $externalId,
        public readonly ?string          $jobId,
        public readonly CatalogJobStatus $status,
        public readonly ?string          $violation = null,
    ) {
    }

    public function isRejected(): bool
    {
        return $this->status === CatalogJobStatus::Rejected;
    }
}
