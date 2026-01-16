{{-- API Protocols Tab --}}
@props(['apiTokens'])

<div x-show="activeTab === 'api'" class="space-y-10 animate-in fade-in duration-300">
    <div class="bg-card border border-border rounded-[40px] p-10 shadow-sm relative overflow-hidden">
        <div class="absolute -top-20 -right-20 w-40 h-40 bg-primary/5 rounded-full blur-3xl"></div>
        
        <h2 class="text-2xl font-black uppercase tracking-tighter mb-2">Industrial API Nodes</h2>
        <p class="text-sm text-muted-foreground mb-10 italic font-medium">Provision secure access keys for external automated integration.</p>

        @if(session('plain_text_token'))
            <div class="mb-10 p-8 rounded-3xl bg-emerald-50 border border-emerald-100 animate-in zoom-in-95 duration-300">
                <div class="flex items-center gap-3 text-emerald-600 mb-4">
                    <i data-lucide="shield-check" class="w-5 h-5"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest">New Identity Key Generated</span>
                </div>
                <p class="text-xs text-emerald-800 mb-4 italic leading-relaxed">This key will only be shown once. Ingest it into your authorized application immediately.</p>
                <div class="flex gap-2">
                    <code class="flex-1 bg-white border border-emerald-200 rounded-xl p-4 text-xs font-mono font-bold text-emerald-900 break-all">{{ session('plain_text_token') }}</code>
                    <button @click="navigator.clipboard.writeText('{{ session('plain_text_token') }}'); alert('Key captured to grid buffer.')" 
                            class="px-6 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition-all">
                        <i data-lucide="copy" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        @endif

        {{-- Key Generator --}}
        <form action="{{ route('settings.api.generate') }}" method="POST" class="p-8 rounded-3xl bg-muted/20 border border-border mb-12">
            @csrf
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1 space-y-2">
                    <label class="text-[9px] font-black uppercase text-slate-500 tracking-widest px-1">Node Identifier</label>
                    <input type="text" name="token_name" required placeholder="e.g. Zapier Workflow"
                           class="w-full h-12 bg-card border border-border rounded-xl px-4 text-xs font-bold focus:ring-1 focus:ring-primary outline-none">
                </div>
                <div class="md:pt-5">
                    <button type="submit" class="h-12 px-8 bg-primary text-primary-foreground rounded-xl font-black uppercase text-[10px] tracking-widest shadow-lg shadow-primary/20 hover:scale-[1.02] transition-all">Generate Node</button>
                </div>
            </div>
        </form>

        {{-- Active Registry --}}
        <div class="space-y-6">
            <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1 italic">Active Identity Nodes</h3>
            <div class="grid grid-cols-1 gap-4">
                @forelse($apiTokens as $token)
                    <div class="p-6 rounded-3xl border border-border bg-muted/5 flex items-center justify-between group hover:border-primary/30 transition-all">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-primary/5 flex items-center justify-center text-primary border border-primary/10">
                                <i data-lucide="key" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h4 class="font-black text-xs uppercase tracking-tight text-foreground">{{ $token->name }}</h4>
                                <p class="text-[9px] text-slate-500 mt-1 uppercase tracking-widest">Last usage: {{ $token->last_used_at ? $token->last_used_at->diffForHumans() : 'Never' }}</p>
                            </div>
                        </div>
                        <form action="{{ route('settings.api.revoke', $token->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-10 h-10 rounded-xl hover:bg-red-50 text-slate-400 hover:text-red-500 transition-all">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="py-12 text-center opacity-30 italic">
                        <i data-lucide="terminal" class="w-10 h-10 mx-auto mb-3"></i>
                        <p class="text-[10px] font-black uppercase tracking-widest">No external nodes provisioned</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Technical Specs Node --}}
    <div class="bg-card border border-border rounded-[40px] p-10 shadow-sm">
        <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-6 px-1 italic">Technical Specifications</h3>
        <div class="p-6 bg-slate-950 rounded-3xl border border-white/5 mono text-[10px] text-slate-400 leading-relaxed overflow-x-auto">
            <p class="text-primary mb-2">// Authenticate your external grid node</p>
            <p class="mb-4">curl -X POST "{{ config('app.url') }}/api/v1/content/generate" \</p>
            <p class="pl-4">-H "Authorization: Bearer <span class="text-white">YOUR_IDENTITY_KEY</span>" \</p>
            <p class="pl-4">-H "Accept: application/json" \</p>
            <p class="pl-4">-d "topic=Modern Architecture" \</p>
            <p class="pl-4">-d "type=social-post"</p>
        </div>
    </div>
</div>
