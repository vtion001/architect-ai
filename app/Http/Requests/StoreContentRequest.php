<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request for content generation.
 * 
 * Extracts validation from ContentCreatorController::store()
 * following Separation of Concerns principle.
 */
class StoreContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Core fields
            'topic' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string'],
            'count' => ['nullable', 'integer', 'min:1', 'max:10'],
            'tone' => ['nullable', 'string', 'max:50'],
            'length' => ['nullable', 'string', 'max:50'],
            'context' => ['nullable', 'string', 'max:5000'],
            'cta' => ['nullable', 'string', 'max:255'],
            'addLineBreaks' => ['nullable', 'boolean'],
            'includeHashtags' => ['nullable', 'boolean'],
            'generator' => ['nullable', 'string', 'in:post,video,blog,framework'],
            'brand_id' => ['nullable', 'uuid', 'exists:brands,id'],
            
            // Video parameters
            'video_platform' => ['nullable', 'string', 'in:reels,tiktok,youtube_shorts,youtube'],
            'video_hook' => ['nullable', 'string', 'max:100'],
            'video_duration' => ['nullable', 'string', 'max:20'],
            'video_style' => ['nullable', 'string', 'max:50'],
            'video_description' => ['nullable', 'string', 'max:1000'],
            'source_image' => ['nullable', 'string'],
            'ai_model' => ['nullable', 'string'],
            'resolution' => ['nullable', 'string'],
            'aspect_ratio' => ['nullable', 'string'],
            'generation_duration' => ['nullable', 'string'],

            // Blog parameters
            'blog_keywords' => ['nullable', 'string', 'max:500'],
            'blog_structure' => ['nullable', 'string', 'max:50'],
            'is_batch_mode' => ['nullable', 'boolean'],
            'featured_image_type' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'topic.required' => 'Please provide a topic for content generation.',
            'type.required' => 'Please select a content type.',
            'brand_id.exists' => 'The selected brand kit does not exist.',
        ];
    }

    /**
     * Calculate token cost based on request parameters.
     */
    public function getTokenCost(): int
    {
        $count = $this->input('count', 1);
        return $count * 10; // 10 tokens per content piece
    }

    /**
     * Get content options array.
     */
    public function getOptions(): array
    {
        return $this->only([
            'count', 'tone', 'length', 'cta', 'addLineBreaks', 'includeHashtags',
            'generator', 'video_platform', 'video_hook', 'video_duration', 'video_style', 
            'video_description', 'source_image', 'ai_model', 'resolution', 'aspect_ratio', 
            'generation_duration', 'blog_keywords', 'blog_structure', 'is_batch_mode', 
            'featured_image_type', 'brand_id'
        ]);
    }
}
