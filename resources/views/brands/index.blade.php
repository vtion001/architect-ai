{{--
    Brand Identity Engine - Main Layout
    
    Modularized from 836 lines to ~130 lines.
    All sections extracted to partials in /partials directory.
    Alpine.js logic extracted to /resources/js/components/brand-manager.js
--}}
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
        if (!url) { alert('Please enter a website URL first.'); return; }
        this.isScraping = true;
        try {
            const res = await fetch('{{ route('brands.scrape') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify({ url: url })
            });
            const data = await res.json();
            if (data.success) {
                const dna = data.data;
                if (dna.name) this.newBrand.name = dna.name;
                if (dna.tagline) this.newBrand.tagline = dna.tagline;
                if (dna.description) this.newBrand.description = dna.description;
                if (dna.industry) this.newBrand.industry = dna.industry;
                if (dna.colors && dna.colors.primary) this.newBrand.colors.primary = dna.colors.primary;
                if (dna.voice_profile) {
                    if (dna.voice_profile.tone) this.newBrand.voice_profile.tone = dna.voice_profile.tone;
                    if (dna.voice_profile.personality) this.newBrand.voice_profile.personality = dna.voice_profile.personality;
                    if (dna.voice_profile.keywords) this.newBrand.voice_profile.keywords = dna.voice_profile.keywords;
                }
                alert('Brand DNA extracted successfully!');
            } else { alert(data.message || 'Scraping failed.'); }
        } catch (e) { console.error(e); alert('Failed to analyze website.'); }
        finally { this.isScraping = false; }
    },

    async analyzeBlueprint(type, isEdit = false) {
        const fileInput = document.getElementById((isEdit ? 'edit_' : 'create_') + type + '_upload');
        if (!fileInput || !fileInput.files.length) { alert('Please select a file first.'); return; }
        this.isAnalyzing = true;
        const formData = new FormData();
        formData.append('document', fileInput.files[0]);
        formData.append('type', type);
        try {
            const res = await fetch('{{ route('brands.analyze-blueprint') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: formData });
            const data = await res.json();
            if (data.success) {
                const target = isEdit ? this.selectedBrand.blueprints[type] : this.newBrand.blueprints[type];
                target.boilerplate_intro = data.data.boilerplate_intro || '';
                target.scope_of_work_template = data.data.scope_of_work_template || '';
                target.legal_terms = data.data.legal_terms || '';
                target.structure_instruction = data.data.structure_instruction || '';
                alert('Blueprint extracted successfully!');
            } else { alert(data.message || 'Analysis failed.'); }
        } catch (e) { console.error(e); alert('Analysis failed.'); }
        finally { this.isAnalyzing = false; }
    },
    
    resetNewBrand() {
        this.newBrand = {
            name: '', tagline: '', description: '', industry: '',
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
        if (file) { this.logoFile = file; this.logoPreview = URL.createObjectURL(file); }
    },
    
    handleEditLogoSelect(event) {
        const file = event.target.files[0];
        if (file) { this.editLogoFile = file; this.editLogoPreview = URL.createObjectURL(file); }
    },
    
    async saveBrand() {
        if (!this.newBrand.name) { alert('Brand Name is required.'); return; }
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
        if (this.logoFile) { formData.append('logo', this.logoFile); }
        try {
            const res = await fetch('{{ route('brands.store') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }, body: formData });
            const data = await res.json();
            if (data.success) { window.location.reload(); } else { alert(data.message || 'Failed to create brand.'); this.isSaving = false; }
        } catch (err) { console.error(err); window.location.reload(); }
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
        if (this.editLogoFile) { formData.append('logo', this.editLogoFile); }
        try {
            const res = await fetch(`/settings/brands/${this.selectedBrand.id}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }, body: formData });
            await res.json();
            window.location.reload();
        } catch (err) { console.error(err); window.location.reload(); }
    },
    
    deleteBrand(id) {
        if(confirm('Delete this Brand Kit? This cannot be undone.')) {
            fetch(`/settings/brands/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }).then(() => window.location.reload());
        }
    },

    setDefault(id) {
        fetch(`/settings/brands/${id}/default`, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }).then(() => window.location.reload());
    }
}">

    {{-- Page Header --}}
    @include('brands.partials.header')

    {{-- Brands Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($brands as $brand)
            @include('brands.partials.brand-card', ['brand' => $brand])
        @empty
            @include('brands.partials.empty-state')
        @endforelse
    </div>

    {{-- Create Modal --}}
    @include('brands.partials.modals.create-modal')

    {{-- Edit Modal --}}
    @include('brands.partials.modals.edit-modal')

</div>
@endsection
