<?php

namespace App\Jobs;

use App\Models\Content;
use App\Models\User;
use App\Services\ContentService;
use App\Services\TokenService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GenerateCalendarFramework implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes

    public $tries = 5; // Allow 5 retry attempts (1 initial + 4 retries)

    public $backoff = [30, 60, 120, 240]; // Exponential backoff between retries

    public function __construct(
        protected Content $content,
        protected User $user,
        protected int $tokenCost
    ) {
        Log::info("GenerateCalendarFramework Job CREATED for Content ID: {$this->content->id}, User: {$this->user->id}");
    }

    public function handle(ContentService $contentService, TokenService $tokenService): void
    {
        Log::info("GenerateCalendarFramework Job STARTED for Content ID: {$this->content->id}");

        // Re-fetch model to ensure we have the absolute latest state
        $this->content = $this->content->fresh();

        if (! $this->content) {
            Log::error('Calendar Framework Generation Job: Model not found.');

            return;
        }

        // Set Tenant Context for Isolation
        if ($this->user->tenant) {
            app()->instance(\App\Models\Tenant::class, $this->user->tenant);
            session(['current_tenant_id' => $this->user->tenant_id]);
            Log::info("Tenant context set: {$this->user->tenant_id}");
        }

        try {
            Log::info("Job processing calendar framework for Content ID: {$this->content->id}");

            // Execute Generation
            // The generator class (FrameworkCalendarGenerator) forces 'gpt-4o' and 'max_tokens' => 4000
            $generatedJson = $contentService->generateText(
                $this->content->topic,
                'framework_calendar',
                $this->content->context,
                $this->content->options ?? []
            );

            Log::info('Framework JSON Raw (Job): '.substr($generatedJson, 0, 500).'...');

            // Validation & Sanitization
            Log::info('Raw JSON length: '.strlen($generatedJson));
            $decoded = json_decode($generatedJson, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::warning('JSON decode failed: '.json_last_error_msg());
                // Attempt to fix common JSON issues (control chars)
                $sanitized = preg_replace('/[\x00-\x1F\x7F]/', '', $generatedJson);
                $decodedSanitized = json_decode($sanitized, true);

                if ($decodedSanitized) {
                    Log::info('Sanitization succeeded');
                    $generatedJson = $sanitized;
                    $decoded = $decodedSanitized;
                } else {
                    Log::error('Invalid JSON from AI (Job) after sanitization: '.json_last_error_msg());
                    Log::error('Raw JSON preview: '.substr($generatedJson, 0, 1000));
                    // Fallback structure - THIS WILL CAUSE RETRY DUE TO 0 ITEMS
                    $decoded = [
                        'educational' => [],
                        'showcase' => [],
                        'conversational' => [],
                        'promotional' => [],
                    ];
                }
            } else {
                Log::info('JSON decode succeeded on first attempt');
            }

            // STRICT QUANTITY CHECK - Validate exact pillar counts
            $expectedCounts = [
                'educational' => 3,
                'showcase' => 2,
                'conversational' => 2,
                'promotional' => 1,
            ];

            $totalItems = 0;
            $itemsWithContent = 0;
            $errors = [];

            foreach ($expectedCounts as $pillar => $expected) {
                if (! isset($decoded[$pillar])) {
                    $errors[] = "Pillar '$pillar' is missing entirely (expected $expected)";

                    continue;
                }

                $count = count($decoded[$pillar]);
                $totalItems += $count;

                $validCount = 0;
                foreach ($decoded[$pillar] as $idx => $item) {
                    if (! empty($item['hook']) && ! empty($item['caption'])) {
                        $validCount++;
                        $itemsWithContent++;
                    } else {
                        $errors[] = "Empty item in pillar '$pillar' at index $idx";
                        Log::warning("Empty item in pillar '$pillar' at index $idx");
                    }
                }

                if ($count !== $expected) {
                    $errors[] = "Pillar '$pillar' has $count items but expected $expected";
                }

                if ($validCount !== $expected) {
                    $errors[] = "Pillar '$pillar' has $validCount valid items but expected $expected";
                }

                Log::info("Pillar '$pillar': $count total ($expected expected), $validCount with content ($validCount expected)");
            }

            Log::info("Total items: $totalItems/8 (with content: $itemsWithContent/8)");

            if (! empty($errors)) {
                Log::error('Pillar validation failed: '.implode('; ', $errors));
                Log::error('Full decoded structure: '.json_encode($decoded));
                throw new \Exception('Validation failed: '.implode('; ', $errors).'. Triggering retry.');
            }

            if ($itemsWithContent < 8) {
                Log::warning("AI returned insufficient valid items ($itemsWithContent/8). Full JSON: ".json_encode($decoded));
                throw new \Exception("Insufficient valid content generated ($itemsWithContent/8). Triggering retry.");
            }

            // Create Child Drafts
            if (! empty($decoded)) {
                $this->createCalendarDrafts($this->content, $decoded);
            }

            // Update Parent Content
            $this->content->update([
                'result' => $generatedJson,
                'status' => 'draft', // Ready for review
            ]);

            Log::info("Calendar framework generated successfully for ID: {$this->content->id}");

        } catch (\Throwable $e) {
            Log::error('Calendar Framework Job Failed: '.$e->getMessage());

            // Refund tokens on failure
            $tokenService->grant($this->user->tenant, $this->tokenCost, 'refund_failed_generation');

            $this->content->update(['status' => 'failed']);

            throw $e;
        }
    }

    protected function createCalendarDrafts(Content $parent, array $data): void
    {
        $pillars = ['educational', 'showcase', 'conversational', 'promotional'];

        foreach ($pillars as $pillar) {
            if (! isset($data[$pillar]) || ! is_array($data[$pillar])) {
                continue;
            }

            foreach ($data[$pillar] as $post) {
                Content::create([
                    'title' => ucfirst($pillar).': '.Str::limit($post['hook'] ?? 'Untitled', 30),
                    'topic' => $parent->topic,
                    'type' => 'social-post',
                    'status' => 'draft',
                    'context' => "Derived from Weekly Framework. Pillar: $pillar",
                    'result' => ($post['hook'] ?? '')."\n\n".($post['caption'] ?? ''),
                    'options' => [
                        'original_content_id' => $parent->id,
                        'visual_idea' => $post['visual_idea'] ?? null,
                        'pillar' => $pillar,
                    ],
                ]);
            }
        }
    }
}
