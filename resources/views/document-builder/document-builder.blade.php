{{--
    Document Architect - Main Layout
    
    REFACTORED: Modularized Alpine.js logic into separate JS modules.
    - /resources/js/document-builder/api-client.js   - API communication
    - /resources/js/document-builder/form-state.js   - Form data management
    - /resources/js/document-builder/preview-manager.js - Preview & generation
    - /resources/js/document-builder/document-builder.js - Main entry point
    
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
            place_of_birth: '', religion: '', languages: '',
            city: '', alternate_phone: ''
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
                    const parsed = this.normalizeResumeParse(data);
                    this.applyResumeAutofill(parsed);
                    alert('Resume parsed and candidate identity autofilled!');
                } else {
                    alert(data.message || 'Failed to parse resume.');
                }
            })
            .catch(err => { console.error(err); alert('Error parsing document.'); })
            .finally(() => { this.isParsing = false; event.target.value = ''; });
        },

                    normalizeResumeParse(data) {
            const extracted = data?.extracted_data || {};
            const personalInfo = extracted.personal_info || {};
            const contactInfo = extracted.contact_info || extracted.contact || extracted.contacts || {};
            const rawText = data?.text || '';

            const pickValue = (...values) => {
                for (const value of values) {
                    if (value != null && String(value).trim() !== '') {
                        return String(value).trim();
                    }
                }
                return '';
            };

                        const derived = this.deriveContactFromText(rawText);
                        const derivedPersonal = this.derivePersonalFromText(rawText);

            const normalizedPersonalInfo = {};
            for (const [k, v] of Object.entries(personalInfo)) {
                normalizedPersonalInfo[k] = v == null ? '' : String(v).trim();
            }

            // Normalize age to numeric string
            if (normalizedPersonalInfo.age) {
                const ageMatch = normalizedPersonalInfo.age.match(/\d{1,3}/);
                normalizedPersonalInfo.age = ageMatch ? ageMatch[0] : '';
            }

            // Normalize DOB to YYYY-MM-DD for date input compatibility
            if (normalizedPersonalInfo.dob) {
                const isoMatch = normalizedPersonalInfo.dob.match(/\d{4}-\d{2}-\d{2}/);
                if (!isoMatch) {
                    const parsedDate = new Date(normalizedPersonalInfo.dob);
                    if (!Number.isNaN(parsedDate.getTime())) {
                        const yyyy = parsedDate.getFullYear();
                        const mm = String(parsedDate.getMonth() + 1).padStart(2, '0');
                        const dd = String(parsedDate.getDate()).padStart(2, '0');
                        normalizedPersonalInfo.dob = `${yyyy}-${mm}-${dd}`;
                    } else {
                        normalizedPersonalInfo.dob = '';
                    }
                }
            }

                        const rawPhone = String(extracted.phone || '').trim();
            if (!normalizedPersonalInfo.alternate_phone && rawPhone) {
                const splitMatch = rawPhone.split(/\s*[\/;,]\s*/).filter(Boolean);
                if (splitMatch.length > 1) {
                    normalizedPersonalInfo.alternate_phone = splitMatch[1];
                }
            }

            if (!normalizedPersonalInfo.city && extracted.location) {
                const cityGuess = String(extracted.location).split(',')[0]?.trim();
                normalizedPersonalInfo.city = cityGuess || '';
            }

                        const mergedPersonalInfo = { ...derivedPersonal, ...normalizedPersonalInfo };
                        for (const [key, value] of Object.entries(derivedPersonal)) {
                            if (!mergedPersonalInfo[key]) {
                                mergedPersonalInfo[key] = value;
                            }
                        }

                        return {
                sourceContent: rawText || '',
                fullName: pickValue(extracted.full_name, extracted.name),
                title: pickValue(extracted.title, extracted.current_title, extracted.role),
                email: pickValue(extracted.email, contactInfo.email, contactInfo.email_address, personalInfo.email, personalInfo.email_address, derived.email),
                phone: pickValue(rawPhone, contactInfo.phone, contactInfo.contact_number, personalInfo.phone, personalInfo.contact_number, derived.phone),
                location: pickValue(extracted.location, contactInfo.location, contactInfo.address, personalInfo.address, personalInfo.location, derived.location),
                website: pickValue(extracted.website, contactInfo.website, contactInfo.linkedin, personalInfo.website, personalInfo.linkedin, derived.website),
                targetRole: pickValue(extracted.target_role, extracted.target_position),
                jobDescription: pickValue(extracted.job_description, extracted.job_posting),
                            personalInfo: mergedPersonalInfo
            };
        },

        deriveContactFromText(text) {
            const lines = String(text || '')
                .split(/\r?\n/)
                .map((line) => line.trim())
                .filter(Boolean);

            const emailMatch = text.match(/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i);
            const phoneMatch = text.match(/(?:\+?\d[\d\s().\/-]{7,}\d)/);

            const locationLine = lines.find((line) => /\d/.test(line) && /,/.test(line))
                || lines.find((line) => /(city|province|country|address)/i.test(line));

            const websiteMatch = text.match(/https?:\/\/[^\s]+|www\.[^\s]+/i);

            return {
                email: emailMatch ? emailMatch[0] : '',
                phone: phoneMatch ? phoneMatch[0] : '',
                location: locationLine ? locationLine.replace(/^(address|location)\s*[:\-]\s*/i, '').trim() : '',
                website: websiteMatch ? websiteMatch[0] : ''
            };
        },

                    derivePersonalFromText(text) {
                        const lines = String(text || '')
                            .split(/\r?\n/)
                            .map((line) => line.trim())
                            .filter(Boolean);

                        const getLabeledValue = (label) => {
                            const regex = new RegExp(`^${label}\\s*[:\-]\\s*(.+)$`, 'i');
                            const match = lines.find((line) => regex.test(line));
                            if (!match) return '';
                            return match.replace(regex, '$1').trim();
                        };

                        const age = getLabeledValue('Age');
                        const dob = getLabeledValue('Date of Birth|DOB');
                        const gender = getLabeledValue('Gender');
                        const civilStatus = getLabeledValue('Civil Status|Marital Status');
                        const nationality = getLabeledValue('Nationality');
                        const religion = getLabeledValue('Religion');
                        const placeOfBirth = getLabeledValue('Place of Birth|Birthplace');
                        const languages = getLabeledValue('Languages|Languages Spoken');

                        const extractedGender = gender || (lines.find((line) => /\b(Male|Female|Other)\b/i.test(line)) || '');
                        const extractedCivil = civilStatus || (lines.find((line) => /\b(Single|Married|Widowed|Separated)\b/i.test(line)) || '');

                        return {
                            age: age ? age.replace(/[^0-9]/g, '') : '',
                            dob: dob,
                            gender: extractedGender.replace(/^Gender\s*[:\-]?\s*/i, ''),
                            civil_status: extractedCivil.replace(/^(Civil Status|Marital Status)\s*[:\-]?\s*/i, ''),
                            nationality,
                            religion,
                            place_of_birth: placeOfBirth,
                            languages
                        };
                    },

        applyResumeAutofill(parsed) {
            if (parsed.sourceContent) this.sourceContent = parsed.sourceContent;
            if (parsed.fullName) this.recipientName = parsed.fullName;
            if (parsed.title) this.recipientTitle = parsed.title;
            if (parsed.email) this.email = parsed.email;
            if (parsed.phone) this.phone = parsed.phone;
            if (parsed.location) this.location = parsed.location;
            if (parsed.website) this.website = parsed.website;
            if (parsed.targetRole) this.targetRole = parsed.targetRole;
            if (parsed.jobDescription) this.jobDescription = parsed.jobDescription;
            if (Object.keys(parsed.personalInfo || {}).length) {
                this.personalInfo = { ...this.personalInfo, ...parsed.personalInfo };
            }
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
                    recipientTitle: this.recipientTitle,
                    companyAddress: this.companyAddress,
                    profilePhotoUrl: this.profilePhotoUrl,
                    targetRole: this.targetRole,
                    email: this.email,
                    phone: this.phone,
                    location: this.location,
                    website: this.website,
                    personalInfo: this.personalInfo
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
            // Use requestIdleCallback for initial preview (non-critical)
            if (window.requestIdleCallback) {
                window.requestIdleCallback(() => this._fetchPreviewImpl());
            } else {
                setTimeout(() => this._fetchPreviewImpl(), 100);
            }
            
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
            
            // Watch sender/recipient identity changes
            this.$watch(() => [this.senderName, this.senderTitle, this.recipientName, this.recipientTitle], () => {
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
                    {{-- Brand Selection (Always Visible) --}}
                    @include('document-builder.partials.config-panel.brand-select')
                    
                    {{-- Template-Specific Form Router --}}
                    {{-- Dynamically loads the appropriate form based on selected template & variant --}}
                    @include('document-builder.partials.template-form-router')
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