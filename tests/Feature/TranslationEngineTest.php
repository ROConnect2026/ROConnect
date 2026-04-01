<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

test('translation endpoint returns translated payload on success', function () {
    Cache::flush();

    Http::fake([
        'api.translateapi.ai/*' => Http::response([
            'translated_text' => 'Hola mundo',
            'source_language' => 'en',
            'character_count' => 11,
            'translation_time' => 1.2,
            'credits_remaining' => 97,
        ], 200),
    ]);

    $response = $this->postJson('/api/translate', [
        'text' => 'Hello world',
        'target_language' => 'es',
        'source_language' => 'en',
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('original_text', 'Hello world')
        ->assertJsonPath('translated_text', 'Hola mundo')
        ->assertJsonPath('source_language', 'en')
        ->assertJsonPath('target_language', 'es')
        ->assertJsonPath('engine', 'translateapi')
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('fallback', false)
        ->assertJsonPath('usage.credits_remaining', 97)
        ->assertJsonPath('original_character_count', 11)
        ->assertJsonPath('translated_character_count', 10)
        ->assertJsonPath('local_stats.total_successful_translations', 1);
});

test('translation endpoint validates required and language format fields', function () {
    Cache::flush();

    $response = $this->postJson('/api/translate', [
        'text' => '',
        'target_language' => 'english',
        'source_language' => '123',
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'text',
            'target_language',
            'source_language',
        ]);
});

test('translation endpoint supports auto-detect source language', function () {
    Cache::flush();

    Http::fake([
        'api.translateapi.ai/*' => Http::response([
            'translated_text' => 'Hallo wereld',
            'source_language' => 'en',
        ], 200),
    ]);

    $response = $this->postJson('/api/translate', [
        'text' => 'Hello world',
        'target_language' => 'nl',
        'source_language' => 'auto',
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('source_language', 'en')
        ->assertJsonPath('target_language', 'nl');

    Http::assertSent(function ($request) {
        $payload = $request->data();

        return ! array_key_exists('source_language', $payload);
    });
});

test('translation endpoint falls back to original text when provider fails', function () {
    Cache::flush();

    Http::fake([
        'api.translateapi.ai/*' => Http::response([
            'message' => 'Service unavailable',
        ], 503),
    ]);

    $response = $this->postJson('/api/translate', [
        'text' => 'Good morning',
        'target_language' => 'tr',
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('original_text', 'Good morning')
        ->assertJsonPath('translated_text', 'Good morning')
        ->assertJsonPath('target_language', 'tr')
        ->assertJsonPath('status', 'fallback')
        ->assertJsonPath('fallback', true)
        ->assertJsonPath('error', 'provider_unavailable')
        ->assertJsonPath('provider_http_status', 503);
});

test('translation endpoint marks quota fallback errors clearly', function () {
    Cache::flush();

    Http::fake([
        'api.translateapi.ai/*' => Http::response([
            'message' => 'Insufficient credits',
        ], 402),
    ]);

    $response = $this->postJson('/api/translate', [
        'text' => 'Need more credits',
        'target_language' => 'fr',
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('status', 'fallback')
        ->assertJsonPath('error', 'provider_quota_exceeded')
        ->assertJsonPath('provider_http_status', 402);
});

test('language endpoint returns provider language list', function () {
    Cache::flush();

    Http::fake([
        'api.translateapi.ai/*' => Http::response([
            'languages' => [
                ['code' => 'en', 'name' => 'English'],
                ['code' => 'nl', 'name' => 'Dutch'],
            ],
        ], 200),
    ]);

    $response = $this->getJson('/api/translation-languages');

    $response
        ->assertOk()
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('engine', 'translateapi')
        ->assertJsonCount(2, 'languages')
        ->assertJsonPath('languages.0.code', 'nl')
        ->assertJsonPath('languages.0.icon', '🇳🇱');
});

test('language endpoint uses cache to avoid repeated provider calls', function () {
    Cache::flush();

    Http::fake([
        'api.translateapi.ai/*' => Http::response([
            'languages' => [
                ['code' => 'en', 'name' => 'English'],
                ['code' => 'es', 'name' => 'Spanish'],
            ],
        ], 200),
    ]);

    $this->getJson('/api/translation-languages')->assertOk();
    $this->getJson('/api/translation-languages')->assertOk();

    Http::assertSentCount(1);
});

test('language endpoint parses results format and follows pagination', function () {
    Cache::flush();

    Http::fake([
        'api.translateapi.ai/api/v1/languages/' => Http::response([
            'count' => 3,
            'next' => 'https://api.translateapi.ai/api/v1/languages/?page=2',
            'results' => [
                ['iso' => 'en', 'name' => 'English', 'en_label' => 'English'],
                ['iso' => 'es', 'name' => 'Español', 'en_label' => 'Spanish'],
            ],
        ], 200),
        'api.translateapi.ai/api/v1/languages/?page=2' => Http::response([
            'count' => 3,
            'next' => null,
            'results' => [
                ['iso' => 'tr', 'name' => 'Türkçe', 'en_label' => 'Turkish'],
            ],
        ], 200),
    ]);

    $response = $this->getJson('/api/translation-languages');

    $response
        ->assertOk()
        ->assertJsonPath('status', 'success')
        ->assertJsonCount(3, 'languages')
        ->assertJsonPath('languages.0.code', 'en')
        ->assertJsonPath('languages.1.name', 'Spanish (Español)')
        ->assertJsonPath('languages.2.icon', '🇹🇷');
});
