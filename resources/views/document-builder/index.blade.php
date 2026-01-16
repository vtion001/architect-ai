{{--
    Document Builder Index Page
    
    Report configuration and live preview interface.
    Uses Alpine.js for reactive state management.
    
    Required variables:
    - $templateCategories: Array of template categories with variants
    - $selectedResearch: Optional selected research for pre-filling
    
    Features:
    - Template selection with variant picker
    - Analysis type configuration
    - Source content and research topic inputs
    - Recipient information
    - File upload support
    - Live A4 preview with zoom controls
    - PDF export via print dialog
--}}

@extends('layouts.app')

@section('content')
<div class="p-8 max-w-7xl mx-auto" x-data="{ 
    categories: @js($templateCategories),
    template: 'executive-summary',
    templateVariant: 'exec-corporate',
    recipientName: '',
    recipientTitle: '',
    analysisType: 'Comparative Analysis',
    prompt: @js($selectedResearch?->title ?? ''),
    sourceContent: '',
    researchTopic: @js($selectedResearch?->title ?? ''),
    isGenerating: false,
    isLoadingPreview: false,
    activeTab: 'preview',
    htmlPreview: '',
    zoomLevel: 0.5,
    showVariantModal: false, 
    selectedCategory: null,
    
    get selectedCategoryData() { 
        return this.categories.find(c => c.id === this.template); 
    },
    get selectedVariantData() { 
        if (!this.selectedCategoryData) return null;
        return this.selectedCategoryData.variants.find(v => v.id === this.templateVariant);
    },
    
    fetchPreview() {
        this.isLoadingPreview = true;
        const params = new URLSearchParams({
            template: this.template,
            variant: this.templateVariant
        });
        fetch('{{ route('report-builder.preview') }}?' + params.toString())
            .then(response => response.json())
            .then(data => {
                this.htmlPreview = data.html;
                this.isLoadingPreview = false;
            })
            .catch(error => {
                console.error('Preview error:', error);
                this.isLoadingPreview = false;
            });
    },
    
    generateReport() {
        this.isGenerating = true;
        fetch('{{ route('report-builder.generate') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                template: this.template,
                variant: this.templateVariant,
                recipientName: this.recipientName,
                recipientTitle: this.recipientTitle,
                analysisType: this.analysisType,
                prompt: this.prompt,
                contentData: this.sourceContent,
                researchTopic: this.researchTopic
            })
        })
        .then(response => response.json())
        .then(data => {
            this.htmlPreview = data.html;
            this.isGenerating = false;
            this.activeTab = 'preview';
        })
        .catch(error => {
            console.error('Generation Error:', error);
            this.isGenerating = false;
        });
    },
    
    handleFullView() {
        if (!this.htmlPreview) return;
        const newWin = window.open('', '_blank');
        newWin.document.write(this.htmlPreview);
        newWin.document.close();
    },
    
    downloadPdf() {
        if (!this.htmlPreview) return;
        const newWin = window.open('', '_blank');
        newWin.document.write(this.htmlPreview);
        newWin.document.close();
        setTimeout(() => newWin.print(), 500);
    },
    
    init() {
        this.fetchPreview();
        this.$nextTick(() => {
            if (window.lucide) window.lucide.createIcons();
        });

        this.$watch('template', () => {
            if (this.selectedCategoryData && this.selectedCategoryData.variants.length > 0) {
                this.templateVariant = this.selectedCategoryData.variants[0].id;
            }
            this.fetchPreview();
            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });
        });
        
        this.$watch('templateVariant', () => {
            this.fetchPreview();
            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });
        });
        
        this.$watch('showVariantModal', (value) => {
            if (value) this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });
        });
    }
}">
    {{-- Page Header --}}
    @include('document-builder.partials.index.header')

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Configuration Panel --}}
        @include('document-builder.partials.index.config-panel')

        {{-- Preview Panel --}}
        @include('document-builder.partials.index.preview-panel')
    </div>
</div>
@endsection
