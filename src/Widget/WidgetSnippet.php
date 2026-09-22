<?php

declare(strict_types=1);

namespace FluffyDiscord\Honkers\Widget;

class WidgetSnippet
{
    public function render(string $backendUrl, string $siteKey, string $cdnUrl = '', string $locale = ''): string
    {
        $scriptUrl = $this->resolveScriptUrl($backendUrl, $cdnUrl);

        $script = sprintf('<script src="%s" defer></script>', $this->escape($scriptUrl));
        $element = $this->renderElement($backendUrl, $siteKey, $locale);

        return $script . "\n" . $element;
    }

    private function resolveScriptUrl(string $backendUrl, string $cdnUrl): string
    {
        $hasCdnUrl = $cdnUrl !== '';
        if ($hasCdnUrl) {
            return $cdnUrl;
        }

        return rtrim($backendUrl, '/') . '/widget/v1/chat.js';
    }

    private function renderElement(string $backendUrl, string $siteKey, string $locale): string
    {
        $localeAttribute = '';
        $hasLocale = $locale !== '';
        if ($hasLocale) {
            $localeAttribute = sprintf(' locale="%s"', $this->escape($locale));
        }

        return sprintf(
            '<ai-chat-widget site-key="%s"%s backend-url="%s"></ai-chat-widget>',
            $this->escape($siteKey),
            $localeAttribute,
            $this->escape($backendUrl),
        );
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
