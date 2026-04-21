<?php

namespace App\Services;

use App\Services\AI\OpenAIClient;
use App\Services\Factories\ContentGeneratorFactory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ContentService
{
    protected ?string $hikerApiKey;

    public function __construct(
        protected ContentGeneratorFactory $factory,
        protected KnowledgeBaseService $knowledgeBaseService,
        protected OpenAIClient $openAIClient
    ) {
        $this->hikerApiKey = config('services.hiker_api.key');
    }

    public function generateText(string $topic, string $type, ?string $context = null, array $options = []): string
    {
        // Use specialized generator via factory
        $generator = $this->factory->make($type);

        return $generator->generate($topic, $context, $options);
    }

    protected function getViralPosts(string $topic): array
    {
        if (! $this->hikerApiKey) {
            return [];
        }

        $hashtag = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $topic));

        try {
            $response = Http::withHeaders([
                'x-access-key' => $this->hikerApiKey,
                'accept' => 'application/json',
            ])->get('https://api.hikerapi.com/v2/hashtag/medias/top', [
                'name' => $hashtag,
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
            Log::warning('HikerAPI fetch failed: '.$e->getMessage());
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

    // Image generation via OpenAI DALL-E
    public function generateImage(string $prompt, string $format = 'realistic', array $options = []): ?string
    {
        $apiKey = config('services.openai.key');
        if (!$apiKey) {
            Log::error('OpenAI API key not configured for image generation.');
            return null;
        }

        // Build format-specific prompt
        $enhancedPrompt = $this->buildImagePrompt($prompt, $format, $options);

        try {
            $response = Http::withToken($apiKey)
                ->timeout(120)
                ->post('https://api.openai.com/v1/images/generations', [
                    'model' => 'dall-e-3',
                    'prompt' => $enhancedPrompt,
                    'n' => 1,
                    'size' => '1024x1024',
                    'quality' => 'standard',
                ]);

            if ($response->successful()) {
                $url = $response->json('data.0.url');
                Log::info('Image generated via DALL-E: ' . $url);
                return $url;
            }

            Log::error('DALL-E image generation failed: ' . $response->body());
            return null;
        } catch (\Throwable $e) {
            Log::error('DALL-E image generation error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Image generation via MiniMax (fallback when OpenAI fails).
     * Uses MiniMax Image-01 API.
     */
    public function generateImageMiniMax(string $prompt, string $format = 'realistic', array $options = []): ?string
    {
        $apiKey = config('services.minimax.key');
        if (!$apiKey) {
            Log::info('MiniMax image fallback: API key not configured.');
            return null;
        }

        $enhancedPrompt = $this->buildImagePrompt($prompt, $format, $options);

        try {
            $response = Http::withToken($apiKey)
                ->timeout(120)
                ->post('https://api.minimax.io/v1/image_generation', [
                    'model' => 'MiniMax-Image-01',
                    'prompt' => $enhancedPrompt,
                    'num_images' => 1,
                    'width' => 1024,
                    'height' => 1024,
                ]);

            if ($response->successful()) {
                $url = $response->json('data.0.url') ?? $response->json('data.0.urls.0');
                Log::info('Image generated via MiniMax: ' . $url);
                return $url;
            }

            Log::error('MiniMax image generation failed: ' . $response->body());
            return null;
        } catch (\Throwable $e) {
            Log::error('MiniMax image generation error: ' . $e->getMessage());
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
        return "Create a photograph that looks like it was captured by a professional human photographer on location.

SUBJECT:
- Authentic moment captured, not posed or staged
- Genuine human expressions, micro-emotions, and natural interaction
- Real people with natural features, not retouched models
- Documentary-style authenticity

TECHNICAL EXECUTION:
- Camera: Professional full-frame camera (Canon EOS R5, Sony A7R V, or Nikon Z8)
- Lens: 50mm f/1.4 - classic portrait lens with beautiful optical character
- Shot at ISO 400-800 for natural film-like grain
- Shallow depth of field with organic bokeh from f/1.4 aperture
- Real shallow perspective distortion natural to 50mm focal length
- Sharp focus on subject with smooth falloff to background

AUTHENTICITY MARKERS:
- Subtle film grain texture (like Kodak Portra 400 or Fuji Pro 400H)
- Natural lens vignette, not heavy post-processing
- Warm, accurate skin-tone color science
- Real location lighting: window light, open shade, or golden hour
- Evidence of actual on-site shooting, not studio setup
- Slight imperfections that prove human capture

AVOID:
- Artificial, CGI, illustration, cartoon, or painting styles
- Portrait-mode phone aesthetic with fake blur
- HDR oversharpening or excessive clarity
- Digital AI smoothness and plastic-looking skin
- 'AI generated' perfection - oversaturated, oversharpened
- Perfect stock photo vibe with fake smiles
- Unrealistic proportions or plastic features
- Anime or illustration style

The result should be indistinguishable from a genuine photograph taken by an experienced photographer using professional equipment.";
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
            : 'Space must be optimized for overlay text placement.';

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
