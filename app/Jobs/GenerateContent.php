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

class GenerateContent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes

    public function __construct(
        protected Content $content,
        protected User $user,
        protected int $tokenCost
    ) {}

    public function handle(ContentService $contentService, TokenService $tokenService): void
    {
        // Set Tenant Context for Isolation
        if ($this->user->tenant) {
            app()->instance(\App\Models\Tenant::class, $this->user->tenant);
            session(['current_tenant_id' => $this->user->tenant_id]);
        }

        try {
            Log::info("Job processing content generation for Content ID: {$this->content->id}");
            
            // Execute Generation
            $generatedText = $contentService->generateText(
                $this->content->topic, 
                $this->content->type, 
                $this->content->context,
                $this->content->options ?? []
            );

            // Process Results (Title extraction, Word count)
            $lines = explode("\n", trim($generatedText));
            $title = !empty($lines[0]) ? str_replace(['#', '*', '='], '', $lines[0]) : $this->content->topic;
            if (strlen($title) > 100) $title = substr($title, 0, 97) . '...';

            $wordCount = str_word_count(strip_tags($generatedText));

            $this->content->update([
                'title' => $title,
                'result' => $generatedText,
                'word_count' => $wordCount,
                'status' => 'published',
            ]);

            Log::info("Content generated successfully for ID: {$this->content->id}");

        } catch (\Throwable $e) {
            Log::error("Content Generation Job Failed: " . $e->getMessage());
            
            // Refund tokens on failure
            $tokenService->grant($this->user->tenant, $this->tokenCost, 'refund_failed_generation');
            
            $this->content->update(['status' => 'failed']);
            
            throw $e;
        }
    }
}
