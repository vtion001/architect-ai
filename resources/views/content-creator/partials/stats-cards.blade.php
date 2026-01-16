{{-- Content Creator Stats Cards Partial --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    {{-- Total Content --}}
    <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
        <div class="p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-muted-foreground">Total Content</p>
                    <p class="text-2xl font-bold">{{ number_format($stats['total_content']) }}</p>
                </div>
                <i data-lucide="file-text" class="w-8 h-8 text-blue-500"></i>
            </div>
        </div>
    </div>

    {{-- This Month --}}
    <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
        <div class="p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-muted-foreground">This Month</p>
                    <p class="text-2xl font-bold">{{ number_format($stats['this_month']) }}</p>
                </div>
                <i data-lucide="trending-up" class="w-8 h-8 text-green-500"></i>
            </div>
        </div>
    </div>

    {{-- In Draft --}}
    <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
        <div class="p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-muted-foreground">In Draft</p>
                    <p class="text-2xl font-bold">{{ number_format($stats['in_draft']) }}</p>
                </div>
                <i data-lucide="pencil" class="w-8 h-8 text-amber-500"></i>
            </div>
        </div>
    </div>

    {{-- Published --}}
    <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
        <div class="p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-muted-foreground">Published</p>
                    <p class="text-2xl font-bold">{{ number_format($stats['published']) }}</p>
                </div>
                <i data-lucide="sparkles" class="w-8 h-8 text-purple-500"></i>
            </div>
        </div>
    </div>
</div>
