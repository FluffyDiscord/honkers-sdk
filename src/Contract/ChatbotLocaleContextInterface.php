<?php

declare(strict_types=1);

namespace FluffyDiscord\Honkers\Contract;

interface ChatbotLocaleContextInterface
{
    public function applyChannel(?string $channelCode): void;

    public function getCurrentLocale(): string;

    /**
     * @return list<string>
     */
    public function getChannelLocales(): array;

    public function resolveForChannel(string $requestedLocale): ?string;
}
