{{-- Admin Dashboard - Waitlist Queue --}}
<div class="space-y-6" x-data="{
    isConverting: false,
    convertLead(id) {
        if (!confirm('Provision new tenant from this lead?')) return;
        this.isConverting = true;
        fetch(`/admin/waitlist/${id}/convert`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message + '\n\nInvitation Link: ' + data.invitation_url);
                window.location.reload();
            }
        })
        .finally(() => { this.isConverting = false; });
    }
}">
    <h3 class="text-sm font-black text-slate-500 uppercase tracking-[0.2em] px-1 text-cyan-500">Beta Protocol Queue</h3>
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 space-y-4 shadow-sm">
        @forelse($waitlistLeads as $lead)
            <div class="flex items-center gap-4 p-4 rounded-2xl bg-slate-950/50 border border-slate-800 group hover:border-cyan-500/30 transition-all">
                <div class="w-10 h-10 rounded-xl bg-cyan-500/10 flex items-center justify-center text-cyan-500 font-black">
                    {{ substr($lead->email, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-white truncate">{{ $lead->email }}</p>
                    <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest">{{ $lead->agency_name ?? 'Individual' }}</p>
                </div>
                @if($lead->status === 'pending')
                    <button @click="convertLead('{{ $lead->id }}')" :disabled="isConverting" class="opacity-0 group-hover:opacity-100 transition-opacity p-2 text-cyan-500 hover:bg-cyan-500/10 rounded-lg disabled:opacity-50">
                        <i data-lucide="zap" class="w-4 h-4 fill-current"></i>
                    </button>
                @else
                    <span class="text-[8px] font-black uppercase text-slate-600">Invited</span>
                @endif
            </div>
        @empty
            <div class="py-12 text-center text-slate-600 italic">
                <i data-lucide="inbox" class="w-10 h-10 mx-auto mb-3 opacity-20"></i>
                <p class="text-xs">Queue is empty.</p>
            </div>
        @endforelse
    </div>
</div>
