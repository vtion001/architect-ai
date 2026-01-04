<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Research;
use App\Models\KnowledgeBaseAsset;
use App\Models\TokenAllocation;
use App\Models\AuditLog;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $tenant = app(Tenant::class);

        $researchCount = Research::count();
        $contentCount = Content::where('type', '!=', 'social-post')->count();
        $socialCount = Content::where('type', 'social-post')->count();
        $kbCount = KnowledgeBaseAsset::count();
        $tokenBalance = TokenAllocation::where('tenant_id', $tenant->id)->sum('balance');

        $moduleUsageData = [
            ['name' => "Research Engine", 'value' => $researchCount, 'color' => "#3b82f6"],
            ['name' => "Content Creator", 'value' => $contentCount, 'color' => "#8b5cf6"],
            ['name' => "Social Planner", 'value' => $socialCount, 'color' => "#10b981"],
            ['name' => "Knowledge Base", 'value' => $kbCount, 'color' => "#f59e0b"],
        ];

        // Real activities from audit logs
        $recentActivities = AuditLog::with('actor')
            ->where('tenant_id', $tenant->id)
            ->orderBy('timestamp', 'desc')
            ->take(10)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'activityId' => "#" . strtoupper(substr($log->id, 0, 6)),
                    'user' => [
                        'name' => $log->actor?->email ?? 'System',
                        'avatar' => null
                    ],
                    'module' => $this->getModuleFromAction($log->action),
                    'topic' => $log->metadata['topic'] ?? $log->metadata['query'] ?? $log->action,
                    'date' => $log->timestamp->diffForHumans(),
                    'status' => $log->result === 'success' ? 'Success' : ($log->result === 'denied' ? 'Denied' : 'Failure'),
                    'output' => $log->metadata['amount'] ?? 0,
                ];
            });

        // 6-Month Trend Data
        $contentTrendsData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $contentTrendsData[] = [
                'month' => $month->format('M'),
                'requests' => AuditLog::where('tenant_id', $tenant->id)
                    ->whereMonth('timestamp', $month->month)
                    ->whereYear('timestamp', $month->year)
                    ->count(),
                'generated' => Content::whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->count(),
            ];
        }

        return view('dashboard', compact('moduleUsageData', 'contentTrendsData', 'recentActivities', 'researchCount', 'contentCount', 'tokenBalance'));
    }

    protected function getModuleFromAction(string $action): string
    {
        if (str_contains($action, 'research')) return 'Research Engine';
        if (str_contains($action, 'content')) return 'Content Creator';
        if (str_contains($action, 'social')) return 'Social Planner';
        if (str_contains($action, 'user') || str_contains($action, 'access')) return 'IAM Gateway';
        return 'System';
    }
}