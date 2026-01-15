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
                    {{ $document->name }}
                </h1>
                <span class="text-xs text-muted-foreground">Recorded on {{ $document->created_at->format('M d, Y \a\t H:i') }}</span>
            </div>
        </div>
        <div class="flex gap-2">
            <button class="px-4 py-2 bg-primary text-primary-foreground rounded-lg text-sm font-bold shadow-lg hover:opacity-90" disabled title="Coming Phase 3">
                Edit Studio
            </button>
        </div>
    </div>
    
    <div class="flex-1 bg-slate-950 flex items-center justify-center p-8 relative overflow-hidden">
        <div id="player-container" class="shadow-2xl border border-slate-800 rounded-lg overflow-hidden bg-white"></div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/rrweb-player@latest/dist/style.css" />
<script src="https://cdn.jsdelivr.net/npm/rrweb-player@latest/dist/index.js"></script>

<script>
    // Hack to allow scripts in rrweb iframe
    const originalSetAttribute = Element.prototype.setAttribute;
    Element.prototype.setAttribute = function(name, value) {
        if (this.tagName === 'IFRAME' && name === 'sandbox') {
            if (!value.includes('allow-scripts')) {
                value += ' allow-scripts';
            }
        }
        originalSetAttribute.call(this, name, value);
    };

    document.addEventListener('DOMContentLoaded', function() {
        if (window.lucide) window.lucide.createIcons();

        const events = @json($events);
        
        try {
            new rrwebPlayer({
                target: document.getElementById('player-container'),
                props: {
                    events,
                    width: 1024,
                    height: 576,
                    autoPlay: true,
                    showController: true,
                },
            });
        } catch (e) {
            console.error('Player Init Failed:', e);
            alert('Failed to initialize player. See console.');
        }
    });
</script>
@endsection
