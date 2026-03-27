<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Content;
use App\Models\KnowledgeBaseAsset;
use App\Models\Research;
use App\Models\Tenant;
use App\Services\TokenService;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function __construct(protected TokenService $tokenService) {}

    public function index()
    {
        $tenant = app(Tenant::class);

        // 1. Grid Telemetry
        $researchCount = Research::count();
        $contentCount = Content::where('type', '!=', 'social-post')->count();
        $socialCount = Content::where('type', 'social-post')->count();
        $kbCount = KnowledgeBaseAsset::count();
        $tokenBalance = $this->tokenService->getBalance($tenant);

        // 2. Intelligence Sync Status
        $lastSync = AuditLog::where('tenant_id', $tenant->id)->latest('timestamp')->first()?->timestamp;
        $gridStatus = $lastSync && $lastSync->isAfter(now()->subHours(6)) ? 'Synchronized' : 'Idle';

        // 3. Network Intensity (7-Day Heatmap)
        $intensityData = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::now()->subDays($i);
            $intensityData[] = [
                'label' => $day->format('D'),
                'value' => AuditLog::where('tenant_id', $tenant->id)->whereDate('timestamp', $day->toDateString())->count(),
            ];
        }

        // 4. Industrial Activity Feed
        $recentActivities = AuditLog::with('actor')
            ->where('tenant_id', $tenant->id)
            ->orderBy('timestamp', 'desc')
            ->take(10)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'node' => $this->getModuleFromAction($log->action),
                    'actor' => $log->actor?->email ?? 'SYSTEM_CORE',
                    'protocol' => strtoupper($log->action),
                    'result' => $log->result,
                    'time' => $log->timestamp->diffForHumans(),
                    'context' => Str::limit($log->justification ?? $log->metadata['topic'] ?? $log->metadata['query'] ?? 'No metadata provided', 40),
                ];
            });

        return view('dashboard', compact(
            'researchCount',
            'contentCount',
            'socialCount',
            'kbCount',
            'tokenBalance',
            'gridStatus',
            'intensityData',
            'recentActivities'
        ));
    }

    protected function getModuleFromAction(string $action): string
    {
        if (str_contains($action, 'research')) {
            return 'Research Engine';
        }
        if (str_contains($action, 'content')) {
            return 'Content Creator';
        }
        if (str_contains($action, 'social')) {
            return 'Social Planner';
        }
        if (str_contains($action, 'user') || str_contains($action, 'access')) {
            return 'IAM Gateway';
        }

        return 'System';
    }
}
