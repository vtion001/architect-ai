<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request for AI image generation.
 */
class GenerateImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'prompt' => ['required', 'string', 'min:3', 'max:4000'],
        ];
    }

    public function messages(): array
    {
        return [
            'prompt.required' => 'Please describe the image you want to generate.',
            'prompt.min' => 'Image description must be at least 3 characters.',
        ];
    }

    /**
     * Token cost for image generation.
     */
    public function getTokenCost(): int
    {
        return 5;
    }
}
