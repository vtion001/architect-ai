{{--
    Document Architect - Main Layout
    
    REFACTORED: Modularized Alpine.js logic into separate JS modules.
    - /resources/js/document-builder/api-client.js   - API communication
    - /resources/js/document-builder/form-state.js   - Form data management
    - /resources/js/document-builder/preview-manager.js - Preview & generation
    - /resources/js/document-builder/index.js        - Main entry point
    
    Key Optimizations:
    - Request debouncing prevents API spam
    - Abort controller cancels stale requests
    - Separated concerns for easier maintenance
--}}
@extends('layouts.app')

@section('content')
<script>
/**
 * Document Builder Alpine Component
 * 
 * Optimized with:
 * - Request debouncing (300ms)
 * - Abort controller for in-flight requests
 * - Separated form state from preview logic
 */
function documentBuilder() {
    // Configuration from server
    const config = {
        categories: @js($templateCategories),
        brands: @js($brands ?? []),
        selectedResearch: @js($selectedResearch),
        user: @js(auth()->user()),
        csrfToken: @js(csrf_token()),
        routes: {
            preview: @js(route('document-builder.preview')),
            generate: @js(route('document-builder.generate')),
            uploadPhoto: @js(route('document-builder.upload-photo')),
            parseResume: @js(route('document-builder.parse-resume')),
            draftCoverLetter: @js(route('document-builder.draft-cover-letter')),
            knowledgeBase: @js(route('knowledge-base.store'))
        }
    };

    // Debounce utility
    function debounce(fn, delay = 300) {
        let timeoutId;
        return function (...args) {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => fn.apply(this, args), delay);
        };
    }

    // Abort controller for preview requests
    let previewAbortController = null;

    return {
        // Template & Brand Selection
        categories: config.categories,
        brands: config.brands,
        selectedBrandId: '',
        template: 'executive-summary',
        templateVariant: 'exec-corporate',
        
        // Sender Identity
        senderName: config.user?.name || '',
        senderTitle: '',
        
        // Recipient/Client Identity
        recipientName: '',
        recipientTitle: '',
        companyAddress: '',
        
        // CV/Resume Specific
        targetRole: '',
        jobDescription: '',
        profilePhotoUrl: '',
        email: '',
        phone: '',
        location: '',
        website: '',
        personalInfo: { 
            age: '', dob: '', gender: '', civil_status: '', 
            nationality: '', height: '', weight: '', 
            place_of_birth: '', religion: '', languages: '' 
        },
        
        // Financial/Proposal
        financials: {
            totalInvestment: '1000', currency: 'USD', timeline: '4-5 weeks',
            paymentMilestones: [
                { name: 'Project Kickoff', percentage: 50 },
                { name: 'Development Complete', percentage: 30 },
                { name: 'Launch & Final Handoff', percentage: 20 }
            ]
        },
        
        // Contract Details
        contractDetails: { 
            clientAddress: '', clientCity: '', clientCountry: 'United States', 
            clientEmail: '', clientTaxId: '', 
            startDate: new Date().toISOString().split('T')[0], 
            duration: '12 months', 
            providerBusiness: '', providerAddress: '', providerTaxId: '' 
        },
        
        // Content & Analysis
        analysisType: 'Comparative Analysis',
        prompt: config.selectedResearch?.title || '',
        sourceContent: '',
        researchTopic: config.selectedResearch?.title || '',
        
        // UI State
        isUploadingPhoto: false,
        isGenerating: false,
        isParsing: false,
        isLoadingPreview: false,
        generateStage: '', 
        generateProgress: 0,
        pendingDocumentId: null,
        activeTab: 'preview',
        htmlPreview: '',
        tailoringReport: '',
        zoomLevel: 0.45,
        showVariantModal: false, 
        selectedCategory: null,

        // Computed: Selected Category Data
        get selectedCategoryData() { 
            return this.categories.find(c => c.id === this.template); 
        },
        
        // Computed: Selected Variant Data
        get selectedVariantData() { 
            return this.selectedCategoryData?.variants.find(v => v.id === this.templateVariant) ?? null; 
        },
        
        // Computed: Current Brand Color
        get currentBrandColor() { 
            if (!this.selectedBrandId) return '#00F2FF'; 
            const brand = this.brands.find(b => b.id === this.selectedBrandId);
            return brand?.colors?.primary || '#00F2FF';
        },
        
        // Computed: Available Objectives per Template
        get availableObjectives() {
            const mapping = {
                'proposal': ['Project Proposal', 'Sales Pitch', 'Grant Application', 'Partnership Offer'],
                'contract': ['Service Agreement', 'Non-Disclosure Agreement', 'Employment Contract', 'Vendor Contract'],
                'cover-letter': ['Job Application', 'Networking Letter', 'Follow-Up', 'Prospecting Letter']
            };
            return mapping[this.template] || ['Comparative Analysis', 'Growth Strategy', 'Financial Audit', 'SWOT Matrix'];
        },
        
        // Computed: Stage Titles
        get stageTitle() { 
            return { 
                'initializing': 'Initializing Build Protocol', 
                'analyzing': 'Analyzing Content & Context', 
                'generating': 'Generating Document', 
                'rendering': 'Rendering Preview', 
                'complete': 'Build Complete!' 
            }[this.generateStage] || 'Loading Preview...'; 
        },
        
        get stageShortLabel() { 
            return { 
                'initializing': 'Initializing...', 
                'analyzing': 'Analyzing...', 
                'generating': 'Generating...', 
                'rendering': 'Rendering...', 
                'complete': 'Complete!' 
            }[this.generateStage] || 'Processing...'; 
        },

        // === FILE HANDLERS ===
        
        uploadPhoto(event) {
            const file = event.target.files[0]; 
            if (!file) return;
            this.isUploadingPhoto = true;
            const formData = new FormData(); 
            formData.append('photo', file);
            fetch(config.routes.uploadPhoto, { 
                method: 'POST', 
                headers: { 'X-CSRF-TOKEN': config.csrfToken }, 
                body: formData 
            })
            .then(res => res.json())
            .then(data => { 
                if(data.success) this.profilePhotoUrl = data.url; 
                else alert('Upload failed'); 
            })
            .finally(() => { this.isUploadingPhoto = false; });
        },
        
        parseResume(event) {
            const file = event.target.files[0]; 
            if (!file) return;
            this.isParsing = true;
            const formData = new FormData(); 
            formData.append('resume', file);
            fetch(config.routes.parseResume, { 
                method: 'POST', 
                headers: { 'X-CSRF-TOKEN': config.csrfToken }, 
                body: formData 
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.sourceContent = data.text;
                    if (data.extracted_data) {
                        const ex = data.extracted_data;
                        if (ex.full_name) this.recipientName = ex.full_name; 
                        if (ex.title) this.recipientTitle = ex.title;
                        if (ex.email) this.email = ex.email; 
                        if (ex.phone) this.phone = ex.phone;
                        if (ex.location) this.location = ex.location; 
                        if (ex.website) this.website = ex.website;
                        if (ex.personal_info) { 
                            const si = {}; 
                            for (const [k, v] of Object.entries(ex.personal_info)) { 
                                si[k] = v == null ? '' : String(v); 
                            } 
                            this.personalInfo = { ...this.personalInfo, ...si }; 
                        }
                        alert('Resume parsed and candidate identity autofilled!');
                    }
                } else {
                    alert(data.message || 'Failed to parse resume.');
                }
            })
            .catch(err => { console.error(err); alert('Error parsing document.'); })
            .finally(() => { this.isParsing = false; event.target.value = ''; });
        },
        
        draftCoverLetter() {
            if (!this.sourceContent || !this.targetRole) { 
                alert('Please import your CV and paste a Target Role first.'); 
                return; 
            }
            this.isParsing = true;
            fetch(config.routes.draftCoverLetter, { 
                method: 'POST', 
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': config.csrfToken }, 
                body: JSON.stringify({ target_role: this.targetRole, source_content: this.sourceContent }) 
            })
            .then(res => res.json())
            .then(data => { 
                if (data.success) { 
                    this.sourceContent = data.draft; 
                    alert('Cover letter drafted!'); 
                } else {
                    alert(data.message || 'Drafting failed.'); 
                }
            })
            .catch(err => { console.error(err); alert('Error drafting cover letter.'); })
            .finally(() => { this.isParsing = false; });
        },

        // === PREVIEW (Optimized with debouncing & abort) ===
        
        _fetchPreviewImpl() {
            // Cancel any in-flight preview request
            if (previewAbortController) {
                previewAbortController.abort();
            }
            previewAbortController = new AbortController();
            
            this.isLoadingPreview = true;
            
            fetch(config.routes.preview, { 
                method: 'POST', 
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': config.csrfToken }, 
                body: JSON.stringify({ 
                    template: this.template, 
                    variant: this.templateVariant, 
                    brand_id: this.selectedBrandId, 
                    contractDetails: this.contractDetails, 
                    senderName: this.senderName, 
                    senderTitle: this.senderTitle, 
                    recipientName: this.recipientName, 
                    recipientTitle: this.recipientTitle 
                }),
                signal: previewAbortController.signal
            })
            .then(r => r.json())
            .then(d => { 
                this.htmlPreview = d.html; 
                this.isLoadingPreview = false; 
            })
            .catch(e => { 
                // Ignore abort errors
                if (e.name !== 'AbortError') {
                    console.error('Preview error:', e); 
                }
                this.isLoadingPreview = false; 
            });
        },
        
        // Debounced version - prevents API spam during rapid changes
        fetchPreview: debounce(function() {
            this._fetchPreviewImpl();
        }, 300),

        // === DOCUMENT GENERATION ===
        
        generateReport() {
            this.isGenerating = true; 
            this.generateStage = 'initializing'; 
            this.generateProgress = 5;
            
            const pi = setInterval(() => { 
                if (this.generateProgress < 90) this.generateProgress += Math.random() * 8; 
                if (this.generateProgress > 20 && this.generateStage === 'initializing') this.generateStage = 'analyzing'; 
                else if (this.generateProgress > 50 && this.generateStage === 'analyzing') this.generateStage = 'generating'; 
                else if (this.generateProgress > 80 && this.generateStage === 'generating') this.generateStage = 'rendering'; 
            }, 500);
            
            fetch(config.routes.generate, { 
                method: 'POST', 
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': config.csrfToken }, 
                body: JSON.stringify({ 
                    template: this.template, 
                    variant: this.templateVariant, 
                    senderName: this.senderName, 
                    senderTitle: this.senderTitle, 
                    recipientName: this.recipientName, 
                    recipientTitle: this.recipientTitle, 
                    companyAddress: this.companyAddress, 
                    analysisType: this.analysisType, 
                    prompt: this.prompt, 
                    contentData: this.sourceContent, 
                    researchTopic: this.researchTopic, 
                    brand_id: this.selectedBrandId, 
                    targetRole: this.targetRole, 
                    jobDescription: this.jobDescription, 
                    profilePhotoUrl: this.profilePhotoUrl, 
                    email: this.email, 
                    phone: this.phone, 
                    location: this.location, 
                    website: this.website, 
                    personalInfo: this.personalInfo, 
                    financials: this.financials 
                }) 
            })
            .then(async r => { 
                const ct = r.headers.get('content-type'); 
                if (!ct || !ct.includes('application/json')) throw new Error('Server returned HTML instead of JSON.'); 
                const d = await r.json(); 
                if (!r.ok || !d.success) throw new Error(d.message || 'Generation failed.'); 
                return d; 
            })
            .then(d => { 
                clearInterval(pi); 
                this.generateProgress = 100; 
                this.generateStage = 'complete'; 
                if (d.status === 'processing' && d.document_id) this.pollDocumentStatus(d.document_id); 
                else if (d.html) this.processGeneratedHtml(d.html); 
            })
            .catch(e => { 
                clearInterval(pi); 
                console.error('Generation Error:', e); 
                alert('Error: ' + e.message); 
                this.isGenerating = false; 
                this.generateStage = ''; 
                this.generateProgress = 0; 
            });
        },
        
        pollDocumentStatus(id) {
            this.pendingDocumentId = id;
            const poll = setInterval(() => { 
                fetch(`/documents/${id}`, { 
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } 
                })
                .then(r => r.json())
                .then(doc => { 
                    if (doc.status === 'processing') { 
                        this.generateStage = 'generating'; 
                        if (this.generateProgress < 85) this.generateProgress += 2; 
                    } 
                    if (doc.status === 'completed') { 
                        clearInterval(poll); 
                        this.generateProgress = 100; 
                        this.generateStage = 'complete'; 
                        this.processGeneratedHtml(doc.content); 
                        this.pendingDocumentId = null; 
                    } else if (doc.status === 'failed') { 
                        clearInterval(poll); 
                        this.isGenerating = false; 
                        this.generateStage = ''; 
                        this.generateProgress = 0; 
                        this.pendingDocumentId = null; 
                        alert('Report generation failed: ' + (doc.metadata?.error || 'Unknown error')); 
                    } 
                })
                .catch(e => { console.error('Polling error:', e); }); 
            }, 3000);
            
            setTimeout(() => { 
                clearInterval(poll); 
                if (this.isGenerating && this.pendingDocumentId === id) { 
                    this.isGenerating = false; 
                    this.generateStage = ''; 
                    this.generateProgress = 0; 
                    alert('Generation timed out.'); 
                } 
            }, 5 * 60 * 1000);
        },
        
        processGeneratedHtml(html) { 
            let fh = html; 
            const sp = /<!-- TAILORING_REPORT_START -->([\s\S]*?)<!-- TAILORING_REPORT_END -->/; 
            const m = fh.match(sp); 
            if (m) { 
                this.tailoringReport = m[1]; 
                fh = fh.replace(m[0], ''); 
            } else {
                this.tailoringReport = ''; 
            }
            this.htmlPreview = fh; 
            this.isGenerating = false; 
            this.generateStage = ''; 
            this.generateProgress = 0; 
            this.activeTab = 'preview'; 
        },
        
        saveToKb() { 
            if (!this.htmlPreview) return; 
            this.isGenerating = true; 
            fetch(config.routes.knowledgeBase, { 
                method: 'POST', 
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': config.csrfToken }, 
                body: JSON.stringify({ 
                    title: (this.researchTopic || 'Generated Document') + ' (Architected)', 
                    type: 'text', 
                    content: this.htmlPreview, 
                    category: 'Documents' 
                }) 
            })
            .then(r => r.json())
            .then(d => { 
                if (d.success) alert('Document indexed.'); 
                this.isGenerating = false; 
            }); 
        },

        // === INITIALIZATION ===
        
        init() { 
            // Initial preview fetch (immediate, not debounced)
            this._fetchPreviewImpl(); 
            
            this.$nextTick(() => { 
                if (window.lucide) window.lucide.createIcons(); 
            }); 
            
            // Watch template changes
            this.$watch('template', () => { 
                if (this.selectedCategoryData?.variants.length > 0) {
                    this.templateVariant = this.selectedCategoryData.variants[0].id; 
                }
                this.analysisType = this.availableObjectives[0]; 
                this.fetchPreview(); 
                this.$nextTick(() => { 
                    if (window.lucide) window.lucide.createIcons(); 
                }); 
            }); 
            
            // Watch variant changes
            this.$watch('templateVariant', () => { 
                this.fetchPreview(); 
                this.$nextTick(() => { 
                    if (window.lucide) window.lucide.createIcons(); 
                }); 
            }); 
            
            // Watch brand changes (debounced via fetchPreview)
            this.$watch('selectedBrandId', () => { 
                this.fetchPreview(); 
            }); 
            
            // Watch variant modal for icon refresh
            this.$watch('showVariantModal', (v) => { 
                if (v) this.$nextTick(() => { 
                    if (window.lucide) window.lucide.createIcons(); 
                }); 
            }); 
        }
    };
}
</script>

<div class="p-8 max-w-[1600px] mx-auto animate-in fade-in duration-700" x-data="documentBuilder()">
    
    {{-- Page Header --}}
    @include('document-builder.partials.header')

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        
        {{-- Configuration Node --}}
        <div class="lg:col-span-4 space-y-8">
            <div class="bg-card border border-border rounded-[40px] p-10 shadow-sm relative overflow-hidden">
                <div class="absolute -top-20 -right-20 w-40 h-40 bg-primary/5 rounded-full blur-3xl"></div>
                
                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-10 px-1 italic">Protocol Configuration</h3>

                <div class="space-y-8 relative z-10">
                    @include('document-builder.partials.config-panel.brand-select')
                    @include('document-builder.partials.config-panel.research-grounding')
                    @include('document-builder.partials.config-panel.analysis-type')
                    @include('document-builder.partials.config-panel.financials')
                    @include('document-builder.partials.config-panel.target-role')
                    @include('document-builder.partials.config-panel.sender-identity')
                    @include('document-builder.partials.config-panel.recipient-identity')
                </div>
            </div>

            {{-- Supplementary Context --}}
            @include('document-builder.partials.context-panel')

            {{-- Template Category Grid --}}
            <div class="space-y-6">
                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1 italic">Architecture Templates</h3>
                @include('components.template-selector')
            </div>
        </div>

        {{-- Executive Display Node --}}
        <div class="lg:col-span-8 space-y-6">
            
            {{-- AI Tailoring Insight (Dynamic) --}}
            @include('document-builder.partials.preview-panel.tailoring-insight')

            {{-- Tabs and Zoom Controls --}}
            @include('document-builder.partials.preview-panel.tabs')

            {{-- Preview Container --}}
            <div class="bg-card border border-border rounded-[40px] min-h-[900px] shadow-sm relative flex flex-col items-center p-10 overflow-hidden">
                {{-- Grid Canvas Pattern --}}
                <div class="absolute inset-0 grid-canvas pointer-events-none opacity-20"></div>

                {{-- Loading Overlay --}}
                @include('document-builder.partials.preview-panel.loading-overlay')

                {{-- Preview Tab Content --}}
                @include('document-builder.partials.preview-panel.preview-tab')

                {{-- HTML Tab Content --}}
                @include('document-builder.partials.preview-panel.html-tab')
            </div>
        </div>
    </div>
</div>

@endsection