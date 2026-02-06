<?php

namespace App\Http\Controllers;

use App\Models\MediaAsset;
use App\Services\AuthorizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MediaRegistryController extends Controller
{
    public function __construct(protected AuthorizationService $authService) {}

    /**
     * Display the Industrial Media Matrix.
     */
    public function index()
    {
        $this->authService->audit(auth()->user(), 'media_registry.view');
        
        $assets = MediaAsset::latest()->paginate(24);
        
        $stats = [
            'total_assets' => MediaAsset::count(),
            'ai_generated' => MediaAsset::where('source', 'ai_generation')->count(),
            'uploads' => MediaAsset::where('source', 'upload')->count(),
        ];

        return view('media-registry.media-registry', compact('assets', 'stats'));
    }

    /**
     * Store a new media asset (Cloudinary Upload).
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|image|max:10240', // 10MB max
        ]);

        $file = $request->file('file');
        $url = null;
        $source = 'upload';

        // 1. Attempt Cloudinary Upload
        $cloudName = config('services.cloudinary.cloud_name');
        $apiKey = config('services.cloudinary.api_key');
        $apiSecret = config('services.cloudinary.api_secret');

        if ($cloudName && $apiKey && $apiSecret) {
            try {
                $timestamp = time();
                $params = ['timestamp' => $timestamp];
                ksort($params);
                $signString = http_build_query($params) . $apiSecret;
                $signature = sha1(urldecode($signString)); // Cloudinary signature usually requires unencoded string for simple params, but let's follow standard signature gen

                // Simpler manual signature for just timestamp
                $stringToSign = "timestamp=" . $timestamp . $apiSecret;
                $signature = sha1($stringToSign);

                $response = Http::attach(
                    'file',
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                )->post("https://api.cloudinary.com/v1_1/$cloudName/image/upload", [
                    'api_key' => $apiKey,
                    'timestamp' => $timestamp,
                    'signature' => $signature,
                ]);

                if ($response->successful()) {
                    $url = $response->json('secure_url');
                    Log::info("Media Registry: Cloudinary upload successful. URL: $url");
                } else {
                    Log::error("Media Registry: Cloudinary upload failed. " . $response->body());
                }
            } catch (\Exception $e) {
                Log::error("Media Registry: Cloudinary exception. " . $e->getMessage());
            }
        }

        // 2. Fallback to Local Storage
        if (!$url) {
            $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/media-registry'), $filename);
            $url = '/uploads/media-registry/' . $filename;
            Log::warning("Media Registry: Fallback to local storage. URL: $url");
        }

        // 3. Create Record
        $asset = MediaAsset::create([
            'tenant_id' => auth()->user()->tenant_id,
            'user_id' => auth()->id(),
            'name' => $file->getClientOriginalName(),
            'url' => $url,
            'type' => 'image',
            'source' => $source,
            'metadata' => [
                'size' => $file->getSize(),
                'mime' => $file->getMimeType(),
                'original_name' => $file->getClientOriginalName()
            ]
        ]);

        return response()->json([
            'success' => true,
            'asset' => $asset
        ]);
    }

    /**
     * Securely purge a visual asset from the registry.
     */
    public function destroy(MediaAsset $asset)
    {
        $this->authService->audit(auth()->user(), 'media_registry.purge', $asset);
        
        $asset->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Get media assets as JSON (for Image Creator reference mode).
     */
    public function getAssets(Request $request)
    {
        $limit = min((int) $request->input('limit', 20), 50);
        
        $assets = MediaAsset::where('tenant_id', auth()->user()->tenant_id)
            ->where('type', 'image')
            ->select('id', 'name', 'url')
            ->latest()
            ->limit($limit)
            ->get();

        return response()->json(['assets' => $assets]);
    }
}