@extends('layouts.app')

@section('title', 'API Spotlight')

@push('head')
    <link rel="stylesheet" href="https://unpkg.com/@stoplight/elements/styles.min.css">
    <style>
        .spotlight-header {
            color: var(--foreground);
            font-family: var(--font-sans);
        }
        .spotlight-section {
            background: var(--card);
            border-radius: 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .spotlight-table th, .spotlight-table td {
            border-bottom: 1px solid var(--border);
            padding: 0.5rem 1rem;
        }
        .spotlight-table th {
            background: var(--muted);
            color: var(--muted-foreground);
        }
        .spotlight-table tr:last-child td {
            border-bottom: none;
        }
        .spotlight-code {
            background: var(--muted);
            color: var(--muted-foreground);
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            font-family: var(--font-mono);
            font-size: 0.95rem;
            margin-bottom: 1rem;
        }
        .spotlight-section h2 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .spotlight-section h3 {
            font-size: 1.15rem;
            font-weight: 600;
            margin-top: 1.5rem;
            margin-bottom: 0.5rem;
        }
        .spotlight-section ul {
            list-style: disc;
            margin-left: 2rem;
        }
        .spotlight-section a {
            color: var(--primary);
        }
    </style>
@endpush

@section('content')
<div class="min-h-screen bg-background">
    <div class="container mx-auto px-4 py-6">
        <div class="flex items-center gap-3 mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="file-code" class="lucide lucide-file-code w-4 h-4"><path d="M6 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2z"></path><path d="M14 2v5a1 1 0 0 0 1 1h5"></path><path d="M10 12.5 8 15l2 2.5"></path><path d="m14 12.5 2 2.5-2 2.5"></path></svg>
            <h1 class="text-3xl font-bold spotlight-header">API Spotlight</h1>
        </div>
        <div class="spotlight-section">
            <h2>API Documentation</h2>
            <a href="{{ route('api-docs') }}" target="_blank" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors text-sidebar-foreground/70 hover:bg-sidebar-accent/50 hover:text-sidebar-accent-foreground">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="file-code" class="lucide lucide-file-code w-4 h-4"><path d="M6 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2z"></path><path d="M14 2v5a1 1 0 0 0 1 1h5"></path><path d="M10 12.5 8 15l2 2.5"></path><path d="m14 12.5 2 2.5-2 2.5"></path></svg>
                API Documentation
            </a>
        </div>
        <div class="spotlight-section">
            <h2>API Endpoints</h2>
            <table class="w-full spotlight-table">
                <thead>
                    <tr>
                        <th>Method</th>
                        <th>Path</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>POST</td><td>/content/receive</td><td>Receive content from automation tools (n8n, OpenClaw, Zapier, etc.)</td></tr>
                    <tr><td>GET</td><td>/content/drafts</td><td>List all drafts</td></tr>
                    <tr><td>GET</td><td>/content/drafts/{id}</td><td>Get a specific draft</td></tr>
                    <tr><td>PUT</td><td>/content/drafts/{id}</td><td>Update a draft</td></tr>
                    <tr><td>DELETE</td><td>/content/drafts/{id}</td><td>Delete a draft</td></tr>
                    <tr><td>POST</td><td>/content/drafts/{id}/publish</td><td>Publish a draft to platforms</td></tr>
                    <tr><td>POST</td><td>/publish</td><td>Direct publish (create & publish in one step)</td></tr>
                    <tr><td>GET</td><td>/platforms</td><td>Get platform connection status</td></tr>
                    <!-- Add more endpoints as needed -->
                </tbody>
            </table>
        </div>
        <div class="spotlight-section">
            <h2>OpenAPI Explorer</h2>
            <div class="api-console rounded-xl border border-border overflow-hidden">
                <elements-api
                    apiDescriptionUrl="{{ route('api-docs.json') }}"
                    router="history"
                    layout="sidebar"
                    tryItKey=""
                    style="height:100%; width:100%;"
                ></elements-api>
            </div>
        </div>
    </div>
</div>
<script src="https://unpkg.com/@stoplight/elements/web-components.min.js"></script>
@endsection
