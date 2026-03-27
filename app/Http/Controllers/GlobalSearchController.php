<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Research;
use App\Models\Tenant;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    /**
     * Search authorized assets across the entire agency grid.
     */
    public function index(Request $request)
    {
        $query = $request->get('q');
        if (empty($query) || strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $results = [];
        $tenant = app(Tenant::class);

        // 1. Search Authorized Nodes (Sub-Accounts)
        if ($tenant->type === 'agency') {
            $nodes = $tenant->subAccounts()
                ->where('name', 'like', "%{$query}%")
                ->limit(3)
                ->get()
                ->map(fn ($n) => [
                    'title' => $n->name,
                    'type' => 'Workspace Node',
                    'icon' => 'grid',
                    'url' => route('tenant.switch', $n->id),
                    'metadata' => 'Switch to '.$n->slug,
                ]);
            $results = array_merge($results, $nodes->toArray());
        }

        // 2. Search Intelligence Reports
        $reports = Research::where('title', 'like', "%{$query}%")
            ->where('status', 'completed')
            ->limit(5)
            ->get()
            ->map(fn ($r) => [
                'title' => $r->title,
                'type' => 'Research Report',
                'icon' => 'brain',
                'url' => route('research-engine.show', $r->id),
                'metadata' => $r->created_at->diffForHumans(),
            ]);
        $results = array_merge($results, $reports->toArray());

        // 3. Search Content Architectures
        $content = Content::where('title', 'like', "%{$query}%")
            ->where('status', 'published')
            ->limit(5)
            ->get()
            ->map(fn ($c) => [
                'title' => $c->title,
                'type' => 'Content Batch',
                'icon' => 'pencil',
                'url' => route('content-creator.show', $c->id),
                'metadata' => strtoupper($c->type),
            ]);
        $results = array_merge($results, $content->toArray());

        return response()->json([
            'results' => array_slice($results, 0, 10),
        ]);
    }
}
