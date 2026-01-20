<?php

namespace App\Jobs;

use App\Services\VideoGenerationService;
use App\Models\Content;
use App\Models\MediaAsset;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RenderVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1200; // 20 minutes for video rendering

    public function __construct(
        protected Content $content,
        protected string $prompt,
        protected array $options
    ) {}

    public function handle(VideoGenerationService $videoService): void
    {
        Log::info("Job: Rendering Video for Content {$this->content->id}");

        try {
            // 1. Start Generation
            $taskId = $videoService->startGeneration($this->prompt, $this->options);
            
            // 2. Poll for Completion (Simplified for synchronous Job runner, ideally distinct scheduled job)
            // For MVP/Simulation, we'll wait a bit or just assume simulation is instant.
            
            $status = ['status' => 'processing'];
            $attempts = 0;
            
            while ($status['status'] !== 'completed' && $status['status'] !== 'failed' && $attempts < 20) {
                sleep(2); // Short sleep for simulation
                $status = $videoService->checkStatus($taskId);
                $attempts++;
            }

            if ($status['status'] === 'completed') {
                $videoUrl = $status['url'];
                
                // 3. Update Content
                $options = $this->content->options ?? [];
                $options['visuals'] = [$videoUrl]; // Assign to first slot
                
                $this->content->update([
                    'status' => 'published', // Ready for viewing
                    'result' => "Video Generated Successfully based on prompt: " . $this->prompt,
                    'options' => $options
                ]);

                // 4. Register in Media Registry
                MediaAsset::create([
                    'tenant_id' => $this->content->tenant_id,
                    'user_id' => $this->content->user_id ?? auth()->id(), // Job might not have auth context
                    'name' => 'Sora Render: ' . \Illuminate\Support\Str::limit($this->prompt, 20),
                    'url' => $videoUrl,
                    'type' => 'video',
                    'source' => 'ai_generation',
                    'prompt' => $this->prompt,
                    'metadata' => [
                        'generator' => 'Sora 2 (Simulated)',
                        'duration' => $this->options['duration'] ?? 10
                    ]
                ]);

                Log::info("Video Render Complete: $videoUrl");
            } else {
                throw new \Exception("Video rendering timed out or failed.");
            }

        } catch (\Exception $e) {
            Log::error("Video Render Failed: " . $e->getMessage());
            $this->content->update(['status' => 'failed']);
            // Refund tokens logic could go here
        }
    }
}