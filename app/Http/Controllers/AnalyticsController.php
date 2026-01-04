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

        // 1. Top Level Metrics
        $totalActivities = AuditLog::where('tenant_id', $tenant->id)->count();
        $activeUsersCount = User::where('tenant_id', $tenant->id)->where('status', 'active')->count();
        
        $successCount = AuditLog::where('tenant_id', $tenant->id)->where('result', 'success')->count();
        $successRate = $totalActivities > 0 ? round(($successCount / $totalActivities) * 100, 1) : 100;

        $tokensConsumed = abs(TokenTransaction::where('tenant_id', $tenant->id)->where('amount', '<', 0)->sum('amount'));

        // 2. Chart Data: Last 6 Months Trends
        $labels = [];
        $researchTrend = [];
        $contentTrend = [];
        $socialTrend = [];
        $kbTrend = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $labels[] = $month->format('M');

            $researchTrend[] = AuditLog::where('tenant_id', $tenant->id)
                ->where('action', 'like', '%research%')
                ->whereMonth('timestamp', $month->month)
                ->whereYear('timestamp', $month->year)
                ->count();

            $contentTrend[] = AuditLog::where('tenant_id', $tenant->id)
                ->where('action', 'like', '%content%')
                ->whereMonth('timestamp', $month->month)
                ->whereYear('timestamp', $month->year)
                ->count();

            $socialTrend[] = AuditLog::where('tenant_id', $tenant->id)
                ->where('action', 'like', '%social%')
                ->whereMonth('timestamp', $month->month)
                ->whereYear('timestamp', $month->year)
                ->count();

            $kbTrend[] = AuditLog::where('tenant_id', $tenant->id)
                ->where('action', 'like', '%knowledge%')
                ->whereMonth('timestamp', $month->month)
                ->whereYear('timestamp', $month->year)
                ->count();
        }

        return view('analytics.analytics', compact(
            'totalActivities', 
            'activeUsersCount', 
            'successRate', 
            'tokensConsumed',
            'labels',
            'researchTrend',
            'contentTrend',
            'socialTrend',
            'kbTrend'
        ));
    }
}