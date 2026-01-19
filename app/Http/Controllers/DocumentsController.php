<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Document;
use App\Services\AuthorizationService;
use Illuminate\Http\Request;

/**
 * Documents Controller
 * 
 * Manages document listing, viewing, updating, and deletion.
 * 
 * TENANT ISOLATION: Document model uses BelongsToTenant trait
 * for automatic query scoping.
 */
class DocumentsController extends Controller
{
    public function __construct(
        protected AuthorizationService $authService
    ) {}

    public function index()
    {
        $this->authService->audit(auth()->user(), 'documents.view');
        
        $documents = Document::latest()->get();

        $stats = [
            'total_assets' => $documents->count(),
            'report_count' => $documents->where('category', 'Reports')->count(),
            'storage_used' => $this->formatBytes($documents->sum('size')),
        ];

        return view('documents.documents', compact('documents', 'stats'));
    }

    public function show(Request $request, Document $document)
    {
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

    /**
     * Format bytes to human readable string.
     */
    private function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        }
        if ($bytes < 1048576) {
            return round($bytes / 1024, 1) . ' KB';
        }
        return round($bytes / 1048576, 1) . ' MB';
    }
}