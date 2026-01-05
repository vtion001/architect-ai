@extends('layouts.app')

@section('content')
<div class="p-8 max-w-7xl mx-auto" x-data="{
    showInviteModal: false,
    inviteEmail: '',
    inviteRoleId: '',
    isInviting: false,

    sendInvite() {
        if (!this.inviteEmail || !this.inviteRoleId) {
            alert('Identity and Role are mandatory.');
            return;
        }
        this.isInviting = true;
        fetch('{{ route('users.invite') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                email: this.inviteEmail,
                role_id: this.inviteRoleId
            })
        })
        .then(async res => {
            const data = await res.json();
            if (res.ok) {
                window.location.reload();
            } else {
                alert(data.message || 'Identity provisioning failed.');
                this.isInviting = false;
            }
        })
        .catch(err => {
            console.error(err);
            this.isInviting = false;
        });
    }
}">
    <div class="mb-12 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black uppercase tracking-tighter text-foreground mb-2">Identity Management</h1>
            <p class="text-muted-foreground font-medium italic">Oversee the authorized personnel and security health of your agency grid.</p>
        </div>
        <button @click="showInviteModal = true" class="bg-primary text-primary-foreground px-6 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest shadow-lg shadow-primary/20 flex items-center gap-2 transition-all hover:scale-[1.02]">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            Invite New Identity
        </button>
    </div>

    <!-- Identity Telemetry -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div class="bg-card border border-border rounded-3xl p-6 flex items-center gap-4 shadow-sm relative overflow-hidden group">
            <div class="w-12 h-12 bg-blue-500/10 rounded-2xl flex items-center justify-center text-blue-500">
                <i data-lucide="shield" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Total Identities</p>
                <p class="text-2xl font-black text-white">{{ $stats['total_identities'] }}</p>
            </div>
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform">
                <i data-lucide="shield" class="w-24 h-24 text-blue-500"></i>
            </div>
        </div>
        
        <div class="bg-card border border-border rounded-3xl p-6 flex items-center gap-4 shadow-sm relative overflow-hidden group">
            <div class="w-12 h-12 bg-green-500/10 rounded-2xl flex items-center justify-center text-green-500">
                <i data-lucide="activity" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Active Sessions</p>
                <p class="text-2xl font-black text-white">{{ $stats['active_sessions'] }}</p>
            </div>
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform">
                <i data-lucide="activity" class="w-24 h-24 text-green-500"></i>
            </div>
        </div>

        <div class="bg-card border border-border rounded-3xl p-6 flex items-center gap-4 shadow-sm relative overflow-hidden group">
            <div class="w-12 h-12 bg-purple-500/10 rounded-2xl flex items-center justify-center text-purple-500">
                <i data-lucide="lock" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Security Health</p>
                <p class="text-2xl font-black text-white">{{ $stats['security_health'] }}%</p>
            </div>
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform">
                <i data-lucide="lock" class="w-24 h-24 text-purple-500"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        <!-- Active Members -->
        <div class="lg:col-span-8 space-y-6">
            <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1">Authorized Grid Personnel</h3>
            <div class="grid grid-cols-1 gap-4">
                @foreach($users as $user)
                    <div class="bg-card border border-border rounded-[32px] p-6 hover:border-primary/30 transition-all group flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="relative">
                                <div class="w-14 h-14 rounded-2xl bg-muted border border-border flex items-center justify-center text-foreground font-black text-xl overflow-hidden shadow-sm">
                                    {{ substr($user->email, 0, 1) }}
                                </div>
                                @if($user->last_login_at?->isAfter(now()->subDay()))
                                    <div class="absolute -top-1 -right-1 w-4 h-4 bg-green-500 border-4 border-card rounded-full animate-pulse"></div>
                                @endif
                            </div>
                            <div>
                                <h4 class="font-black text-foreground uppercase tracking-tight">{{ $user->email }}</h4>
                                <div class="flex items-center gap-3 mt-1">
                                    @foreach($user->roles as $role)
                                        <span class="text-[9px] font-black uppercase text-primary tracking-widest">{{ $role->name }}</span>
                                    @endforeach
                                    <span class="text-muted-foreground/30">•</span>
                                    <span class="text-[9px] font-bold text-muted-foreground uppercase">MFA: {{ $user->mfa_enabled ? 'Enabled' : 'Disabled' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-6">
                            <div class="text-right hidden md:block">
                                <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-0.5">Last Grid Sync</p>
                                <p class="text-[10px] font-bold text-foreground italic">{{ $user->last_login_at?->diffForHumans() ?? 'Never' }}</p>
                            </div>
                            <button class="w-10 h-10 rounded-xl bg-muted/50 border border-border flex items-center justify-center text-muted-foreground hover:bg-white hover:text-black transition-all">
                                <i data-lucide="more-horizontal" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Pending Protocols -->
        <div class="lg:col-span-4 space-y-6">
            <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1 text-primary">Pending Provisioning</h3>
            <div class="bg-card border border-border rounded-[40px] p-8 space-y-6 shadow-sm">
                @forelse($invitations as $invite)
                    <div class="p-4 rounded-2xl bg-muted/20 border border-border hover:bg-muted/30 transition-all relative overflow-hidden group">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-foreground truncate">{{ $invite->email }}</span>
                            <span class="text-[8px] font-bold text-primary uppercase tracking-widest mt-1">{{ $invite->role->name }}</span>
                        </div>
                        <div class="mt-4 flex items-center justify-between">
                            <span class="text-[8px] font-mono text-slate-500">Exp: {{ $invite->expires_at->format('M d') }}</span>
                            <button class="text-red-500 hover:text-red-600 transition-colors">
                                <i data-lucide="x-circle" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>
                        <!-- Copy Link Button -->
                        <button @click="navigator.clipboard.writeText('{{ url('/auth/join/'.$invite->token) }}'); alert('Invitation link copied to grid buffer.');"
                                class="absolute top-2 right-2 p-1.5 rounded-lg bg-white opacity-0 group-hover:opacity-100 shadow-sm transition-all hover:scale-105" title="Copy Invitation Link">
                            <i data-lucide="copy" class="w-3 h-3 text-primary"></i>
                        </button>
                    </div>
                @empty
                    <div class="py-12 text-center text-slate-500 italic">
                        <i data-lucide="inbox" class="w-10 h-10 mx-auto mb-3 opacity-20"></i>
                        <p class="text-xs">No pending identities found.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Invite Modal -->
    <div x-show="showInviteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
        <div @click.away="!isInviting && (showInviteModal = false)" class="bg-card w-full max-w-lg rounded-[40px] shadow-2xl border border-border p-10 animate-in zoom-in-95 duration-200 relative overflow-hidden">
            <!-- Decoration -->
            <div class="absolute -top-20 -right-20 w-40 h-40 bg-primary/10 rounded-full blur-3xl"></div>

            <h2 class="text-2xl font-black uppercase tracking-tighter mb-2">Provision Identity</h2>
            <p class="text-sm text-muted-foreground mb-10 italic">Authorize a new personnel node within your agency grid.</p>
            
            <form @submit.prevent="sendInvite" class="space-y-6 relative z-10">
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1 text-primary">Authorized Email</label>
                    <input x-model="inviteEmail" type="email" required placeholder="personnel@agency.com"
                           class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Access Protocol (Role)</label>
                    <select x-model="inviteRoleId" required class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
                        <option value="">Select Role...</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="pt-6 flex flex-col gap-3">
                    <button type="submit" :disabled="isInviting" class="w-full h-16 bg-primary text-primary-foreground rounded-2xl font-black uppercase tracking-[0.3em] text-xs shadow-xl shadow-primary/20 hover:bg-primary/90 transition-all flex items-center justify-center gap-3">
                        <template x-if="isInviting">
                            <i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i>
                        </template>
                        <span x-text="isInviting ? 'PROVISIONING IDENTITY...' : 'INITIATE PROVISIONING'"></span>
                    </button>
                    <button type="button" @click="showInviteModal = false" :disabled="isInviting" class="w-full h-14 rounded-2xl border border-border font-black uppercase text-xs tracking-widest hover:bg-muted transition-all">Abort</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
