@extends('layouts.app')

@section('title', 'API Documentation')

@push('head')
    <link rel="stylesheet" href="https://unpkg.com/@stoplight/elements/styles.min.css">
    <style>
        .api-console {
            height: calc(100vh - 140px);
        }
        .elements {
            height: 100%;
        }
    </style>
@endpush

@section('content')
<div class="min-h-screen bg-background">
    <div class="container mx-auto px-4 py-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-foreground">API Documentation</h1>
                <p class="text-muted-foreground mt-1">Integrate with n8n, OpenClaw, and other automation tools</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('api-docs.json') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-muted text-muted-foreground rounded-lg hover:bg-muted/80 transition-colors">
                    <i data-lucide="file-json" class="w-4 h-4"></i>
                    OpenAPI Spec
                </a>
            </div>
        </div>

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

<script src="https://unpkg.com/@stoplight/elements/web-components.min.js"></script>
<script>
    const apiDescriptionDocument = {
        "openapi": "3.0.3",
        "info": {
            "title": "Architect Grid API",
            "version": "1.0.0",
            "description": "## Authentication\n\nAll API requests require a Bearer token. Include in header:\n```\nAuthorization: Bearer YOUR_TOKEN\n```\n\n## Base URL\n`{{ config('app.url') }}/api`\n\n## Rate Limits\n- 60 requests per minute for content creation\n- 30 requests per minute for publishing",
            "contact": {
                "name": "Support",
                "email": "support@architect.ai"
            }
        },
        "servers": [
            {
                "url": "{{ config('app.url') }}/api",
                "description": "Production Server"
            }
        ],
        "security": [
            {
                "bearerAuth": []
            }
        ],
        "components": {
            <script src="https://unpkg.com/@stoplight/elements/web-components.min.js"></script>
            @endsection
            "schemas": {
