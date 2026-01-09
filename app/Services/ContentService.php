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
        protected ContentGeneratorFactory $factory
    ) {
        $this->apiKey = config('services.openai.key');
        $this->model = config('services.openai.model', 'gpt-4o-mini');
        $this->hikerApiKey = config('services.hiker_api.key');
    }

    public function generateText(string $topic, string $type, ?string $context = null, array $options = []): string
    {
        $generatorType = $options['generator'] ?? 'post';
        
        // 1. RAG: Fetch relevant knowledge base assets
        $kbContext = $this->getKnowledgeBaseContext($topic);
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
     * RAG: Retrieve relevant context from the tenant's knowledge base.
     */
    protected function getKnowledgeBaseContext(string $topic): ?string
    {
        $tenant = app(\App\Models\Tenant::class);
        if (!$tenant) return null;

        // Check if topic is a specific Asset ID (UUID format) - typically from AI Agent selection
        if (\Illuminate\Support\Str::isUuid($topic)) {
            $asset = \App\Models\KnowledgeBaseAsset::where('tenant_id', $tenant->id)->find($topic);
            if ($asset) {
                if ($asset->type === 'folder') {
                    return $this->getFolderContentRecursive($asset);
                }
                return "--- SOURCE: {$asset->title} ---\n{$asset->content}";
            }
        }

        // Basic keyword search for MVP RAG
        // In production, this would use vector embeddings (Pinecone/Milvus)
        $assets = \App\Models\KnowledgeBaseAsset::where('tenant_id', $tenant->id)
            ->where(function($q) use ($topic) {
                $q->where('title', 'like', "%$topic%")
                  ->orWhere('content', 'like', "%$topic%");
            })
            ->limit(3)
            ->get();

        if ($assets->isEmpty()) return null;

        return $assets->map(fn($a) => "--- SOURCE: {$a->title} ---\n{$a->content}")->implode("\n\n");
    }

    protected function getFolderContentRecursive($folder): string
    {
        $content = "";
        $children = \App\Models\KnowledgeBaseAsset::where('parent_id', $folder->id)->get();

        foreach ($children as $child) {
            if ($child->type === 'folder') {
                $content .= $this->getFolderContentRecursive($child);
            } else {
                $content .= "--- SOURCE: {$child->title} (in {$folder->title}) ---\n{$child->content}\n\n";
            }
        }
        return $content;
    }

    public function generateImage(string $prompt): ?string
    {
        try {
            // Enhanced prompt for realism
            $enhancedPrompt = "A highly realistic, candid photograph capturing the essence of: $prompt. " .
                              "Style: Documentary or lifestyle photography, appearing 100% authentic and un-staged. " .
                              "Lighting: Natural, soft, slightly imperfect to add realism (e.g., dappled sunlight, window light). " .
                              "Texture: Real skin texture, natural material finishes, slight film grain. " .
                              "Avoid: Hyper-realism, excessive saturation, glossy 3D render aesthetics, surrealism, or 'perfect' stock photo vibes. " .
                              "Goal: Make it look like a high-quality photo taken by a professional photographer with a DSLR.";

            $response = Http::withToken($this->apiKey)
                ->timeout(60)
                ->post('https://api.openai.com/v1/images/generations', [
                    'model' => 'dall-e-3',
                    'prompt' => substr($enhancedPrompt, 0, 4000), // DALL-E 3 limit
                    'n' => 1,
                    'size' => '1024x1024',
                    'style' => 'natural', // DALL-E 3 specific parameter for less "artistic" output
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
}