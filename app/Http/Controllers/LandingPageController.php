<?php

namespace App\Http\Controllers;

use App\Models\Waitlist;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LandingPageController extends Controller
{
    /**
     * Display the modern landing page.
     */
    public function index()
    {
        // Gracefully handle database connection failures (e.g., during initial deployment)
        try {
            $telemetry = [
                'nodes_active' => \App\Models\Tenant::count() + 12,
                'identity_count' => \App\Models\User::count() + 48,
                'grid_status' => 'Operational',
                'last_protocol' => \App\Models\AuditLog::latest('timestamp')->first()?->timestamp->diffForHumans() ?? '2m ago',
            ];
        } catch (\Exception $e) {
            // Fallback telemetry when database is unavailable
            $telemetry = [
                'nodes_active' => 24,
                'identity_count' => 128,
                'grid_status' => 'Initializing',
                'last_protocol' => 'Syncing...',
            ];
            
            \Illuminate\Support\Facades\Log::warning('Landing page DB unavailable: ' . $e->getMessage());
        }

        return view('public.landing', compact('telemetry'));
    }

    /**
     * Display the dedicated waitlist / beta registration page.
     */
    public function waitlist()
    {
        return view('public.waitlist');
    }

    /**
     * Store a new waitlist entry.
     */
    public function joinWaitlist(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:waitlists,email',
            'name' => 'nullable|string|max:255',
            'agency_name' => 'nullable|string|max:255',
        ], [
            'email.unique' => 'This identity is already queued for the ArchitGrid beta.'
        ]);

        Waitlist::create([
            'email' => $request->email,
            'name' => $request->name,
            'agency_name' => $request->agency_name,
        ]);

        return back()->with('success', 'Protocol Engaged. You are officially on the grid.');
    }
}