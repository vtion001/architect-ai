<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Cloudinary file upload service.
 * 
 * Centralizes all Cloudinary operations for consistency and reusability.
 * Follows the Service Layer Pattern.
 */
class CloudinaryService
{
    protected ?string $cloudName;
    protected ?string $apiKey;
    protected ?string $apiSecret;

    public function __construct()
    {
        $this->cloudName = config('services.cloudinary.cloud_name');
        $this->apiKey = config('services.cloudinary.api_key');
        $this->apiSecret = config('services.cloudinary.api_secret');
    }

    /**
     * Check if Cloudinary is configured.
     */
    public function isConfigured(): bool
    {
        return !empty($this->cloudName) && !empty($this->apiKey) && !empty($this->apiSecret);
    }

    /**
     * Upload a file to Cloudinary.
     *
     * @param UploadedFile|string $file File object or URL to upload
     * @param string $folder Folder in Cloudinary
     * @param array $options Additional options
     * @return array{url: string, public_id: string}|null
     */
    public function upload(UploadedFile|string $file, string $folder = 'uploads', array $options = []): ?array
    {
        if (!$this->isConfigured()) {
            Log::warning('CloudinaryService: Not configured, returning null');
            return null;
        }

        try {
            $timestamp = time();
            
            // Build signature params
            $signParams = array_merge([
                'folder' => $folder,
                'timestamp' => $timestamp,
            ], $options);
            
            // Sort and build signature string
            ksort($signParams);
            $signString = collect($signParams)
                ->map(fn($v, $k) => "{$k}={$v}")
                ->implode('&');
            $signString .= $this->apiSecret;
            $signature = sha1($signString);

            // Prepare upload payload
            $payload = array_merge($signParams, [
                'api_key' => $this->apiKey,
                'signature' => $signature,
            ]);

            // Handle file type
            if ($file instanceof UploadedFile) {
                $response = Http::timeout(60)
                    ->attach('file', $file->getContent(), $file->getClientOriginalName())
                    ->post("https://api.cloudinary.com/v1_1/{$this->cloudName}/image/upload", $payload);
            } else {
                // URL upload
                $payload['file'] = $file;
                $response = Http::timeout(60)
                    ->asForm()
                    ->post("https://api.cloudinary.com/v1_1/{$this->cloudName}/image/upload", $payload);
            }

            if ($response->successful()) {
                $data = $response->json();
                Log::info("CloudinaryService: Upload successful", ['url' => $data['secure_url']]);
                
                return [
                    'url' => $data['secure_url'],
                    'public_id' => $data['public_id'],
                    'format' => $data['format'] ?? null,
                    'width' => $data['width'] ?? null,
                    'height' => $data['height'] ?? null,
                    'bytes' => $data['bytes'] ?? null,
                ];
            }

            Log::error("CloudinaryService: Upload failed", [
                'status' => $response->status(),
                'body' => Str::limit($response->body(), 500),
            ]);
            
            return null;

        } catch (\Exception $e) {
            Log::error("CloudinaryService: Exception during upload", [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Delete a file from Cloudinary by public_id.
     *
     * @param string $publicId The public_id of the resource
     * @return bool
     */
    public function delete(string $publicId): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        try {
            $timestamp = time();
            $signString = "public_id={$publicId}&timestamp={$timestamp}{$this->apiSecret}";
            $signature = sha1($signString);

            $response = Http::timeout(30)
                ->asForm()
                ->post("https://api.cloudinary.com/v1_1/{$this->cloudName}/image/destroy", [
                    'public_id' => $publicId,
                    'api_key' => $this->apiKey,
                    'timestamp' => $timestamp,
                    'signature' => $signature,
                ]);

            if ($response->successful() && $response->json('result') === 'ok') {
                Log::info("CloudinaryService: Deleted {$publicId}");
                return true;
            }

            Log::warning("CloudinaryService: Delete may have failed for {$publicId}", [
                'response' => $response->json(),
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error("CloudinaryService: Exception during delete", [
                'public_id' => $publicId,
                'message' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Upload from a temporary URL (e.g., AI-generated image).
     * Falls back to local storage if Cloudinary fails.
     *
     * @param string $tempUrl Temporary URL to fetch and upload
     * @param string $folder Cloudinary folder
     * @param string $localFallbackPath Local path for fallback
     * @return array{url: string, source: string}
     */
    public function uploadFromUrl(string $tempUrl, string $folder = 'ai-generated', string $localFallbackPath = 'uploads/content-media'): array
    {
        // Try Cloudinary first
        $result = $this->upload($tempUrl, $folder);
        
        if ($result) {
            return [
                'url' => $result['url'],
                'public_id' => $result['public_id'],
                'source' => 'cloudinary',
            ];
        }

        // Local fallback
        return $this->saveToLocalStorage($tempUrl, $localFallbackPath);
    }

    /**
     * Save a file to local storage (fallback when Cloudinary fails).
     *
     * @param string $url URL to download
     * @param string $relativePath Path relative to public directory
     * @return array{url: string, source: string}
     */
    protected function saveToLocalStorage(string $url, string $relativePath): array
    {
        try {
            $imageContent = file_get_contents($url);
            
            if ($imageContent === false) {
                Log::error("CloudinaryService: Failed to download from URL for local fallback");
                return ['url' => $url, 'source' => 'temp_url', 'public_id' => null];
            }

            $filename = 'ai_' . Str::random(40) . '.png';
            $fullPath = public_path($relativePath);
            
            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0755, true);
            }
            
            file_put_contents($fullPath . '/' . $filename, $imageContent);
            $localUrl = '/' . $relativePath . '/' . $filename;
            
            Log::info("CloudinaryService: Saved to local storage", ['path' => $localUrl]);
            
            return [
                'url' => $localUrl,
                'public_id' => null,
                'source' => 'local',
            ];

        } catch (\Exception $e) {
            Log::error("CloudinaryService: Local fallback failed", ['message' => $e->getMessage()]);
            return ['url' => $url, 'source' => 'temp_url', 'public_id' => null];
        }
    }
}
