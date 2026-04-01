<?php

namespace App\Http\Controllers;

use App\Http\Requests\TranslateTextRequest;
use App\Services\Translation\TranslationService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class TranslationController extends Controller
{
    public function index(): View
    {
        return view('translator.index');
    }

    public function translate(TranslateTextRequest $request, TranslationService $translationService): JsonResponse
    {
        $payload = $request->validated();

        $result = $translationService->translate(
            text: $payload['text'],
            targetLanguage: $payload['target_language'],
            sourceLanguage: $payload['source_language'] ?? null,
        );

        return response()->json($result);
    }

    public function languages(TranslationService $translationService): JsonResponse
    {
        return response()->json($translationService->languages());
    }
}
