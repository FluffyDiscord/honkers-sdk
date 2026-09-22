<?php

declare(strict_types=1);

namespace FluffyDiscord\Honkers\Contract;

use FluffyDiscord\Honkers\DTO\DocumentPage;
use FluffyDiscord\Honkers\DTO\SourceDefinition;
use FluffyDiscord\Honkers\DTO\SourceQuery;

interface ChatbotDataSourceInterface
{
    public function getDefinition(): SourceDefinition;

    public function getDocuments(SourceQuery $query): DocumentPage;
}
