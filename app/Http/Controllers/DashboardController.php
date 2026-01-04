<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Research;
use App\Models\TokenAllocation;
use App\Models\AuditLog;
use App\Models\Tenant;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $tenant = app(Tenant::class);

        $researchCount = Research::count();
        $contentCount = Content::where('type', '!=', 'social-post')->count();
        $tokenBalance = TokenAllocation::where('tenant_id', $tenant->id)->sum('balance');

        $moduleUsageData = [
            ['name' => "Research Engine", 'value' => $researchCount, 'color' => "oklch(0.55 0.22 264)"],
            ['name' => "Content Creator", 'value' => $contentCount, 'color' => "oklch(0.35 0.12 264)"],
            ['name' => "Social Planner", 'value' => Content::where('type', 'social-post')->count(), 'color' => "oklch(0.75 0.15 264)"],
        ];

        // Real activities from audit logs
        $recentActivities = AuditLog::with('actor')
            ->where('tenant_id', $tenant->id)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'activityId' => "#" . substr($log->id, 0, 6),
                    'user' => [
                        'name' => $log->actor?->email ?? 'System',
                        'avatar' => "https://i.pravatar.cc/150?u=" . ($log->actor_id ?? 'sys')
                    ],
                    'module' => $this->getModuleFromAction($log->action),
                    'topic' => $log->metadata['topic'] ?? $log->metadata['query'] ?? 'System Action',
                    'date' => $log->timestamp->format('d/m/Y H:i'),
                    'status' => ucfirst($log->result),
                    'output' => $log->metadata['amount'] ?? 0,
                ];
            });

        $contentTrendsData = [
            ['month' => "Jan", 'requests' => 45, 'generated' => 38],
            // ... keep placeholders for trends for now as it needs time-series grouping
        ];

        return view('dashboard', compact('moduleUsageData', 'contentTrendsData', 'recentActivities', 'researchCount', 'contentCount', 'tokenBalance'));
    }

    protected function getModuleFromAction(string $action): string
    {
        if (str_contains($action, 'research')) return 'Research Engine';
        if (str_contains($action, 'content')) return 'Content Creator';
        if (str_contains($action, 'social')) return 'Social Planner';
        return 'System';
    }
}
