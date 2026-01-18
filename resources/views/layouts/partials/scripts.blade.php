{{-- 
    Global Scripts (Performance Optimized - Stable Version)
    
    This file loads global widgets and initializes icons.
    
    Strategy:
    - Widgets are included directly for reliability
    - Icons are refreshed efficiently with debouncing
    - Performance monitoring in dev mode only
--}}

{{-- Initialize Lucide icons from Vite bundle --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof window.refreshIcons === 'function') {
            window.refreshIcons();
        }
    });
</script>

{{-- Task & Note Widget --}}
@auth
    @include('components.note-task-widget')
@endauth

{{-- Global AI Agent Chat Widget --}}
@auth
    @php
        $globalAgent = \App\Models\AiAgent::where('tenant_id', auth()->user()->tenant_id)
            ->where('is_active', true)
            ->where('widget_enabled', true)
            ->first();
    @endphp
    
    @if($globalAgent)
        @include('components.ai-chat-widget', ['agent' => $globalAgent])
    @endif
@endauth

{{-- Page-specific scripts stack --}}
@stack('scripts')

{{-- Debounced Lucide refresh for dynamic content --}}
<script>
    (function() {
        let refreshTimeout;
        const debouncedRefresh = () => {
            clearTimeout(refreshTimeout);
            refreshTimeout = setTimeout(() => {
                if (typeof window.refreshIcons === 'function') {
                    window.refreshIcons();
                }
            }, 100);
        };
        
        // Observe DOM for dynamic content (for Alpine.js updates)
        if ('MutationObserver' in window) {
            const observer = new MutationObserver((mutations) => {
                const hasNewContent = mutations.some(m => m.addedNodes.length > 0);
                if (hasNewContent) {
                    debouncedRefresh();
                }
            });
            
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        }
        
        // Alpine integration
        document.addEventListener('alpine:initialized', debouncedRefresh);
    })();
</script>

{{-- Performance monitoring (dev only) --}}
@if(config('app.debug'))
<script>
    window.addEventListener('load', () => {
        setTimeout(() => {
            const perf = performance.getEntriesByType('navigation')[0];
            if (perf) {
                console.log('⚡ Performance Metrics:');
                console.table({
                    'TTFB (ms)': Math.round(perf.responseStart - perf.requestStart),
                    'DOM Interactive (ms)': Math.round(perf.domInteractive),
                    'DOM Complete (ms)': Math.round(perf.domComplete),
                    'Load Complete (ms)': Math.round(perf.loadEventEnd),
                });
            }
        }, 0);
    });
</script>
@endif
