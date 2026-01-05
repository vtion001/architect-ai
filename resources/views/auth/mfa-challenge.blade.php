@extends('layouts.auth')

@section('content')
<div class="w-full max-w-lg animate-in fade-in zoom-in-95 duration-500" x-data="{
    code: '',
    isLoading: false,
    
    verify() {
        if (this.code.length !== 6) return;
        this.isLoading = true;
        this.$refs.form.submit();
    }
}">
    <div class="bg-card border border-border rounded-[40px] shadow-2xl relative overflow-hidden">
        <!-- Security Shield Decoration -->
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-primary/10 rounded-full blur-3xl"></div>

        <div class="p-12 space-y-10 relative z-10">
            <div class="text-center">
                <div class="w-20 h-20 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-6 border border-primary/20 shadow-xl shadow-primary/5">
                    <i data-lucide="lock" class="w-10 h-10 text-primary"></i>
                </div>
                <h2 class="text-3xl font-black uppercase tracking-tighter mb-2 text-foreground">Identity Verification</h2>
                <p class="text-sm text-muted-foreground italic font-medium">Secondary security protocol required. Enter the validation key from your authorized node.</p>
            </div>

            @if($errors->any())
                <div class="p-4 rounded-xl bg-red-50 border border-red-100 text-red-600 text-[10px] font-black uppercase tracking-widest flex items-center gap-3 animate-in slide-in-from-top-2">
                    <i data-lucide="shield-alert" class="w-4 h-4"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form x-ref="form" action="{{ route('mfa.verify') }}" method="POST" class="space-y-8">
                @csrf
                <div class="space-y-4">
                    <label class="text-[10px] font-black uppercase tracking-widest text-muted-foreground italic px-1 text-center block w-full">Authentication Key</label>
                    <input type="text" name="code" x-model="code" maxlength="6" placeholder="000000" autofocus
                           @input="if(code.length === 6) verify()"
                           class="w-full h-24 text-center text-5xl font-black tracking-[0.5em] bg-muted/20 border border-border rounded-3xl mono text-foreground focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none transition-all placeholder:opacity-20">
                </div>

                <div class="pt-4">
                    <button type="button" @click="verify()" :disabled="isLoading || code.length !== 6"
                            class="w-full h-20 bg-primary text-primary-foreground rounded-3xl font-black uppercase tracking-[0.3em] text-xs shadow-xl shadow-primary/20 hover:bg-primary/90 transition-all flex items-center justify-center gap-4 disabled:opacity-50">
                        <template x-if="isLoading">
                            <i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i>
                        </template>
                        <span x-text="isLoading ? 'VERIFYING...' : 'ESTABLISH CONNECTION'"></span>
                    </button>
                </div>
            </form>
        </div>
        
        <div class="bg-muted/30 p-8 border-t border-border text-center">
            <p class="mono text-[8px] font-black uppercase tracking-[0.4em] text-slate-500">
                Authorized Entry Point: {{ request()->ip() }} <br>
                <span class="text-primary opacity-50">Industrial Security Gateway v1.0.4</span>
            </p>
        </div>
    </div>
</div>
@endsection