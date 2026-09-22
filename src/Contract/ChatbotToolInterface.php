<?php

declare(strict_types=1);

namespace FluffyDiscord\Honkers\Contract;

use FluffyDiscord\Honkers\DTO\ToolCallContext;
use FluffyDiscord\Honkers\DTO\ToolDefinition;
use FluffyDiscord\Honkers\DTO\ToolResult;

interface ChatbotToolInterface
{
    public function getDefinition(): ToolDefinition;

    public function getArgumentsClass(): string;

    public function execute(object $arguments, ToolCallContext $context): ToolResult;
}
