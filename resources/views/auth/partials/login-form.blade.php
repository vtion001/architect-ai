{{-- Login - Form Fields --}}
<form @submit.prevent="login" class="space-y-6">
    <div class="space-y-6">
        <!-- Slug -->
        <div class="space-y-3">
            <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Workspace Node</label>
            <div class="relative">
                <i data-lucide="hash" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
                <input x-model="slug" type="text" placeholder="your-agency-slug" required
                       class="w-full h-14 bg-muted/20 border border-border rounded-2xl pl-11 text-sm font-bold text-foreground focus:ring-2 focus:ring-primary/20 outline-none transition-all">
            </div>
        </div>

        <!-- Email -->
        <div class="space-y-3">
            <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1">Identity (Email)</label>
            <div class="relative">
                <i data-lucide="mail" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
                <input x-model="email" type="email" placeholder="name@agency.com" required
                       class="w-full h-14 bg-muted/20 border border-border rounded-2xl pl-11 text-sm font-bold text-foreground focus:ring-2 focus:ring-primary/20 outline-none transition-all">
            </div>
        </div>

        <!-- Password -->
        <div class="space-y-3">
            <div class="flex items-center justify-between px-1">
                <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic">Access Key</label>
                <a href="#" class="text-[9px] font-black uppercase tracking-widest text-primary hover:underline">Forgot?</a>
            </div>
            <div class="relative">
                <i data-lucide="lock" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
                <input x-model="password" type="password" placeholder="••••••••••••" required
                       class="w-full h-14 bg-muted/20 border border-border rounded-2xl pl-11 text-sm font-bold text-foreground focus:ring-2 focus:ring-primary/20 outline-none transition-all">
            </div>
        </div>
    </div>

    <div class="pt-4">
        <button type="submit" :disabled="isLoading"
                class="w-full h-16 bg-primary text-primary-foreground rounded-2xl font-black uppercase tracking-[0.2em] text-xs shadow-xl shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-3 disabled:opacity-50">
            <template x-if="isLoading">
                <i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i>
            </template>
            <span x-text="isLoading ? 'AUTHENTICATING...' : 'ESTABLISH CONNECTION'"></span>
        </button>
    </div>
</form>
