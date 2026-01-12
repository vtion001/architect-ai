@extends('layouts.app')

@section('content')
<div class="p-8 max-w-7xl mx-auto" x-data="{
    showCreateModal: false,
    showEditModal: false,
    selectedBrand: null,
    logoFile: null,
    logoPreview: null,
    editLogoFile: null,
    editLogoPreview: null,
    newBrand: {
        name: '',
        tagline: '',
        description: '',
        industry: '',
        colors: { primary: '#000000', secondary: '#ffffff', accent: '#3b82f6' },
        typography: { headings: 'Inter', body: 'Inter' },
        voice_profile: { tone: 'Professional', personality: '', keywords: '', avoid_words: '', writing_style: 'Balanced' },
        contact_info: { website: '', email: '', phone: '' },
        social_handles: { instagram: '', twitter: '', linkedin: '', facebook: '' },
        blueprints: {
            'executive-summary': { boilerplate_intro: '', scope_of_work_template: '', legal_terms: '', structure_instruction: '' },
            'proposal': { boilerplate_intro: '', scope_of_work_template: '', legal_terms: '', structure_instruction: '' },
            'contract': { boilerplate_intro: '', scope_of_work_template: '', legal_terms: '', structure_instruction: '' }
        }
    },
    isSaving: false,
    isAnalyzing: false,
    isScraping: false,

    async scrapeWebsite() {
        const url = this.newBrand.contact_info.website;
        if (!url) {
            alert('Please enter a website URL first.');
            return;
        }

        this.isScraping = true;

        try {
            const res = await fetch('{{ route('brands.scrape') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ url: url })
            });
            const data = await res.json();

            if (data.success) {
                const dna = data.data;
                if (dna.name) this.newBrand.name = dna.name;
                if (dna.tagline) this.newBrand.tagline = dna.tagline;
                if (dna.description) this.newBrand.description = dna.description;
                if (dna.industry) this.newBrand.industry = dna.industry;
                
                if (dna.colors && dna.colors.primary) {
                    this.newBrand.colors.primary = dna.colors.primary;
                }
                
                if (dna.voice_profile) {
                    if (dna.voice_profile.tone) this.newBrand.voice_profile.tone = dna.voice_profile.tone;
                    if (dna.voice_profile.personality) this.newBrand.voice_profile.personality = dna.voice_profile.personality;
                    if (dna.voice_profile.keywords) this.newBrand.voice_profile.keywords = dna.voice_profile.keywords;
                }
                
                alert('Brand DNA extracted successfully!');
            } else {
                alert(data.message || 'Scraping failed.');
            }
        } catch (e) {
            console.error(e);
            alert('Failed to analyze website.');
        } finally {
            this.isScraping = false;
        }
    },

    async analyzeBlueprint(type, isEdit = false) {
        const fileInput = document.getElementById((isEdit ? 'edit_' : 'create_') + type + '_upload');
        if (!fileInput || !fileInput.files.length) {
            alert('Please select a file first.');
            return;
        }

        this.isAnalyzing = true;
        const formData = new FormData();
        formData.append('document', fileInput.files[0]);
        formData.append('type', type);

        try {
            const res = await fetch('{{ route('brands.analyze-blueprint') }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: formData
            });
            const data = await res.json();
            if (data.success) {
                const target = isEdit ? this.selectedBrand.blueprints[type] : this.newBrand.blueprints[type];
                target.boilerplate_intro = data.data.boilerplate_intro || '';
                target.scope_of_work_template = data.data.scope_of_work_template || '';
                target.legal_terms = data.data.legal_terms || '';
                target.structure_instruction = data.data.structure_instruction || '';
                alert('Blueprint extracted successfully!');
            } else {
                alert(data.message || 'Analysis failed.');
            }
        } catch (e) {
            console.error(e);
            alert('Analysis failed.');
        } finally {
            this.isAnalyzing = false;
        }
    },
    
    resetNewBrand() {
        this.newBrand = {
            name: '',
            tagline: '',
            description: '',
            industry: '',
            colors: { primary: '#000000', secondary: '#ffffff', accent: '#3b82f6' },
            typography: { headings: 'Inter', body: 'Inter' },
            voice_profile: { tone: 'Professional', personality: '', keywords: '', avoid_words: '', writing_style: 'Balanced' },
            contact_info: { website: '', email: '', phone: '' },
            social_handles: { instagram: '', twitter: '', linkedin: '', facebook: '' },
            blueprints: {
                'executive-summary': { boilerplate_intro: '', scope_of_work_template: '', legal_terms: '', structure_instruction: '' },
                'proposal': { boilerplate_intro: '', scope_of_work_template: '', legal_terms: '', structure_instruction: '' },
                'contract': { boilerplate_intro: '', scope_of_work_template: '', legal_terms: '', structure_instruction: '' }
            }
        };
        this.logoFile = null;
        this.logoPreview = null;
    },
    
    handleLogoSelect(event) {
        const file = event.target.files[0];
        if (file) {
            this.logoFile = file;
            this.logoPreview = URL.createObjectURL(file);
        }
    },
    
    handleEditLogoSelect(event) {
        const file = event.target.files[0];
        if (file) {
            this.editLogoFile = file;
            this.editLogoPreview = URL.createObjectURL(file);
        }
    },
    
    async saveBrand() {
        if (!this.newBrand.name) {
            alert('Brand Name is required.');
            return;
        }
        this.isSaving = true;
        
        const formData = new FormData();
        formData.append('name', this.newBrand.name);
        formData.append('tagline', this.newBrand.tagline || '');
        formData.append('description', this.newBrand.description || '');
        formData.append('industry', this.newBrand.industry || '');
        formData.append('colors', JSON.stringify(this.newBrand.colors));
        formData.append('typography', JSON.stringify(this.newBrand.typography));
        formData.append('voice_profile', JSON.stringify(this.newBrand.voice_profile));
        formData.append('contact_info', JSON.stringify(this.newBrand.contact_info));
        formData.append('social_handles', JSON.stringify(this.newBrand.social_handles));
        formData.append('blueprints', JSON.stringify(this.newBrand.blueprints));
        
        if (this.logoFile) {
            formData.append('logo', this.logoFile);
        }
        
        try {
            const res = await fetch('{{ route('brands.store') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            });
            const data = await res.json();
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Failed to create brand.');
                this.isSaving = false;
            }
        } catch (err) {
            console.error(err);
            window.location.reload();
        }
    },
    
    editBrand(brand) {
        this.selectedBrand = JSON.parse(JSON.stringify(brand));
        this.selectedBrand.colors = this.selectedBrand.colors || { primary: '#000000', secondary: '#ffffff', accent: '#3b82f6' };
        this.selectedBrand.typography = this.selectedBrand.typography || { headings: 'Inter', body: 'Inter' };
        this.selectedBrand.voice_profile = this.selectedBrand.voice_profile || { tone: 'Professional', personality: '', keywords: '', avoid_words: '', writing_style: 'Balanced' };
        this.selectedBrand.contact_info = this.selectedBrand.contact_info || { website: '', email: '', phone: '' };
        this.selectedBrand.social_handles = this.selectedBrand.social_handles || { instagram: '', twitter: '', linkedin: '', facebook: '' };
        this.selectedBrand.blueprints = this.selectedBrand.blueprints || {
            'executive-summary': { boilerplate_intro: '', scope_of_work_template: '', legal_terms: '', structure_instruction: '' },
            'proposal': { boilerplate_intro: '', scope_of_work_template: '', legal_terms: '', structure_instruction: '' },
            'contract': { boilerplate_intro: '', scope_of_work_template: '', legal_terms: '', structure_instruction: '' }
        };
        // Ensure sub-fields exist for each blueprint
        ['executive-summary', 'proposal', 'contract'].forEach(type => {
            this.selectedBrand.blueprints[type] = this.selectedBrand.blueprints[type] || { boilerplate_intro: '', scope_of_work_template: '', legal_terms: '', structure_instruction: '' };
        });
        this.editLogoFile = null;
        this.editLogoPreview = null;
        this.showEditModal = true;
    },
    
    async updateBrand() {
        this.isSaving = true;
        
        const formData = new FormData();
        formData.append('_method', 'PUT');
        formData.append('name', this.selectedBrand.name);
        formData.append('tagline', this.selectedBrand.tagline || '');
        formData.append('description', this.selectedBrand.description || '');
        formData.append('industry', this.selectedBrand.industry || '');
        formData.append('colors', JSON.stringify(this.selectedBrand.colors));
        formData.append('typography', JSON.stringify(this.selectedBrand.typography));
        formData.append('voice_profile', JSON.stringify(this.selectedBrand.voice_profile));
        formData.append('contact_info', JSON.stringify(this.selectedBrand.contact_info));
        formData.append('social_handles', JSON.stringify(this.selectedBrand.social_handles));
        formData.append('blueprints', JSON.stringify(this.selectedBrand.blueprints));
        
        if (this.editLogoFile) {
            formData.append('logo', this.editLogoFile);
        }
        
        try {
            const res = await fetch(`/settings/brands/${this.selectedBrand.id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            });
            const data = await res.json();
            window.location.reload();
        } catch (err) {
            console.error(err);
            window.location.reload();
        }
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
                        @if($brand->tagline)
                            <p class="text-xs text-muted-foreground italic">{{ $brand->tagline }}</p>
                        @endif
                        <div class="flex gap-1 mt-1">
                            <div class="w-4 h-4 rounded-full border border-black/10 shadow-sm" style="background-color: {{ $brand->colors['primary'] ?? '#000000' }}"></div>
                            <div class="w-4 h-4 rounded-full border border-black/10 shadow-sm" style="background-color: {{ $brand->colors['secondary'] ?? '#ffffff' }}"></div>
                            <div class="w-4 h-4 rounded-full border border-black/10 shadow-sm" style="background-color: {{ $brand->colors['accent'] ?? '#3b82f6' }}"></div>
                        </div>
                    </div>
                </div>

                <!-- Industry & Voice -->
                <div class="bg-muted/30 rounded-2xl p-5 mb-6 flex-1 border border-border/50 space-y-3">
                    @if($brand->industry)
                        <div>
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Industry</p>
                            <p class="text-xs font-bold text-foreground">{{ $brand->industry }}</p>
                        </div>
                    @endif
                    <div>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Voice & Tone</p>
                        <div class="flex flex-wrap gap-2 mt-1">
                            <span class="px-2 py-1 bg-white border border-border rounded-md text-[10px] font-bold text-foreground">
                                {{ $brand->voice_profile['tone'] ?? 'Standard' }}
                            </span>
                            @if(!empty($brand->voice_profile['writing_style']))
                                <span class="px-2 py-1 bg-white border border-border rounded-md text-[10px] text-muted-foreground">
                                    {{ $brand->voice_profile['writing_style'] }}
                                </span>
                            @endif
                        </div>
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
    <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-[150] flex items-center justify-center bg-black/80 backdrop-blur-md p-4 overflow-y-auto">
        <div @click.away="showCreateModal = false" class="bg-card w-full max-w-3xl max-h-[90vh] rounded-[32px] shadow-2xl border border-border flex flex-col overflow-hidden animate-in zoom-in-95 duration-200 my-auto">
            <div class="p-8 border-b border-border bg-muted/30 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-black uppercase tracking-tighter">New Brand Kit</h2>
                    <p class="text-xs text-muted-foreground mt-1">Define your brand's visual and voice identity</p>
                </div>
                <button @click="showCreateModal = false"><i data-lucide="x" class="w-6 h-6 text-muted-foreground"></i></button>
            </div>
            <div class="p-8 overflow-y-auto max-h-[70vh] custom-scrollbar space-y-8">
                
                <!-- Basic Info Section -->
                <div class="space-y-6">
                    <h3 class="text-[10px] font-black uppercase tracking-widest text-primary flex items-center gap-2">
                        <i data-lucide="info" class="w-3 h-3"></i> Basic Information
                    </h3>
                    
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase text-slate-500 italic">Brand Name *</label>
                            <input x-model="newBrand.name" type="text" required placeholder="My Brand" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase text-slate-500 italic">Tagline</label>
                            <input x-model="newBrand.tagline" type="text" placeholder="Your catchy tagline..." class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm focus:ring-2 focus:ring-primary/20 outline-none">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase text-slate-500 italic">Industry</label>
                            <select x-model="newBrand.industry" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-bold">
                                <option value="">Select Industry...</option>
                                <option>Technology</option>
                                <option>Healthcare</option>
                                <option>Finance</option>
                                <option>E-commerce</option>
                                <option>Real Estate</option>
                                <option>Food & Beverage</option>
                                <option>Fashion</option>
                                <option>Beauty & Wellness</option>
                                <option>Education</option>
                                <option>Entertainment</option>
                                <option>Travel & Hospitality</option>
                                <option>Automotive</option>
                                <option>Construction</option>
                                <option>Other</option>
                            </select>
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase text-slate-500 italic">Website</label>
                            <div class="flex gap-2">
                                <input x-model="newBrand.contact_info.website" type="text" placeholder="https://example.com" class="flex-1 h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm">
                                <button @click="scrapeWebsite" :disabled="isScraping" type="button" class="h-12 px-4 rounded-xl bg-primary/10 text-primary font-black text-[10px] uppercase tracking-widest hover:bg-primary/20 transition-all flex items-center gap-2 disabled:opacity-50">
                                    <template x-if="isScraping"><i data-lucide="loader-2" class="w-3 h-3 animate-spin"></i></template>
                                    <span x-text="isScraping ? 'Scanning...' : 'Scan DNA'"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase text-slate-500 italic">Description</label>
                        <textarea x-model="newBrand.description" rows="2" placeholder="Brief description of your brand..." class="w-full bg-muted/20 border border-border rounded-xl p-4 text-sm focus:ring-2 focus:ring-primary/20 outline-none resize-none"></textarea>
                    </div>
                </div>

                <!-- Logo Upload Section -->
                <div class="space-y-6 pt-6 border-t border-border/50">
                    <h3 class="text-[10px] font-black uppercase tracking-widest text-primary flex items-center gap-2">
                        <i data-lucide="image" class="w-3 h-3"></i> Brand Logo
                    </h3>
                    
                    <div class="flex items-start gap-6">
                        <!-- Logo Preview -->
                        <div class="w-32 h-32 rounded-2xl border-2 border-dashed border-border bg-muted/20 flex items-center justify-center overflow-hidden shrink-0">
                            <template x-if="logoPreview">
                                <img :src="logoPreview" class="w-full h-full object-contain p-2">
                            </template>
                            <template x-if="!logoPreview">
                                <div class="text-center">
                                    <i data-lucide="image" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                                    <span class="text-[9px] text-slate-400 uppercase font-bold">No Logo</span>
                                </div>
                            </template>
                        </div>
                        
                        <!-- Upload Input -->
                        <div class="flex-1 space-y-3">
                            <label class="block">
                                <span class="text-[10px] font-black uppercase text-slate-500 italic">Upload Logo</span>
                                <input type="file" @change="handleLogoSelect" accept="image/png,image/jpeg,image/gif,image/webp,image/svg+xml" class="mt-2 block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer">
                            </label>
                            <p class="text-[10px] text-muted-foreground">PNG, JPG, GIF, WebP or SVG. Max 5MB.</p>
                        </div>
                    </div>
                </div>

                <!-- Colors Section -->
                <div class="space-y-6 pt-6 border-t border-border/50">
                    <h3 class="text-[10px] font-black uppercase tracking-widest text-primary flex items-center gap-2">
                        <i data-lucide="palette" class="w-3 h-3"></i> Color Palette
                    </h3>
                    
                    <div class="grid grid-cols-3 gap-4">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase text-slate-500 italic">Primary</label>
                            <div class="flex gap-2">
                                <input x-model="newBrand.colors.primary" type="color" class="h-12 w-12 rounded-xl border border-border p-1 bg-card cursor-pointer">
                                <input x-model="newBrand.colors.primary" type="text" class="flex-1 h-12 bg-muted/20 border border-border rounded-xl px-3 text-xs font-mono uppercase">
                            </div>
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase text-slate-500 italic">Secondary</label>
                            <div class="flex gap-2">
                                <input x-model="newBrand.colors.secondary" type="color" class="h-12 w-12 rounded-xl border border-border p-1 bg-card cursor-pointer">
                                <input x-model="newBrand.colors.secondary" type="text" class="flex-1 h-12 bg-muted/20 border border-border rounded-xl px-3 text-xs font-mono uppercase">
                            </div>
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase text-slate-500 italic">Accent</label>
                            <div class="flex gap-2">
                                <input x-model="newBrand.colors.accent" type="color" class="h-12 w-12 rounded-xl border border-border p-1 bg-card cursor-pointer">
                                <input x-model="newBrand.colors.accent" type="text" class="flex-1 h-12 bg-muted/20 border border-border rounded-xl px-3 text-xs font-mono uppercase">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Voice Profile Section -->
                <div class="space-y-6 pt-6 border-t border-border/50">
                    <h3 class="text-[10px] font-black uppercase tracking-widest text-primary flex items-center gap-2">
                        <i data-lucide="mic" class="w-3 h-3"></i> Voice & Tone
                    </h3>
                    
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase text-slate-500 italic">Tone</label>
                            <select x-model="newBrand.voice_profile.tone" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-bold">
                                <option>Professional</option>
                                <option>Casual</option>
                                <option>Friendly</option>
                                <option>Authoritative</option>
                                <option>Playful</option>
                                <option>Luxurious</option>
                                <option>Empathetic</option>
                                <option>Bold</option>
                            </select>
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase text-slate-500 italic">Writing Style</label>
                            <select x-model="newBrand.voice_profile.writing_style" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-bold">
                                <option>Balanced</option>
                                <option>Concise</option>
                                <option>Detailed</option>
                                <option>Conversational</option>
                                <option>Technical</option>
                                <option>Storytelling</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase text-slate-500 italic">Brand Personality</label>
                        <input x-model="newBrand.voice_profile.personality" type="text" placeholder="e.g. Innovative, Trustworthy, Approachable" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase text-slate-500 italic">Key Phrases to Use</label>
                            <textarea x-model="newBrand.voice_profile.keywords" rows="2" placeholder="Phrases that represent your brand..." class="w-full bg-muted/20 border border-border rounded-xl p-4 text-sm"></textarea>
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase text-slate-500 italic">Words to Avoid</label>
                            <textarea x-model="newBrand.voice_profile.avoid_words" rows="2" placeholder="Words that don't fit your brand..." class="w-full bg-muted/20 border border-border rounded-xl p-4 text-sm"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Document Blueprints Section -->
                <div class="space-y-6 pt-6 border-t border-border/50" x-data="{ activeBlueprintTab: 'proposal' }">
                    <h3 class="text-[10px] font-black uppercase tracking-widest text-primary flex items-center gap-2">
                        <i data-lucide="file-text" class="w-3 h-3"></i> Document Blueprints
                    </h3>
                    <p class="text-[10px] text-muted-foreground italic">Define strict templates for specific document types (e.g. Monsterbug Contracts).</p>

                    <div class="flex gap-2 p-1 bg-muted/30 rounded-xl mb-4">
                        <template x-for="type in ['proposal', 'contract', 'executive-summary']">
                            <button @click="activeBlueprintTab = type" 
                                    :class="activeBlueprintTab === type ? 'bg-white shadow-sm text-primary' : 'text-muted-foreground'"
                                    class="flex-1 py-2 text-[10px] font-black uppercase tracking-widest rounded-lg transition-all"
                                    x-text="type.replace('-', ' ')">
                            </button>
                        </template>
                    </div>

                    <div class="space-y-6 animate-in fade-in duration-300">
                        <!-- AI Auto-Fill -->
                        <div class="bg-primary/5 border border-primary/20 rounded-xl p-4 flex items-center justify-between">
                            <div class="flex-1 mr-4">
                                <h4 class="text-[10px] font-black uppercase text-primary mb-1">AI Auto-Fill</h4>
                                <p class="text-[10px] text-muted-foreground">Upload an existing PDF/Text document to automatically extract these fields.</p>
                                <input type="file" :id="'create_' + activeBlueprintTab + '_upload'" accept=".pdf,.txt,.md" class="mt-2 block w-full text-[10px] text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer">
                            </div>
                            <button @click="analyzeBlueprint(activeBlueprintTab, false)" :disabled="isAnalyzing" class="shrink-0 h-8 px-4 rounded-lg bg-primary text-primary-foreground text-[10px] font-black uppercase tracking-widest shadow-lg disabled:opacity-50 flex items-center gap-2">
                                <template x-if="isAnalyzing"><i data-lucide="loader-2" class="w-3 h-3 animate-spin"></i></template>
                                <span x-text="isAnalyzing ? 'Scanning...' : 'Extract'"></span>
                            </button>
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase text-slate-500 italic">Standard Introduction (Boilerplate)</label>
                            <textarea x-model="newBrand.blueprints[activeBlueprintTab].boilerplate_intro" rows="3" placeholder="We would like to thank you very sincerely for..." class="w-full bg-muted/20 border border-border rounded-xl p-4 text-sm"></textarea>
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase text-slate-500 italic">Core Scope / Terms (Static Text)</label>
                            <textarea x-model="newBrand.blueprints[activeBlueprintTab].scope_of_work_template" rows="5" placeholder="A. SOIL TREATMENT... B. DRILLING..." class="w-full bg-muted/20 border border-border rounded-xl p-4 text-sm"></textarea>
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase text-slate-500 italic">Legal Clauses / Payment Terms</label>
                            <textarea x-model="newBrand.blueprints[activeBlueprintTab].legal_terms" rows="3" placeholder="NOTE: All chemicals and equipment... Terms of Payment..." class="w-full bg-muted/20 border border-border rounded-xl p-4 text-sm"></textarea>
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase text-slate-500 italic">Structure Instruction for AI</label>
                            <input x-model="newBrand.blueprints[activeBlueprintTab].structure_instruction" type="text" placeholder="Use Monsterbug standard layout: Intro -> Scope -> Pricing -> Terms." class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm">
                        </div>
                    </div>
                </div>

            </div>
            <div class="p-6 border-t border-border bg-muted/30 flex justify-end gap-3">
                <button @click="showCreateModal = false" class="px-6 py-3 rounded-xl border border-border font-bold text-xs uppercase">Cancel</button>
                <button @click="saveBrand" :disabled="isSaving" class="px-8 py-3 rounded-xl bg-primary text-primary-foreground font-black text-xs uppercase tracking-widest shadow-lg disabled:opacity-50 flex items-center gap-2">
                    <template x-if="isSaving"><i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i></template>
                    <span x-text="isSaving ? 'Creating...' : 'Create Brand Kit'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div x-show="showEditModal && selectedBrand" x-cloak class="fixed inset-0 z-[150] flex items-center justify-center bg-black/80 backdrop-blur-md p-4 overflow-y-auto">
        <template x-if="selectedBrand">
            <div @click.away="showEditModal = false" class="bg-card w-full max-w-3xl max-h-[90vh] rounded-[32px] shadow-2xl border border-border flex flex-col overflow-hidden animate-in zoom-in-95 duration-200 my-auto">
                <div class="p-8 border-b border-border bg-muted/30 flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-black uppercase tracking-tighter">Edit Brand Kit</h2>
                        <p class="text-xs text-muted-foreground mt-1" x-text="selectedBrand.name"></p>
                    </div>
                    <button @click="showEditModal = false"><i data-lucide="x" class="w-6 h-6 text-muted-foreground"></i></button>
                </div>
                <div class="p-8 overflow-y-auto max-h-[70vh] custom-scrollbar space-y-8">
                    
                    <!-- Basic Info -->
                    <div class="space-y-6">
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-primary flex items-center gap-2">
                            <i data-lucide="info" class="w-3 h-3"></i> Basic Information
                        </h3>
                        
                        <div class="grid grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <label class="text-[10px] font-black uppercase text-slate-500 italic">Brand Name</label>
                                <input x-model="selectedBrand.name" type="text" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-bold">
                            </div>
                            <div class="space-y-3">
                                <label class="text-[10px] font-black uppercase text-slate-500 italic">Tagline</label>
                                <input x-model="selectedBrand.tagline" type="text" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <label class="text-[10px] font-black uppercase text-slate-500 italic">Industry</label>
                                <select x-model="selectedBrand.industry" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-bold">
                                    <option value="">Select Industry...</option>
                                    <option>Technology</option>
                                    <option>Healthcare</option>
                                    <option>Finance</option>
                                    <option>E-commerce</option>
                                    <option>Real Estate</option>
                                    <option>Food & Beverage</option>
                                    <option>Fashion</option>
                                    <option>Beauty & Wellness</option>
                                    <option>Education</option>
                                    <option>Entertainment</option>
                                    <option>Travel & Hospitality</option>
                                    <option>Automotive</option>
                                    <option>Construction</option>
                                    <option>Other</option>
                                </select>
                            </div>
                            <div class="space-y-3">
                                <label class="text-[10px] font-black uppercase text-slate-500 italic">Website</label>
                                <input x-model="selectedBrand.contact_info.website" type="text" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm">
                            </div>
                        </div>
                    </div>

                    <!-- Logo Upload -->
                    <div class="space-y-6 pt-6 border-t border-border/50">
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-primary flex items-center gap-2">
                            <i data-lucide="image" class="w-3 h-3"></i> Brand Logo
                        </h3>
                        
                        <div class="flex items-start gap-6">
                            <div class="w-32 h-32 rounded-2xl border-2 border-dashed border-border bg-muted/20 flex items-center justify-center overflow-hidden shrink-0">
                                <template x-if="editLogoPreview">
                                    <img :src="editLogoPreview" class="w-full h-full object-contain p-2">
                                </template>
                                <template x-if="!editLogoPreview && selectedBrand.logo_url">
                                    <img :src="selectedBrand.logo_url" class="w-full h-full object-contain p-2">
                                </template>
                                <template x-if="!editLogoPreview && !selectedBrand.logo_url">
                                    <div class="text-center">
                                        <i data-lucide="image" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                                        <span class="text-[9px] text-slate-400 uppercase font-bold">No Logo</span>
                                    </div>
                                </template>
                            </div>
                            
                            <div class="flex-1 space-y-3">
                                <label class="block">
                                    <span class="text-[10px] font-black uppercase text-slate-500 italic">Upload New Logo</span>
                                    <input type="file" @change="handleEditLogoSelect" accept="image/png,image/jpeg,image/gif,image/webp,image/svg+xml" class="mt-2 block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer">
                                </label>
                                <p class="text-[10px] text-muted-foreground">Upload a new image to replace the current logo.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Colors -->
                    <div class="space-y-6 pt-6 border-t border-border/50">
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-primary flex items-center gap-2">
                            <i data-lucide="palette" class="w-3 h-3"></i> Color Palette
                        </h3>
                        
                        <div class="grid grid-cols-3 gap-4">
                            <div class="space-y-3">
                                <label class="text-[10px] font-black uppercase text-slate-500 italic">Primary</label>
                                <div class="flex gap-2">
                                    <input x-model="selectedBrand.colors.primary" type="color" class="h-12 w-12 rounded-xl border border-border p-1 bg-card cursor-pointer">
                                    <input x-model="selectedBrand.colors.primary" type="text" class="flex-1 h-12 bg-muted/20 border border-border rounded-xl px-3 text-xs font-mono uppercase">
                                </div>
                            </div>
                            <div class="space-y-3">
                                <label class="text-[10px] font-black uppercase text-slate-500 italic">Secondary</label>
                                <div class="flex gap-2">
                                    <input x-model="selectedBrand.colors.secondary" type="color" class="h-12 w-12 rounded-xl border border-border p-1 bg-card cursor-pointer">
                                    <input x-model="selectedBrand.colors.secondary" type="text" class="flex-1 h-12 bg-muted/20 border border-border rounded-xl px-3 text-xs font-mono uppercase">
                                </div>
                            </div>
                            <div class="space-y-3">
                                <label class="text-[10px] font-black uppercase text-slate-500 italic">Accent</label>
                                <div class="flex gap-2">
                                    <input x-model="selectedBrand.colors.accent" type="color" class="h-12 w-12 rounded-xl border border-border p-1 bg-card cursor-pointer">
                                    <input x-model="selectedBrand.colors.accent" type="text" class="flex-1 h-12 bg-muted/20 border border-border rounded-xl px-3 text-xs font-mono uppercase">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Voice Profile -->
                    <div class="space-y-6 pt-6 border-t border-border/50">
                        <h3  class="text-[10px] font-black uppercase tracking-widest text-primary flex items-center gap-2">
                            <i data-lucide="mic" class="w-3 h-3"></i> Voice & Tone
                        </h3>
                        
                        <div class="grid grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <label class="text-[10px] font-black uppercase text-slate-500 italic">Tone</label>
                                <select x-model="selectedBrand.voice_profile.tone" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-bold">
                                    <option>Professional</option>
                                    <option>Casual</option>
                                    <option>Friendly</option>
                                    <option>Authoritative</option>
                                    <option>Playful</option>
                                    <option>Luxurious</option>
                                    <option>Empathetic</option>
                                    <option>Bold</option>
                                </select>
                            </div>
                            <div class="space-y-3">
                                <label class="text-[10px] font-black uppercase text-slate-500 italic">Writing Style</label>
                                <select x-model="selectedBrand.voice_profile.writing_style" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-bold">
                                    <option>Balanced</option>
                                    <option>Concise</option>
                                    <option>Detailed</option>
                                    <option>Conversational</option>
                                    <option>Technical</option>
                                    <option>Storytelling</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <label class="text-[10px] font-black uppercase text-slate-500 italic">Brand Personality</label>
                            <input x-model="selectedBrand.voice_profile.personality" type="text" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm">
                        </div>
                    </div>

                    <!-- Document Blueprints Section -->
                    <div class="space-y-6 pt-6 border-t border-border/50" x-data="{ editBlueprintTab: 'proposal' }">
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-primary flex items-center gap-2">
                            <i data-lucide="file-text" class="w-3 h-3"></i> Document Blueprints
                        </h3>
                        <p class="text-[10px] text-muted-foreground italic">Define strict templates for specific document types (e.g. Monsterbug Contracts).</p>

                        <div class="flex gap-2 p-1 bg-muted/30 rounded-xl mb-4">
                            <template x-for="type in ['proposal', 'contract', 'executive-summary']">
                                <button @click="editBlueprintTab = type" 
                                        :class="editBlueprintTab === type ? 'bg-white shadow-sm text-primary' : 'text-muted-foreground'"
                                        class="flex-1 py-2 text-[10px] font-black uppercase tracking-widest rounded-lg transition-all"
                                        x-text="type.replace('-', ' ')">
                                </button>
                            </template>
                        </div>

                        <div class="space-y-6 animate-in fade-in duration-300">
                            <!-- AI Auto-Fill -->
                            <div class="bg-primary/5 border border-primary/20 rounded-xl p-4 flex items-center justify-between">
                                <div class="flex-1 mr-4">
                                    <h4 class="text-[10px] font-black uppercase text-primary mb-1">AI Auto-Fill</h4>
                                    <p class="text-[10px] text-muted-foreground">Upload an existing PDF/Text document to automatically extract these fields.</p>
                                    <input type="file" :id="'edit_' + editBlueprintTab + '_upload'" accept=".pdf,.txt,.md" class="mt-2 block w-full text-[10px] text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer">
                                </div>
                                <button @click="analyzeBlueprint(editBlueprintTab, true)" :disabled="isAnalyzing" class="shrink-0 h-8 px-4 rounded-lg bg-primary text-primary-foreground text-[10px] font-black uppercase tracking-widest shadow-lg disabled:opacity-50 flex items-center gap-2">
                                    <template x-if="isAnalyzing"><i data-lucide="loader-2" class="w-3 h-3 animate-spin"></i></template>
                                    <span x-text="isAnalyzing ? 'Scanning...' : 'Extract'"></span>
                                </button>
                            </div>

                            <div class="space-y-3">
                                <label class="text-[10px] font-black uppercase text-slate-500 italic">Standard Introduction (Boilerplate)</label>
                                <textarea x-model="selectedBrand.blueprints[editBlueprintTab].boilerplate_intro" rows="3" class="w-full bg-muted/20 border border-border rounded-xl p-4 text-sm"></textarea>
                            </div>
                            <div class="space-y-3">
                                <label class="text-[10px] font-black uppercase text-slate-500 italic">Core Scope / Terms (Static Text)</label>
                                <textarea x-model="selectedBrand.blueprints[editBlueprintTab].scope_of_work_template" rows="5" class="w-full bg-muted/20 border border-border rounded-xl p-4 text-sm"></textarea>
                            </div>
                            <div class="space-y-3">
                                <label class="text-[10px] font-black uppercase text-slate-500 italic">Legal Clauses / Payment Terms</label>
                                <textarea x-model="selectedBrand.blueprints[editBlueprintTab].legal_terms" rows="3" class="w-full bg-muted/20 border border-border rounded-xl p-4 text-sm"></textarea>
                            </div>
                            <div class="space-y-3">
                                <label class="text-[10px] font-black uppercase text-slate-500 italic">Structure Instruction for AI</label>
                                <input x-model="selectedBrand.blueprints[editBlueprintTab].structure_instruction" type="text" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="p-6 border-t border-border bg-muted/30 flex justify-end gap-3">
                    <button @click="showEditModal = false" class="px-6 py-3 rounded-xl border border-border font-bold text-xs uppercase">Cancel</button>
                    <button @click="updateBrand" :disabled="isSaving" class="px-8 py-3 rounded-xl bg-primary text-primary-foreground font-black text-xs uppercase tracking-widest shadow-lg disabled:opacity-50 flex items-center gap-2">
                        <template x-if="isSaving"><i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i></template>
                        <span x-text="isSaving ? 'Saving...' : 'Save Changes'"></span>
                    </button>
                </div>
            </div>
        </template>
    </div>
</div>
@endsection
