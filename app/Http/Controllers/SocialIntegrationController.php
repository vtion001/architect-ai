<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Tenant;
use App\Services\SocialPublishingService;
use App\Services\TokenService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SocialIntegrationController extends Controller
{
    public function __construct(
        protected SocialPublishingService $socialPublishingService,
        protected TokenService $tokenService
    ) {}

    /**
     * Receive content from external sources (n8n, openclaw)
     * POST /api/content/receive
     */
    public function receiveContent(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'nullable|string|in:social-post,blog-post,video,email',
            'media_url' => 'nullable|url',
            'image_url' => 'nullable|url',
            'platforms' => 'nullable|array',
            'platforms.*' => 'string|in:facebook,instagram,linkedin,twitter,x',
            'scheduled_at' => 'nullable|date',
            'source' => 'nullable|string|in:n8n,openclaw,external',
        ]);

        $source = $validated['source'] ?? $this->detectSource($request);
        $scheduledAt = $validated['scheduled_at'] ?? null;

        $content = Content::create([
            'title' => $validated['title'],
            'topic' => $validated['title'],
            'type' => $validated['type'] ?? 'social-post',
            'context' => null,
            'status' => 'draft',
            'result' => $validated['content'],
            'options' => [
                'source' => $source,
                'media_url' => $validated['media_url'] ?? null,
                'image_url' => $validated['image_url'] ?? null,
                'platforms' => $validated['platforms'] ?? [],
                'scheduled_at' => $scheduledAt,
                'received_at' => now()->toIso8601String(),
            ],
        ]);

        $this->fireWebhook('content.received', [
            'content_id' => $content->id,
            'title' => $content->title,
            'type' => $content->type,
            'source' => $source,
            'created_at' => $content->created_at->toIso8601String(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Content received and saved as draft',
            'content' => [
                'id' => $content->id,
                'title' => $content->title,
                'status' => $content->status,
                'source' => $source,
            ],
        ], 201);
    }

    /**
     * List all drafts
     * GET /api/content/drafts
     */
    public function listDrafts(Request $request): JsonResponse
    {
        $drafts = Content::where('status', 'draft')
            ->orWhere('status', 'scheduled')
            ->latest()
            ->get()
            ->map(function ($draft) {
                return [
                    'id' => $draft->id,
                    'title' => $draft->title,
                    'type' => $draft->type,
                    'status' => $draft->status,
                    'source' => $draft->options['source'] ?? 'manual',
                    'platforms' => $draft->options['platforms'] ?? [],
                    'image_url' => $draft->options['image_url'] ?? null,
                    'media_url' => $draft->options['media_url'] ?? null,
                    'result' => $draft->result,
                    'created_at' => $draft->created_at->toIso8601String(),
                    'scheduled_at' => $draft->options['scheduled_at'] ?? null,
                ];
            });

        return response()->json([
            'success' => true,
            'drafts' => $drafts,
            'count' => $drafts->count(),
        ]);
    }

    /**
     * Get specific draft
     * GET /api/content/drafts/{id}
     */
    public function getDraft(Content $draft): JsonResponse
    {
        return response()->json([
            'success' => true,
            'draft' => [
                'id' => $draft->id,
                'title' => $draft->title,
                'type' => $draft->type,
                'status' => $draft->status,
                'source' => $draft->options['source'] ?? 'manual',
                'platforms' => $draft->options['platforms'] ?? [],
                'image_url' => $draft->options['image_url'] ?? null,
                'media_url' => $draft->options['media_url'] ?? null,
                'result' => $draft->result,
                'context' => $draft->context,
                'topic' => $draft->topic,
                'created_at' => $draft->created_at->toIso8601String(),
                'updated_at' => $draft->updated_at->toIso8601String(),
                'scheduled_at' => $draft->options['scheduled_at'] ?? null,
            ],
        ]);
    }

    /**
     * Update a draft
     * PUT /api/content/drafts/{id}
     */
    public function updateDraft(Request $request, Content $draft): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'type' => 'sometimes|string|in:social-post,blog-post,video,email',
            'media_url' => 'nullable|url',
            'image_url' => 'nullable|url',
            'platforms' => 'nullable|array',
            'platforms.*' => 'string|in:facebook,instagram,linkedin,twitter,x',
            'scheduled_at' => 'nullable|date',
        ]);

        $options = $draft->options ?? [];

        if (isset($validated['title'])) {
            $draft->title = $validated['title'];
        }
        if (isset($validated['content'])) {
            $draft->result = $validated['content'];
        }
        if (isset($validated['type'])) {
            $draft->type = $validated['type'];
        }
        if (isset($validated['media_url'])) {
            $options['media_url'] = $validated['media_url'];
        }
        if (isset($validated['image_url'])) {
            $options['image_url'] = $validated['image_url'];
        }
        if (isset($validated['platforms'])) {
            $options['platforms'] = $validated['platforms'];
        }
        if (isset($validated['scheduled_at'])) {
            $options['scheduled_at'] = $validated['scheduled_at'];
        }

        $draft->options = $options;
        $draft->save();

        return response()->json([
            'success' => true,
            'message' => 'Draft updated successfully',
            'draft' => [
                'id' => $draft->id,
                'title' => $draft->title,
                'status' => $draft->status,
            ],
        ]);
    }

    /**
     * Delete a draft
     * DELETE /api/content/drafts/{id}
     */
    public function deleteDraft(Content $draft): JsonResponse
    {
        $draft->delete();

        return response()->json([
            'success' => true,
            'message' => 'Draft deleted successfully',
        ]);
    }

    /**
     * Publish a draft to social platforms
     * POST /api/content/drafts/{id}/publish
     */
    public function publishDraft(Request $request, Content $draft): JsonResponse
    {
        $validated = $request->validate([
            'platforms' => 'required|array|min:1',
            'platforms.*' => 'string|in:facebook,instagram,linkedin,twitter,x',
            'scheduled_at' => 'nullable|string',
            'facebook_page_id' => 'nullable|string',
            'facebook_page_token' => 'nullable|string',
            'instagram_account_id' => 'nullable|string',
        ]);

        $platforms = $validated['platforms'];
        $scheduledAt = $validated['scheduled_at'] ?? 'now';
        $isImmediate = $scheduledAt === 'now';

        $totalTokenCost = count($platforms) * 5;

        if (! $this->tokenService->consume(auth()->user(), $totalTokenCost, 'social_deployment', ['content_id' => $draft->id])) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient tokens.',
            ], 402);
        }

        $results = [];
        $imageUrl = $draft->options['image_url'] ?? null;
        $mediaUrl = $draft->options['media_url'] ?? null;
        $finalMediaUrl = $mediaUrl ?? $imageUrl;

        foreach ($platforms as $platform) {
            $options = [
                'platform' => $platform,
                'scheduled_at' => $isImmediate ? now()->toDateTimeString() : $scheduledAt,
                'image_url' => $finalMediaUrl,
                'original_content_id' => $draft->id,
            ];

            if ($platform === 'facebook') {
                $options['page_id'] = $validated['facebook_page_id'] ?? null;
                $options['page_token'] = $validated['facebook_page_token'] ?? null;
            }

            if ($platform === 'instagram') {
                $options['instagram_id'] = $validated['instagram_account_id'] ?? null;
                $options['page_token'] = $validated['facebook_page_token'] ?? null;
            }

            $contentRecord = Content::create([
                'title' => ($isImmediate ? 'Published ' : 'Scheduled ').ucfirst($platform).' - '.Str::limit($draft->title, 20),
                'topic' => $draft->topic,
                'type' => 'social-post',
                'context' => $draft->context,
                'status' => 'scheduled',
                'result' => $draft->result,
                'options' => $options,
            ]);

            if ($isImmediate || Carbon::parse($scheduledAt)->isPast()) {
                if ($platform === 'facebook' && ! empty($options['page_id']) && ! empty($options['page_token'])) {
                    $fbResult = $this->socialPublishingService->postToFacebook($contentRecord);
                    $results['facebook'] = $fbResult;

                    if ($fbResult['success']) {
                        $currentOptions = $contentRecord->options;
                        $currentOptions['platform_post_id'] = $fbResult['id'];
                        $contentRecord->update([
                            'status' => 'published',
                            'result' => $draft->result."\n\n[Posted to Facebook: ".($fbResult['id'] ?? 'Success').']',
                            'options' => $currentOptions,
                        ]);

                        $this->fireWebhook('content.published', [
                            'content_id' => $contentRecord->id,
                            'original_content_id' => $draft->id,
                            'platform' => 'facebook',
                            'post_id' => $fbResult['id'],
                            'source' => $draft->options['source'] ?? 'manual',
                        ]);
                    } else {
                        $this->fireWebhook('content.failed', [
                            'content_id' => $contentRecord->id,
                            'original_content_id' => $draft->id,
                            'platform' => 'facebook',
                            'error' => $fbResult['error'] ?? 'Unknown error',
                            'source' => $draft->options['source'] ?? 'manual',
                        ]);
                    }
                }

                if ($platform === 'instagram' && ! empty($options['instagram_id']) && ! empty($options['page_token'])) {
                    $igResult = $this->socialPublishingService->postToInstagram($contentRecord, $options['instagram_id']);
                    $results['instagram'] = $igResult;

                    if ($igResult['success']) {
                        $currentOptions = $contentRecord->options;
                        $currentOptions['platform_post_id'] = $igResult['id'];
                        $contentRecord->update([
                            'status' => 'published',
                            'result' => $draft->result."\n\n[Posted to Instagram: ".($igResult['id'] ?? 'Success').']',
                            'options' => $currentOptions,
                        ]);

                        $this->fireWebhook('content.published', [
                            'content_id' => $contentRecord->id,
                            'original_content_id' => $draft->id,
                            'platform' => 'instagram',
                            'post_id' => $igResult['id'],
                            'source' => $draft->options['source'] ?? 'manual',
                        ]);
                    } else {
                        $this->fireWebhook('content.failed', [
                            'content_id' => $contentRecord->id,
                            'original_content_id' => $draft->id,
                            'platform' => 'instagram',
                            'error' => $igResult['error'] ?? 'Unknown error',
                            'source' => $draft->options['source'] ?? 'manual',
                        ]);
                    }
                }
            }
        }

        $draft->update(['status' => 'published']);

        return response()->json([
            'success' => true,
            'message' => $isImmediate ? 'Content published successfully' : 'Content scheduled successfully',
            'results' => $results,
        ]);
    }

    /**
     * Direct publish (skip draft)
     * POST /api/publish
     */
    public function directPublish(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'nullable|string|in:social-post,blog-post,video,email',
            'media_url' => 'nullable|url',
            'image_url' => 'nullable|url',
            'platforms' => 'required|array|min:1',
            'platforms.*' => 'string|in:facebook,instagram,linkedin,twitter,x',
            'scheduled_at' => 'nullable|string',
            'source' => 'nullable|string|in:n8n,openclaw,external',
            'facebook_page_id' => 'nullable|string',
            'facebook_page_token' => 'nullable|string',
            'instagram_account_id' => 'nullable|string',
        ]);

        $source = $validated['source'] ?? 'external';
        $scheduledAt = $validated['scheduled_at'] ?? 'now';
        $isImmediate = $scheduledAt === 'now';

        $content = Content::create([
            'title' => $validated['title'],
            'topic' => $validated['title'],
            'type' => $validated['type'] ?? 'social-post',
            'context' => null,
            'status' => 'draft',
            'result' => $validated['content'],
            'options' => [
                'source' => $source,
                'media_url' => $validated['media_url'] ?? null,
                'image_url' => $validated['image_url'] ?? null,
                'platforms' => $validated['platforms'],
                'scheduled_at' => $scheduledAt,
            ],
        ]);

        $request->merge(['content_id' => $content->id]);
        $request->merge(['segment_index' => 0]);
        $request->merge(['final_text' => $validated['content']]);

        $publishRequest = Request::create('/api/content/drafts/'.$content->id.'/publish', 'POST', [
            'platforms' => $validated['platforms'],
            'scheduled_at' => $scheduledAt,
            'facebook_page_id' => $validated['facebook_page_id'] ?? null,
            'facebook_page_token' => $validated['facebook_page_token'] ?? null,
            'instagram_account_id' => $validated['instagram_account_id'] ?? null,
        ]);

        return $this->publishDraft($publishRequest, $content);
    }

    /**
     * Get connected platforms
     * GET /api/platforms
     */
    public function getPlatforms(): JsonResponse
    {
        $path = storage_path('app/social_tokens.json');
        $platforms = [
            'facebook' => ['connected' => false, 'pages' => []],
            'instagram' => ['connected' => false, 'accounts' => []],
            'linkedin' => ['connected' => false],
            'twitter' => ['connected' => false],
        ];

        if (file_exists($path)) {
            $tokens = json_decode(file_get_contents($path), true);

            if (! empty($tokens['facebook'])) {
                $platforms['facebook']['connected'] = true;
            }

            if (! empty($tokens['instagram'])) {
                $platforms['instagram']['connected'] = true;
            }
        }

        return response()->json([
            'success' => true,
            'platforms' => $platforms,
        ]);
    }

    /**
     * Detect source from request headers or API key
     */
    protected function detectSource(Request $request): string
    {
        $sourceHeader = $request->header('X-Content-Source');
        if (in_array($sourceHeader, ['n8n', 'openclaw', 'external'])) {
            return $sourceHeader;
        }

        return 'external';
    }

    /**
     * Fire webhook to configured URL
     */
    protected function fireWebhook(string $event, array $data): void
    {
        $tenant = app(Tenant::class);
        $webhookUrl = $tenant?->settings['webhook_url'] ?? config('app.webhook_url');

        if (! $webhookUrl) {
            return;
        }

        $payload = [
            'event' => $event,
            'timestamp' => now()->toIso8601String(),
            'data' => $data,
        ];

        try {
            Http::timeout(10)
                ->post($webhookUrl, $payload);
        } catch (\Exception $e) {
            Log::warning("Webhook failed for event {$event}: ".$e->getMessage());
        }
    }
}
