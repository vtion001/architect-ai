<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeBaseAsset;
use App\Services\AuthorizationService;
use Illuminate\Http\Request;

class KnowledgeBaseController extends Controller
{
    public function __construct(protected AuthorizationService $authService) {}

    public function index()
    {
        $this->authService->audit(auth()->user(), 'knowledge_base.view');
        
        $assets = KnowledgeBaseAsset::latest()->get();
        
        $stats = [
            'total_docs' => KnowledgeBaseAsset::count(),
            'categories' => KnowledgeBaseAsset::distinct('category')->count(),
            'recent_updates' => KnowledgeBaseAsset::where('updated_at', '>=', now()->subDays(7))->count(),
        ];

        return view('knowledge-base.knowledge-hub', compact('assets', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:file,website,text,youtube',
            'content' => 'required|string',
            'category' => 'nullable|string|max:255',
            'source_url' => 'nullable|url',
        ]);

        $asset = KnowledgeBaseAsset::create([
            'tenant_id' => auth()->user()->tenant_id,
            'user_id' => auth()->id(),
            'title' => $request->title,
            'type' => $request->type,
            'content' => $request->content,
            'category' => $request->category ?? 'Uncategorized',
            'source_url' => $request->source_url,
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