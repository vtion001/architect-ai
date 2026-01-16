{{-- God View - Beta Lead Hub --}}
<div class="lg:col-span-4 space-y-6" x-data="{
    isConverting: false,
    convertLead(id) {
        if (!confirm('Provision new tenant from this master lead?')) return;
        this.isConverting = true;
        fetch(`/admin/waitlist/${id}/convert`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Identity Provisioned Successfully.\n\nLink: ' + data.invitation_url);
                window.location.reload();
            }
        })
        .finally(() => { this.isConverting = false; });
    }
}">
    <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1 text-cyan-500">Master Lead Provisioning</h3>
    <div class="bg-slate-900 border border-slate-800 rounded-[40px] p-8 space-y-6 shadow-2xl relative overflow-hidden">
        <div class="absolute -top-20 -right-20 w-40 h-40 bg-cyan-400/5 rounded-full blur-3xl"></div>
        
        @forelse($waitlistEntries as $entry)
            @include('admin.partials.god-view.lead-card', ['entry' => $entry])
        @empty
            <div class="py-20 text-center opacity-30 italic">
                <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-4"></i>
                <p class="text-xs font-bold uppercase tracking-widest">Lead queue empty</p>
            </div>
        @endforelse

        <a href="/admin/tenants" class="w-full h-14 bg-slate-800 border border-slate-700 rounded-2xl font-black uppercase text-[9px] tracking-widest hover:bg-slate-700 text-white transition-all flex items-center justify-center gap-3">
            Grid Explorer
            <i data-lucide="arrow-right" class="w-4 h-4"></i>
        </a>
    </div>
</div>
