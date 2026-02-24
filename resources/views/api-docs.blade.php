@extends('layouts.app')

@section('title', 'API Documentation')

@push('head')
    <link rel="stylesheet" href="https://unpkg.com/@stoplight/elements/styles.min.css">
    <style>
        .api-console {
            @extends('layouts.app')

            @section('title', 'API Documentation')

            @push('head')
                <link rel="stylesheet" href="https://unpkg.com/@stoplight/elements/styles.min.css">
                <style>
                    .api-docs-bg { background: var(--background); }
                    .api-docs-header { color: var(--foreground); font-family: var(--font-sans); }
                    .api-docs-section { background: var(--card); border-radius: 1rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04); padding: 2rem; margin-bottom: 2rem; }
                    .api-docs-table th, .api-docs-table td { border-bottom: 1px solid var(--border); padding: 0.5rem 1rem; }
                    .api-docs-table th { background: var(--muted); color: var(--muted-foreground); }
                    .api-docs-table tr:last-child td { border-bottom: none; }
                    .api-docs-code { background: var(--muted); color: var(--muted-foreground); border-radius: 0.5rem; padding: 0.75rem 1rem; font-family: var(--font-mono); font-size: 0.95rem; margin-bottom: 1rem; }
                    .api-docs-section h2 { font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; }
                    .api-docs-section h3 { font-size: 1.15rem; font-weight: 600; margin-top: 1.5rem; margin-bottom: 0.5rem; }
                    .api-docs-section ul { list-style: disc; margin-left: 2rem; }
                    .api-docs-section a { color: var(--primary); }
                </style>
            @endpush

            @section('content')
            <div class="min-h-screen api-docs-bg">
                <div class="container mx-auto px-4 py-6">
                    <div class="flex items-center gap-3 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="file-code" class="lucide lucide-file-code w-4 h-4"><path d="M6 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2z"></path><path d="M14 2v5a1 1 0 0 0 1 1h5"></path><path d="M10 12.5 8 15l2 2.5"></path><path d="m14 12.5 2 2.5-2 2.5"></path></svg>
                        <h1 class="text-3xl font-bold api-docs-header">API Documentation</h1>
                    </div>
                    <div class="api-docs-section">
                        <a href="http://localhost:8000/api-docs" target="_blank" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors text-sidebar-foreground/70 hover:bg-sidebar-accent/50 hover:text-sidebar-accent-foreground">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="file-code" class="lucide lucide-file-code w-4 h-4"><path d="M6 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2z"></path><path d="M14 2v5a1 1 0 0 0 1 1h5"></path><path d="M10 12.5 8 15l2 2.5"></path><path d="m14 12.5 2 2.5-2 2.5"></path></svg>
                            API Documentation
                        </a>
                    </div>
                    <div class="api-docs-section">
                        <h2>Quick Start</h2>
                        <div class="api-docs-code">
                            <strong>Base URL:</strong> <br>
                            <span>{{ config('app.url') }}/api</span>
                        </div>
                        <div class="api-docs-code">
                            <strong>Authentication:</strong> <br>
                            <span>Authorization: Bearer <em>YOUR_TOKEN</em></span>
                        </div>
                        <ul>
                            <li>All requests require a Bearer token (JWT).</li>
                            <li>Rate limits: 60/min for content creation, 30/min for publishing.</li>
                            <li>Contact: <a href="mailto:support@architect.ai">support@architect.ai</a></li>
                        </ul>
                    </div>
                    <div class="api-docs-section">
                        <h2>Integration Guides</h2>
                        <h3>n8n</h3>
                        <ul>
                            <li>Use the <strong>HTTP Request</strong> node to POST to <code>/content/receive</code>.</li>
                            <li>Set headers: <code>Authorization: Bearer YOUR_TOKEN</code>, <code>Content-Type: application/json</code>.</li>
                            <li>Body example:
                                <div class="api-docs-code">{
                "title": "New Post from n8n",
                "content": "This is the content body...",
                "type": "social-post",
                "platforms": ["facebook", "instagram"],
                "scheduled_at": "now",
                "source": "n8n"
            }</div>
                            </li>
                        </ul>
                        <h3>OpenClaw</h3>
                        <ul>
                            <li>Send content to <code>/content/receive</code> with <code>source: "openclaw"</code>.</li>
                            <li>Follow the same authentication and body format as above.</li>
                        </ul>
                        <h3>Zapier</h3>
                        <ul>
                            <li>Use a <strong>Webhooks by Zapier</strong> action to POST to <code>/content/receive</code>.</li>
                            <li>Set headers and body as above.</li>
                        </ul>
                        <h3>Direct Publish</h3>
                        <ul>
                            <li>POST to <code>/publish</code> to create and publish content in one step.</li>
                            <li>Body example:
                                <div class="api-docs-code">{
                "title": "Quick Post",
                "content": "Content to publish...",
                "platforms": ["facebook"],
                "scheduled_at": "now",
                "source": "external"
            }</div>
                            </li>
                        </ul>
                    </div>
                    <div class="api-docs-section">
                        <h2>API Endpoints</h2>
                        <table class="w-full api-docs-table">
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
                            </tbody>
                        </table>
                    </div>
                    <div class="api-docs-section">
                        <h2>Schema Examples</h2>
                        <h3>ContentReceive</h3>
                        <div class="api-docs-code">{
                "title": "New Post",
                "content": "Main content body text",
                "type": "social-post",
                "platforms": ["facebook", "instagram"],
                "scheduled_at": "now",
                "source": "n8n"
            }</div>
                        <h3>DirectPublish</h3>
                        <div class="api-docs-code">{
                "title": "Quick Post",
                "content": "Content to publish...",
                "platforms": ["facebook"],
                "scheduled_at": "now",
                "source": "external"
            }</div>
                    </div>
                    <div class="api-docs-section">
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
