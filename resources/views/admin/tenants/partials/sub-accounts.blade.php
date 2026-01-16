{{-- Tenant Show - Sub-Accounts Grid --}}
@if($tenant->subAccounts->isNotEmpty())
<div class="space-y-4">
    <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Nested Child Accounts</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($tenant->subAccounts as $sub)
            <a href="{{ route('admin.tenants.show', $sub) }}" class="p-4 rounded-2xl bg-slate-900 border border-slate-800 hover:border-blue-500/30 transition-all flex items-center justify-between">
                <div>
                    <p class="font-bold text-white text-sm">{{ $sub->name }}</p>
                    <p class="text-[10px] text-slate-500">/{{ $sub->slug }}</p>
                </div>
                <i data-lucide="chevron-right" class="w-4 h-4 text-slate-700"></i>
            </a>
        @endforeach
    </div>
</div>
@endif
