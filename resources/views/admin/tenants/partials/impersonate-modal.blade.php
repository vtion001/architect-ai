{{-- Tenant Show - Impersonation Modal --}}
<div x-show="showImpersonateModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-md p-4">
    <div @click.away="!isImpersonating && (showImpersonateModal = false)" class="bg-slate-900 border border-slate-800 w-full max-w-md rounded-3xl shadow-2xl p-8 text-center animate-in fade-in zoom-in-95 duration-200">
        <div class="w-20 h-20 rounded-full bg-red-600/10 flex items-center justify-center mx-auto mb-6 border border-red-600/20">
            <i data-lucide="user-plus" class="w-10 h-10 text-red-600"></i>
        </div>
        <h2 class="text-2xl font-black text-white mb-2">Initiate Impersonation</h2>
        <p class="text-slate-400 text-sm mb-8 leading-relaxed">
            You are about to access the system as <span class="text-white font-bold" x-text="selectedUser?.email"></span>. 
            This action will be strictly audited under the IAM Break-Glass Protocol.
        </p>

        <div class="space-y-4">
            <textarea x-model="justification" class="w-full h-24 bg-slate-950 border border-slate-800 rounded-2xl p-4 text-xs text-slate-300 outline-none focus:ring-1 focus:ring-red-600 transition-all" placeholder="Required: Why do you need to impersonate this user? (min 10 chars)"></textarea>
            
            <div class="flex flex-col gap-3">
                <button @click="impersonate" :disabled="isImpersonating || justification.length < 10" class="w-full py-4 rounded-2xl bg-red-600 text-white font-black uppercase tracking-widest text-xs shadow-lg shadow-red-900/40 hover:bg-red-700 disabled:opacity-50 transition-all">
                    <span x-show="!isImpersonating">Authorize & Enter Session</span>
                    <span x-show="isImpersonating">Provisioning Access...</span>
                </button>
                <button @click="showImpersonateModal = false" :disabled="isImpersonating" class="w-full py-4 rounded-2xl bg-slate-800 text-slate-400 font-bold uppercase tracking-widest text-xs hover:bg-slate-700 transition-all">
                    Abort Protocol
                </button>
            </div>
        </div>
    </div>
</div>
