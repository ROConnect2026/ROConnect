<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TranslateTextRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $maxTextLength = (int) config('services.translation.max_text_length', 5000);

        return [
            'text' => ['required', 'string', 'max:'.$maxTextLength],
            'target_language' => ['required', 'string', 'regex:/^[a-z]{2,3}(?:-[a-z]{2})?$/i'],
            'source_language' => ['nullable', 'string', 'regex:/^(auto|[a-z]{2,3}(?:-[a-z]{2})?)$/i'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'text.required' => 'Text is required for translation.',
            'text.max' => 'Text exceeds the configured translation length limit.',
            'target_language.required' => 'A target language code is required.',
            'target_language.regex' => 'Target language must use a valid language code format.',
            'source_language.regex' => 'Source language must be auto or a valid language code.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $target = $this->input('target_language');
        $source = $this->input('source_language');

        $this->merge([
            'text' => is_string($this->input('text')) ? trim($this->input('text')) : $this->input('text'),
            'target_language' => is_string($target) ? strtolower(trim($target)) : $target,
            'source_language' => is_string($source) ? strtolower(trim($source)) : $source,
        ]);
    }
}
