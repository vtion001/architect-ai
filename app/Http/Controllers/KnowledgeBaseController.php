<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeBaseAsset;
use App\Services\AuthorizationService;
use Illuminate\Http\Request;

class KnowledgeBaseController extends Controller
{
    public function __construct(protected AuthorizationService $authService) {}

    public function index(Request $request)
    {
        $this->authService->audit(auth()->user(), 'knowledge_base.view');
        
        $parentId = $request->query('folder');
        $currentFolder = $parentId ? KnowledgeBaseAsset::find($parentId) : null;

        $assets = KnowledgeBaseAsset::where('tenant_id', auth()->user()->tenant_id)
            ->where('parent_id', $parentId)
            ->latest()
            ->get();
        
        $stats = [
            'total_docs' => KnowledgeBaseAsset::count(),
            'categories' => KnowledgeBaseAsset::distinct('category')->count(),
            'recent_updates' => KnowledgeBaseAsset::where('updated_at', '>=', now()->subDays(7))->count(),
        ];

        return view('knowledge-base.knowledge-hub', compact('assets', 'stats', 'currentFolder'));
    }

    public function store(Request $request, \App\Services\PdfToTextService $pdfService)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string',
            'content' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'source_url' => 'nullable|url',
            'parent_id' => 'nullable|exists:knowledge_base_assets,id',
            'file' => 'nullable|file|max:10240', // 10MB
        ]);

        $content = $request->content ?? '';
        $sourceUrl = $request->source_url;

        // Handle File Upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            
            // 1. Text Extraction (needs local file access)
            if ($file->extension() === 'txt' || $file->extension() === 'md') {
                $content = file_get_contents($file->getRealPath());
            } elseif ($file->extension() === 'pdf') {
                $content = $pdfService->extract($file->getRealPath());
                if (empty($content)) {
                    $content = "PDF Document: " . $file->getClientOriginalName() . ". (Content extraction failed or empty)";
                }
            }

            // 2. Cloudinary Upload
            $cloudName = config('services.cloudinary.cloud_name');
            $apiKey = config('services.cloudinary.api_key');
            $apiSecret = config('services.cloudinary.api_secret');
            $uploadedToCloud = false;

            if ($cloudName && $apiKey && $apiSecret) {
                try {
                    $timestamp = time();
                    $signature = sha1("timestamp=" . $timestamp . $apiSecret);

                    // Use 'auto' or 'raw' depending on file type. PDF is often 'image' or 'raw' in Cloudinary. 
                    // 'auto' is safest.
                    $response = \Illuminate\Support\Facades\Http::attach(
                        'file',
                        file_get_contents($file->getRealPath()),
                        $file->getClientOriginalName()
                    )->post("https://api.cloudinary.com/v1_1/$cloudName/auto/upload", [
                        'api_key' => $apiKey,
                        'timestamp' => $timestamp,
                        'signature' => $signature,
                    ]);

                    if ($response->successful()) {
                        $sourceUrl = $response->json('secure_url');
                        $uploadedToCloud = true;
                    } else {
                        \Illuminate\Support\Facades\Log::error("KB Cloudinary Upload Failed: " . $response->body());
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("KB Cloudinary Upload Error: " . $e->getMessage());
                }
            }

            // 3. Local Fallback
            if (!$uploadedToCloud) {
                $path = $file->store('knowledge-base', 'public');
                $sourceUrl = asset('storage/' . $path);
            }
        }

        if ($request->type === 'folder') {
            $content = $content ?: 'Folder Container';
        }

        $asset = KnowledgeBaseAsset::create([
            'tenant_id' => auth()->user()->tenant_id,
            'user_id' => auth()->id(),
            'parent_id' => $request->parent_id,
            'title' => $request->title,
            'type' => $request->type,
            'content' => $content ?: 'No content',
            'category' => $request->category ?? 'Uncategorized',
            'source_url' => $sourceUrl,
        ]);

        return response()->json([
            'success' => true,
            'asset' => $asset
        ]);
    }

    public function destroy(KnowledgeBaseAsset $asset)
    {
        $asset->delete();
        return response()->json(['success' => true]);
    }
}