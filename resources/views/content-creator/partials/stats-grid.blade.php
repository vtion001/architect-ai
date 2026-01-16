{{-- Stats Grid Partial --}}
<div class="grid grid-cols-3 gap-4 mb-8">
    <div class="p-4 rounded-xl border border-border bg-card">
        <p class="text-xs text-muted-foreground uppercase tracking-wider font-semibold">Length</p>
        <p class="text-2xl font-bold text-blue-500">{{ $content->word_count }} Words</p>
    </div>
    <div class="p-4 rounded-xl border border-border bg-card">
        <p class="text-xs text-muted-foreground uppercase tracking-wider font-semibold">Topic</p>
        <p class="text-lg font-bold truncate">{{ $content->topic }}</p>
    </div>
    <div class="p-4 rounded-xl border border-border bg-card">
        <p class="text-xs text-muted-foreground uppercase tracking-wider font-semibold">Status</p>
        <p class="text-2xl font-bold text-purple-500 uppercase">{{ $content->status }}</p>
    </div>
</div>
