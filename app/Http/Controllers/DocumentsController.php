<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Services\AuthorizationService;
use Illuminate\Http\Request;

class DocumentsController extends Controller
{
    public function __construct(protected AuthorizationService $authService) {}

    public function index()
    {
        $this->authService->audit(auth()->user(), 'documents.view');
        
        $documents = Document::latest()->get();

        $stats = [
            'total_assets' => $documents->count(),
            'report_count' => $documents->where('category', 'Reports')->count(),
            'storage_used' => round($documents->sum('size') / 1024, 1) . ' KB',
        ];

        return view('documents.documents', compact('documents', 'stats'));
    }

    public function show(Request $request, Document $document)
    {
        // Return JSON for AJAX status polling
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'id' => $document->id,
                'name' => $document->name,
                'status' => $document->status ?? 'completed',
                'content' => $document->content,
                'metadata' => $document->metadata,
                'created_at' => $document->created_at,
            ]);
        }
        
        return view('documents.viewer', compact('document'));
    }

    public function update(Request $request, Document $document)
    {
        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $document->update([
            'content' => $validated['content'],
            'size' => strlen($validated['content']),
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy(Document $document)
    {
        $document->delete();
        return response()->json(['success' => true]);
    }
}