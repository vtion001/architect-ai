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
            <button class="px-4 py-2 bg-primary text-primary-foreground rounded-lg text-sm font-bold shadow-lg hover:opacity-100" disabled title="Coming Phase 3">
                Edit Studio
            </button>
        </div>
    </div>
    
    <div class="flex-1 bg-slate-950 flex items-center justify-center p-8 relative overflow-hidden">
        <div id="player-container" class="shadow-2xl border border-slate-800 rounded-lg overflow-hidden bg-white"></div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/rrweb-player@1.0.0-alpha.10/dist/style.css" />
<script src="https://cdn.jsdelivr.net/npm/rrweb-player@1.0.0-alpha.10/dist/index.js"></script>

<script>
    // Hack to allow scripts in rrweb iframe
    const originalSetAttribute = Element.prototype.setAttribute;
    Element.prototype.setAttribute = function(name, value) {
        if (this.tagName === 'IFRAME' && name === 'sandbox') {
            if (!value.includes('allow-scripts')) {
                value += ' allow-scripts';
            }
            if (!value.includes('allow-same-origin')) {
                value += ' allow-same-origin';
            }
        }
        originalSetAttribute.call(this, name, value);
    };

    document.addEventListener('DOMContentLoaded', function() {
        if (window.lucide) window.lucide.createIcons();

        const events = @json($events);
        
        console.log('=== RRWEB PLAYER DEBUG ===');
        console.log('Total events:', events.length);
        console.log('First event (full snapshot):', events[0]);
        console.log('Event types:', events.map(e => e.type).slice(0, 20));
        
        if (!events || events.length === 0) {
            alert('No recording data found!');
            return;
        }
        
        // Check if first event has proper data
        if (events[0] && events[0].data && events[0].data.node) {
            console.log('First snapshot node:', events[0].data.node);
            console.log('Has childNodes:', events[0].data.node.childNodes?.length);
        }
        
        try {
            const player = new rrwebPlayer({
                target: document.getElementById('player-container'),
                props: {
                    events,
                    width: 1024,
                    height: 576,
                    autoPlay: false,  // Don't autoplay to inspect first frame
                    showController: true,
                    skipInactive: false,
                    speed: 1,
                    // Canvas replay
                    UNSAFE_replayCanvas: true,
                    // Mouse trail
                    mouseTail: {
                        duration: 500,
                        lineCap: 'round',
                        lineWidth: 3,
                        strokeStyle: 'red',
                    },
                    // Insert CSS rules
                    insertStyleRules: [
                        'iframe { background: white !important; }',
                        '* { font-family: inherit !important; }',
                    ],
                },
            });
            
            console.log('Player initialized successfully');
            console.log('Player instance:', player);
        } catch (e) {
            console.error('Player Init Failed:', e);
            console.error('Error stack:', e.stack);
            alert('Failed to initialize player: ' + e.message + '. Check console for details.');
        }
    });
</script>
@endsection
