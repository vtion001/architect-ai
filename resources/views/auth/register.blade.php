@extends('layouts.auth')

@section('content')
<div class="w-full max-w-md animate-in fade-in zoom-in-95 duration-500" x-data="{
    companyName: '',
    slug: '',
    email: '',
    password: '',
    isLoading: false,
    errorMessage: '',
    
    register() {
        this.isLoading = true;
        this.errorMessage = '';
        
        fetch('{{ url('/auth/register-agency') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                company_name: this.companyName,
                slug: this.slug,
                email: this.email,
                password: this.password
            })
        })
        .then(async res => {
            const data = await res.json();
            if (res.ok) {
                window.location.href = data.login_url;
            } else {
                this.errorMessage = data.message || 'Registration failed';
                this.isLoading = false;
            }
        })
        .catch(err => {
            console.error(err);
            this.errorMessage = 'Communication failure with the Grid.';
            this.isLoading = false;
        });
    }
}">
    <div class="bg-card border border-border rounded-3xl shadow-2xl relative overflow-hidden">
        <!-- Decoration -->
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-primary/10 rounded-full blur-3xl"></div>

        <div class="p-10 space-y-8 relative z-10">
            <div class="text-center">
                <h2 class="text-2xl font-black uppercase tracking-tighter mb-1">Launch Workspace</h2>
                <p class="text-sm text-muted-foreground italic font-medium">Establish your agency node on the grid.</p>
            </div>

            <template x-if="errorMessage">
                <div class="p-4 rounded-xl bg-red-50 border border-red-100 text-red-600 text-xs font-bold flex items-center gap-3 animate-in slide-in-from-top-2">
                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                    <span x-text="errorMessage"></span>
                </div>
            </template>

            <form @submit.prevent="register" class="space-y-6">
                <div class="space-y-5">
                    <!-- Agency Name -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Agency Brand</label>
                        <div class="relative">
                            <i data-lucide="building" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
                            <input x-model="companyName" type="text" placeholder="Acme Digital" required
                                   @input="slug = companyName.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '')"
                                   class="w-full h-14 pl-11 bg-muted/20 border border-border rounded-2xl text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                        </div>
                    </div>

                    <!-- Slug -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Workspace Identifier (Slug)</label>
                        <div class="relative">
                            <i data-lucide="link" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
                            <input x-model="slug" type="text" placeholder="acme-digital" required
                                   class="w-full h-14 pl-11 bg-muted/20 border border-border rounded-2xl text-[11px] font-mono font-bold focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                        </div>
                        <p class="text-[9px] text-muted-foreground px-1 italic">Internal URL: architgrid.com/login/<span class="text-primary font-bold" x-text="slug || '...'"></span></p>
                    </div>

                    <!-- Owner Email -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Master Identity (Email)</label>
                        <div class="relative">
                            <i data-lucide="mail" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
                            <input x-model="email" type="email" placeholder="owner@acme.com" required
                                   class="w-full h-14 bg-muted/20 border border-border rounded-2xl pl-11 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                        </div>
                    </div>

                    <!-- Passphrase -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Security Passphrase</label>
                        <div class="relative">
                            <i data-lucide="key" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
                            <input x-model="password" type="password" placeholder="Min. 12 characters" required
                                   class="w-full h-14 bg-muted/20 border border-border rounded-2xl pl-11 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                        </div>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" :disabled="isLoading"
                            class="w-full h-16 bg-primary text-primary-foreground rounded-2xl font-black uppercase tracking-[0.3em] text-xs shadow-xl shadow-primary/20 hover:bg-primary/90 transition-all flex items-center justify-center gap-3 disabled:opacity-50">
                        <template x-if="isLoading">
                            <i data-lucide="loader-2" class="w-5 h-5 animate-spin text-primary-foreground"></i>
                        </template>
                        <span x-text="isLoading ? 'PROVISIONING...' : 'INITIATE WORKSPACE'"></span>
                    </button>
                </div>
            </form>
        </div>
        
        <div class="bg-muted/30 p-6 border-t border-border text-center">
            <p class="text-[10px] text-muted-foreground font-black uppercase tracking-widest">
                Existing Node? <a href="/auth/login" class="text-primary hover:underline ml-1">Connect Here</a>
            </p>
        </div>
    </div>
</div>
@endsection