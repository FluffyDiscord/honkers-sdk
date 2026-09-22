<?php

declare(strict_types=1);

namespace FluffyDiscord\Honkers\Enum;

enum CatalogJobStatus: string
{
    case Queued = 'queued';
    case Processing = 'processing';
    case Done = 'done';
    case Failed = 'failed';
    case Cancelled = 'cancelled';
    case Rejected = 'rejected';
    case Unknown = 'unknown';
}
