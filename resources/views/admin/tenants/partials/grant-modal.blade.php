{{-- Tenant Show - Grant Resources Modal --}}
<div x-show="showGrantModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-md p-4">
    <div @click.away="!isGranting && (showGrantModal = false)" class="bg-slate-900 border border-slate-800 w-full max-w-sm rounded-3xl shadow-2xl p-10 animate-in zoom-in-95 duration-200">
        <h2 class="text-2xl font-black text-white mb-2 uppercase tracking-tighter">Provision Resources</h2>
        <p class="text-slate-400 text-sm mb-8 italic">Manually allocate tokens to this agency node.</p>

        <div class="space-y-6">
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Token Quantity</label>
                <input type="number" x-model="grantAmount" class="w-full h-14 bg-slate-950 border border-slate-800 rounded-2xl px-5 text-xl font-black text-cyan-500 outline-none focus:ring-1 focus:ring-cyan-500 transition-all">
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Allocation Reason</label>
                <input type="text" x-model="grantReason" class="w-full h-14 bg-slate-950 border border-slate-800 rounded-2xl px-5 text-sm font-bold text-white outline-none focus:ring-1 focus:ring-cyan-500 transition-all">
            </div>
            
            <div class="flex flex-col gap-3 pt-4">
                <button @click="grantTokens" :disabled="isGranting" class="w-full h-16 bg-cyan-500 text-black font-black uppercase tracking-[0.2em] text-xs rounded-2xl shadow-lg shadow-cyan-900/40 hover:bg-white transition-all flex items-center justify-center gap-3">
                    <template x-if="isGranting">
                        <i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i>
                    </template>
                    <span x-text="isGranting ? 'DISPATCHING...' : 'AUTHORIZE GRANT'"></span>
                </button>
                <button @click="showGrantModal = false" :disabled="isGranting" class="w-full h-14 bg-slate-800 text-slate-400 font-bold uppercase tracking-widest text-[10px] rounded-2xl hover:bg-slate-700 transition-all">Abort</button>
            </div>
        </div>
    </div>
</div>
