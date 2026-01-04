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

        return view('documents.documents', compact('documents'));
    }

    public function show(Document $document)
    {
        return response($document->content)->header('Content-Type', 'text/html');
    }

    public function destroy(Document $document)
    {
        $document->delete();
        return response()->json(['success' => true]);
    }
}