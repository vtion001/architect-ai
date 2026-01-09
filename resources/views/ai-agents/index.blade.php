@extends('layouts.app')

@section('content')
<div class="p-8 max-w-7xl mx-auto" x-data="{
    showCreateModal: false,
    showEditModal: false,
    selectedAgent: null,
    newAgent: {
        name: '',
        role: '',
        goal: '',
        backstory: '',
        knowledge_sources: [],
        primary_color: '#00F2FF',
        welcome_message: 'Hello! How can I assist you today?',
        temperature: 0.7,
        widget_position: 'bottom-right'
    },
    isSaving: false,
    
    resetNewAgent() {
        this.newAgent = {
            name: '',
            role: '',
            goal: '',
            backstory: '',
            knowledge_sources: [],
            primary_color: '#00F2FF',
            welcome_message: 'Hello! How can I assist you today?',
            temperature: 0.7,
            widget_position: 'bottom-right'
        };
    },
    
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
    
    editAgent(agent) {
        this.selectedAgent = { ...agent };
        this.showEditModal = true;
    },
    
    updateAgent() {
        this.isSaving = true;
        fetch(`/ai-agents/${this.selectedAgent.id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(this.selectedAgent)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Failed to update agent.');
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
        <button @click="showCreateModal = true; resetNewAgent()" class="bg-primary text-primary-foreground px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest shadow-xl shadow-primary/20 flex items-center gap-2 hover:scale-[1.02] transition-all">
            <i data-lucide="bot" class="w-4 h-4"></i>
            Deploy New Agent
        </button>
    </div>

    <!-- Agents Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($agents as $agent)
            <div class="bg-card border border-border rounded-[40px] p-8 shadow-sm hover:border-primary/30 transition-all group relative overflow-hidden flex flex-col">
                <!-- Status Badge -->
                <div class="absolute top-6 right-6">
                    <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest {{ $agent->is_active ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-500' }}">
                        {{ $agent->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                
                <!-- Header -->
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-14 h-14 rounded-2xl border border-primary/10 flex items-center justify-center shadow-inner overflow-hidden"
                         style="background: linear-gradient(135deg, {{ $agent->primary_color ?? '#00F2FF' }}30, {{ $agent->primary_color ?? '#00F2FF' }}10);">
                        @if($agent->avatar_url)
                            <img src="{{ $agent->avatar_url }}" alt="{{ $agent->name }}" class="w-14 h-14 object-cover">
                        @else
                            <i data-lucide="cpu" class="w-7 h-7" style="color: {{ $agent->primary_color ?? '#00F2FF' }}"></i>
                        @endif
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-foreground uppercase tracking-tight">{{ $agent->name }}</h3>
                        <p class="text-[10px] font-bold uppercase tracking-widest" style="color: {{ $agent->primary_color ?? '#00F2FF' }}">{{ $agent->role }}</p>
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
                <div class="flex gap-3 pt-6 border-t border-border/50">
                    <button @click="$store.aiChat.openWithAgent(@js($agent))" 
                            class="flex-1 h-12 rounded-xl text-white font-black uppercase text-[10px] tracking-widest shadow-lg hover:scale-[1.02] transition-all flex items-center justify-center gap-2"
                            style="background-color: {{ $agent->primary_color ?? '#00F2FF' }}; box-shadow: 0 8px 20px {{ $agent->primary_color ?? '#00F2FF' }}40;">
                        <i data-lucide="message-square" class="w-4 h-4"></i>
                        Open Console
                    </button>
                    <div class="flex gap-2">
                        <button @click="editAgent(@js($agent))" class="w-12 h-12 rounded-xl border border-border bg-card flex items-center justify-center hover:bg-muted transition-all text-slate-500" title="Reconfigure">
                            <i data-lucide="settings" class="w-4 h-4"></i>
                        </button>
                        <button @click="deleteAgent('{{ $agent->id }}')" class="w-12 h-12 rounded-xl border border-red-200 bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-600 hover:text-white transition-all" title="Decommission">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-40 text-center space-y-6 opacity-30 italic border-2 border-dashed border-border rounded-[40px]">
                <div class="w-24 h-24 bg-muted/50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="bot" class="w-12 h-12 text-slate-400"></i>
                </div>
                <p class="text-lg font-bold uppercase tracking-[0.2em]">No Agents Deployed</p>
                <p class="text-sm">Initialize your first autonomous intelligence node to begin.</p>
            </div>
        @endforelse
    </div>

    <!-- Create Agent Modal -->
    <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-[150] flex items-center justify-center bg-black/80 backdrop-blur-md p-4">
        <div @click.away="showCreateModal = false" class="bg-card w-full max-w-2xl rounded-[40px] shadow-2xl border border-border flex flex-col overflow-hidden animate-in zoom-in-95 duration-200">
            <!-- Modal Header -->
            <div class="p-10 border-b border-border bg-muted/30 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-black uppercase tracking-tighter text-foreground">Deploy New Agent</h2>
                    <p class="text-[10px] text-muted-foreground font-black uppercase tracking-[0.2em] mt-1">Grounding Identity & Knowledge Protocol</p>
                </div>
                <button @click="showCreateModal = false" class="w-10 h-10 rounded-full hover:bg-muted flex items-center justify-center text-slate-500 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-10 overflow-y-auto max-h-[65vh] custom-scrollbar">
                <div class="space-y-8">
                    <div class="grid grid-cols-2 gap-8">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Designation Name</label>
                            <input x-model="newAgent.name" type="text" required placeholder="e.g. Nexus-7" class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Functional Role</label>
                            <input x-model="newAgent.role" type="text" placeholder="e.g. Market Research Analyst" class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Primary Directive (Goal)</label>
                        <textarea x-model="newAgent.goal" rows="3" placeholder="Define the agent's main objective and success criteria..." class="w-full bg-muted/20 border border-border rounded-2xl p-6 text-sm font-medium focus:ring-2 focus:ring-primary/20 outline-none"></textarea>
                    </div>

                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Backstory & Context</label>
                        <textarea x-model="newAgent.backstory" rows="3" placeholder="Provide personality traits or specific context for the agent..." class="w-full bg-muted/20 border border-border rounded-2xl p-6 text-sm font-medium focus:ring-2 focus:ring-primary/20 outline-none"></textarea>
                    </div>

                    <!-- Appearance Settings -->
                    <div class="pt-6 border-t border-border/50 space-y-6">
                        <label class="text-[10px] font-black uppercase tracking-widest text-primary italic px-1 flex items-center gap-2">
                            <i data-lucide="palette" class="w-3 h-3"></i>
                            Appearance Matrix
                        </label>
                        <div class="grid grid-cols-2 gap-8">
                            <div class="space-y-3">
                                <label class="text-[9px] font-black uppercase text-slate-500 px-1">Primary Signature Color</label>
                                <div class="flex gap-3">
                                    <input x-model="newAgent.primary_color" type="color" class="w-14 h-14 rounded-2xl cursor-pointer border border-border bg-muted/20 p-1">
                                    <input x-model="newAgent.primary_color" type="text" class="flex-1 h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-mono font-bold uppercase">
                                </div>
                            </div>
                            <div class="space-y-3">
                                <label class="text-[9px] font-black uppercase text-slate-500 px-1">Widget Position</label>
                                <select x-model="newAgent.widget_position" class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-bold">
                                    <option value="bottom-right">Bottom Right</option>
                                    <option value="bottom-left">Bottom Left</option>
                                    <option value="top-right">Top Right</option>
                                    <option value="top-left">Top Left</option>
                                </select>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <label class="text-[9px] font-black uppercase text-slate-500 px-1">Welcome Protocol Message</label>
                            <input x-model="newAgent.welcome_message" type="text" placeholder="Hello! How can I assist you?" class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-bold">
                        </div>
                    </div>

                    <!-- Knowledge Sources -->
                    <div class="space-y-4 pt-6 border-t border-border/50">
                        <label class="text-[10px] font-black uppercase tracking-widest text-primary italic px-1 flex items-center gap-2">
                            <i data-lucide="link" class="w-3 h-3"></i>
                            Knowledge Grounding
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-60 overflow-y-auto custom-scrollbar p-1">
                            @foreach($knowledgeAssets as $asset)
                                <label class="flex items-center gap-4 p-4 rounded-2xl border border-border bg-card hover:border-primary/40 cursor-pointer transition-all group relative overflow-hidden">
                                    <div class="absolute inset-0 bg-primary/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                    <input type="checkbox" value="{{ $asset->id }}" x-model="newAgent.knowledge_sources" class="w-5 h-5 rounded-lg border-slate-300 text-primary focus:ring-primary relative z-10">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 relative z-10 {{ $asset->type === 'folder' ? 'bg-amber-100 text-amber-600' : 'bg-blue-100 text-blue-600' }}">
                                        <i data-lucide="{{ $asset->type === 'folder' ? 'folder' : 'file-text' }}" class="w-5 h-5"></i>
                                    </div>
                                    <div class="min-w-0 relative z-10">
                                        <p class="text-sm font-black truncate text-foreground">{{ $asset->title }}</p>
                                        <p class="text-[9px] text-muted-foreground uppercase font-bold tracking-widest">{{ $asset->category }}</p>
                                    </div>
                                </label>
                            @endforeach
                            @if($knowledgeAssets->isEmpty())
                                <div class="col-span-2 py-8 text-center bg-muted/10 border-2 border-dashed border-border rounded-3xl">
                                    <p class="text-xs text-muted-foreground italic">No intelligence nodes available. Index data in the Hub first.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-8 border-t border-border bg-muted/30 flex justify-end gap-4">
                <button @click="showCreateModal = false" class="px-8 py-4 rounded-2xl border border-border font-black uppercase text-[10px] tracking-widest hover:bg-white transition-all">Abort</button>
                <button @click="saveAgent" :disabled="isSaving" class="px-10 py-4 rounded-2xl bg-primary text-primary-foreground font-black uppercase text-[10px] tracking-[0.2em] shadow-xl shadow-primary/20 hover:scale-[1.02] transition-all flex items-center gap-3 disabled:opacity-50">
                    <template x-if="isSaving"><i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i></template>
                    <span x-text="isSaving ? 'INITIALIZING...' : 'Deploy Agent'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Edit Agent Modal -->
    <div x-show="showEditModal && selectedAgent" x-cloak class="fixed inset-0 z-[150] flex items-center justify-center bg-black/80 backdrop-blur-md p-4">
        <template x-if="selectedAgent">
            <div @click.away="showEditModal = false" class="bg-card w-full max-w-2xl rounded-[40px] shadow-2xl border border-border flex flex-col overflow-hidden animate-in zoom-in-95 duration-200">
                <div class="p-10 border-b border-border bg-muted/30 flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-black uppercase tracking-tighter text-foreground">Reconfigure Agent</h2>
                        <p class="text-[10px] text-muted-foreground font-black uppercase tracking-[0.2em] mt-1" x-text="'NODE: ' + selectedAgent.name"></p>
                    </div>
                    <button @click="showEditModal = false" class="w-10 h-10 rounded-full hover:bg-muted flex items-center justify-center text-slate-500 transition-colors">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <div class="p-10 overflow-y-auto max-h-[65vh] custom-scrollbar">
                    <div class="space-y-8">
                        <div class="grid grid-cols-2 gap-8">
                            <div class="space-y-3">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Designation</label>
                                <input x-model="selectedAgent.name" type="text" class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
                            </div>
                            <div class="space-y-3">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Role</label>
                                <input x-model="selectedAgent.role" type="text" class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
                            </div>
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Primary Directive</label>
                            <textarea x-model="selectedAgent.goal" rows="3" class="w-full bg-muted/20 border border-border rounded-2xl p-6 text-sm font-medium focus:ring-2 focus:ring-primary/20 outline-none"></textarea>
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Backstory context</label>
                            <textarea x-model="selectedAgent.backstory" rows="3" class="w-full bg-muted/20 border border-border rounded-2xl p-6 text-sm font-medium focus:ring-2 focus:ring-primary/20 outline-none"></textarea>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-8 pt-6 border-t border-border/50">
                            <div class="space-y-3">
                                <label class="text-[9px] font-black uppercase text-slate-500 px-1">Primary Signature Color</label>
                                <input x-model="selectedAgent.primary_color" type="color" class="w-full h-14 rounded-2xl cursor-pointer border border-border bg-muted/20 p-1">
                            </div>
                            <div class="space-y-3">
                                <label class="text-[9px] font-black uppercase text-slate-500 px-1">Intelligence Variance (Temp)</label>
                                <div class="flex items-center gap-4 h-14 px-5 bg-muted/20 border border-border rounded-2xl">
                                    <input x-model="selectedAgent.temperature" type="range" min="0" max="2" step="0.1" class="flex-1 accent-primary">
                                    <span class="mono text-xs font-bold text-primary w-8 text-right" x-text="selectedAgent.temperature"></span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-8 pt-4">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="checkbox" x-model="selectedAgent.is_active" class="w-6 h-6 rounded-lg border-slate-300 text-primary focus:ring-primary transition-all">
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-500 group-hover:text-foreground">Protocol Active</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="checkbox" x-model="selectedAgent.widget_enabled" class="w-6 h-6 rounded-lg border-slate-300 text-primary focus:ring-primary transition-all">
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-500 group-hover:text-foreground">Widget Enabled</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="p-8 border-t border-border bg-muted/30 flex justify-end gap-4">
                    <button @click="showEditModal = false" class="px-8 py-4 rounded-2xl border border-border font-black uppercase text-[10px] tracking-widest hover:bg-white transition-all">Cancel</button>
                    <button @click="updateAgent" :disabled="isSaving" class="px-10 py-4 rounded-2xl bg-primary text-primary-foreground font-black uppercase text-[10px] tracking-[0.2em] shadow-xl shadow-primary/20 hover:scale-[1.02] transition-all flex items-center gap-3 disabled:opacity-50">
                        <template x-if="isSaving"><i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i></template>
                        <span x-text="isSaving ? 'SYNCHRONIZING...' : 'Save Reconfiguration'"></span>
                    </button>
                </div>
            </div>
        </template>
    </div>
</div>
@endsection
