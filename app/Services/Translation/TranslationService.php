<?php

namespace App\Services\Translation;

use App\Services\Translation\Contracts\TranslationProviderInterface;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class TranslationService
{
    public function __construct(
        private readonly TranslationProviderInterface $provider,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function translate(string $text, string $targetLanguage, ?string $sourceLanguage = null): array
    {
        $startedAt = microtime(true);

        $normalizedTarget = $this->normalizeLanguageCode($targetLanguage);
        $normalizedSource = $this->normalizeLanguageCode($sourceLanguage);

        try {
            $result = $this->provider->translate($text, $normalizedTarget, $normalizedSource);
            $durationMs = $this->elapsedMs($startedAt);

            $this->updateLocalStats($text, $result['translated_text'], $durationMs);

            return [
                'original_text' => $text,
                'translated_text' => $result['translated_text'],
                'source_language' => $this->normalizeLanguageCode($result['source_language'] ?? $normalizedSource),
                'target_language' => $normalizedTarget,
                'engine' => $this->provider->engineName(),
                'duration_ms' => $durationMs,
                'status' => 'success',
                'fallback' => false,
                'error' => null,
                'usage' => $result['usage'] ?? null,
                'provider_http_status' => 200,
                'original_character_count' => mb_strlen($text),
                'translated_character_count' => mb_strlen($result['translated_text']),
                'local_stats' => $this->localStats(),
            ];
        } catch (Throwable $exception) {
            $providerHttpStatus = $this->providerHttpStatus($exception);

            Log::warning('Translation provider request failed; using fallback response.', [
                'engine' => $this->provider->engineName(),
                'target_language' => $normalizedTarget,
                'source_language' => $normalizedSource,
                'error_message' => $exception->getMessage(),
                'error_class' => get_class($exception),
                'provider_http_status' => $providerHttpStatus,
            ]);

            return [
                'original_text' => $text,
                'translated_text' => $text,
                'source_language' => $normalizedSource,
                'target_language' => $normalizedTarget,
                'engine' => $this->provider->engineName(),
                'duration_ms' => $this->elapsedMs($startedAt),
                'status' => 'fallback',
                'fallback' => true,
                'error' => $this->providerErrorCode($providerHttpStatus),
                'usage' => null,
                'provider_http_status' => $providerHttpStatus,
                'original_character_count' => mb_strlen($text),
                'translated_character_count' => mb_strlen($text),
                'local_stats' => $this->localStats(),
            ];
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function languages(): array
    {
        $startedAt = microtime(true);

        try {
            $languages = Cache::remember(
                $this->languagesCacheKey(),
                now()->addMinutes((int) config('services.translation.language_cache_ttl_minutes', 720)),
                fn (): array => $this->normalizeLanguages($this->provider->supportedLanguages()),
            );

            return [
                'engine' => $this->provider->engineName(),
                'status' => 'success',
                'languages' => $languages,
                'error' => null,
                'duration_ms' => $this->elapsedMs($startedAt),
            ];
        } catch (Throwable $exception) {
            Log::warning('Translation language list request failed.', [
                'engine' => $this->provider->engineName(),
                'error_message' => $exception->getMessage(),
                'error_class' => get_class($exception),
            ]);

            return [
                'engine' => $this->provider->engineName(),
                'status' => 'fallback',
                'languages' => [],
                'error' => 'language_list_unavailable',
                'duration_ms' => $this->elapsedMs($startedAt),
            ];
        }
    }

    /**
     * @return array<int, string>
     */
    public function supportedLanguageCodes(): array
    {
        $languages = $this->languages()['languages'] ?? [];

        if (! is_array($languages)) {
            return [];
        }

        return collect($languages)
            ->pluck('code')
            ->filter(fn ($code): bool => is_string($code) && preg_match('/^[a-z]{2,3}(?:-[a-z]{2})?$/', $code) === 1)
            ->values()
            ->all();
    }

    public function localStats(): array
    {
        return [
            'total_successful_translations' => (int) Cache::get($this->statsCacheKey('successful_translations'), 0),
            'total_translated_characters' => (int) Cache::get($this->statsCacheKey('translated_characters'), 0),
            'last_translation_duration_ms' => Cache::get($this->statsCacheKey('last_duration_ms')),
            'last_translated_at' => Cache::get($this->statsCacheKey('last_translated_at')),
        ];
    }

    private function normalizeLanguageCode(?string $language): ?string
    {
        if ($language === null) {
            return null;
        }

        $normalized = strtolower(trim($language));

        return $normalized === '' ? null : $normalized;
    }

    private function elapsedMs(float $startedAt): int
    {
        return (int) round((microtime(true) - $startedAt) * 1000);
    }

    /**
     * @param array<int, array{code:string,name:string}> $languages
     * @return array<int, array<string, string>>
     */
    private function normalizeLanguages(array $languages): array
    {
        return collect($languages)
            ->filter(fn ($item): bool => is_array($item) && isset($item['code'], $item['name']))
            ->map(function (array $item): array {
                $code = $this->normalizeLanguageCode((string) $item['code']) ?? 'unknown';
                $countryCode = $this->countryCodeForLanguage($code);
                $flagEmoji = $countryCode !== null ? $this->countryCodeToFlagEmoji($countryCode) : '🌐';

                return [
                    'code' => $code,
                    'name' => (string) $item['name'],
                    'country_code' => $countryCode ?? '',
                    'flag_emoji' => $flagEmoji,
                    'flag_url' => $countryCode !== null ? 'https://flagcdn.com/w40/'.strtolower($countryCode).'.png' : '',
                    'icon' => $flagEmoji,
                ];
            })
            ->sortBy('name')
            ->values()
            ->all();
    }

    private function countryCodeForLanguage(string $languageCode): ?string
    {
        $countryByLanguage = [
            'aa' => 'ET',
            'ab' => 'GE',
            'af' => 'ZA',
            'ak' => 'GH',
            'am' => 'ET',
            'ar' => 'SA',
            'as' => 'IN',
            'az' => 'AZ',
            'be' => 'BY',
            'bg' => 'BG',
            'bn' => 'BD',
            'bs' => 'BA',
            'ca' => 'ES',
            'cs' => 'CZ',
            'cy' => 'GB',
            'da' => 'DK',
            'de' => 'DE',
            'el' => 'GR',
            'en' => 'US',
            'es' => 'ES',
            'et' => 'EE',
            'eu' => 'ES',
            'fa' => 'IR',
            'fi' => 'FI',
            'fr' => 'FR',
            'ga' => 'IE',
            'gl' => 'ES',
            'gu' => 'IN',
            'he' => 'IL',
            'hi' => 'IN',
            'hr' => 'HR',
            'hu' => 'HU',
            'hy' => 'AM',
            'id' => 'ID',
            'is' => 'IS',
            'it' => 'IT',
            'ja' => 'JP',
            'ka' => 'GE',
            'kk' => 'KZ',
            'ko' => 'KR',
            'lt' => 'LT',
            'lv' => 'LV',
            'mk' => 'MK',
            'ml' => 'IN',
            'mr' => 'IN',
            'ms' => 'MY',
            'nb' => 'NO',
            'ne' => 'NP',
            'nl' => 'NL',
            'nn' => 'NO',
            'pa' => 'IN',
            'pl' => 'PL',
            'pt' => 'PT',
            'ro' => 'RO',
            'ru' => 'RU',
            'sk' => 'SK',
            'sl' => 'SI',
            'sq' => 'AL',
            'sr' => 'RS',
            'sv' => 'SE',
            'sw' => 'TZ',
            'ta' => 'IN',
            'te' => 'IN',
            'th' => 'TH',
            'tr' => 'TR',
            'ur' => 'PK',
            'uk' => 'UA',
            'uz' => 'UZ',
            'vi' => 'VN',
            'zh' => 'CN',
        ];

        $parts = explode('-', $languageCode);
        $base = $parts[0];

        if (count($parts) > 1 && strlen($parts[1]) === 2) {
            return strtoupper($parts[1]);
        }

        return $countryByLanguage[$base] ?? null;
    }

    private function countryCodeToFlagEmoji(string $countryCode): string
    {
        if (strlen($countryCode) !== 2) {
            return '🌐';
        }

        $offset = 127397;

        $countryCode = strtoupper($countryCode);

        return mb_chr(ord($countryCode[0]) + $offset).mb_chr(ord($countryCode[1]) + $offset);
    }

    private function updateLocalStats(string $originalText, string $translatedText, int $durationMs): void
    {
        Cache::increment($this->statsCacheKey('successful_translations'));
        Cache::increment($this->statsCacheKey('translated_characters'), mb_strlen($translatedText));
        Cache::forever($this->statsCacheKey('last_duration_ms'), $durationMs);
        Cache::forever($this->statsCacheKey('last_translated_at'), now()->toISOString());
        Cache::forever($this->statsCacheKey('last_original_characters'), mb_strlen($originalText));
    }

    private function providerHttpStatus(Throwable $exception): ?int
    {
        if ($exception instanceof RequestException && $exception->response !== null) {
            return $exception->response->status();
        }

        return null;
    }

    private function providerErrorCode(?int $httpStatus): string
    {
        return match ($httpStatus) {
            401 => 'provider_unauthorized',
            402 => 'provider_quota_exceeded',
            429 => 'provider_rate_limited',
            503 => 'provider_unavailable',
            default => 'translation_unavailable',
        };
    }

    private function languagesCacheKey(): string
    {
        return (string) config('services.translation.cache_keys.languages', 'translation.languages.v2');
    }

    private function statsCacheKey(string $suffix): string
    {
        $prefix = (string) config('services.translation.cache_keys.stats_prefix', 'translation.stats');

        return $prefix.'.'.$suffix;
    }
}
