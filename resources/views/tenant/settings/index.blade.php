@extends('layouts.app')

@section('content')
<div class="p-8 max-w-6xl mx-auto" x-data="{ activeTab: '{{ $activeTab }}' }">
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-2">Control Plane</h1>
        <p class="text-muted-foreground font-medium">Manage your identity, workspace branding, and security protocols.</p>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Tab Sidebar -->
        <div class="w-full lg:w-64 shrink-0">
            <nav class="space-y-1">
                <button @click="activeTab = 'profile'" :class="activeTab === 'profile' ? 'bg-primary/10 text-primary' : 'text-muted-foreground hover:bg-muted'" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all">
                    <i data-lucide="user" class="w-4 h-4"></i>
                    My Profile
                </button>
                <button @click="activeTab = 'branding'" :class="activeTab === 'branding' ? 'bg-primary/10 text-primary' : 'text-muted-foreground hover:bg-muted'" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all">
                    <i data-lucide="palette" class="w-4 h-4"></i>
                    Workspace Branding
                </button>
                <button @click="activeTab = 'integrations'" :class="activeTab === 'integrations' ? 'bg-primary/10 text-primary' : 'text-muted-foreground hover:bg-muted'" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all">
                    <i data-lucide="plug" class="w-4 h-4"></i>
                    Integrations
                </button>
                <button @click="activeTab = 'billing'" :class="activeTab === 'billing' ? 'bg-primary/10 text-primary' : 'text-muted-foreground hover:bg-muted'" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all">
                    <i data-lucide="credit-card" class="w-4 h-4"></i>
                    Billing & Tokens
                </button>
                <button @click="activeTab = 'security'" :class="activeTab === 'security' ? 'bg-primary/10 text-primary' : 'text-muted-foreground hover:bg-muted'" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all">
                    <i data-lucide="shield" class="w-4 h-4"></i>
                    Security & Audit
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="flex-1">
            <div class="bg-card border border-border rounded-3xl p-8 shadow-sm">
                
                <!-- Profile Section -->
                <div x-show="activeTab === 'profile'" x-cloak class="space-y-8 animate-in fade-in slide-in-from-right-4 duration-300">
                    <div>
                        <h3 class="text-xl font-bold mb-1">Identity Intelligence</h3>
                        <p class="text-sm text-muted-foreground font-medium">Manage your personal profile and account credentials.</p>
                    </div>
                    
                    <form action="{{ route('settings.profile') }}" method="POST" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Email Address</label>
                                <input type="email" name="email" value="{{ $user->email }}" required class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium focus:ring-2 focus:ring-primary/20 outline-none">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Current Role</label>
                                <input type="text" value="{{ $user->roles()->first()?->name }}" disabled class="w-full h-12 bg-muted/50 border border-border rounded-xl px-4 text-sm font-medium opacity-60">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">New Passphrase (Optional)</label>
                                <input type="password" name="password" placeholder="••••••••••••" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium focus:ring-2 focus:ring-primary/20 outline-none">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Confirm Passphrase</label>
                                <input type="password" name="password_confirmation" placeholder="••••••••••••" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium focus:ring-2 focus:ring-primary/20 outline-none">
                            </div>
                        </div>

                        <button type="submit" class="bg-primary text-primary-foreground px-6 py-2.5 rounded-xl font-bold text-xs uppercase tracking-widest shadow-lg shadow-primary/20 transition-all hover:scale-[1.02]">Save Identity Changes</button>
                    </form>
                </div>

                <!-- Branding Section -->
                <div x-show="activeTab === 'branding'" x-cloak class="space-y-8 animate-in fade-in slide-in-from-right-4 duration-300">
                    <div>
                        <h3 class="text-xl font-bold mb-1">Brand DNA</h3>
                        <p class="text-sm text-muted-foreground font-medium">Customize the look and feel of this workspace.</p>
                    </div>

                    <form action="{{ route('settings.branding') }}" method="POST" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Workspace Name</label>
                                <input type="text" name="name" value="{{ $tenant->name }}" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium focus:ring-2 focus:ring-primary/20 outline-none">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1 flex items-center justify-between">
                                    Custom Domain (Whitelabel)
                                    <span class="text-primary font-bold">Pro Feature</span>
                                </label>
                                <input type="text" name="metadata[custom_domain]" value="{{ $tenant->metadata['custom_domain'] ?? '' }}" placeholder="app.youragency.com" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium focus:ring-2 focus:ring-primary/20 outline-none">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Primary Color</label>
                                <div class="flex gap-3">
                                    <input type="color" name="metadata[primary_color]" value="{{ $tenant->metadata['primary_color'] ?? '#000000' }}" class="h-12 w-12 rounded-xl bg-muted/20 border border-border p-1">
                                    <input type="text" value="{{ $tenant->metadata['primary_color'] ?? '#000000' }}" class="flex-1 h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-mono uppercase">
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Timezone</label>
                                <select name="metadata[timezone]" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium">
                                    <option value="UTC" {{ ($tenant->metadata['timezone'] ?? 'UTC') === 'UTC' ? 'selected' : '' }}>UTC</option>
                                    <option value="Asia/Manila" {{ ($tenant->metadata['timezone'] ?? '') === 'Asia/Manila' ? 'selected' : '' }}>Manila (PHT)</option>
                                    <option value="America/New_York" {{ ($tenant->metadata['timezone'] ?? '') === 'America/New_York' ? 'selected' : '' }}>New York (EST)</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="bg-primary text-primary-foreground px-6 py-2.5 rounded-xl font-bold text-xs uppercase tracking-widest shadow-lg shadow-primary/20 transition-all hover:scale-[1.02]">Sync Brand DNA</button>
                    </form>
                </div>

                <!-- Integrations Section -->
                <div x-show="activeTab === 'integrations'" x-cloak class="space-y-8 animate-in fade-in slide-in-from-right-4 duration-300">
                    <div>
                        <h3 class="text-xl font-bold mb-1">Connection Hub</h3>
                        <p class="text-sm text-muted-foreground font-medium">Manage API connections for Social Planner and AI Modules.</p>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        @foreach(['Facebook', 'Instagram', 'LinkedIn', 'Twitter'] as $platform)
                        <div class="flex items-center justify-between p-4 rounded-2xl border border-border bg-muted/10">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-card flex items-center justify-center border border-border shadow-sm font-black text-xs uppercase">
                                    {{ substr($platform, 0, 2) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold">{{ $platform }}</p>
                                    <p class="text-[10px] text-muted-foreground font-medium italic">Requires professional account</p>
                                </div>
                            </div>
                            <a href="{{ route('social-planner.index') }}" class="px-4 py-1.5 rounded-lg bg-muted border border-border text-[10px] font-black uppercase tracking-widest transition-all hover:bg-primary hover:text-white hover:border-primary">Configure</a>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Billing Section -->
                <div x-show="activeTab === 'billing'" x-cloak class="space-y-8 animate-in fade-in slide-in-from-right-4 duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold mb-1">Token Treasury</h3>
                            <p class="text-sm text-muted-foreground font-medium">Resource management and subscription tiers.</p>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-amber-50 text-amber-600 border border-amber-100 text-[10px] font-black uppercase tracking-widest animate-pulse">Enterprise Plan</span>
                    </div>

                    <div class="bg-primary/5 border border-primary/10 rounded-3xl p-8 text-center">
                        <p class="text-[10px] font-black text-primary uppercase tracking-[0.2em] mb-4">Total Tokens Remaining</p>
                        <p class="text-6xl font-black text-foreground tracking-tighter mb-6">{{ number_format($tokenBalance) }}</p>
                        <button class="bg-primary text-primary-foreground px-8 py-3 rounded-2xl font-black uppercase tracking-widest text-xs shadow-xl shadow-primary/20 hover:bg-primary/90 transition-all">Buy More Tokens</button>
                    </div>
                </div>

                <!-- Security Section -->
                <div x-show="activeTab === 'security'" x-cloak class="space-y-8 animate-in fade-in slide-in-from-right-4 duration-300">
                    <div>
                        <h3 class="text-xl font-bold mb-1">Security Protocols</h3>
                        <p class="text-sm text-muted-foreground font-medium">Verify login history and manage Multi-Factor Authentication.</p>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-6 rounded-3xl border border-border bg-card">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl {{ $user->mfa_enabled ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }} flex items-center justify-center">
                                    <i data-lucide="{{ $user->mfa_enabled ? 'shield-check' : 'shield-off' }}" class="w-6 h-6"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold">Two-Factor Authentication</p>
                                    <p class="text-xs text-muted-foreground italic">
                                        {{ $user->mfa_enabled ? 'Currently enabled for your account.' : 'Enhance your security by enabling MFA.' }}
                                    </p>
                                </div>
                            </div>
                            @if($user->mfa_enabled)
                                <form action="{{ route('settings.mfa.disable') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 rounded-xl bg-red-50 text-red-600 text-[10px] font-black uppercase tracking-widest hover:bg-red-100 transition-all">Disable</button>
                                </form>
                            @else
                                <a href="{{ route('mfa.setup') }}" class="px-4 py-2 rounded-xl bg-primary text-primary-foreground text-[10px] font-black uppercase tracking-widest shadow-lg shadow-primary/20 hover:bg-primary/90 transition-all">Enable</a>
                            @endif
                        </div>

                        <div class="p-6 rounded-3xl border border-border bg-muted/5">
                            <h4 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4">Recent Access Protocol Logs</h4>
                            <div class="space-y-3">
                                @forelse($auditLogs as $log)
                                <div class="flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono text-muted-foreground">{{ strtoupper($log->action) }}</span>
                                        @if($log->actor)
                                            <span class="text-[10px] text-slate-400">({{ $log->actor->email }})</span>
                                        @endif
                                    </div>
                                    <span class="font-medium italic text-slate-400">{{ $log->timestamp->diffForHumans() }}</span>
                                </div>
                                @empty
                                <p class="text-xs text-muted-foreground italic text-center py-4">No recent security events recorded.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
