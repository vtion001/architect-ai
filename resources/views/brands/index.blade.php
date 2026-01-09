@extends('layouts.app')

@section('content')
<div class="p-8 max-w-7xl mx-auto" x-data="{
    showCreateModal: false,
    showEditModal: false,
    selectedBrand: null,
    newBrand: {
        name: '',
        logo_url: '',
        colors: { primary: '#000000', secondary: '#ffffff' },
        typography: { headings: 'Inter', body: 'Inter' },
        voice_profile: { tone: 'Professional', keywords: '' },
        contact_info: { website: '', email: '' }
    },
    isSaving: false,
    
    resetNewBrand() {
        this.newBrand = {
            name: '',
            logo_url: '',
            colors: { primary: '#000000', secondary: '#ffffff' },
            typography: { headings: 'Inter', body: 'Inter' },
            voice_profile: { tone: 'Professional', keywords: '' },
            contact_info: { website: '', email: '' }
        };
    },
    
    saveBrand() {
        if (!this.newBrand.name) {
            alert('Brand Name is required.');
            return;
        }
        this.isSaving = true;
        fetch('{{ route('brands.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(this.newBrand)
        })
        .then(res => res.json())
        .then(data => {
            window.location.reload();
        })
        .catch(err => {
            // If it redirects, it might throw here, but usually reload works
            window.location.reload();
        });
    },
    
    editBrand(brand) {
        // Deep copy to avoid mutating original list instantly
        this.selectedBrand = JSON.parse(JSON.stringify(brand));
        // Ensure defaults if null
        this.selectedBrand.colors = this.selectedBrand.colors || { primary: '#000000', secondary: '#ffffff' };
        this.selectedBrand.typography = this.selectedBrand.typography || { headings: 'Inter', body: 'Inter' };
        this.selectedBrand.voice_profile = this.selectedBrand.voice_profile || { tone: 'Professional', keywords: '' };
        this.selectedBrand.contact_info = this.selectedBrand.contact_info || { website: '', email: '' };
        
        this.showEditModal = true;
    },
    
    updateBrand() {
        this.isSaving = true;
        fetch(`/settings/brands/${this.selectedBrand.id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(this.selectedBrand)
        })
        .then(() => window.location.reload())
        .catch(() => window.location.reload());
    },
    
    deleteBrand(id) {
        if(confirm('Delete this Brand Kit? This cannot be undone.')) {
            fetch(`/settings/brands/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).then(() => window.location.reload());
        }
    },

    setDefault(id) {
        fetch(`/settings/brands/${id}/default`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        }).then(() => window.location.reload());
    }
}">
    <div class="mb-12 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black uppercase tracking-tighter text-foreground mb-2">Brand Identity Engine</h1>
            <p class="text-muted-foreground font-medium italic">Manage multiple brand personas, visual assets, and voice profiles.</p>
        </div>
        <button @click="showCreateModal = true; resetNewBrand()" class="bg-primary text-primary-foreground px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest shadow-xl shadow-primary/20 flex items-center gap-2 hover:scale-[1.02] transition-all">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Create Brand Kit
        </button>
    </div>

    <!-- Brands Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($brands as $brand)
            <div class="bg-card border border-border rounded-[32px] p-8 shadow-sm hover:border-primary/30 transition-all group relative overflow-hidden flex flex-col">
                <!-- Default Badge -->
                @if($brand->is_default)
                    <div class="absolute top-6 right-6">
                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-primary/10 text-primary border border-primary/20">
                            Default
                        </span>
                    </div>
                @else
                    <div class="absolute top-6 right-6 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button @click="setDefault('{{ $brand->id }}')" class="px-3 py-1 rounded-full text-[9px] font-bold uppercase tracking-widest bg-muted text-muted-foreground hover:bg-primary/10 hover:text-primary transition-colors">
                            Make Default
                        </button>
                    </div>
                @endif
                
                <!-- Brand Visuals -->
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-16 h-16 rounded-2xl border border-border flex items-center justify-center shadow-sm overflow-hidden bg-white">
                        @if($brand->logo_url)
                            <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" class="w-full h-full object-contain p-2">
                        @else
                            <span class="text-2xl font-black text-slate-300">{{ substr($brand->name, 0, 1) }}</span>
                        @endif
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-foreground tracking-tight">{{ $brand->name }}</h3>
                        <div class="flex gap-1 mt-1">
                            <div class="w-4 h-4 rounded-full border border-black/10 shadow-sm" style="background-color: {{ $brand->colors['primary'] ?? '#000000' }}"></div>
                            <div class="w-4 h-4 rounded-full border border-black/10 shadow-sm" style="background-color: {{ $brand->colors['secondary'] ?? '#ffffff' }}"></div>
                        </div>
                    </div>
                </div>

                <!-- Voice Profile -->
                <div class="bg-muted/30 rounded-2xl p-5 mb-6 flex-1 border border-border/50">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Voice & Tone</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="px-2 py-1 bg-white border border-border rounded-md text-[10px] font-bold text-foreground">
                            {{ $brand->voice_profile['tone'] ?? 'Standard' }}
                        </span>
                        @if(!empty($brand->voice_profile['keywords']))
                            <span class="px-2 py-1 bg-white border border-border rounded-md text-[10px] text-muted-foreground italic">
                                Keywords set
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-3 pt-6 border-t border-border/50">
                    <button @click="editBrand(@js($brand))" class="flex-1 h-12 rounded-xl bg-card border border-border hover:bg-muted transition-all flex items-center justify-center gap-2 text-xs font-bold uppercase tracking-widest">
                        <i data-lucide="settings-2" class="w-4 h-4"></i>
                        Configure
                    </button>
                    <button @click="deleteBrand('{{ $brand->id }}')" class="w-12 h-12 rounded-xl border border-red-200 bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-600 hover:text-white transition-all">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 text-center space-y-6 opacity-50 italic border-2 border-dashed border-border rounded-[40px]">
                <div class="w-20 h-20 bg-muted/50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="briefcase" class="w-10 h-10 text-slate-400"></i>
                </div>
                <p class="text-lg font-bold uppercase tracking-[0.2em]">No Brand Kits Found</p>
                <p class="text-sm">Create your first brand kit to start using custom identities.</p>
            </div>
        @endforelse
    </div>

    <!-- Create Modal -->
    <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-[150] flex items-center justify-center bg-black/80 backdrop-blur-md p-4">
        <div @click.away="showCreateModal = false" class="bg-card w-full max-w-2xl rounded-[40px] shadow-2xl border border-border flex flex-col overflow-hidden animate-in zoom-in-95 duration-200">
            <div class="p-8 border-b border-border bg-muted/30 flex justify-between items-center">
                <h2 class="text-xl font-black uppercase tracking-tighter">New Brand Kit</h2>
                <button @click="showCreateModal = false"><i data-lucide="x" class="w-6 h-6 text-muted-foreground"></i></button>
            </div>
            <div class="p-8 overflow-y-auto max-h-[70vh] custom-scrollbar space-y-6">
                <!-- Fields -->
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase text-slate-500 italic">Brand Name</label>
                    <input x-model="newBrand.name" type="text" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-bold">
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase text-slate-500 italic">Primary Color</label>
                        <div class="flex gap-2">
                            <input x-model="newBrand.colors.primary" type="color" class="h-12 w-12 rounded-xl border border-border p-1 bg-card cursor-pointer">
                            <input x-model="newBrand.colors.primary" type="text" class="flex-1 h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-mono">
                        </div>
                    </div>
                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase text-slate-500 italic">Secondary Color</label>
                        <div class="flex gap-2">
                            <input x-model="newBrand.colors.secondary" type="color" class="h-12 w-12 rounded-xl border border-border p-1 bg-card cursor-pointer">
                            <input x-model="newBrand.colors.secondary" type="text" class="flex-1 h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-mono">
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase text-slate-500 italic">Logo URL</label>
                    <input x-model="newBrand.logo_url" type="text" placeholder="https://..." class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm">
                </div>

                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase text-slate-500 italic">Voice Tone</label>
                    <select x-model="newBrand.voice_profile.tone" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-bold">
                        <option>Professional</option>
                        <option>Casual</option>
                        <option>Friendly</option>
                        <option>Authoritative</option>
                        <option>Playful</option>
                    </select>
                </div>

                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase text-slate-500 italic">Website</label>
                    <input x-model="newBrand.contact_info.website" type="text" placeholder="www.example.com" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm">
                </div>
            </div>
            <div class="p-6 border-t border-border bg-muted/30 flex justify-end gap-3">
                <button @click="showCreateModal = false" class="px-6 py-3 rounded-xl border border-border font-bold text-xs uppercase">Cancel</button>
                <button @click="saveBrand" :disabled="isSaving" class="px-8 py-3 rounded-xl bg-primary text-primary-foreground font-black text-xs uppercase tracking-widest shadow-lg">
                    {{ isSaving ? 'Saving...' : 'Create Kit' }}
                </button>
            </div>
        </div>
    </div>

    <!-- Edit Modal (Simplified copy of Create for brevity, bind to selectedBrand) -->
    <div x-show="showEditModal && selectedBrand" x-cloak class="fixed inset-0 z-[150] flex items-center justify-center bg-black/80 backdrop-blur-md p-4">
        <div @click.away="showEditModal = false" class="bg-card w-full max-w-2xl rounded-[40px] shadow-2xl border border-border flex flex-col overflow-hidden animate-in zoom-in-95 duration-200">
            <div class="p-8 border-b border-border bg-muted/30 flex justify-between items-center">
                <h2 class="text-xl font-black uppercase tracking-tighter">Edit Brand Kit</h2>
                <button @click="showEditModal = false"><i data-lucide="x" class="w-6 h-6 text-muted-foreground"></i></button>
            </div>
            <div class="p-8 overflow-y-auto max-h-[70vh] custom-scrollbar space-y-6">
                <!-- Fields linked to selectedBrand -->
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase text-slate-500 italic">Brand Name</label>
                    <input x-model="selectedBrand.name" type="text" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-bold">
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase text-slate-500 italic">Primary Color</label>
                        <div class="flex gap-2">
                            <input x-model="selectedBrand.colors.primary" type="color" class="h-12 w-12 rounded-xl border border-border p-1 bg-card cursor-pointer">
                            <input x-model="selectedBrand.colors.primary" type="text" class="flex-1 h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-mono">
                        </div>
                    </div>
                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase text-slate-500 italic">Secondary Color</label>
                        <div class="flex gap-2">
                            <input x-model="selectedBrand.colors.secondary" type="color" class="h-12 w-12 rounded-xl border border-border p-1 bg-card cursor-pointer">
                            <input x-model="selectedBrand.colors.secondary" type="text" class="flex-1 h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-mono">
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase text-slate-500 italic">Logo URL</label>
                    <input x-model="selectedBrand.logo_url" type="text" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm">
                </div>

                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase text-slate-500 italic">Voice Tone</label>
                    <select x-model="selectedBrand.voice_profile.tone" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-bold">
                        <option>Professional</option>
                        <option>Casual</option>
                        <option>Friendly</option>
                        <option>Authoritative</option>
                        <option>Playful</option>
                    </select>
                </div>

                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase text-slate-500 italic">Website</label>
                    <input x-model="selectedBrand.contact_info.website" type="text" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm">
                </div>
            </div>
            <div class="p-6 border-t border-border bg-muted/30 flex justify-end gap-3">
                <button @click="showEditModal = false" class="px-6 py-3 rounded-xl border border-border font-bold text-xs uppercase">Cancel</button>
                <button @click="updateBrand" :disabled="isSaving" class="px-8 py-3 rounded-xl bg-primary text-primary-foreground font-black text-xs uppercase tracking-widest shadow-lg">
                    {{ isSaving ? 'Updating...' : 'Save Changes' }}
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
