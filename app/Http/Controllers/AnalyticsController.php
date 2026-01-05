<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Content;
use App\Models\Research;
use App\Models\Tenant;
use App\Models\TokenTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index()
    {
        $tenant = app(Tenant::class);

        // 1. Core Grid Telemetry
        $totalActivities = AuditLog::where('tenant_id', $tenant->id)->count();
        $activeUsersCount = User::where('tenant_id', $tenant->id)->where('status', 'active')->count();
        
        $successCount = AuditLog::where('tenant_id', $tenant->id)->where('result', 'success')->count();
        $successRate = $totalActivities > 0 ? round(($successCount / $totalActivities) * 100, 1) : 100;

        $tokensConsumed = abs(TokenTransaction::where('tenant_id', $tenant->id)->where('amount', '<', 0)->sum('amount'));

        // 2. Productivity Analytics
        $totalContent = Content::where('tenant_id', $tenant->id)->count();
        $productivityIndex = $activeUsersCount > 0 ? round($totalContent / $activeUsersCount, 1) : 0;
        
        $totalResearch = Research::where('tenant_id', $tenant->id)->count();
        $intelDensity = $totalResearch > 0 ? round($totalContent / $totalResearch, 1) : $totalContent;

        // 3. Chart Data: Last 7 Days Intensity
        $labels = [];
        $intensityTrend = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::now()->subDays($i);
            $labels[] = $day->format('D');

            $intensityTrend[] = AuditLog::where('tenant_id', $tenant->id)
                ->whereDate('timestamp', $day->toDateString())
                ->count();
        }

        return view('analytics.analytics', compact(
            'totalActivities', 
            'activeUsersCount', 
            'successRate', 
            'tokensConsumed',
            'productivityIndex',
            'intelDensity',
            'labels',
            'intensityTrend'
        ));
    }
}