@extends('layouts.app')

@section('content')
<div class="p-8 max-w-7xl mx-auto" x-data="{ 
    activeTab: '{{ $activeTab }}',
    brandColor: '{{ $tenant->metadata['primary_color'] ?? '#00F2FF' }}',
    tempColor: '{{ $tenant->metadata['primary_color'] ?? '#00F2FF' }}'
}">
    <div class="mb-12 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black uppercase tracking-tighter text-foreground mb-2">Grid Configuration</h1>
            <p class="text-muted-foreground font-medium italic">Adjust the identity, resources, and security protocols of your agency workspace.</p>
        </div>
        <div class="px-4 py-2 rounded-xl bg-primary/10 border border-primary/20 flex items-center gap-3">
            <i data-lucide="coins" class="w-4 h-4 text-primary"></i>
            <span class="text-xs font-black uppercase text-primary tracking-widest">{{ number_format($tokenBalance) }} Tokens</span>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-8 p-4 rounded-xl bg-green-50 border border-green-100 text-green-600 text-[10px] font-black uppercase tracking-widest flex items-center gap-3 animate-in slide-in-from-top-2">
            <i data-lucide="check-circle" class="w-4 h-4"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        <!-- Navigation Nodes -->
        <div class="lg:col-span-3 space-y-2">
            <button @click="activeTab = 'profile'" :class="activeTab === 'profile' ? 'bg-primary text-primary-foreground' : 'hover:bg-muted text-muted-foreground'" class="w-full flex items-center gap-3 px-6 py-4 rounded-2xl text-xs font-black uppercase tracking-widest transition-all">
                <i data-lucide="user" class="w-4 h-4"></i>
                Personal Identity
            </button>
            
            @if(auth()->user()->tenant->type === 'agency')
            <button @click="activeTab = 'branding'" :class="activeTab === 'branding' ? 'bg-primary text-primary-foreground' : 'hover:bg-muted text-muted-foreground'" class="w-full flex items-center gap-3 px-6 py-4 rounded-2xl text-xs font-black uppercase tracking-widest transition-all">
                <i data-lucide="palette" class="w-4 h-4"></i>
                Visual DNA
            </button>
            <a href="{{ route('brands.index') }}" class="w-full flex items-center gap-3 px-6 py-4 rounded-2xl text-xs font-black uppercase tracking-widest transition-all hover:bg-muted text-muted-foreground">
                <i data-lucide="fingerprint" class="w-4 h-4"></i>
                Brand Kits
            </a>
            <button @click="activeTab = 'billing'" :class="activeTab === 'billing' ? 'bg-primary text-primary-foreground' : 'hover:bg-muted text-muted-foreground'" class="w-full flex items-center gap-3 px-6 py-4 rounded-2xl text-xs font-black uppercase tracking-widest transition-all">
                <i data-lucide="credit-card" class="w-4 h-4"></i>
                Resource Treasury
            </button>
            @endif

            <button @click="activeTab = 'security'" :class="activeTab === 'security' ? 'bg-primary text-primary-foreground' : 'hover:bg-muted text-muted-foreground'" class="w-full flex items-center gap-3 px-6 py-4 rounded-2xl text-xs font-black uppercase tracking-widest transition-all">
                <i data-lucide="shield-check" class="w-4 h-4"></i>
                Security Hub
            </button>
            <button @click="activeTab = 'api'" :class="activeTab === 'api' ? 'bg-primary text-primary-foreground' : 'hover:bg-muted text-muted-foreground'" class="w-full flex items-center gap-3 px-6 py-4 rounded-2xl text-xs font-black uppercase tracking-widest transition-all">
                <i data-lucide="terminal" class="w-4 h-4"></i>
                API Protocols
            </button>
        </div>

        <!-- Configuration Panel -->
        <div class="lg:col-span-9">
            <!-- ... (Existing tabs remain same) ... -->

            <!-- API Protocols -->
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

                    <!-- Key Generator -->
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

                    <!-- Active Registry -->
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

                <!-- Technical Specs Node -->
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

            <!-- Profile Identity -->
            <div x-show="activeTab === 'profile'" class="bg-card border border-border rounded-[40px] p-10 shadow-sm animate-in fade-in duration-300">
                <h2 class="text-2xl font-black uppercase tracking-tighter mb-8">Personal Identity</h2>
                <form action="{{ route('settings.profile') }}" method="POST" class="space-y-8">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Authorized Email</label>
                            <input type="email" name="email" value="{{ $user->email }}" required
                                   class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">New Passphrase</label>
                            <input type="password" name="password" placeholder="••••••••••••"
                                   class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Confirm Passphrase</label>
                            <input type="password" name="password_confirmation" placeholder="••••••••••••"
                                   class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
                        </div>
                    </div>
                    <div class="pt-6 border-t border-border">
                        <button type="submit" class="h-14 px-10 bg-primary text-primary-foreground rounded-2xl font-black uppercase text-[10px] tracking-widest shadow-lg shadow-primary/20 transition-all hover:scale-[1.02]">Update Identity</button>
                    </div>
                </form>
            </div>

            <!-- Visual DNA (Branding) -->
            <div x-show="activeTab === 'branding'" class="bg-card border border-border rounded-[40px] p-10 shadow-sm animate-in fade-in duration-300">
                <div class="flex items-center justify-between mb-10">
                    <h2 class="text-2xl font-black uppercase tracking-tighter">Visual DNA</h2>
                    <div class="flex items-center gap-3 px-4 py-2 rounded-xl border border-border bg-muted/30">
                        <div class="w-4 h-4 rounded-full" :style="'background-color: ' + tempColor"></div>
                        <span class="text-[10px] font-black uppercase text-slate-500 tracking-widest">Active Preview</span>
                    </div>
                </div>

                <form action="{{ route('settings.branding') }}" method="POST" enctype="multipart/form-data" class="space-y-10">
                    @csrf
                    <div class="space-y-8">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Workspace Label</label>
                            <input type="text" name="name" value="{{ $tenant->name }}" required
                                   class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-lg font-black uppercase tracking-tight focus:ring-2 focus:ring-primary/20 outline-none">
                        </div>

                        <!-- Logo Node -->
                        <div class="p-8 rounded-[32px] border border-border bg-muted/5">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic mb-6 block px-1">Corporate Identity Asset (Logo)</label>
                            <div class="flex flex-col md:flex-row items-center gap-8">
                                <div class="w-32 h-32 rounded-3xl bg-white border border-border flex items-center justify-center p-4 shadow-inner relative group">
                                    @if($tenant->metadata['logo_url'] ?? false)
                                        <img src="{{ $tenant->metadata['logo_url'] }}" class="max-w-full max-h-full object-contain">
                                    @else
                                        <img src="https://res.cloudinary.com/dbviya1rj/image/upload/v1767554289/xe54y8zsvhursjrpbnvm.png" class="max-w-full max-h-full object-contain opacity-20 grayscale">
                                    @endif
                                </div>
                                <div class="flex-1 space-y-4">
                                    <p class="text-xs font-medium text-muted-foreground italic leading-relaxed">
                                        Upload your agency's master logo. This will replace the default grid branding across all personnel nodes and sub-account sidebars.
                                    </p>
                                    <input type="file" name="logo" id="logo-upload" class="hidden" accept="image/*">
                                    <button type="button" onclick="document.getElementById('logo-upload').click()" class="h-12 px-8 bg-muted border border-border rounded-xl font-black uppercase text-[9px] tracking-widest hover:bg-white hover:text-black transition-all">Select Node Asset</button>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Primary Grid Color</label>
                                <div class="flex gap-4">
                                    <input type="color" x-model="tempColor" name="metadata[primary_color]"
                                           class="w-20 h-14 bg-muted/20 border border-border rounded-2xl p-1 cursor-pointer">
                                    <input type="text" x-model="tempColor" class="flex-1 h-14 bg-muted/20 border border-border rounded-2xl px-5 mono text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
                                </div>
                                <p class="text-[9px] text-muted-foreground mt-2 italic">This color will drive the primary accents, sidebars, and active states across your grid nodes.</p>
                            </div>
                            
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Custom Domain</label>
                                <div class="relative">
                                    <i data-lucide="globe" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                                    <input type="text" name="metadata[custom_domain]" value="{{ $tenant->metadata['custom_domain'] ?? '' }}" placeholder="grid.youragency.com"
                                           class="w-full h-14 bg-muted/20 border border-border rounded-2xl pl-11 pr-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
                                </div>
                                <p class="text-[9px] text-muted-foreground mt-2 italic">Whitelist your agency domain for a seamless white-label experience.</p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-border">
                        <button type="submit" class="h-14 px-10 bg-primary text-primary-foreground rounded-2xl font-black uppercase text-[10px] tracking-widest shadow-lg shadow-primary/20 transition-all hover:scale-[1.02]">Persist Visual DNA</button>
                    </div>
                </form>
            </div>

            <!-- Billing & Treasury -->
            <div x-show="activeTab === 'billing'" class="bg-card border border-border rounded-[40px] p-10 shadow-sm animate-in fade-in duration-300">
                <h2 class="text-2xl font-black uppercase tracking-tighter mb-8">Resource Treasury</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                    <div class="p-8 rounded-[32px] bg-primary/5 border border-primary/10 relative overflow-hidden group">
                        <p class="text-[10px] font-black text-primary uppercase tracking-[0.2em] mb-4">Current Balance</p>
                        <p class="text-5xl font-black text-primary">{{ number_format($tokenBalance) }}</p>
                        <div class="mt-8 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                            <span class="text-[9px] font-bold text-slate-500 uppercase">Treasury Node Healthy</span>
                        </div>
                        <i data-lucide="coins" class="absolute -right-4 -bottom-4 w-24 h-24 text-primary/5 group-hover:scale-110 transition-transform"></i>
                    </div>
                    <div class="p-8 rounded-[32px] border border-border flex flex-col justify-between">
                        <div>
                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Usage Analysis</p>
                            <p class="text-sm font-medium text-muted-foreground leading-relaxed italic">Your resource consumption has been stable. Automated top-ups are disabled.</p>
                        </div>
                        <button class="w-full h-12 bg-white text-black rounded-xl font-black uppercase text-[9px] tracking-widest shadow-lg hover:bg-primary hover:text-white transition-all">Acquire Tokens</button>
                    </div>
                </div>

                <div class="space-y-6">
                    <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1">Registry Audit Trail</h3>
                    <div class="bg-muted/10 border border-border rounded-3xl overflow-hidden">
                        <table class="w-full text-left text-[10px]">
                            <thead class="bg-muted/50 border-b border-border text-slate-500 font-black uppercase tracking-widest">
                                <tr>
                                    <th class="p-4 px-6">Timestamp</th>
                                    <th class="p-4">Action Protocol</th>
                                    <th class="p-4">Identity</th>
                                    <th class="p-4 text-right">Result</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border/50">
                                @foreach($auditLogs as $log)
                                    <tr class="hover:bg-muted/30 transition-colors">
                                        <td class="p-4 px-6 font-medium text-slate-500">{{ $log->timestamp->format('Y-m-d H:i') }}</td>
                                        <td class="p-4 font-bold text-foreground uppercase tracking-tight">{{ $log->action }}</td>
                                        <td class="p-4 text-slate-500 italic">{{ $log->actor?->email ?? 'SYSTEM' }}</td>
                                        <td class="p-4 text-right">
                                            <span class="px-2 py-0.5 rounded-md font-black uppercase tracking-widest {{ $log->result === 'success' ? 'text-green-500 bg-green-500/5' : 'text-red-500 bg-red-500/5' }}">
                                                {{ $log->result }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Security Hub -->
            <div x-show="activeTab === 'security'" class="bg-card border border-border rounded-[40px] p-10 shadow-sm animate-in fade-in duration-300">
                <h2 class="text-2xl font-black uppercase tracking-tighter mb-8">Security Protocols</h2>
                
                <div class="space-y-8">
                    <!-- MFA Protocol -->
                    <div class="p-8 rounded-[32px] border border-border flex items-center justify-between relative overflow-hidden group">
                        <div class="flex items-center gap-6">
                            <div class="w-16 h-16 rounded-2xl bg-purple-500/10 flex items-center justify-center text-purple-500">
                                <i data-lucide="shield-check" class="w-8 h-8"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-black uppercase tracking-tight">Multi-Factor Authentication</h3>
                                <p class="text-xs text-muted-foreground font-medium italic">Requirement level: High. Protect your agency identity.</p>
                            </div>
                        </div>
                        
                        @if($user->mfa_enabled)
                            <div class="flex items-center gap-4">
                                <span class="text-[9px] font-black text-green-500 uppercase tracking-widest">Protocol Active</span>
                                <form action="{{ route('settings.mfa.disable') }}" method="POST">
                                    @csrf
                                    <button class="h-10 px-6 rounded-xl border border-red-100 bg-red-50 text-red-600 font-black uppercase text-[9px] tracking-widest hover:bg-red-600 hover:text-white transition-all">Deactivate</button>
                                </form>
                            </div>
                        @else
                            <a href="{{ route('mfa.setup') }}" class="h-12 px-8 bg-purple-600 text-white rounded-xl font-black uppercase text-[9px] tracking-widest shadow-lg shadow-purple-900/20 hover:scale-[1.02] transition-all">Initialize MFA</a>
                        @endif
                    </div>

                    <!-- Session Registry -->
                    <div class="p-8 rounded-[32px] border border-border">
                        <div class="flex items-center gap-3 mb-6">
                            <i data-lucide="monitor" class="w-5 h-5 text-slate-400"></i>
                            <h3 class="text-sm font-black uppercase tracking-widest">Active Identity Sessions</h3>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 rounded-2xl bg-muted/20 border border-border">
                                <div class="flex items-center gap-4">
                                    <i data-lucide="chrome" class="w-5 h-5 text-blue-500"></i>
                                    <div>
                                        <p class="text-xs font-bold text-foreground">Chrome on MacOS (This Session)</p>
                                        <p class="text-[9px] text-slate-500 font-mono">{{ request()->ip() }}</p>
                                    </div>
                                </div>
                                <span class="text-[8px] font-black text-primary uppercase tracking-widest">Current</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection