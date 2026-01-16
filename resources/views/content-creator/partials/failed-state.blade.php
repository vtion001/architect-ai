{{-- Failed State Partial --}}
<div class="min-h-[60vh] flex flex-col items-center justify-center animate-in fade-in duration-700">
    <div class="w-20 h-20 bg-red-50 text-red-500 rounded-full flex items-center justify-center mb-6 border border-red-100 shadow-sm">
        <i data-lucide="alert-triangle" class="w-10 h-10"></i>
    </div>
    <h2 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Generation Failed</h2>
    <p class="text-slate-500 mt-2 font-medium">The architect encountered an anomaly. Tokens have been refunded.</p>
    <div class="flex gap-4 mt-8" 
         x-data="{ 
             isDismissing: false, 
             dismiss() { 
                 if(!confirm('Dismiss failed entry?')) return; 
                 this.isDismissing = true; 
                 fetch('{{ route('content-creator.destroy', $content->id) }}', { 
                     method: 'DELETE', 
                     headers: {
                         'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                         'Accept': 'application/json'
                     } 
                 })
                 .then(res => res.json())
                 .then(data => { 
                     if(data.success) window.location.href = '{{ route('content-creator.index') }}'; 
                     else { alert(data.message); this.isDismissing = false; } 
                 })
                 .catch(e => { console.error(e); this.isDismissing = false; }); 
             } 
         }">
        <a href="{{ route('content-creator.index') }}" 
           class="px-8 py-4 bg-white border border-slate-200 text-slate-700 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-slate-50 transition-colors">
            Back to Dashboard
        </a>
        <button @click="dismiss" 
                :disabled="isDismissing" 
                class="px-8 py-4 bg-primary text-primary-foreground rounded-xl text-xs font-black uppercase tracking-widest hover:bg-primary/90 transition-all shadow-lg shadow-primary/20 disabled:opacity-50">
            <span x-show="!isDismissing">Dismiss & Retry</span>
            <span x-show="isDismissing">Dismissing...</span>
        </button>
    </div>
</div>
