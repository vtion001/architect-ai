@extends('layouts.app')

@section('content')
<div class="p-8 max-w-7xl mx-auto" x-data="{
    showCreateModal: false,
    newAgent: {
        name: '',
        role: '',
        goal: '',
        backstory: '',
        knowledge_sources: []
    },
    isSaving: false,
    saveAgent() {
        if (!this.newAgent.name || !this.newAgent.role || !this.newAgent.goal) {
            alert('Name, Role, and Goal are mandatory.');
            return;
        }
        this.isSaving = true;
        fetch('{{ route('ai-agents.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(this.newAgent)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Failed to create agent.');
                this.isSaving = false;
            }
        })
        .catch(err => {
            console.error(err);
            this.isSaving = false;
        });
    },
    deleteAgent(id) {
        if(confirm('Decommission this AI Agent?')) {
            fetch(`/ai-agents/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).then(() => window.location.reload());
        }
    }
}">
    <div class="mb-12 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black uppercase tracking-tighter text-foreground mb-2">AI Agent Protocol</h1>
            <p class="text-muted-foreground font-medium italic">Deploy specialized autonomous agents grounded in your knowledge base.</p>
        </div>
        <button @click="showCreateModal = true" class="bg-primary text-primary-foreground px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest shadow-xl shadow-primary/20 flex items-center gap-2 hover:scale-[1.02] transition-all">
            <i data-lucide="bot" class="w-4 h-4"></i>
            Deploy New Agent
        </button>
    </div>

    <!-- Agents Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($agents as $agent)
            <div class="bg-card border border-border rounded-[40px] p-8 shadow-sm hover:border-primary/30 transition-all group relative overflow-hidden flex flex-col">
                <!-- Header -->
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary/20 to-primary/5 border border-primary/10 flex items-center justify-center text-primary shadow-inner">
                        <i data-lucide="cpu" class="w-7 h-7"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-foreground uppercase tracking-tight">{{ $agent->name }}</h3>
                        <p class="text-[10px] font-bold text-primary uppercase tracking-widest">{{ $agent->role }}</p>
                    </div>
                </div>

                <!-- Goal -->
                <div class="bg-muted/30 rounded-2xl p-5 mb-6 flex-1 border border-border/50">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Primary Objective</p>
                    <p class="text-xs text-muted-foreground font-medium leading-relaxed italic">
                        "{{ Str::limit($agent->goal, 120) }}"
                    </p>
                </div>

                <!-- Knowledge Sources -->
                <div class="mb-6">
                    <div class="flex items-center gap-2 mb-2">
                        <i data-lucide="database" class="w-3 h-3 text-slate-400"></i>
                        <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">Grounding Sources</span>
                    </div>
                    <div class="flex flex-wrap gap-1">
                        @forelse(collect($agent->knowledge_sources)->take(3) as $sourceId)
                            @php $source = $knowledgeAssets->find($sourceId); @endphp
                            @if($source)
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 border border-slate-200 text-[8px] font-bold text-slate-600 uppercase tracking-tight truncate max-w-[100px]">
                                    {{ $source->title }}
                                </span>
                            @endif
                        @empty
                            <span class="text-[9px] text-slate-400 italic">No specific sources linked</span>
                        @endforelse
                        @if(count($agent->knowledge_sources ?? []) > 3)
                            <span class="px-2 py-0.5 text-[8px] text-slate-400 font-bold">+{{ count($agent->knowledge_sources) - 3 }} more</span>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-2 pt-6 border-t border-border/50">
                    <button class="flex-1 h-10 rounded-lg bg-primary text-primary-foreground font-bold uppercase text-[9px] tracking-widest shadow-lg shadow-primary/10 hover:bg-primary/90 transition-all flex items-center justify-center gap-2">
                        <i data-lucide="message-square" class="w-3 h-3"></i>
                        Chat
                    </button>
                    <button @click="deleteAgent('{{ $agent->id }}')" class="w-10 h-10 rounded-lg border border-red-200 bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-32 text-center space-y-6 opacity-50 italic border-2 border-dashed border-border rounded-[40px]">
                <div class="w-20 h-20 bg-muted/50 rounded-full flex items-center justify-center mx-auto">
                    <i data-lucide="bot" class="w-10 h-10 text-slate-400"></i>
                </div>
                <p class="text-sm font-medium">No agents deployed. Initialize your first autonomous worker.</p>
            </div>
        @endforelse
    </div>

    <!-- Create Agent Modal -->
    <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md p-4">
        <div @click.away="showCreateModal = false" class="bg-card w-full max-w-2xl rounded-[40px] shadow-2xl border border-border flex flex-col overflow-hidden animate-in zoom-in-95 duration-200">
            <!-- Modal Header -->
            <div class="p-8 border-b border-border bg-muted/30 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-black uppercase tracking-tighter text-foreground">Deploy New Agent</h2>
                    <p class="text-xs text-muted-foreground font-bold uppercase tracking-widest mt-1">Configure Identity & Knowledge</p>
                </div>
                <button @click="showCreateModal = false" class="w-8 h-8 rounded-full hover:bg-muted flex items-center justify-center text-slate-500 transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-8 overflow-y-auto max-h-[70vh] custom-scrollbar">
                <div class="space-y-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[9px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Designation (Name)</label>
                            <input x-model="newAgent.name" type="text" placeholder="e.g. Nexus-7" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[9px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Functional Role</label>
                            <input x-model="newAgent.role" type="text" placeholder="e.g. Market Research Analyst" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[9px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Primary Directive (Goal)</label>
                        <textarea x-model="newAgent.goal" rows="3" placeholder="Define the agent's main objective and success criteria..." class="w-full bg-muted/20 border border-border rounded-xl p-4 text-sm font-medium focus:ring-2 focus:ring-primary/20 outline-none"></textarea>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[9px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Backstory & Context</label>
                        <textarea x-model="newAgent.backstory" rows="3" placeholder="Provide personality traits or specific context for the agent..." class="w-full bg-muted/20 border border-border rounded-xl p-4 text-sm font-medium focus:ring-2 focus:ring-primary/20 outline-none"></textarea>
                    </div>

                    <div class="space-y-3 pt-4 border-t border-border/50">
                        <label class="text-[9px] font-black uppercase tracking-widest text-primary italic px-1 flex items-center gap-2">
                            <i data-lucide="link" class="w-3 h-3"></i>
                            Link Knowledge Sources
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-40 overflow-y-auto custom-scrollbar p-1">
                            @foreach($knowledgeAssets as $asset)
                                <label class="flex items-center gap-3 p-3 rounded-xl border border-border bg-card hover:border-primary/30 cursor-pointer transition-all">
                                    <input type="checkbox" value="{{ $asset->id }}" x-model="newAgent.knowledge_sources" class="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" 
                                         class="{{ $asset->type === 'folder' ? 'bg-amber-100 text-amber-600' : 'bg-blue-50 text-blue-500' }}">
                                        <i data-lucide="{{ $asset->type === 'folder' ? 'folder' : 'file-text' }}" class="w-4 h-4"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold truncate">{{ $asset->title }}</p>
                                        <p class="text-[9px] text-muted-foreground uppercase tracking-wider">{{ $asset->category }}</p>
                                    </div>
                                </label>
                            @endforeach
                            @if($knowledgeAssets->isEmpty())
                                <p class="text-xs text-muted-foreground col-span-2 italic">No knowledge assets available. Index data in the Knowledge Hub first.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-6 border-t border-border bg-muted/30 flex justify-end gap-3">
                <button @click="showCreateModal = false" class="px-6 py-3 rounded-xl border border-border font-black uppercase text-[10px] tracking-widest hover:bg-white transition-all">Cancel</button>
                <button @click="saveAgent" :disabled="isSaving" class="px-8 py-3 rounded-xl bg-primary text-primary-foreground font-black uppercase text-[10px] tracking-widest shadow-lg shadow-primary/20 hover:scale-[1.02] transition-all flex items-center gap-2 disabled:opacity-50">
                    <template x-if="isSaving"><i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i></template>
                    <span x-text="isSaving ? 'Deploying...' : 'Initialize Agent'"></span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
