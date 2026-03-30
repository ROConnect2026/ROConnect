<?php

namespace App\Services\Translation\Contracts;

interface TranslationProviderInterface
{
    /**
     * @return array{translated_text:string,source_language:?string,target_language:string,usage:array|null,raw:array}
     */
    public function translate(string $text, string $targetLanguage, ?string $sourceLanguage = null): array;

    /**
     * @return array<int, array{code:string,name:string}>
     */
    public function supportedLanguages(): array;

    public function engineName(): string;
}
