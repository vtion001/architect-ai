<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Content;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Centralized service for social media publishing operations.
 * Handles Facebook, Instagram, and future platform integrations.
 */
class SocialPublishingService
{
    /**
     * Post content to Facebook Page.
     */
    public function postToFacebook(Content $content): array
    {
        $options = $content->options ?? [];
        $pageId = $options['page_id'] ?? null;
        $token = $options['page_token'] ?? null;

        if (! $pageId || ! $token) {
            return ['success' => false, 'error' => 'Missing Page ID or Access Token'];
        }

        $message = $this->cleanMarkdownForSocial($content->result ?? '');
        $imageUrl = $options['image_url'] ?? null;

        try {
            if ($imageUrl) {
                if ($this->isVideo($imageUrl)) {
                    // Video Post
                    $response = Http::post("https://graph-video.facebook.com/v18.0/$pageId/videos", [
                        'file_url' => $imageUrl,
                        'description' => $message,
                        'access_token' => $token,
                    ]);
                } else {
                    // Photo Post
                    $response = Http::post("https://graph.facebook.com/v18.0/$pageId/photos", [
                        'url' => $imageUrl,
                        'message' => $message,
                        'access_token' => $token,
                    ]);
                }
            } else {
                // Text Post
                $response = Http::post("https://graph.facebook.com/v18.0/$pageId/feed", [
                    'message' => $message,
                    'access_token' => $token,
                ]);
            }

            $data = $response->json();

            if (isset($data['id'])) {
                return ['success' => true, 'id' => $data['id']];
            }

            Log::error('FB Post Error: '.json_encode($data));

            return ['success' => false, 'error' => $data['error']['message'] ?? 'Unknown error'];

        } catch (\Exception $e) {
            Log::error('FB Exception: '.$e->getMessage());

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Post content to Instagram via Facebook Graph API.
     */
    public function postToInstagram(Content $content, string $igUserId): array
    {
        $options = $content->options ?? [];
        $token = $options['page_token'] ?? null;
        $imageUrl = $options['image_url'] ?? null;
        $caption = $this->cleanMarkdownForSocial($content->result ?? '');

        if (! $token || ! $imageUrl) {
            return ['success' => false, 'error' => 'Instagram requires an image/video and a valid token.'];
        }

        // Normalize relative URLs
        if (str_starts_with($imageUrl, '/')) {
            $imageUrl = rtrim(config('app.url'), '/').$imageUrl;
        }

        Log::info("Posting to IG ($igUserId) with Media: $imageUrl");

        try {
            $params = [
                'caption' => $caption,
                'access_token' => $token,
            ];

            if ($this->isVideo($imageUrl)) {
                $params['media_type'] = 'VIDEO';
                $params['video_url'] = $imageUrl;
            } else {
                $params['image_url'] = $imageUrl;
            }

            // Step 1: Create media container
            $response = Http::post("https://graph.facebook.com/v18.0/$igUserId/media", $params);
            $containerData = $response->json();

            Log::info('IG Container Response: '.json_encode($containerData));

            if (! isset($containerData['id'])) {
                return [
                    'success' => false,
                    'error' => 'Container Create Failed: '.($containerData['error']['message'] ?? json_encode($containerData)),
                ];
            }

            $creationId = $containerData['id'];

            // Wait for processing (videos take longer)
            sleep($this->isVideo($imageUrl) ? 10 : 5);

            // Step 2: Publish the container
            $publishResponse = Http::post("https://graph.facebook.com/v18.0/$igUserId/media_publish", [
                'creation_id' => $creationId,
                'access_token' => $token,
            ]);

            $publishData = $publishResponse->json();
            Log::info('IG Publish Response: '.json_encode($publishData));

            if (isset($publishData['id'])) {
                return ['success' => true, 'id' => $publishData['id']];
            }

            return [
                'success' => false,
                'error' => 'Publish Failed: '.($publishData['error']['message'] ?? json_encode($publishData)),
            ];

        } catch (\Exception $e) {
            Log::error('IG Exception: '.$e->getMessage());

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Remove content from Facebook.
     */
    public function removeFromFacebook(Content $content): bool
    {
        $options = $content->options ?? [];
        $postId = $options['platform_post_id'] ?? null;
        $token = $options['page_token'] ?? null;

        if (! $postId || ! $token) {
            Log::warning("Skipping Facebook removal for Content ID: {$content->id} - missing data.");

            return false;
        }

        try {
            $response = Http::delete("https://graph.facebook.com/v18.0/$postId", [
                'access_token' => $token,
            ]);

            if ($response->successful()) {
                Log::info("Successfully deleted Facebook Post: $postId");

                return true;
            }

            Log::error("Facebook Delete API Error (ID $postId): ".$response->body());

            return false;

        } catch (\Exception $e) {
            Log::error("Exception deleting Facebook post $postId: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Check if URL points to a video file.
     */
    public function isVideo(string $url): bool
    {
        return preg_match('/\.(mp4|mov|avi|wmv|webm)$/i', $url) === 1;
    }

    /**
     * Clean markdown formatting for social media platforms.
     */
    public function cleanMarkdownForSocial(string $text): string
    {
        // Remove bold/italic markers
        $text = str_replace(['**', '*', '__', '_'], '', $text);

        // Remove markdown headers (e.g., # Header or ## Header)
        $text = preg_replace('/^#+\s+/m', '', $text);

        // Remove code block markers
        $text = str_replace('```', '', $text);
        $text = preg_replace('/`(.+?)`/', '$1', $text);

        // Remove trailing or leading whitespace
        return trim($text);
    }
}
