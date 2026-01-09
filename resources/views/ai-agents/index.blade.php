@extends('layouts.app')

@section('content')
<div class="p-8 max-w-7xl mx-auto" x-data="{
    showCreateModal: false,
    showEditModal: false,
    showChatModal: false,
    selectedAgent: null,
    chatMessages: [],
    chatInput: '',
    chatSessionId: null,
    isChatting: false,
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
    
    openChat(agent) {
        this.selectedAgent = agent;
        this.chatMessages = [];
        this.chatInput = '';
        this.chatSessionId = localStorage.getItem(`ai_chat_session_${agent.id}`) || this.generateSessionId();
        localStorage.setItem(`ai_chat_session_${agent.id}`, this.chatSessionId);
        this.showChatModal = true;
        this.loadChatHistory();
    },
    
    generateSessionId() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            const r = Math.random() * 16 | 0;
            const v = c === 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    },
    
    async loadChatHistory() {
        try {
            const res = await fetch(`/ai-agents/conversation?agent_id=${this.selectedAgent.id}&session_id=${this.chatSessionId}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            const data = await res.json();
            if (data.messages) {
                this.chatMessages = data.messages;
            }
        } catch (e) {
            console.error(e);
        }
    },
    
    async sendChat() {
        if (!this.chatInput.trim() || this.isChatting) return;
        
        const message = this.chatInput.trim();
        this.chatInput = '';
        this.chatMessages.push({ role: 'user', content: message });
        this.isChatting = true;
        
        try {
            const res = await fetch('/ai-agents/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    agent_id: this.selectedAgent.id,
                    message: message,
                    session_id: this.chatSessionId
                })
            });
            
            const data = await res.json();
            if (data.success) {
                this.chatMessages.push({ role: 'assistant', content: data.message });
                this.chatSessionId = data.session_id;
                localStorage.setItem(`ai_chat_session_${this.selectedAgent.id}`, this.chatSessionId);
            } else {
                this.chatMessages.push({ role: 'assistant', content: 'Error: ' + (data.message || 'Failed to get response.') });
            }
        } catch (e) {
            this.chatMessages.push({ role: 'assistant', content: 'Connection error. Please try again.' });
        } finally {
            this.isChatting = false;
            this.$nextTick(() => {
                const container = document.getElementById('chatMessages');
                if (container) container.scrollTop = container.scrollHeight;
            });
        }
    },
    
    async clearChatHistory() {
        if (!confirm('Clear conversation history?')) return;
        try {
            await fetch('/ai-agents/conversation/clear', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    agent_id: this.selectedAgent.id,
                    session_id: this.chatSessionId
                })
            });
            this.chatMessages = [];
            this.chatSessionId = this.generateSessionId();
            localStorage.setItem(`ai_chat_session_${this.selectedAgent.id}`, this.chatSessionId);
        } catch (e) {
            console.error(e);
        }
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
                <div class="flex gap-2 pt-6 border-t border-border/50">
                    <button @click="openChat(@js($agent))" class="flex-1 h-10 rounded-lg text-white font-bold uppercase text-[9px] tracking-widest shadow-lg hover:opacity-90 transition-all flex items-center justify-center gap-2"
                            style="background-color: {{ $agent->primary_color ?? '#00F2FF' }}; box-shadow: 0 4px 14px {{ $agent->primary_color ?? '#00F2FF' }}30;">
                        <i data-lucide="message-square" class="w-3 h-3"></i>
                        Chat
                    </button>
                    <button @click="editAgent(@js($agent))" class="w-10 h-10 rounded-lg border border-border bg-card flex items-center justify-center hover:bg-muted transition-all text-slate-500">
                        <i data-lucide="settings" class="w-4 h-4"></i>
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

                    <!-- Appearance Settings -->
                    <div class="pt-4 border-t border-border/50 space-y-4">
                        <label class="text-[9px] font-black uppercase tracking-widest text-primary italic px-1 flex items-center gap-2">
                            <i data-lucide="palette" class="w-3 h-3"></i>
                            Appearance Settings
                        </label>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-[9px] font-bold text-slate-500 px-1">Primary Color</label>
                                <input x-model="newAgent.primary_color" type="color" class="w-full h-10 rounded-xl cursor-pointer border border-border">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[9px] font-bold text-slate-500 px-1">Widget Position</label>
                                <select x-model="newAgent.widget_position" class="w-full h-10 bg-muted/20 border border-border rounded-xl px-3 text-sm">
                                    <option value="bottom-right">Bottom Right</option>
                                    <option value="bottom-left">Bottom Left</option>
                                    <option value="top-right">Top Right</option>
                                    <option value="top-left">Top Left</option>
                                </select>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[9px] font-bold text-slate-500 px-1">Welcome Message</label>
                            <input x-model="newAgent.welcome_message" type="text" placeholder="Hello! How can I assist you?" class="w-full h-10 bg-muted/20 border border-border rounded-xl px-4 text-sm">
                        </div>
                    </div>

                    <!-- Knowledge Sources -->
                    <div class="space-y-3 pt-4 border-t border-border/50">
                        <label class="text-[9px] font-black uppercase tracking-widest text-primary italic px-1 flex items-center gap-2">
                            <i data-lucide="link" class="w-3 h-3"></i>
                            Link Knowledge Sources
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-40 overflow-y-auto custom-scrollbar p-1">
                            @foreach($knowledgeAssets as $asset)
                                <label class="flex items-center gap-3 p-3 rounded-xl border border-border bg-card hover:border-primary/30 cursor-pointer transition-all">
                                    <input type="checkbox" value="{{ $asset->id }}" x-model="newAgent.knowledge_sources" class="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 {{ $asset->type === 'folder' ? 'bg-amber-100 text-amber-600' : 'bg-blue-50 text-blue-500' }}">
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

    <!-- Edit Agent Modal -->
    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md p-4">
        <div @click.away="showEditModal = false" class="bg-card w-full max-w-2xl rounded-[40px] shadow-2xl border border-border flex flex-col overflow-hidden animate-in zoom-in-95 duration-200">
            <div class="p-8 border-b border-border bg-muted/30 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-black uppercase tracking-tighter text-foreground">Configure Agent</h2>
                    <p class="text-xs text-muted-foreground font-bold uppercase tracking-widest mt-1" x-text="selectedAgent?.name"></p>
                </div>
                <button @click="showEditModal = false" class="w-8 h-8 rounded-full hover:bg-muted flex items-center justify-center text-slate-500 transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <div class="p-8 overflow-y-auto max-h-[70vh] custom-scrollbar">
                <div class="space-y-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[9px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Name</label>
                            <input x-model="selectedAgent.name" type="text" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[9px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Role</label>
                            <input x-model="selectedAgent.role" type="text" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[9px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Goal</label>
                        <textarea x-model="selectedAgent.goal" rows="3" class="w-full bg-muted/20 border border-border rounded-xl p-4 text-sm font-medium focus:ring-2 focus:ring-primary/20 outline-none"></textarea>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[9px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Backstory</label>
                        <textarea x-model="selectedAgent.backstory" rows="3" class="w-full bg-muted/20 border border-border rounded-xl p-4 text-sm font-medium focus:ring-2 focus:ring-primary/20 outline-none"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[9px] font-bold text-slate-500 px-1">Primary Color</label>
                            <input x-model="selectedAgent.primary_color" type="color" class="w-full h-10 rounded-xl cursor-pointer border border-border">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[9px] font-bold text-slate-500 px-1">Temperature (Creativity)</label>
                            <input x-model="selectedAgent.temperature" type="range" min="0" max="2" step="0.1" class="w-full">
                            <span class="text-xs text-slate-500" x-text="selectedAgent?.temperature"></span>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" x-model="selectedAgent.is_active" class="w-4 h-4 rounded text-primary">
                            <span class="text-sm font-bold">Active</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" x-model="selectedAgent.widget_enabled" class="w-4 h-4 rounded text-primary">
                            <span class="text-sm font-bold">Widget Enabled</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="p-6 border-t border-border bg-muted/30 flex justify-end gap-3">
                <button @click="showEditModal = false" class="px-6 py-3 rounded-xl border border-border font-black uppercase text-[10px] tracking-widest hover:bg-white transition-all">Cancel</button>
                <button @click="updateAgent" :disabled="isSaving" class="px-8 py-3 rounded-xl bg-primary text-primary-foreground font-black uppercase text-[10px] tracking-widest shadow-lg shadow-primary/20 hover:scale-[1.02] transition-all flex items-center gap-2 disabled:opacity-50">
                    <template x-if="isSaving"><i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i></template>
                    <span x-text="isSaving ? 'Saving...' : 'Save Changes'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Chat Modal -->
    <div x-show="showChatModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md p-4">
        <div @click.away="showChatModal = false" class="bg-card w-full max-w-xl rounded-[32px] shadow-2xl border border-border flex flex-col overflow-hidden animate-in zoom-in-95 duration-200" style="height: 600px;">
            <!-- Chat Header -->
            <div class="px-6 py-4 border-b border-border flex items-center justify-between"
                 :style="{ background: `linear-gradient(135deg, ${selectedAgent?.primary_color || '#00F2FF'}15, ${selectedAgent?.primary_color || '#00F2FF'}05)` }">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                         :style="{ backgroundColor: (selectedAgent?.primary_color || '#00F2FF') + '20', color: selectedAgent?.primary_color || '#00F2FF' }">
                        <i data-lucide="bot" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-foreground" x-text="selectedAgent?.name"></h3>
                        <p class="text-[10px] text-muted-foreground uppercase tracking-wider" x-text="selectedAgent?.role"></p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="clearChatHistory()" class="w-8 h-8 rounded-full hover:bg-muted flex items-center justify-center text-muted-foreground transition-colors" title="Clear history">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                    <button @click="showChatModal = false" class="w-8 h-8 rounded-full hover:bg-muted flex items-center justify-center text-muted-foreground transition-colors">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            <!-- Messages -->
            <div id="chatMessages" class="flex-1 overflow-y-auto p-4 space-y-4 custom-scrollbar">
                <!-- Welcome -->
                <template x-if="chatMessages.length === 0">
                    <div class="flex gap-3">
                        <div class="w-8 h-8 rounded-lg shrink-0 flex items-center justify-center"
                             :style="{ backgroundColor: (selectedAgent?.primary_color || '#00F2FF') + '20', color: selectedAgent?.primary_color || '#00F2FF' }">
                            <i data-lucide="bot" class="w-4 h-4"></i>
                        </div>
                        <div class="bg-muted/50 rounded-2xl rounded-tl-md px-4 py-3 max-w-[80%]">
                            <p class="text-sm text-foreground" x-text="selectedAgent?.welcome_message || 'Hello! How can I help you?'"></p>
                        </div>
                    </div>
                </template>

                <template x-for="(msg, i) in chatMessages" :key="i">
                    <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex gap-3'">
                        <template x-if="msg.role === 'assistant'">
                            <div class="w-8 h-8 rounded-lg shrink-0 flex items-center justify-center"
                                 :style="{ backgroundColor: (selectedAgent?.primary_color || '#00F2FF') + '20', color: selectedAgent?.primary_color || '#00F2FF' }">
                                <i data-lucide="bot" class="w-4 h-4"></i>
                            </div>
                        </template>
                        <div :class="msg.role === 'user' ? 'bg-primary text-primary-foreground rounded-2xl rounded-tr-md px-4 py-3 max-w-[80%]' : 'bg-muted/50 rounded-2xl rounded-tl-md px-4 py-3 max-w-[80%]'">
                            <p class="text-sm whitespace-pre-wrap" x-text="msg.content"></p>
                        </div>
                    </div>
                </template>

                <!-- Typing -->
                <div x-show="isChatting" class="flex gap-3">
                    <div class="w-8 h-8 rounded-lg shrink-0 flex items-center justify-center"
                         :style="{ backgroundColor: (selectedAgent?.primary_color || '#00F2FF') + '20', color: selectedAgent?.primary_color || '#00F2FF' }">
                        <i data-lucide="bot" class="w-4 h-4"></i>
                    </div>
                    <div class="bg-muted/50 rounded-2xl rounded-tl-md px-4 py-3">
                        <div class="flex gap-1">
                            <span class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                            <span class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                            <span class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Input -->
            <div class="p-4 border-t border-border bg-muted/30">
                <form @submit.prevent="sendChat" class="flex gap-2">
                    <input type="text" x-model="chatInput" :disabled="isChatting" placeholder="Type your message..." class="flex-1 h-11 px-4 rounded-xl border border-border bg-card text-sm focus:ring-2 focus:ring-primary/20 outline-none disabled:opacity-50">
                    <button type="submit" :disabled="!chatInput.trim() || isChatting" class="w-11 h-11 rounded-xl flex items-center justify-center text-white transition-all disabled:opacity-50"
                            :style="{ backgroundColor: selectedAgent?.primary_color || '#00F2FF' }">
                        <i data-lucide="send" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
