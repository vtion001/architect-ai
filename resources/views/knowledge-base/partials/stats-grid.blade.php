{{-- Knowledge Hub Stats Grid --}}
@props(['stats'])

<div x-show="!currentFolder" class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
    <div class="bg-card border border-border rounded-3xl p-6 flex items-center gap-4 shadow-sm">
        <div class="w-12 h-12 bg-blue-500/10 rounded-2xl flex items-center justify-center text-blue-500">
            <i data-lucide="database" class="w-6 h-6"></i>
        </div>
        <div>
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Total Assets</p>
            <p class="text-2xl font-black text-white">{{ $stats['total_docs'] }}</p>
        </div>
    </div>
    <div class="bg-card border border-border rounded-3xl p-6 flex items-center gap-4 shadow-sm">
        <div class="w-12 h-12 bg-green-500/10 rounded-2xl flex items-center justify-center text-green-500">
            <i data-lucide="tag" class="w-6 h-6"></i>
        </div>
        <div>
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Categories</p>
            <p class="text-2xl font-black text-white">{{ $stats['categories'] }}</p>
        </div>
    </div>
    <div class="bg-card border border-border rounded-3xl p-6 flex items-center gap-4 shadow-sm">
        <div class="w-12 h-12 bg-purple-500/10 rounded-2xl flex items-center justify-center text-purple-500">
            <i data-lucide="refresh-ccw" class="w-6 h-6"></i>
        </div>
        <div>
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Active Syncs</p>
            <p class="text-2xl font-black text-white">{{ $stats['recent_updates'] }}</p>
        </div>
    </div>
</div>
