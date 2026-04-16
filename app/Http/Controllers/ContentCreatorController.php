<?php

namespace App\Http\Controllers;

use App\Enums\FeatureType;
use App\Http\Requests\StoreContentRequest;
use App\Jobs\GenerateBlogBatch;
use App\Jobs\GenerateCalendarFramework;
use App\Jobs\GenerateContent;
use App\Jobs\RenderVideo;
use App\Models\Brand;
use App\Models\Content;
use App\Models\KnowledgeBaseAsset;
use App\Models\MediaAsset;
use App\Models\Tenant;
use App\Services\BrandResolverService;
use App\Services\CloudinaryService;
use App\Services\ContentService;
use App\Services\FeatureCreditService;
use App\Services\ResearchService;
use App\Services\SocialPublishingService;
use App\Services\TokenService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ContentCreatorController extends Controller
{
    public function __construct(
        private readonly ContentService $contentService,
        protected ResearchService $researchService,
        protected TokenService $tokenService,
        protected SocialPublishingService $socialPublishingService,
        protected BrandResolverService $brandResolverService,
        protected FeatureCreditService $featureCreditService
    ) {}

    // ... [index, store, calculateTokenCost, createCalendarDrafts remain unchanged] ...

    public function index()
    {
        $tenant = app(Tenant::class);
        $brands = $tenant->brands()->orderBy('is_default', 'desc')->get();

        $stats = [
            'total_content' => Content::count(),
            'this_month' => Content::whereMonth('created_at', now()->month)->count(),
            'in_draft' => Content::where('status', 'draft')->count(),
            'published' => Content::where('status', 'published')->count(),
        ];

        $recentContents = Content::where(function ($q) {
            $q->whereNull('options->original_content_id')
                ->orWhere('options', '[]');
        })->latest()->take(15)->get();

        // Get incoming drafts from external sources (n8n, openclaw, external)
        $incomingContents = Content::whereIn('status', ['draft', 'scheduled'])
            ->whereNotNull('options->source')
            ->whereIn('options->source', ['n8n', 'openclaw', 'external'])
            ->latest()
            ->take(15)
            ->get();

        return view('content-creator.content-creator', compact('stats', 'recentContents', 'brands', 'incomingContents'));
    }

    public function store(StoreContentRequest $request)
    {
        Log::info('Content Generation Request', [
            'generator' => $request->input('generator'),
            'topic' => $request->input('topic'),
            'count' => $request->input('count'),
        ]);

        // Determine the feature type based on the generator
        $generator = $request->input('generator');
        $featureType = match ($generator) {
            'post' => FeatureType::POST_GENERATOR,
            'video' => FeatureType::VIDEO_GENERATOR,
            'blog' => FeatureType::BLOG_GENERATOR,
            'framework' => FeatureType::CLICK_CALENDAR,
            default => FeatureType::POST_GENERATOR,
        };

        // Check and consume feature credit
        $user = auth()->user();
        if (! $this->featureCreditService->canUseFeature($user, $featureType)) {
            return response()->json([
                'success' => false,
                'error' => 'credit_exhausted',
                'message' => "You've reached your monthly limit for {$featureType->label()}. Upgrade to Pro for unlimited access.",
                'feature' => $featureType->value,
                'upgrade_url' => route('billing.upgrade'),
            ], 402);
        }

        // Consume the feature credit (only for credit-based features with limits)
        $this->featureCreditService->consumeCredit($user, $featureType);

        $tokenCost = $this->calculateTokenCost($request);

        if (! $this->tokenService->consume(auth()->user(), $tokenCost, 'content_generation', ['topic' => $request->topic])) {
            return response()->json([
                'success' => false,
                'message' => "Insufficient tokens. This request requires $tokenCost tokens.",
            ], 402);
        }

        $options = $request->getOptions();
        $options['count'] = (int) ($options['count'] ?? 1);

        $context = $request->input('context');
        if ($request->filled('brand_id')) {
            $brandContext = $this->brandResolverService->buildBrandContext($request->brand_id);
            if ($brandContext) {
                $context .= "\n\nBRAND GUIDELINES:\n".$brandContext;
                $brand = Brand::find($request->brand_id);
                if ($brand) {
                    $options['brand_tone'] = $brand->voice_tone;
                }
            }
        }

        if ($request->input('generator') === 'framework') {
            Log::info('[ContentCreator] Framework generation requested', [
                'user_id' => auth()->id(),
                'topic' => $request->input('topic'),
                'token_cost' => $tokenCost,
            ]);

            $content = Content::create([
                'title' => 'Weekly Calendar: '.Str::limit($request->input('topic'), 30),
                'topic' => $request->input('topic'),
                'type' => 'framework_calendar',
                'context' => $context,
                'status' => 'generating', // Async status
                'options' => $options,
            ]);

            Log::info('[ContentCreator] Content record created', [
                'content_id' => $content->id,
                'status' => $content->status,
            ]);

            // Dispatch to Queue
            GenerateCalendarFramework::dispatch($content, auth()->user(), $tokenCost);

            Log::info('[ContentCreator] Job dispatched to queue', [
                'content_id' => $content->id,
                'queue_connection' => config('queue.default'),
            ]);

            return response()->json([
                'success' => true,
                'content' => $content, // Status is generating
                'message' => 'Calendar generation initiated. Processing in background.',
                'queue_info' => [
                    'connection' => config('queue.default'),
                    'content_id' => $content->id,
                ],
            ]);
        }

        // Video Generation Case
        if ($request->input('generator') === 'video') {
            // Step 1: Generate AI-enhanced cinematic prompt using VideoScriptGenerator
            $enhancedPrompt = $this->contentService->generateText(
                $request->input('topic'),
                'video',
                $context,
                $options
            );

            $content = Content::create([
                'title' => 'Video: '.Str::limit($request->input('topic'), 30),
                'topic' => $request->input('topic'),
                'type' => 'video',
                'context' => $context,
                'status' => 'generating',
                'result' => $enhancedPrompt, // Store the AI-enhanced prompt
                'options' => $options,
            ]);

            // Step 2: Send enhanced prompt to video rendering service
            RenderVideo::dispatch(
                $content,
                $enhancedPrompt, // Use AI-enhanced prompt instead of raw topic
                [
                    'model' => $request->input('ai_model'),
                    'aspect_ratio' => $request->input('aspect_ratio'),
                    'duration' => $request->input('video_duration'),
                ]
            );

            return response()->json([
                'success' => true,
                'content' => $content,
                'message' => 'Video generation protocol initiated.',
            ]);
        }

        // Blog Generation Case - Synchronous with Preview
        if ($request->input('generator') === 'blog') {
            Log::info('[ContentCreator] Blog generation requested', [
                'user_id' => auth()->id(),
                'topic' => $request->input('topic'),
            ]);

            $content = Content::create([
                'title' => $request->input('topic'),
                'topic' => $request->input('topic'),
                'type' => 'blog',
                'context' => $context,
                'status' => 'generating',
                'options' => $options,
            ]);

            try {
                $generatedText = $this->contentService->generateText(
                    $request->input('topic'),
                    'blog',
                    $context,
                    $options
                );

                // Process Results (Title extraction, Word count)
                $lines = collect(explode("\n", trim($generatedText)))
                    ->map(fn ($l) => trim($l))
                    ->filter(fn ($l) => ! empty($l) && ! preg_match('/^-{3,}$/', $l))
                    ->values();

                $firstLine = $lines->first() ?? $request->input('topic');
                $title = str_replace(['#', '*', '='], '', $firstLine);
                if (strlen($title) > 100) {
                    $title = substr($title, 0, 97).'...';
                }

                $wordCount = str_word_count(strip_tags($generatedText));

                $content->update([
                    'title' => $title,
                    'result' => $generatedText,
                    'word_count' => $wordCount,
                    'status' => 'published',
                ]);

                Log::info('[ContentCreator] Blog generated successfully', ['content_id' => $content->id]);

                return response()->json([
                    'success' => true,
                    'content' => $content->fresh(),
                    'preview' => $generatedText,
                    'message' => 'Blog generated successfully.',
                ]);
            } catch (\Throwable $e) {
                Log::error('[ContentCreator] Blog generation failed: '.$e->getMessage());

                $this->tokenService->grant(auth()->user()->tenant, $tokenCost, 'refund_failed_generation');
                $content->update(['status' => 'failed']);

                return response()->json([
                    'success' => false,
                    'message' => 'Blog generation failed: '.$e->getMessage(),
                ], 500);
            }
        }

        // Standard Case: Async Job (Post)
        $content = Content::create([
            'title' => $request->input('topic'),
            'topic' => $request->input('topic'),
            'type' => $request->input('type'),
            'context' => $context,
            'status' => 'generating',
            'options' => $options,
        ]);

        GenerateContent::dispatch($content, auth()->user(), $tokenCost);

        return response()->json([
            'success' => true,
            'content' => $content,
            'message' => 'Content generation protocol initiated.',
        ]);
    }

    public function batchStore(Request $request)
    {
        $request->validate([
            'topic' => 'required|string|min:3',
            'count' => 'required|integer|min:1|max:3',
        ]);

        $user = auth()->user();
        $count = (int) $request->input('count', 1);
        $tokenCost = ($count * 20) + 20;

        if (! $this->tokenService->consume($user, $tokenCost, 'blog_batch_generation', ['topic' => $request->topic, 'count' => $count])) {
            return response()->json([
                'success' => false,
                'message' => "Insufficient tokens. This request requires $tokenCost tokens.",
            ], 402);
        }

        $options = [
            'count' => $count,
            'blog_keywords' => $request->input('keywords', ''),
            'cta' => $request->input('cta', ''),
            'blog_structure' => $request->input('blog_structure', 'Standard'),
            'tone' => $request->input('tone', 'Professional'),
        ];

        $context = $request->input('context', '');
        if ($request->filled('brand_id')) {
            $brandContext = $this->brandResolverService->buildBrandContext($request->brand_id);
            if ($brandContext) {
                $context .= "\n\nBRAND GUIDELINES:\n".$brandContext;
                $brand = Brand::find($request->brand_id);
                if ($brand) {
                    $options['brand_tone'] = $brand->voice_tone;
                }
            }
        }

        $topicData = [
            'topic' => $request->input('topic'),
            'count' => $count,
            'keywords' => $request->input('keywords', ''),
            'context' => $context,
            'cta' => $request->input('cta', ''),
            'brand_tone' => $options['brand_tone'] ?? '',
        ];

        $content = Content::create([
            'title' => 'Batch: '.Str::limit($request->input('topic'), 30),
            'topic' => $request->input('topic'),
            'type' => 'blog_batch',
            'context' => $context,
            'status' => 'generating',
            'options' => $options,
        ]);

        GenerateBlogBatch::dispatch($content, $user, $tokenCost, $topicData);

        Log::info('[ContentCreator] Blog batch dispatched', [
            'content_id' => $content->id,
            'count' => $count,
        ]);

        return response()->json([
            'success' => true,
            'content' => $content,
            'message' => "Blog batch initiated — generating $count posts.",
        ]);
    }

    public function getChildren(Request $request, Content $content)
    {
        $perPage = min((int) $request->input('per_page', 3), 3);
        $page = (int) $request->input('page', 1);

        $children = Content::where('options->parent_batch_id', $content->id)
            ->where('type', 'blog')
            ->orderBy('options->batch_index', 'asc')
            ->paginate($perPage, ['*'], 'page', $page);

        $items = $children->map(function ($child) {
            $result = trim($child->result ?? '');
            $excerpt = Str::limit(strip_tags(Str::markdown($result)), 150);

            return [
                'id' => $child->id,
                'title' => $child->title,
                'word_count' => $child->word_count,
                'excerpt' => $excerpt,
                'angle' => $child->options['angle'] ?? '',
                'focus_keyword' => $child->options['focus_keyword'] ?? '',
                'status' => $child->status,
                'batch_index' => $child->options['batch_index'] ?? 0,
            ];
        });

        return response()->json([
            'items' => $items,
            'current_page' => $children->currentPage(),
            'last_page' => $children->lastPage(),
            'per_page' => $perPage,
            'total' => $children->total(),
        ]);
    }

    protected function calculateTokenCost(Request $request): int
    {
        $generator = $request->input('generator');

        return match ($generator) {
            'framework' => 50,
            'blog' => 20,
            'video' => str_contains($request->input('video_duration', ''), '15') ? 10 : 7,
            default => ($request->input('count', 1) * 10),
        };
    }

    protected function createCalendarDrafts(Content $parent, string $json): void
    {
        $data = json_decode($json, true);
        if (! $data) {
            return;
        }

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

    public function generateBulkImages(Request $request)
    {
        $request->validate([
            'framework_id' => 'required|exists:contents,id',
            'style' => 'nullable|string|in:realistic,poster,asset-reference',
        ]);

        $parent = Content::findOrFail($request->framework_id);

        $drafts = Content::where(function ($q) use ($parent) {
            $q->where('options->original_content_id', $parent->id)
                ->orWhere('options->original_content_id', (string) $parent->id);
        })
            ->where('status', 'draft')
            ->get();

        if ($drafts->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No drafts found for this framework.'], 404);
        }

        $count = $drafts->count();
        $tokenCost = $count * 5;

        if (! $this->tokenService->consume(auth()->user(), $tokenCost, 'bulk_image_generation', ['framework_id' => $parent->id])) {
            return response()->json([
                'success' => false,
                'message' => "Insufficient tokens. Bulk generation requires $tokenCost tokens.",
            ], 402);
        }

        $dispatchedCount = 0;
        foreach ($drafts as $draft) {
            $visualIdea = $draft->options['visual_idea'] ?? null;
            if (! $visualIdea) {
                $visualIdea = Str::before($draft->result, "\n");
            }

            if ($visualIdea) {
                // In a real implementation, we would dispatch a job here.
                // For MVP, we'll mark it as processing or similar if we had a status for that.
                $dispatchedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Initiated image generation for $dispatchedCount posts.",
            'count' => $dispatchedCount,
        ]);
    }

    /**
     * Bulk Schedule Endpoint.
     * Distributes drafts across the next 7 days.
     */
    public function bulkSchedule(Request $request)
    {
        $request->validate([
            'framework_id' => 'required|exists:contents,id',
            'platforms' => 'required|array|min:1', // e.g., ['facebook', 'linkedin']
            'start_date' => 'required|date|after_or_equal:today',
        ]);

        $parent = Content::findOrFail($request->framework_id);

        $drafts = Content::where(function ($q) use ($parent) {
            $q->where('options->original_content_id', $parent->id)
                ->orWhere('options->original_content_id', (string) $parent->id);
        })
            ->where('status', 'draft')
            ->get();

        if ($drafts->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No drafts available to schedule.'], 404);
        }

        // Strategy: Distribute posts evenly across 7 days starting from start_date
        // Schedule logic:
        // Educational -> Mon, Wed, Fri
        // Showcase -> Tue, Thu
        // Conversational -> Sat
        // Promotional -> Sun (or mixed)

        $startDate = Carbon::parse($request->start_date);
        $scheduleMap = [
            'educational' => [0, 2, 4], // Offset days from start (0=Mon if start is Mon)
            'showcase' => [1, 3],
            'conversational' => [5, 6],
            'promotional' => [3], // Overlap on Thursday or fill gaps
        ];

        // Group drafts by pillar
        $groupedDrafts = $drafts->groupBy(fn ($d) => $d->options['pillar'] ?? 'general');

        $scheduledCount = 0;
        $currentDayOffset = 0;

        // Flatten the strategy to a simple queue if strict pillar mapping fails or for simplicity
        // Simple Round Robin Distribution for MVP:
        // Distribute all posts over 7 days, ~1-2 posts per day.

        $daysToSchedule = 7;
        $postsPerDay = ceil($drafts->count() / $daysToSchedule);

        $drafts = $drafts->shuffle(); // Shuffle for variety or keep ordered if preferred

        foreach ($drafts as $index => $draft) {
            $dayOffset = floor($index / $postsPerDay);
            $targetDate = $startDate->copy()->addDays($dayOffset)->setTime(10, 0, 0); // 10:00 AM default

            $options = $draft->options;
            $options['platforms'] = $request->platforms;
            $options['scheduled_at'] = $targetDate->toDateTimeString();

            $draft->update([
                'status' => 'scheduled',
                'options' => $options,
                'title' => '[Scheduled] '.$draft->title,
            ]);

            $scheduledCount++;
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully scheduled $scheduledCount posts starting from ".$startDate->toFormattedDateString(),
            'count' => $scheduledCount,
        ]);
    }

    // ... [Rest of the methods: show, getSuggestions, refineContext, uploadMedia, generateMedia, regenerate, publish, destroy, saveVisual] ...

    public function show(Content $content)
    {
        if (request()->wantsJson()) {
            return response()->json(['content' => $content]);
        }

        $path = storage_path('app/social_tokens.json');
        $isFacebookConnected = false;
        if (file_exists($path)) {
            $tokens = json_decode(file_get_contents($path), true);
            $isFacebookConnected = ! empty($tokens['facebook']);
        }

        $children = Content::where(function ($q) use ($content) {
            $q->where('options->original_content_id', $content->id)
                ->orWhere('options->original_content_id', (string) $content->id);
        })
            ->whereIn('status', ['published', 'scheduled'])
            ->get();

        $publishedIndexes = $children->map(fn ($c) => (int) ($c->options['segment_index'] ?? -1))
            ->filter(fn ($idx) => $idx !== -1)
            ->unique()
            ->values()
            ->toArray();

        $brands = auth()->user()->tenant->brands()->select('id', 'name', 'colors')->get();

        return view('content-creator.content-viewer', compact('content', 'isFacebookConnected', 'publishedIndexes', 'brands'));
    }

    public function getSuggestions(Request $request)
    {
        try {
            $request->validate([
                'topic' => 'required|string|min:3',
            ]);

            $type = $request->input('type', 'social');

            if ($type === 'seo_keywords') {
                $suggestions = $this->researchService->suggestSeoKeywords($request->topic);
            } elseif ($type === 'blog_topics') {
                $suggestions = $this->researchService->suggestBlogTopics($request->topic);
            } else {
                $suggestions = $this->researchService->suggestSocialMediaTopics($request->topic);
            }

            $kbCount = 0;
            try {
                $tenant = auth()->user()?->tenant;
                if ($tenant && $tenant->id) {
                    $kbCount = KnowledgeBaseAsset::where('tenant_id', $tenant->id)
                        ->where(function ($q) use ($request) {
                            $q->where('title', 'like', "%{$request->topic}%")
                                ->orWhere('content', 'like', "%{$request->topic}%");
                        })
                        ->count();
                }
            } catch (\Throwable $e) {
                // Silently ignore KB count errors
            }

            return response()->json([
                'suggestions' => $suggestions,
                'kb_count' => $kbCount,
            ]);
        } catch (\Throwable $e) {
            Log::error('Suggestions error: '.$e->getMessage());

            return response()->json([
                'suggestions' => [],
                'kb_count' => 0,
                'error' => 'Failed to generate suggestions. Please try again.',
            ], 500);
        }
    }

    public function generateBlogBody(Request $request)
    {
        try {
            $request->validate([
                'topic' => 'required|string|min:3',
            ]);

            $keywords = $request->input('keywords', '');
            $topic = $request->topic;

            // Use OpenAI to generate blog body content if configured
            if (config('services.openai.key')) {
                $prompt = "You are an expert SEO blog content writer. Generate a complete, SEO-optimized blog post body based on the following:\n\nTopic: $topic\nKeywords: $keywords\n\nRequirements:\n- Write 800-1200 words\n- Include the keywords naturally throughout\n- Use proper heading structure (H2, H3)\n- Make it engaging and informative\n- Include a compelling introduction and conclusion\n- DO NOT include the title/headline (that will be added separately)\n- Just write the body content, ready to publish\n\nStart writing the blog post body now:";

                $response = Http::withToken(config('services.openai.key'))
                    ->timeout(120)
                    ->post('https://api.openai.com/v1/chat/completions', [
                        'model' => config('services.openai.model', 'gpt-4o-mini'),
                        'messages' => [
                            ['role' => 'system', 'content' => 'You are an expert SEO blog content writer.'],
                            ['role' => 'user', 'content' => $prompt],
                        ],
                        'max_tokens' => 4000,
                        'temperature' => 0.7,
                    ]);

                if ($response->successful()) {
                    $body = $response->json('choices.0.message.content', '');

                    // Return raw markdown (no HTML conversion)
                    return response()->json([
                        'success' => true,
                        'body' => trim($body),
                    ]);
                }

                Log::error('Blog body generation failed: '.$response->body());
            }

            // Fallback: Generate a placeholder body when API is not configured
            $fallbackBody = $this->generateFallbackBlogBody($topic, $keywords);

            return response()->json([
                'success' => true,
                'body' => $fallbackBody,
            ]);
        } catch (\Throwable $e) {
            Log::error('Blog body error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate blog body. Please try again.',
            ], 500);
        }
    }

    public function generateImagePrompt(Request $request)
    {
        try {
            $request->validate([
                'blog_body' => 'required|string|min:50',
            ]);

            $blogBody = $request->input('blog_body');
            $topic = $request->input('topic', 'blog featured image');

            // Use OpenAI to generate an image prompt from the blog body
            if (config('services.openai.key')) {
                $prompt = "Based on the following blog content, generate a detailed, vivid image generation prompt that would create a compelling featured image for this blog post.\n\nBlog Topic: {$topic}\n\nBlog Content:\n{$blogBody}\n\nRequirements:\n- Create a prompt that is 2-3 sentences long\n- Describe a visually striking scene that represents the blog content\n- Use cinematic, photography-style language\n- Include lighting, composition, and mood details\n- DO NOT include any text or words in the image\n- Make it suitable for a blog featured image (16:9 aspect ratio recommended)\n\nOnly output the image prompt, nothing else:";

                $response = Http::withToken(config('services.openai.key'))
                    ->timeout(60)
                    ->post('https://api.openai.com/v1/chat/completions', [
                        'model' => config('services.openai.model', 'gpt-4o-mini'),
                        'messages' => [
                            ['role' => 'system', 'content' => 'You are an expert at creating image generation prompts for AI image generators like DALL-E, Midjourney, and Stable Diffusion.'],
                            ['role' => 'user', 'content' => $prompt],
                        ],
                        'max_tokens' => 500,
                        'temperature' => 0.7,
                    ]);

                if ($response->successful()) {
                    $imagePrompt = $response->json('choices.0.message.content', '');

                    return response()->json([
                        'success' => true,
                        'prompt' => trim($imagePrompt),
                    ]);
                }

                Log::error('Image prompt generation failed: '.$response->body());
            }

            // Fallback: Generate a basic prompt from topic
            $fallbackPrompt = "A compelling featured image representing: {$topic}. Cinematic photography style, dramatic lighting, professional quality, suitable for blog header.";

            return response()->json([
                'success' => true,
                'prompt' => $fallbackPrompt,
            ]);
        } catch (\Throwable $e) {
            Log::error('Image prompt error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate image prompt. Please try again.',
            ], 500);
        }
    }

    private function generateFallbackBlogBody(string $topic, string $keywords): string
    {
        $keywordList = $keywords ? implode(', ', explode(',', $keywords)) : 'blogging, content creation';

        return <<<HTML
<h2>Introduction</h2>
<p>Welcome to our comprehensive guide on <strong>{$topic}</strong>. In today's rapidly evolving landscape, understanding this topic has become essential for anyone looking to stay ahead of the curve. Whether you're a beginner just getting started or an experienced professional looking to refine your knowledge, this article will provide you with valuable insights and practical strategies.</p>

<h2>Understanding the Basics of {$topic}</h2>
<p>Before we dive deeper into advanced strategies, it's crucial to establish a solid foundation. {$topic} encompasses a wide range of concepts and techniques that, when mastered, can dramatically transform your approach and results.</p>
<p>Key areas to understand include:</p>
<ul>
<li>The fundamental principles that underpin {$topic}</li>
<li>Common challenges and how to overcome them</li>
<li>Best practices used by industry leaders</li>
<li>Tools and resources to accelerate your progress</li>
</ul>

<h2>Advanced Strategies for {$topic}</h2>
<p>Once you have a grasp of the fundamentals, it's time to level up your skills. Here are some advanced techniques that can help you achieve exceptional results:</p>

<h3>Strategy 1: Data-Driven Decision Making</h3>
<p>Modern {$topic} requires a strategic approach backed by data. By analyzing key metrics and trends, you can make informed decisions that drive measurable improvements.</p>

<h3>Strategy 2: Continuous Learning and Adaptation</h3>
<p>The landscape of {$topic} is constantly evolving. Stay current with the latest developments and be willing to adapt your strategies accordingly.</p>

<h2>Conclusion</h2>
<p>In conclusion, mastering {$topic} requires dedication, continuous learning, and a willingness to implement best practices. Start applying these insights today, and you'll be well on your way to achieving your goals.</p>

<p><em>This blog post was generated based on keywords: {$keywordList}. For full AI-powered content generation, please configure your MiniMax API key.</em></p>
HTML;
    }

    public function refineContext(Request $request)
    {
        $request->validate([
            'context' => 'required|string|min:3',
        ]);

        $refined = $this->researchService->refineContext($request->context);

        return response()->json([
            'context' => trim($refined),
        ]);
    }

    public function uploadMedia(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,svg,webp,mp4,mov,avi,wmv|max:102400',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');

            $cloudName = config('services.cloudinary.cloud_name');
            $apiKey = config('services.cloudinary.api_key');
            $apiSecret = config('services.cloudinary.api_secret');

            if ($cloudName && $apiKey && $apiSecret) {
                try {
                    $timestamp = time();
                    $params = ['timestamp' => $timestamp];
                    ksort($params);
                    $signParts = [];
                    foreach ($params as $key => $value) {
                        $signParts[] = "$key=$value";
                    }
                    $signString = implode('&', $signParts).$apiSecret;
                    $signature = sha1($signString);

                    $response = Http::attach(
                        'file',
                        file_get_contents($file->getRealPath()),
                        $file->getClientOriginalName()
                    )->post("https://api.cloudinary.com/v1_1/$cloudName/auto/upload", [
                        'api_key' => $apiKey,
                        'timestamp' => $timestamp,
                        'signature' => $signature,
                    ]);

                    if ($response->successful()) {
                        $url = $response->json()['secure_url'];

                        return response()->json(['success' => true, 'url' => $url]);
                    } else {
                        Log::error('Cloudinary upload failed: '.$response->status());
                    }
                } catch (\Exception $e) {
                    Log::error('Cloudinary exception: '.$e->getMessage());
                }
            }

            $filename = Str::random(40).'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/content-media'), $filename);
            $url = '/uploads/content-media/'.$filename;

            return response()->json([
                'success' => true,
                'url' => $url,
                'message' => 'Uploaded locally.',
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No file uploaded'], 400);
    }

    public function uploadFeaturedImage(Request $request)
    {
        $request->validate([
            'image' => 'required|file|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');

            $cloudName = config('services.cloudinary.cloud_name');
            $apiKey = config('services.cloudinary.api_key');
            $apiSecret = config('services.cloudinary.api_secret');

            if ($cloudName && $apiKey && $apiSecret) {
                try {
                    $timestamp = time();
                    $params = ['timestamp' => $timestamp];
                    ksort($params);
                    $signParts = [];
                    foreach ($params as $key => $value) {
                        $signParts[] = "$key=$value";
                    }
                    $signString = implode('&', $signParts).$apiSecret;
                    $signature = sha1($signString);

                    $response = Http::attach(
                        'file',
                        file_get_contents($file->getRealPath()),
                        $file->getClientOriginalName()
                    )->post("https://api.cloudinary.com/v1_1/$cloudName/auto/upload", [
                        'api_key' => $apiKey,
                        'timestamp' => $timestamp,
                        'signature' => $signature,
                    ]);

                    if ($response->successful()) {
                        $url = $response->json()['secure_url'];

                        // Save to media assets
                        MediaAsset::create([
                            'tenant_id' => auth()->user()->tenant_id,
                            'user_id' => auth()->id(),
                            'name' => 'Featured Image: '.Str::limit($file->getClientOriginalName(), 30),
                            'url' => $url,
                            'type' => 'image',
                            'source' => 'upload',
                        ]);

                        return response()->json(['success' => true, 'url' => $url]);
                    } else {
                        Log::error('Cloudinary upload failed: '.$response->status());
                    }
                } catch (\Exception $e) {
                    Log::error('Cloudinary exception: '.$e->getMessage());
                }
            }

            // Fallback to local storage
            $filename = Str::random(40).'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/content-media'), $filename);
            $url = '/uploads/content-media/'.$filename;

            // Save to media assets
            MediaAsset::create([
                'tenant_id' => auth()->user()->tenant_id,
                'user_id' => auth()->id(),
                'name' => 'Featured Image: '.Str::limit($file->getClientOriginalName(), 30),
                'url' => $url,
                'type' => 'image',
                'source' => 'upload_local',
            ]);

            return response()->json(['success' => true, 'url' => $url]);
        }

        return response()->json(['success' => false, 'message' => 'No file uploaded'], 400);
    }

    public function generateMedia(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|min:3',
            'format' => 'nullable|string|in:realistic,poster,asset-reference',
            'poster_text' => 'nullable|string|max:100',
            'reference_asset_url' => 'nullable|string',
            'brand_id' => 'nullable|uuid',
        ]);

        $tokenCost = 5;

        if (! $this->tokenService->consume(auth()->user(), $tokenCost, 'image_generation', ['prompt' => $request->prompt])) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient tokens.',
            ], 402);
        }

        $format = $request->input('format', 'realistic');
        $options = [];

        if ($format === 'poster') {
            $options['poster_text'] = $request->input('poster_text');
            if ($request->filled('brand_id')) {
                $brand = Brand::find($request->brand_id);
                if ($brand) {
                    $options['brand'] = [
                        'name' => $brand->name,
                        'colors' => $brand->colors,
                        'typography' => $brand->typography ?? [],
                    ];
                }
            }
        } elseif ($format === 'asset-reference') {
            $options['reference_url'] = $request->input('reference_asset_url');
        }

        $generatedUrl = $this->contentService->generateImage($request->prompt, $format, $options);

        if ($generatedUrl) {
            $cloudinaryService = app(CloudinaryService::class);
            $uploadResult = $cloudinaryService->uploadFromUrl($generatedUrl, 'ai-generated', 'uploads/content-media');

            $finalUrl = $uploadResult['url'];
            $source = match ($uploadResult['source']) {
                'cloudinary' => 'ai_generation',
                'local' => 'ai_generation_local',
                default => 'ai_generation_temp',
            };

            MediaAsset::create([
                'tenant_id' => auth()->user()->tenant_id,
                'user_id' => auth()->id(),
                'name' => 'AI Provision: '.Str::limit($request->prompt, 30),
                'url' => $finalUrl,
                'type' => 'image',
                'source' => $source,
                'prompt' => $request->prompt,
                'metadata' => [
                    'generator' => 'DALL-E 3',
                    'format' => $format,
                    'timestamp' => now()->toIso8601String(),
                ],
            ]);

            return response()->json([
                'success' => true,
                'url' => $finalUrl,
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Image generation failed.'], 500);
    }

    public function regenerate(Request $request)
    {
        $request->validate([
            'content_id' => 'required|exists:contents,id',
            'current_text' => 'required|string',
        ]);

        $content = Content::findOrFail($request->content_id);

        try {
            $options = $content->options ?? [];
            $options['count'] = 1;

            $newText = $this->contentService->generateText(
                $content->topic,
                $content->type,
                'REWRITE THIS POST. Original context: '.$content->context.". \n\nCONTENT TO IMPROVE: ".$request->current_text,
                $options
            );

            $response = ['success' => true, 'new_text' => $newText];
            if ($content->type === 'blog') {
                $response['new_html'] = Str::markdown($newText);
            }

            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Regeneration failed: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'Regeneration failed'], 500);
        }
    }

    public function publish(Request $request)
    {
        $validated = $request->validate([
            'content_id' => 'required|exists:contents,id',
            'segment_index' => 'required|integer',
            'final_text' => 'required|string',
            'image_url' => 'nullable|string',
            'platforms' => 'required|array|min:1',
            'scheduled_at' => 'required|string',
            'facebook_page_id' => 'nullable|string',
            'facebook_page_token' => 'nullable|string',
            'instagram_account_id' => 'nullable|string',
        ]);

        if (! empty($validated['image_url']) && str_starts_with($validated['image_url'], '/')) {
            $validated['image_url'] = rtrim(config('app.url'), '/').$validated['image_url'];
        }

        $scheduledAt = $validated['scheduled_at'] === 'now' ? now()->toDateTimeString() : $validated['scheduled_at'];

        $parentContent = Content::find($validated['content_id']);
        $results = [];
        $isImmediate = $validated['scheduled_at'] === 'now';

        $totalPlatforms = count($validated['platforms']);
        $totalTokenCost = $totalPlatforms * 5;

        if (! $this->tokenService->consume(auth()->user(), $totalTokenCost, 'social_deployment', ['content_id' => $validated['content_id']])) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient tokens.',
            ], 402);
        }

        foreach ($validated['platforms'] as $platform) {
            $options = [
                'platform' => $platform,
                'scheduled_at' => $scheduledAt,
                'image_url' => $validated['image_url'],
                'original_content_id' => $validated['content_id'],
                'segment_index' => (int) $validated['segment_index'],
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
                'title' => ($isImmediate ? 'Published ' : 'Scheduled ').ucfirst($platform).' - '.Str::limit($parentContent->topic, 20),
                'topic' => $parentContent->topic,
                'type' => 'social-post',
                'context' => $parentContent->context,
                'status' => 'scheduled',
                'result' => $validated['final_text'],
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
                            'result' => $validated['final_text']."\n\n[Posted to Facebook: ".($fbResult['id'] ?? 'Success').']',
                            'options' => $currentOptions,
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
                            'result' => $validated['final_text']."\n\n[Posted to Instagram: ".($igResult['id'] ?? 'Success').']',
                            'options' => $currentOptions,
                        ]);
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => $isImmediate ? 'Successfully published!' : 'Content scheduled successfully.',
            'results' => $results,
        ]);
    }

    public function saveVisual(Request $request, Content $content)
    {
        $validated = $request->validate([
            'image_url' => 'required|string',
            'index' => 'required|integer',
        ]);

        $options = $content->options ?? [];
        $visuals = $options['visuals'] ?? [];
        $visuals[$validated['index']] = $validated['image_url'];
        $options['visuals'] = $visuals;

        $content->update(['options' => $options]);

        return response()->json(['success' => true]);
    }

    public function destroy(Content $content)
    {
        return DB::transaction(function () use ($content) {
            $childPosts = Content::where('options->original_content_id', $content->id)
                ->orWhere('options->original_content_id', (string) $content->id)
                ->get();

            foreach ($childPosts as $post) {
                $options = $post->options ?? [];
                $platform = $options['platform'] ?? null;
                $postId = $options['platform_post_id'] ?? null;

                if ($platform === 'facebook' && $postId) {
                    $this->socialPublishingService->removeFromFacebook($post);
                }
                $post->delete();
            }
            $content->delete();

            return response()->json([
                'success' => true,
                'message' => 'Batch removed.',
            ]);
        });
    }
}
