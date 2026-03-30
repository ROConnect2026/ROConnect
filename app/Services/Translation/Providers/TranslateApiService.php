<?php

namespace App\Services\Translation\Providers;

use App\Services\Translation\Contracts\TranslationProviderInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class TranslateApiService implements TranslationProviderInterface
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly string $apiKey,
        private readonly int $timeoutSeconds,
        private readonly bool $verifySsl = true,
        private readonly ?string $caBundlePath = null,
    ) {
    }

    public function translate(string $text, string $targetLanguage, ?string $sourceLanguage = null): array
    {
        $payload = [
            'text' => $text,
            'target_language' => $targetLanguage,
        ];

        if (! empty($sourceLanguage) && $sourceLanguage !== 'auto') {
            $payload['source_language'] = $sourceLanguage;
        }

        $response = $this->client()
            ->post('translate/', $payload);

        $response->throw();

        $json = $response->json() ?? [];

        $translatedText = Arr::get($json, 'translated_text')
            ?? Arr::get($json, 'translation')
            ?? Arr::get($json, 'data.translated_text')
            ?? Arr::get($json, 'data.translation');

        if (! is_string($translatedText) || $translatedText === '') {
            throw new RuntimeException('TranslateAPI response did not include translated text.');
        }

        $detectedSourceLanguage = Arr::get($json, 'source_language')
            ?? Arr::get($json, 'detected_source_language')
            ?? Arr::get($json, 'data.source_language')
            ?? Arr::get($json, 'data.detected_source_language')
            ?? $sourceLanguage;

        return [
            'translated_text' => $translatedText,
            'source_language' => is_string($detectedSourceLanguage) ? $detectedSourceLanguage : null,
            'target_language' => $targetLanguage,
            'usage' => $this->extractUsage($json),
            'raw' => is_array($json) ? $json : [],
        ];
    }

    public function supportedLanguages(): array
    {
        $languages = $this->fetchLanguages('languages/');

        // Keep backward compatibility if the provider account still uses legacy routing.
        if ($languages === []) {
            $languages = $this->fetchLanguages('translate/languages/');
        }

        if ($languages === []) {
            throw new RuntimeException('No languages were returned by the provider.');
        }

        return collect($languages)
            ->unique('code')
            ->values()
            ->all();
    }

    public function engineName(): string
    {
        return 'translateapi';
    }

    private function extractUsage(array $payload): ?array
    {
        $usage = Arr::get($payload, 'usage')
            ?? Arr::get($payload, 'meta.usage')
            ?? Arr::get($payload, 'data.usage');

        if (is_array($usage)) {
            return $usage;
        }

        $translatedApiUsage = array_filter([
            'character_count' => Arr::get($payload, 'character_count'),
            'credits_remaining' => Arr::get($payload, 'credits_remaining'),
            'translation_time' => Arr::get($payload, 'translation_time'),
        ], static fn ($value) => $value !== null);

        return $translatedApiUsage !== [] ? $translatedApiUsage : null;
    }

    private function client(): \Illuminate\Http\Client\PendingRequest
    {
        $verifyOption = $this->verifySsl;

        if ($this->caBundlePath !== null && trim($this->caBundlePath) !== '') {
            $verifyOption = $this->caBundlePath;
        }

        return Http::baseUrl($this->baseUrl)
            ->withToken($this->apiKey)
            ->acceptJson()
            ->timeout($this->timeoutSeconds)
            ->withOptions(['verify' => $verifyOption]);
    }

    /**
     * @return array<int, array{code:string,name:string}>
     */
    private function fetchLanguages(string $endpoint): array
    {
        $url = $endpoint;
        $languages = [];

        for ($page = 0; $page < 10; $page++) {
            $response = $this->client()->get($url);

            if ($response->status() === 404) {
                return [];
            }

            $response->throw();

            $json = $response->json() ?? [];
            $items = Arr::get($json, 'results')
                ?? Arr::get($json, 'languages')
                ?? Arr::get($json, 'data.languages')
                ?? Arr::get($json, 'data')
                ?? $json;

            if (is_array($items)) {
                $languages = array_merge($languages, $this->normalizeLanguageItems($items));
            }

            $next = Arr::get($json, 'next');

            if (! is_string($next) || trim($next) === '') {
                break;
            }

            $url = $next;
        }

        return $languages;
    }

    /**
     * @param array<int, mixed> $items
     * @return array<int, array{code:string,name:string}>
     */
    private function normalizeLanguageItems(array $items): array
    {
        $languages = [];

        foreach ($items as $item) {
            if (is_array($item)) {
                $code = Arr::get($item, 'code')
                    ?? Arr::get($item, 'iso')
                    ?? Arr::get($item, 'language');

                if (! is_string($code)) {
                    continue;
                }

                $englishLabel = Arr::get($item, 'en_label');
                $nativeName = Arr::get($item, 'name') ?? Arr::get($item, 'label');

                $name = $this->displayLanguageName($englishLabel, $nativeName, $code);

                $languages[] = [
                    'code' => strtolower(trim($code)),
                    'name' => $name,
                ];

                continue;
            }

            if (is_string($item)) {
                $normalized = strtolower(trim($item));

                $languages[] = [
                    'code' => $normalized,
                    'name' => strtoupper($normalized),
                ];
            }
        }

        return $languages;
    }

    private function displayLanguageName(mixed $englishLabel, mixed $nativeName, string $fallbackCode): string
    {
        $english = is_string($englishLabel) ? trim($englishLabel) : '';
        $native = is_string($nativeName) ? trim($nativeName) : '';

        if ($english !== '' && $native !== '' && mb_strtolower($english) !== mb_strtolower($native)) {
            return $english.' ('.$native.')';
        }

        if ($english !== '') {
            return $english;
        }

        if ($native !== '') {
            return $native;
        }

        return strtoupper($fallbackCode);
    }
}
