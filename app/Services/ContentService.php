<?php

namespace App\Services;

use App\Services\Factories\ContentGeneratorFactory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ContentService
{
    protected string $apiKey;
    protected string $model;
    protected ?string $hikerApiKey;

    public function __construct(
        protected ContentGeneratorFactory $factory,
        protected KnowledgeBaseService $knowledgeBaseService
    ) {
        $this->apiKey = config('services.openai.key');
        $this->model = config('services.openai.model', 'gpt-4o-mini');
        $this->hikerApiKey = config('services.hiker_api.key');
    }

    public function generateText(string $topic, string $type, ?string $context = null, array $options = []): string
    {
        $generatorType = $options['generator'] ?? 'post';
        
        // 1. RAG: Fetch relevant knowledge base assets
        $kbContext = $this->knowledgeBaseService->getContext($topic);
        if ($kbContext) {
            $context = ($context ? $context . "\n\n" : "") . "EXTERNAL KNOWLEDGE BASE DATA:\n" . $kbContext;
        }

        // 2. Prepare specialized data for specific generators
        if ($generatorType === 'post' || $type === 'social-media post') {
            $viralPosts = $this->getViralPosts($topic);
            if (!empty($viralPosts)) {
                $examples = collect($viralPosts)->map(function ($post) {
                    if (is_string($post)) return $post;
                    return $post['caption_text'] ?? $post['caption']['text'] ?? null;
                })->filter()->take(5)->implode("\n\n---\n\n");
                
                $options['viral_examples'] = $examples;
            }
        }

        // 3. Delegate to Strategy via Factory
        $generator = $this->factory->make($generatorType);
        
        // Pass common options that might be needed by base class
        $options['type'] = $type;
        
        return $generator->generate($topic, $context, $options);
    }

    protected function getViralPosts(string $topic): array
    {
        if (!$this->hikerApiKey) {
            return [];
        }

        $hashtag = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $topic));
        
        try {
            $response = Http::withHeaders([
                'x-access-key' => $this->hikerApiKey,
                'accept' => 'application/json',
            ])->get("https://api.hikerapi.com/v2/hashtag/medias/top", [
                'name' => $hashtag
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (is_array($data)) {
                    return $data;
                }
                
                if (isset($data['response'])) {
                    return $data['response'];
                }
            }
        } catch (\Exception $e) {
            Log::warning("HikerAPI fetch failed: " . $e->getMessage());
        }

        return [];
    }

    /**
     * RAG: Retrieve relevant context from knowledge base.
     * 
     * @deprecated Use KnowledgeBaseService::getContext() directly. This is a backward-compatible delegate.
     */
    protected function getKnowledgeBaseContext(string $topic): ?string
    {
        return $this->knowledgeBaseService->getContext($topic);
    }

    public function generateImage(string $prompt, string $format = 'realistic', array $options = []): ?string
    {
        try {
            $enhancedPrompt = $this->buildImagePrompt($prompt, $format, $options);

            $response = Http::withToken($this->apiKey)
                ->timeout(60)
                ->post('https://api.openai.com/v1/images/generations', [
                    'model' => 'dall-e-3',
                    'prompt' => substr($enhancedPrompt, 0, 4000), // DALL-E 3 limit
                    'n' => 1,
                    'size' => '1024x1024',
                    'style' => $format === 'poster' ? 'vivid' : 'natural',
                ]);

            if ($response->successful()) {
                return $response->json('data.0.url');
            }
            
            Log::error("Image generation failed: " . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error("Image generation exception: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Build format-specific prompts for DALL-E 3 image generation.
     */
    protected function buildImagePrompt(string $prompt, string $format, array $options): string
    {
        switch ($format) {
            case 'poster':
                return $this->buildPosterPrompt($prompt, $options);
            
            case 'asset-reference':
                return $this->buildReferencePrompt($prompt, $options);
            
            case 'realistic':
            default:
                return $this->buildRealisticPrompt($prompt);
        }
    }

    /**
     * Structured Realistic Photo Generation Prompt.
     */
    protected function buildRealisticPrompt(string $prompt): string
    {
        return "Create a photorealistic image capturing the essence of: $prompt.

SUBJECT DETAILS:
- Authentic interaction and natural posture appropriate for the subject.
- Lifelike expressions, engaged characteristics, and professional styling.

ENVIRONMENT:
- Lighting: Natural, soft, slightly imperfect to add realism (e.g., simulated golden hour or window light).
- Atmosphere: High-end editorial, documentary, or candid lifestyle vibe.

TECHNICAL SPECS:
- Camera: Simulated full-frame DSLR/Mirrorless quality.
- Lens: 35mm or 50mm f/1.8 for natural depth of field and organic bokeh.
- Style: Professional photography, sharp focus on primary subject with natural skin textures.

QUALITY KEYWORDS:
Photorealistic, 8K, high resolution, sharp focus, professional photography, natural lighting, authentic, lifelike, detailed textures, cinematic lighting.

AVOID:
Artificial, CGI, illustration, cartoon, painting, unrealistic proportions, oversaturation, anime style, 'perfect' stock photo vibes, plastic-looking skin.";
    }

    /**
     * Structured Poster/Advertisement Media Asset Prompt.
     */
    protected function buildPosterPrompt(string $prompt, array $options): string
    {
        $posterText = $options['poster_text'] ?? '';
        $brand = $options['brand'] ?? null;
        
        $colorScheme = 'modern, vibrant professional colors';
        $brandContext = '';
        
        if ($brand) {
            $primaryColor = $brand['colors']['primary'] ?? '#000000';
            $secondaryColor = $brand['colors']['secondary'] ?? '#ffffff';
            $accentColor = $brand['colors']['accent'] ?? '#3b82f6';
            
            $colorScheme = "brand color palette (Primary: $primaryColor, Secondary: $secondaryColor, Accent: $accentColor)";
            $brandContext = " representing the identity of '{$brand['name']}'";
        }
        
        $textInstruction = $posterText 
            ? "Space must be optimized for the headline: \"$posterText\"." 
            : "Space must be optimized for overlay text placement.";

        return "Design a professional advertising media asset for: $prompt$brandContext.

VISUAL CONCEPT:
- Primary Focus: Main subject or hero element centered or following the rule of thirds.
- Composition: Structured marketing layout with a clear hierarchy.
- Negative Space: Strategic clear zones (top or bottom thirds) left clear for $textInstruction.

COLOR PALETTE:
- Dominant: $colorScheme.
- Contrast: High contrast areas specifically designed for white and colored typography legibility.
- Mood: Modern, innovative, and brand-focused.

DESIGN ELEMENTS:
- Background: Clean, modern aesthetic (gradient, textured, or high-quality photographic background).
- Graphics: Professional advertising elements, layered depth, and balanced focal points.

TEXT OVERLAY ZONES (Leave Clear):
- Top third and bottom third must remain uncluttered for headline and call-to-action placement.

QUALITY KEYWORDS:
Professional graphic design, advertising quality, bold composition, eye-catching, marketing material, brand-focused, premium quality.

AVOID:
Cluttered composition, low contrast in text areas, amateur design, stock photo look, busy backgrounds in negative space.";
    }

    /**
     * Structured Reference-Based Photo Generation Prompt.
     */
    protected function buildReferencePrompt(string $prompt, array $options): string
    {
        return "Create a new high-fidelity image for: $prompt, strictly maintaining the visual DNA of the reference style.

ELEMENTS TO PRESERVE:
- Subject Framing: Match the composition, framing, and perspective of professional brand photography.
- Visual DNA: Maintain the same color palette, mood, and atmosphere.
- Lighting: Consistent lighting direction and color temperature.

STYLE MATCHING:
- Photography Style: Lifestyle editorial or high-end commercial product photography.
- Post-Processing: Cohesive color treatment and professional grading.
- Texture: Filmic, organic feel with subtle natural grain; avoid clinical sharpness.

TECHNICAL CONSISTENCY:
- Camera Angle: Eye-level or professional studio perspective matching the original series.
- Depth of Field: Consistent focus points and natural background blur.

QUALITY KEYWORDS:
Cohesive series aesthetic, matching photographic style, consistent post-processing, professional execution, brand collateral quality.

AVOID:
Deviating from reference style, mismatched lighting, cooler/warmer tones than reference, sharp clinical look, inconsistent depth of field.";
    }
}