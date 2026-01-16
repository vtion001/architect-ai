{{-- Global Scripts --}}
<script>
    if (window.lucide) {
        lucide.createIcons();
    }
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

@stack('scripts')

{{-- Re-initialize Lucide icons after dynamic content --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.lucide) {
            lucide.createIcons();
        }
    });
    // Also refresh on Alpine init
    document.addEventListener('alpine:init', () => {
        Alpine.effect(() => {
            if (window.lucide) {
                setTimeout(() => lucide.createIcons(), 100);
            }
        });
    });
</script>
