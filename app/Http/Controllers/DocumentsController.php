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

        return view('documents.index', compact('documents', 'stats'));
    }

    public function show(Document $document)
    {
        return view('documents.viewer', compact('document'));
    }

    public function destroy(Document $document)
    {
        $document->delete();
        return response()->json(['success' => true]);
    }
}