<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VideoGenerationService
{
    protected string $apiKey;
    protected string $endpoint;

    public function __construct()
    {
        // Configurable for Sora, Runway, or Stable Video
        $this->apiKey = config('services.video_ai.key', '');
        $this->endpoint = config('services.video_ai.endpoint', 'https://api.openai.com/v1/videos/generations'); 
    }

    /**
     * Initiate a video generation task.
     * Returns a task ID or polling URL.
     */
    public function startGeneration(string $prompt, array $options = []): string
    {
        $model = $options['model'] ?? 'sora-1.0-turbo';
        $aspectRatio = $options['aspect_ratio'] ?? '9:16';
        $duration = $options['duration'] ?? 10; // seconds

        Log::info("Starting Video Generation: [$model] $prompt ($duration s, $aspectRatio)");

        if (!$this->apiKey) {
            // Simulation Mode for Development
            Log::warning("No Video AI API Key found. Simulating generation.");
            return 'simulated_task_' . uniqid();
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->post($this->endpoint, [
                    'model' => $model,
                    'prompt' => $prompt,
                    'size' => $this->mapAspectRatioToResolution($aspectRatio),
                    'duration' => $duration,
                    'response_format' => 'url'
                ]);

            if ($response->successful()) {
                // Assuming sync response or job ID depending on provider
                return $response->json('id') ?? $response->json('url');
            }

            throw new \Exception("Video API Error: " . $response->body());
        } catch (\Exception $e) {
            Log::error("Video Generation Exception: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Check status of a generation task.
     */
    public function checkStatus(string $taskId): array
    {
        if (str_starts_with($taskId, 'simulated_task_')) {
            // Simulate completion after random time
            return [
                'status' => 'completed',
                'url' => 'https://res.cloudinary.com/demo/video/upload/v1690567890/samples/cld-sample-video.mp4', // Placeholder
                'progress' => 100
            ];
        }

        // Real API check implementation would go here
        return ['status' => 'processing', 'progress' => 50];
    }

    protected function mapAspectRatioToResolution(string $ratio): string
    {
        return match($ratio) {
            'Landscape' => '1920x1080',
            'Portrait' => '1080x1920',
            'Square' => '1080x1080',
            default => '1080x1920',
        };
    }
}
