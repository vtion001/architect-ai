@extends('layouts.app')

@section('content')
<div class="flex flex-col h-[calc(100vh-64px)]">
    <div class="p-4 bg-card border-b border-border flex items-center justify-between shrink-0">
        <div class="flex items-center gap-3">
            <a href="/dashboard" class="p-2 hover:bg-muted rounded-lg transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="font-bold text-lg text-foreground flex items-center gap-2">
                    <i data-lucide="ghost" class="w-5 h-5 text-indigo-500"></i>
                    Ghost Studio
                </h1>
                <span class="text-xs text-muted-foreground">Feature Temporarily Disabled</span>
            </div>
        </div>
    </div>
    
    <div class="flex-1 bg-slate-950 flex items-center justify-center p-8">
        <div class="text-center space-y-6 max-w-md">
            <div class="w-24 h-24 mx-auto rounded-3xl bg-amber-500/10 flex items-center justify-center border border-amber-500/20">
                <i data-lucide="construction" class="w-12 h-12 text-amber-500"></i>
            </div>
            
            <div class="space-y-2">
                <h2 class="text-2xl font-black text-white uppercase tracking-tight">Coming Soon</h2>
                <p class="text-slate-400 text-sm">
                    Ghost Studio is being rebuilt with advanced screen recording capabilities. 
                    This feature will be available in a future update.
                </p>
            </div>
            
            <a href="/dashboard" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition-all">
                <i data-lucide="home" class="w-4 h-4"></i>
                Back to Dashboard
            </a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.lucide) window.lucide.createIcons();
    });
</script>
@endsection
