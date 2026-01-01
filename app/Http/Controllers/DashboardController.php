<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $moduleUsageData = [
            ['name' => "Research Engine", 'value' => 1135, 'color' => "oklch(0.55 0.22 264)"],
            ['name' => "Content Creator", 'value' => 514, 'color' => "oklch(0.35 0.12 264)"],
            ['name' => "Social Planner", 'value' => 345, 'color' => "oklch(0.75 0.15 264)"],
            ['name' => "Knowledge Base", 'value' => 234, 'color' => "oklch(0.88 0.08 264)"],
        ];

        $contentTrendsData = [
            ['month' => "Jan", 'requests' => 45, 'generated' => 38],
            ['month' => "Feb", 'requests' => 52, 'generated' => 45],
            ['month' => "Mar", 'requests' => 61, 'generated' => 55],
            ['month' => "Apr", 'requests' => 75, 'generated' => 68],
            ['month' => "May", 'requests' => 68, 'generated' => 62],
            ['month' => "Jun", 'requests' => 73, 'generated' => 67],
            ['month' => "Jul", 'requests' => 82, 'generated' => 76],
            ['month' => "Aug", 'requests' => 79, 'generated' => 71],
            ['month' => "Sep", 'requests' => 88, 'generated' => 82],
            ['month' => "Oct", 'requests' => 85, 'generated' => 78],
            ['month' => "Nov", 'requests' => 92, 'generated' => 86],
            ['month' => "Dec", 'requests' => 88, 'generated' => 82],
        ];

        $recentActivities = [
            [
                'id' => 1,
                'activityId' => "#065499",
                'user' => ['name' => "Sarah Chen", 'avatar' => "https://i.pravatar.cc/150?u=1"],
                'module' => "Research Engine",
                'topic' => "Market Analysis Q4",
                'date' => "21/07/2025 05:21",
                'status' => "Completed",
                'output' => 847,
            ],
            [
                'id' => 2,
                'activityId' => "#065498",
                'user' => ['name' => "Marcus Rodriguez", 'avatar' => "https://i.pravatar.cc/150?u=2"],
                'module' => "Content Creator",
                'topic' => "LinkedIn Campaign",
                'date' => "21/07/2025 04:15",
                'status' => "In Progress",
                'output' => 523,
            ],
            [
                'id' => 3,
                'activityId' => "#065497",
                'user' => ['name' => "Aisha Patel", 'avatar' => "https://i.pravatar.cc/150?u=3"],
                'module' => "Social Planner",
                'topic' => "Weekly Schedule",
                'date' => "21/07/2025 03:42",
                'status' => "Completed",
                'output' => 312,
            ],
            [
                'id' => 4,
                'activityId' => "#065496",
                'user' => ['name' => "James Kim", 'avatar' => "https://i.pravatar.cc/150?u=4"],
                'module' => "Knowledge Base",
                'topic' => "Brand Guidelines Update",
                'date' => "21/07/2025 02:18",
                'status' => "Pending",
                'output' => 1247,
            ],
        ];

        return view('dashboard', compact('moduleUsageData', 'contentTrendsData', 'recentActivities'));
    }
}
