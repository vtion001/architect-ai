<?php

namespace App\Jobs;

use App\Models\Content;
use App\Models\Tenant;
use App\Models\User;
use App\Services\AI\OpenAIClient;
use App\Services\ContentGenerators\BlogPostGenerator;
use App\Services\ContentService;
use App\Services\TokenService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateBlogBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600;

    public function __construct(
        protected Content $parent,
        protected User $user,
        protected int $tokenCost,
        protected array $topicData
    ) {}

    public function handle(ContentService $contentService, TokenService $tokenService, OpenAIClient $openAIClient, BlogPostGenerator $blogGenerator): void
    {
        $this->parent->refresh();

        if ($this->user->tenant) {
            app()->instance(Tenant::class, $this->user->tenant);
            session(['current_tenant_id' => $this->user->tenant_id]);
        }

        $count = $this->topicData['count'] ?? 1;
        $topic = $this->topicData['topic'] ?? $this->parent->topic;
        $keywords = $this->topicData['keywords'] ?? '';
        $context = $this->topicData['context'] ?? $this->parent->context;
        $cta = $this->topicData['cta'] ?? '';
        $brandTone = $this->topicData['brand_tone'] ?? '';

        try {
            Log::info('[GenerateBlogBatch] Phase 1: Extracting angles', ['parent_id' => $this->parent->id, 'count' => $count]);

            $angleSystemPrompt = 'You are an expert content strategist. Output ONLY valid JSON — no explanations, no markdown, no preamble.';
            $angleUserPrompt = $blogGenerator->getAngleExtractionPrompt($topic, $count, $keywords);

            $angleResponse = $openAIClient->chat(
                [
                    ['role' => 'system', 'content' => $angleSystemPrompt],
                    ['role' => 'user', 'content' => $angleUserPrompt],
                ],
                [
                    'model' => 'gpt-4o-mini',
                    'temperature' => 0.7,
                    'max_tokens' => 2000,
                    'response_format' => ['type' => 'json_object'],
                ]
            );

            $angleJson = $angleResponse['success'] ? $angleResponse['message'] : '';
            $angles = $this->parseAngles($angleJson, $count);

            Log::info('[GenerateBlogBatch] Phase 2: Generating blogs', [
                'parent_id' => $this->parent->id,
                'angles' => array_column($angles, 'angle'),
            ]);

            $childResults = [];
            $totalWordCount = 0;

            foreach ($angles as $i => $angle) {
                $options = array_merge($this->parent->options ?? [], [
                    'angle' => $angle['angle'],
                    'focus_keyword' => $angle['keyword'],
                    'count' => 1,
                    'brand_tone' => $brandTone,
                    'cta' => $cta,
                    'parent_batch_id' => $this->parent->id,
                ]);

                $generatedText = $contentService->generateText(
                    $topic,
                    'blog',
                    $context,
                    $options
                );

                $lines = collect(explode("\n", trim($generatedText)))
                    ->map(fn ($l) => trim($l))
                    ->filter(fn ($l) => ! empty($l) && ! preg_match('/^-{3,}$/', $l))
                    ->values();

                $firstLine = $lines->first() ?? $topic;
                $title = trim(preg_replace('/^#+\s*/', '', $firstLine));
                $title = str_replace(['*', '='], '', $title);
                if (strlen($title) > 100) {
                    $title = substr($title, 0, 97).'...';
                }

                $wordCount = str_word_count(strip_tags($generatedText));
                $totalWordCount += $wordCount;

                $child = Content::create([
                    'tenant_id' => $this->user->tenant_id,
                    'title' => $title,
                    'topic' => $topic,
                    'type' => 'blog',
                    'context' => $context,
                    'status' => 'published',
                    'result' => $generatedText,
                    'word_count' => $wordCount,
                    'options' => [
                        'parent_batch_id' => $this->parent->id,
                        'batch_index' => $i,
                        'angle' => $angle['angle'],
                        'focus_keyword' => $angle['keyword'],
                        'featured_image_url' => null,
                    ],
                ]);

                $childResults[] = [
                    'id' => $child->id,
                    'title' => $title,
                    'angle' => $angle['angle'],
                ];

                Log::info('[GenerateBlogBatch] Child blog created', [
                    'child_id' => $child->id,
                    'parent_id' => $this->parent->id,
                    'angle' => $angle['angle'],
                ]);
            }

            $this->parent->update([
                'title' => "Batch: $topic",
                'result' => json_encode([
                    'status' => 'completed',
                    'angles' => $angles,
                    'children' => $childResults,
                    'completed_at' => now()->toIso8601String(),
                ]),
                'word_count' => $totalWordCount,
                'status' => 'published',
            ]);

            Log::info('[GenerateBlogBatch] Batch complete', [
                'parent_id' => $this->parent->id,
                'child_count' => count($childResults),
            ]);

        } catch (\Throwable $e) {
            Log::error('[GenerateBlogBatch] Failed: '.$e->getMessage());

            $tokenService->grant($this->user->tenant, $this->tokenCost, 'refund_failed_generation');
            $this->parent->update(['status' => 'failed']);

            throw $e;
        }
    }

    private function parseAngles(string $json, int $count): array
    {
        $json = trim($json);

        if (preg_match('/```json\s*(.*?)\s*```/s', $json, $m)) {
            $json = $m[1];
        }

        $data = json_decode($json, true);

        if (! is_array($data)) {
            return $this->fallbackAngles($count);
        }

        if (isset($data['angles']) && is_array($data['angles'])) {
            $data = $data['angles'];
        }

        $angles = [];
        foreach (array_slice(array_values($data), 0, $count) as $item) {
            if (! is_array($item)) {
                continue;
            }
            $angles[] = [
                'angle' => $item['angle'] ?? $item['title'] ?? $item['name'] ?? 'General Guide',
                'keyword' => $item['keyword'] ?? $item['focus_keyword'] ?? '',
                'description' => $item['description'] ?? '',
            ];
        }

        if (count($angles) < $count) {
            $angles = array_merge($angles, $this->fallbackAngles($count - count($angles)));
        }

        return array_slice($angles, 0, $count);
    }

    private function fallbackAngles(int $count): array
    {
        $defaults = [
            ['angle' => 'Getting Started Guide', 'keyword' => 'beginner', 'description' => 'Entry-level overview for newcomers'],
            ['angle' => 'Advanced Strategies', 'keyword' => 'advanced', 'description' => 'In-depth techniques for experienced readers'],
            ['angle' => 'Common Mistakes to Avoid', 'keyword' => 'mistakes', 'description' => 'Pitfalls and how to sidestep them'],
            ['angle' => 'Best Practices', 'keyword' => 'best practices', 'description' => 'Proven methods and standards'],
            ['angle' => 'Case Studies & Examples', 'keyword' => 'case study', 'description' => 'Real-world examples and results'],
        ];

        return array_slice($defaults, 0, $count);
    }
}
