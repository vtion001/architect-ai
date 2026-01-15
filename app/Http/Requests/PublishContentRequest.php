<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request for publishing content to social platforms.
 * 
 * Extracts validation from ContentCreatorController::publish()
 */
class PublishContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'content_id' => ['required', 'exists:contents,id'],
            'segment_index' => ['required', 'integer'],
            'final_text' => ['required', 'string'],
            'image_url' => ['nullable', 'string'],
            'platforms' => ['required', 'array', 'min:1'],
            'platforms.*' => ['string', 'in:facebook,instagram,twitter,linkedin'],
            'scheduled_at' => ['required', 'string'],
            'facebook_page_id' => ['nullable', 'string'],
            'facebook_page_token' => ['nullable', 'string'],
            'instagram_account_id' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'content_id.required' => 'Content ID is required.',
            'content_id.exists' => 'The selected content does not exist.',
            'platforms.required' => 'Please select at least one platform.',
            'platforms.min' => 'Please select at least one platform.',
            'final_text.required' => 'Content text is required for publishing.',
        ];
    }

    /**
     * Get normalized image URL (converts relative to absolute).
     */
    public function getNormalizedImageUrl(): ?string
    {
        $url = $this->input('image_url');
        
        if (!empty($url) && str_starts_with($url, '/')) {
            return rtrim(config('app.url'), '/') . $url;
        }
        
        return $url;
    }

    /**
     * Check if this is an immediate publish (now).
     */
    public function isImmediate(): bool
    {
        return $this->input('scheduled_at') === 'now';
    }

    /**
     * Get scheduled timestamp.
     */
    public function getScheduledAt(): string
    {
        return $this->isImmediate() 
            ? now()->toDateTimeString() 
            : $this->input('scheduled_at');
    }

    /**
     * Calculate token cost for publishing.
     */
    public function getTokenCost(): int
    {
        return count($this->input('platforms', [])) * 5; // 5 tokens per platform
    }
}
