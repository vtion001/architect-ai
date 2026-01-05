<?php

namespace App\Http\Controllers;

use App\Models\MediaAsset;
use App\Services\AuthorizationService;
use Illuminate\Http\Request;

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

        return view('media-registry.index', compact('assets', 'stats'));
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
}