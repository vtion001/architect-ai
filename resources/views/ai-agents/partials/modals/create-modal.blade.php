{{-- Create Agent Modal --}}
@props(['knowledgeAssets'])

<div x-show="showCreateModal" x-cloak class="fixed inset-0 z-[150] flex items-center justify-center bg-black/80 backdrop-blur-md p-4">
    <div @click.away="showCreateModal = false" class="bg-card w-full max-w-2xl rounded-[40px] shadow-2xl border border-border flex flex-col overflow-hidden animate-in zoom-in-95 duration-200">
        {{-- Modal Header --}}
        <div class="p-10 border-b border-border bg-muted/30 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-black uppercase tracking-tighter text-foreground">Deploy New Agent</h2>
                <p class="text-[10px] text-muted-foreground font-black uppercase tracking-[0.2em] mt-1">Grounding Identity & Knowledge Protocol</p>
            </div>
            <button @click="showCreateModal = false" class="w-10 h-10 rounded-full hover:bg-muted flex items-center justify-center text-slate-500 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        {{-- Modal Body --}}
        <div class="p-6 overflow-y-auto max-h-[75vh] custom-scrollbar">
            <div class="space-y-5">
                <div class="grid grid-cols-2 gap-5">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Designation Name</label>
                        <input x-model="newAgent.name" type="text" required placeholder="e.g. Nexus-7" class="w-full h-11 bg-muted/20 border border-border rounded-xl px-4 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Functional Role</label>
                        <input x-model="newAgent.role" type="text" placeholder="e.g. Market Research Analyst" class="w-full h-11 bg-muted/20 border border-border rounded-xl px-4 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Primary Directive (Goal)</label>
                    <textarea x-model="newAgent.goal" rows="2" placeholder="Define the agent's main objective and success criteria..." class="w-full bg-muted/20 border border-border rounded-xl p-4 text-sm font-medium focus:ring-2 focus:ring-primary/20 outline-none"></textarea>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Backstory & Context</label>
                    <textarea x-model="newAgent.backstory" rows="2" placeholder="Provide personality traits or specific context for the agent..." class="w-full bg-muted/20 border border-border rounded-xl p-4 text-sm font-medium focus:ring-2 focus:ring-primary/20 outline-none"></textarea>
                </div>

                {{-- Appearance Settings --}}
                <div class="pt-5 border-t border-border/50 space-y-4">
                    <label class="text-[10px] font-black uppercase tracking-widest text-primary italic px-1 flex items-center gap-2">
                        <i data-lucide="palette" class="w-3 h-3"></i>
                        Appearance Matrix
                    </label>
                    <div class="grid grid-cols-2 gap-5">
                        <div class="space-y-2">
                            <label class="text-[9px] font-black uppercase text-slate-500 px-1">Primary Signature Color</label>
                            <div class="flex gap-3">
                                <input x-model="newAgent.primary_color" type="color" class="w-11 h-11 rounded-lg cursor-pointer border border-border bg-muted/20 p-1">
                                <input x-model="newAgent.primary_color" type="text" class="flex-1 h-11 bg-muted/20 border border-border rounded-xl px-4 text-sm font-mono font-bold uppercase">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[9px] font-black uppercase text-slate-500 px-1">Widget Position</label>
                            <select x-model="newAgent.widget_position" class="w-full h-11 bg-muted/20 border border-border rounded-xl px-4 text-sm font-bold">
                                <option value="bottom-right">Bottom Right</option>
                                <option value="bottom-left">Bottom Left</option>
                                <option value="top-right">Top Right</option>
                                <option value="top-left">Top Left</option>
                            </select>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[9px] font-black uppercase text-slate-500 px-1">Welcome Protocol Message</label>
                        <input x-model="newAgent.welcome_message" type="text" placeholder="Hello! How can I assist you?" class="w-full h-11 bg-muted/20 border border-border rounded-xl px-4 text-sm font-bold">
                    </div>
                </div>

                {{-- Knowledge Sources --}}
                <div class="space-y-3 pt-5 border-t border-border/50">
                    <label class="text-[10px] font-black uppercase tracking-widest text-primary italic px-1 flex items-center gap-2">
                        <i data-lucide="link" class="w-3 h-3"></i>
                        Knowledge Grounding
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-48 overflow-y-auto custom-scrollbar p-1">
                        @foreach($knowledgeAssets as $asset)
                            <label class="flex items-center gap-3 p-3 rounded-xl border border-border bg-card hover:border-primary/40 cursor-pointer transition-all group relative overflow-hidden">
                                <div class="absolute inset-0 bg-primary/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                <input type="checkbox" value="{{ $asset->id }}" x-model="newAgent.knowledge_sources" class="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary relative z-10">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 relative z-10 {{ $asset->type === 'folder' ? 'bg-amber-100 text-amber-600' : 'bg-blue-100 text-blue-600' }}">
                                    <i data-lucide="{{ $asset->type === 'folder' ? 'folder' : 'file-text' }}" class="w-4 h-4"></i>
                                </div>
                                <div class="min-w-0 relative z-10">
                                    <p class="text-xs font-black truncate text-foreground">{{ $asset->title }}</p>
                                    <p class="text-[8px] text-muted-foreground uppercase font-bold tracking-widest">{{ $asset->category }}</p>
                                </div>
                            </label>
                        @endforeach
                        @if($knowledgeAssets->isEmpty())
                            <div class="col-span-2 py-6 text-center bg-muted/10 border-2 border-dashed border-border rounded-2xl">
                                <p class="text-[10px] text-muted-foreground italic">No intelligence nodes available.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Footer --}}
        <div class="p-8 border-t border-border bg-muted/30 flex justify-end gap-4">
            <button @click="showCreateModal = false" class="px-8 py-4 rounded-2xl border border-border font-black uppercase text-[10px] tracking-widest hover:bg-white transition-all">Abort</button>
            <button @click="saveAgent" :disabled="isSaving" class="px-10 py-4 rounded-2xl bg-primary text-primary-foreground font-black uppercase text-[10px] tracking-[0.2em] shadow-xl shadow-primary/20 hover:scale-[1.02] transition-all flex items-center gap-3 disabled:opacity-50">
                <template x-if="isSaving"><i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i></template>
                <span x-text="isSaving ? 'INITIALIZING...' : 'Deploy Agent'"></span>
            </button>
        </div>
    </div>
</div>
