@extends('layouts.app')

@section('content')
<div class="p-8 max-w-[1600px] mx-auto animate-in fade-in duration-700" x-data="{ 
    categories: @js($templateCategories),
    brands: @js($brands ?? []),
    selectedBrandId: '',
    template: 'executive-summary',
    templateVariant: 'exec-corporate',
    recipientName: '',
    recipientTitle: '',
    companyAddress: '',
    targetRole: '',
    jobDescription: '',
    profilePhotoUrl: '',
    email: '',
    phone: '',
    location: '',
    website: '',
    personalInfo: {
        age: '',
        dob: '',
        gender: '',
        civil_status: '',
        nationality: '',
        height: '',
        weight: '',
        place_of_birth: '',
        religion: '',
        languages: ''
    },
    isUploadingPhoto: false,
    analysisType: 'Comparative Analysis',
    prompt: @js($selectedResearch?->title ?? ''),
    sourceContent: '',
    researchTopic: @js($selectedResearch?->title ?? ''),
    isGenerating: false,
    isParsing: false,
    isLoadingPreview: false,
    activeTab: 'preview',
    htmlPreview: '',
    tailoringReport: '',
    zoomLevel: 0.45,
    showVariantModal: false, 
    selectedCategory: null,
    get selectedCategoryData() { return this.categories.find(c => c.id === this.template); },
    get selectedVariantData() { 
        if (!this.selectedCategoryData) return null;
        return this.selectedCategoryData.variants.find(v => v.id === this.templateVariant);
    },
    get availableObjectives() {
        if (this.template === 'proposal') {
            return ['Project Proposal', 'Sales Pitch', 'Grant Application', 'Partnership Offer'];
        }
        if (this.template === 'contract') {
            return ['Service Agreement', 'Non-Disclosure Agreement', 'Employment Contract', 'Vendor Contract'];
        }
        if (this.template === 'cover-letter') {
            return ['Job Application', 'Networking Letter', 'Follow-Up', 'Prospecting Letter'];
        }
        return ['Comparative Analysis', 'Growth Strategy', 'Financial Audit', 'SWOT Matrix'];
    },
    uploadPhoto(event) {
        const file = event.target.files[0];
        if (!file) return;
        this.isUploadingPhoto = true;
        const formData = new FormData();
        formData.append('photo', file);
        
        fetch('{{ route('document-builder.upload-photo') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                this.profilePhotoUrl = data.url;
            } else {
                alert('Upload failed');
            }
        })
        .finally(() => { this.isUploadingPhoto = false; });
    },
    parseResume(event) {
        const file = event.target.files[0];
        if (!file) return;

        this.isParsing = true;
        const formData = new FormData();
        formData.append('resume', file);

        fetch('{{ route('document-builder.parse-resume') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                this.sourceContent = data.text;
                
                // AI Autofill
                if (data.extracted_data) {
                    const ex = data.extracted_data;
                    if (ex.full_name) this.recipientName = ex.full_name;
                    if (ex.title) this.recipientTitle = ex.title;
                    if (ex.email) this.email = ex.email;
                    if (ex.phone) this.phone = ex.phone;
                    if (ex.location) this.location = ex.location;
                    if (ex.website) this.website = ex.website;
                    
                    if (ex.personal_info) {
                        // Ensure all personal info values are strings
                        const stringifiedInfo = {};
                        for (const [key, value] of Object.entries(ex.personal_info)) {
                            stringifiedInfo[key] = value === null || value === undefined ? '' : String(value);
                        }
                        this.personalInfo = { ...this.personalInfo, ...stringifiedInfo };
                    }
                    alert('Resume parsed and candidate identity autofilled!');
                }
            } else {
                alert(data.message || 'Failed to parse resume.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error parsing document.');
        })
        .finally(() => {
            this.isParsing = false;
            event.target.value = ''; // Reset input
        });
    },
    draftCoverLetter() {
        if (!this.sourceContent || !this.targetRole) {
            alert('Please import your CV and paste a Target Role first.');
            return;
        }
        this.isParsing = true;
        fetch('{{ route('document-builder.draft-cover-letter') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                target_role: this.targetRole,
                source_content: this.sourceContent
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                this.sourceContent = data.draft;
                alert('Cover letter drafted! You can now refine it in the box below.');
            } else {
                alert(data.message || 'Drafting failed.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error drafting cover letter.');
        })
        .finally(() => {
            this.isParsing = false;
        });
    },
    fetchPreview() {
        this.isLoadingPreview = true;
        const params = new URLSearchParams({
            template: this.template,
            variant: this.templateVariant,
            brand_id: this.selectedBrandId
        });
        fetch('{{ route('document-builder.preview') }}?' + params.toString())
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
        fetch('{{ route('document-builder.generate') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                template: this.template,
                variant: this.templateVariant,
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
                personalInfo: this.personalInfo
            })
        })
        .then(async response => {
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                console.error('Server Error (HTML):', text);
                throw new Error('Server returned HTML instead of JSON. Check console for details. Preview: ' + text.substring(0, 100));
            }
            const data = await response.json();
            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Generation failed. Please check inputs.');
            }
            return data;
        })
        .then(data => {
            let finalHtml = data.html;
            
            // Extract AI Tailoring Report via Delimiter (Service Layer Split)
            const splitPattern = /<!-- TAILORING_REPORT_START -->([\s\S]*?)<!-- TAILORING_REPORT_END -->/;
            const match = finalHtml.match(splitPattern);
            
            if (match) {
                this.tailoringReport = match[1];
                finalHtml = finalHtml.replace(match[0], ''); // Remove report + delimiters
            } else {
                this.tailoringReport = '';
            }

            this.htmlPreview = finalHtml;
            this.isGenerating = false;
            this.activeTab = 'preview';
        })
        .catch(error => {
            console.error('Generation Error:', error);
            alert('Error: ' + error.message);
            this.isGenerating = false;
        });
    },
    saveToKb() {
        if (!this.htmlPreview) return;
        this.isGenerating = true;
        fetch('{{ route('knowledge-base.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                title: (this.researchTopic || 'Generated Document') + ' (Architected)',
                type: 'text',
                content: this.htmlPreview,
                category: 'Documents'
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Document indexed into Global Knowledge Hub.');
            }
            this.isGenerating = false;
        });
    },
    init() {
        // Fetch preview on page load
        this.fetchPreview();
        this.$nextTick(() => {
            if (window.lucide) window.lucide.createIcons();
        });

        // Watch for template or variant changes
        this.$watch('template', () => {
            if (this.selectedCategoryData && this.selectedCategoryData.variants.length > 0) {
                this.templateVariant = this.selectedCategoryData.variants[0].id;
            }
            // Reset Analysis Type based on new template
            this.analysisType = this.availableObjectives[0];
            
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
        this.$watch('selectedBrandId', () => {
            this.fetchPreview();
        });
        this.$watch('showVariantModal', (value) => {
            if (value) this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });
        });
    }
}">
    <div class="mb-12 flex flex-col md:flex-row items-start md:items-center justify-between gap-8 border-b border-border pb-10">
        <div>
            <h1 class="text-4xl font-black uppercase tracking-tighter text-foreground mb-2">Document Architect</h1>
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-primary animate-pulse"></div>
                    <span class="text-[10px] font-black uppercase text-slate-500 tracking-widest">Protocol: RAG-Injected Document Engine</span>
                </div>
                <span class="text-[10px] font-mono text-slate-400 uppercase tracking-tighter italic">Treasury Cost: 30 Tokens per build</span>
            </div>
        </div>
        
        <div class="flex gap-3">
            <button @click="saveToKb" :disabled="!htmlPreview || isGenerating" 
                    class="h-14 px-8 rounded-2xl border border-border bg-card font-black uppercase text-[10px] tracking-widest flex items-center gap-3 hover:bg-muted transition-all disabled:opacity-50">
                <i data-lucide="database" class="w-4 h-4"></i>
                Index to Hub
            </button>
            <button @click="generateReport" :disabled="isGenerating"
                    class="h-14 px-10 rounded-2xl bg-primary text-primary-foreground font-black uppercase text-[10px] tracking-[0.2em] flex items-center gap-3 shadow-xl shadow-primary/20 hover:scale-[1.02] transition-all disabled:opacity-50">
                <template x-if="!isGenerating">
                    <div class="flex items-center gap-2">
                        <i data-lucide="sparkles" class="w-4 h-4"></i>
                        <span>Initiate Build</span>
                    </div>
                </template>
                <template x-if="isGenerating">
                    <div class="flex items-center gap-2">
                        <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                        <span>Architecting...</span>
                    </div>
                </template>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        <!-- Configuration Node -->
        <div class="lg:col-span-4 space-y-8">
            <div class="bg-card border border-border rounded-[40px] p-10 shadow-sm relative overflow-hidden">
                <div class="absolute -top-20 -right-20 w-40 h-40 bg-primary/5 rounded-full blur-3xl"></div>
                
                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-10 px-1 italic">Protocol Configuration</h3>

                <div class="space-y-8 relative z-10">
                    <!-- Brand Kit -->
                    <div class="space-y-3" x-show="brands.length > 0">
                        <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic px-1">Apply Brand Kit</label>
                        <div class="relative">
                            <i data-lucide="fingerprint" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-primary"></i>
                            <select x-model="selectedBrandId" class="w-full h-14 bg-muted/20 border border-border rounded-2xl pl-11 pr-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none appearance-none cursor-pointer hover:bg-muted/30 transition-colors">
                                <option value="">No Brand Applied</option>
                                <template x-for="brand in brands" :key="brand.id">
                                    <option :value="brand.id" x-text="brand.name"></option>
                                </template>
                            </select>
                            <i data-lucide="chevron-down" class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground pointer-events-none"></i>
                        </div>
                    </div>

                    <!-- Research Topic -->
                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic px-1">Research Grounding</label>
                        <div class="relative">
                            <i data-lucide="brain" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-primary"></i>
                            <input x-model="researchTopic" type="text" placeholder="e.g. Q3 Market Sentiment"
                                   class="w-full h-14 bg-muted/20 border border-border rounded-2xl pl-11 pr-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
                        </div>
                        <p class="text-[9px] text-muted-foreground italic px-1">AI will perform a deep sweep based on this identity.</p>
                    </div>

                    <!-- Analysis Type -->
                    <div class="space-y-3" x-show="template !== 'cv-resume'">
                        <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic px-1">Intelligence Objective</label>
                        <select x-model="analysisType" class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
                            <template x-for="objective in availableObjectives" :key="objective">
                                <option x-text="objective" :value="objective"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Target Role (CV & Cover Letter) -->
                    <div class="space-y-3" x-show="template === 'cv-resume' || template === 'cover-letter'" x-transition>
                        <label class="text-[10px] font-black uppercase tracking-widest text-primary italic px-1 flex items-center gap-2">
                            <i data-lucide="crosshair" class="w-3 h-3"></i>
                            Target Role / Job Title
                        </label>
                        <textarea x-model="targetRole" placeholder="e.g. Senior Product Manager" rows="1"
                               class="w-full bg-muted/20 border border-border rounded-2xl p-4 text-sm font-medium focus:ring-2 focus:ring-primary/20 outline-none resize-none"></textarea>
                        
                        <label class="text-[10px] font-black uppercase tracking-widest text-primary italic px-1 flex items-center gap-2 mt-4">
                            <i data-lucide="file-text" class="w-3 h-3"></i>
                            Paste Job Description (Recommended)
                        </label>
                        <textarea x-model="jobDescription" placeholder="Paste the full job offer description here for AI tailoring..." rows="4"
                               class="w-full bg-muted/20 border border-border rounded-2xl p-4 text-xs font-medium focus:ring-2 focus:ring-primary/20 outline-none"></textarea>
                        <p class="text-[9px] text-muted-foreground italic px-1">AI will analyze keywords from this description to optimize your document.</p>
                    </div>

                                    <!-- Recipient / Candidate Identity -->
                                    <div class="pt-6 border-t border-border/50">
                                        <div class="flex items-center justify-between mb-4">
                                            <label class="text-[10px] font-black uppercase tracking-widest text-primary italic px-1" 
                                                   x-text="template === 'cv-resume' ? 'Candidate Identity' : (template === 'cover-letter' ? 'Target Company Details' : 'Identity Destination')"></label>
                                            
                                            <!-- Photo Upload (CV Only) -->
                                            <div x-show="template === 'cv-resume'" class="relative">
                                                <input type="file" id="photoUpload" class="hidden" accept="image/*" @change="uploadPhoto">
                                                <label for="photoUpload" class="cursor-pointer flex items-center gap-2 text-[9px] font-bold text-muted-foreground hover:text-primary transition-colors">
                                                    <template x-if="!profilePhotoUrl && !isUploadingPhoto">
                                                        <span class="flex items-center gap-1"><i data-lucide="camera" class="w-3 h-3"></i> Add Photo</span>
                                                    </template>
                                                    <template x-if="isUploadingPhoto">
                                                        <span class="flex items-center gap-1"><i data-lucide="loader-2" class="w-3 h-3 animate-spin"></i> Uploading...</span>
                                                    </template>
                                                    <template x-if="profilePhotoUrl">
                                                        <div class="flex items-center gap-2">
                                                            <img :src="profilePhotoUrl" class="w-6 h-6 rounded-full object-cover border border-border">
                                                            <span class="text-green-600">Change</span>
                                                        </div>
                                                    </template>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-1 gap-4">
                                            <input x-model="recipientName" type="text" 
                                                   :placeholder="template === 'cv-resume' ? 'Full Name' : (template === 'cover-letter' ? 'Hiring Manager Name' : 'Recipient Name')"
                                                   class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-[11px] font-bold outline-none">
                                            <input x-model="recipientTitle" type="text" 
                                                   :placeholder="template === 'cv-resume' ? 'Professional Title (e.g. Senior Architect)' : (template === 'cover-letter' ? 'Company Name' : 'Identity Role (e.g. CEO)')"
                                                   class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-[11px] font-bold outline-none">
                                            
                                            <div x-show="template === 'cover-letter'" x-transition>
                                                <input x-model="companyAddress" type="text" placeholder="Company Address"
                                                       class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-[11px] font-bold outline-none">
                                            </div>
                                            
                                            <!-- CV Specific Contact Info -->                            <div x-show="template === 'cv-resume'" x-transition class="space-y-4 pt-2">
                                <div class="grid grid-cols-2 gap-4">
                                    <input x-model="email" type="email" placeholder="Email Address"
                                           class="w-full h-10 bg-muted/20 border border-border rounded-lg px-4 text-[10px] font-medium outline-none">
                                    <input x-model="phone" type="text" placeholder="Phone Number"
                                           class="w-full h-10 bg-muted/20 border border-border rounded-lg px-4 text-[10px] font-medium outline-none">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <input x-model="location" type="text" placeholder="Location (City, Country)"
                                           class="w-full h-10 bg-muted/20 border border-border rounded-lg px-4 text-[10px] font-medium outline-none">
                                    <input x-model="website" type="text" placeholder="Portfolio / LinkedIn"
                                           class="w-full h-10 bg-muted/20 border border-border rounded-lg px-4 text-[10px] font-medium outline-none">
                                </div>
                                
                                <!-- Extended Bio Data -->
                                <div class="pt-4 border-t border-border/50">
                                    <label class="text-[9px] font-black uppercase tracking-widest text-muted-foreground mb-3 block">Professional Information (Bio-Data)</label>
                                    <div class="grid grid-cols-2 gap-3 mb-3">
                                        <input x-model="personalInfo.age" type="text" placeholder="Age" class="w-full h-9 bg-muted/20 border border-border rounded-lg px-3 text-[10px] outline-none">
                                        <input x-model="personalInfo.dob" type="text" placeholder="Date of Birth" class="w-full h-9 bg-muted/20 border border-border rounded-lg px-3 text-[10px] outline-none">
                                    </div>
                                    <div class="grid grid-cols-2 gap-3 mb-3">
                                        <input x-model="personalInfo.gender" type="text" placeholder="Gender" class="w-full h-9 bg-muted/20 border border-border rounded-lg px-3 text-[10px] outline-none">
                                        <input x-model="personalInfo.civil_status" type="text" placeholder="Civil Status" class="w-full h-9 bg-muted/20 border border-border rounded-lg px-3 text-[10px] outline-none">
                                    </div>
                                    <div class="grid grid-cols-2 gap-3 mb-3">
                                        <input x-model="personalInfo.height" type="text" placeholder="Height" class="w-full h-9 bg-muted/20 border border-border rounded-lg px-3 text-[10px] outline-none">
                                        <input x-model="personalInfo.weight" type="text" placeholder="Weight" class="w-full h-9 bg-muted/20 border border-border rounded-lg px-3 text-[10px] outline-none">
                                    </div>
                                    <div class="grid grid-cols-2 gap-3 mb-3">
                                        <input x-model="personalInfo.nationality" type="text" placeholder="Nationality" class="w-full h-9 bg-muted/20 border border-border rounded-lg px-3 text-[10px] outline-none">
                                        <input x-model="personalInfo.religion" type="text" placeholder="Religion" class="w-full h-9 bg-muted/20 border border-border rounded-lg px-3 text-[10px] outline-none">
                                    </div>
                                    <div class="space-y-3">
                                        <input x-model="personalInfo.place_of_birth" type="text" placeholder="Place of Birth" class="w-full h-9 bg-muted/20 border border-border rounded-lg px-3 text-[10px] outline-none">
                                        <input x-model="personalInfo.languages" type="text" placeholder="Languages / Dialects" class="w-full h-9 bg-muted/20 border border-border rounded-lg px-3 text-[10px] outline-none">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Supplementary Context -->
            <div class="bg-card border border-border rounded-[40px] p-8 shadow-sm relative overflow-hidden">
                <div class="absolute inset-0 grid-canvas pointer-events-none opacity-10"></div>
                <div class="flex items-center justify-between mb-6 relative z-10">
                    <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1 italic">Supplementary Context</h3>
                    
                    <div class="flex gap-2">
                        <!-- Draft with AI (Cover Letter Assistant) -->
                        <button x-show="template === 'cover-letter'" 
                                @click="draftCoverLetter"
                                :disabled="isGenerating || !sourceContent || !targetRole"
                                class="flex items-center gap-2 px-3 py-1.5 bg-purple-500/10 text-purple-400 rounded-lg hover:bg-purple-500/20 transition-all disabled:opacity-30">
                            <i data-lucide="sparkles" class="w-3 h-3"></i>
                            <span class="text-[9px] font-black uppercase tracking-widest">Draft with AI</span>
                        </button>

                        <!-- Resume Uploader -->
                        <div x-show="template === 'cv-resume' || template === 'cover-letter'" class="relative">
                            <input type="file" id="resumeUpload" class="hidden" accept=".pdf,.txt,.md,.docx" @change="parseResume">
                            <label for="resumeUpload" class="cursor-pointer flex items-center gap-2 px-3 py-1.5 bg-primary/10 text-primary rounded-lg hover:bg-primary/20 transition-colors">
                                <template x-if="!isParsing">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="upload-cloud" class="w-3 h-3"></i>
                                        <span class="text-[9px] font-black uppercase tracking-widest">Import PDF</span>
                                    </div>
                                </template>
                                <template x-if="isParsing">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="loader-2" class="w-3 h-3 animate-spin"></i>
                                        <span class="text-[9px] font-black uppercase tracking-widest">Parsing...</span>
                                    </div>
                                </template>
                            </label>
                        </div>
                    </div>
                </div>
                <textarea x-model="sourceContent" rows="8" placeholder="Inject raw data or specific session notes..."
                          class="w-full bg-muted/20 border border-border rounded-3xl p-6 text-xs font-medium italic focus:ring-2 focus:ring-primary/20 outline-none relative z-10"></textarea>
                <div x-show="template === 'cover-letter'" class="mt-3 px-2">
                    <p class="text-[9px] text-muted-foreground italic">Tip: Import your CV first, then click 'Draft with AI' to build your story.</p>
                </div>
            </div>

            <!-- Template Category Grid -->
            <div class="space-y-6">
                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1 italic">Architecture Templates</h3>
                @include('components.template-selector')
            </div>
        </div>

        <!-- Executive Display Node -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- AI Tailoring Insight (Dynamic) -->
            <div x-show="tailoringReport" x-transition class="bg-blue-50 border border-blue-200 rounded-2xl p-5 shadow-sm relative overflow-hidden">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600 shrink-0">
                        <i data-lucide="sparkles" class="w-5 h-5"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-sm font-black uppercase tracking-wide text-blue-800 mb-2">Resume Tailoring Active</h4>
                        <div class="prose prose-sm text-xs text-blue-900/80 leading-relaxed" x-html="tailoringReport"></div>
                    </div>
                    <button @click="tailoringReport = ''" class="text-blue-400 hover:text-blue-600">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between px-1">
                <div class="flex items-center gap-4 bg-muted/30 p-1 rounded-2xl">
                    <button @click="activeTab = 'preview'" 
                            :class="activeTab === 'preview' ? 'bg-card text-foreground shadow-sm' : 'text-muted-foreground hover:bg-muted'"
                            class="px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                        Preview
                    </button>
                    <button @click="activeTab = 'html'" 
                            :class="activeTab === 'html' ? 'bg-card text-foreground shadow-sm' : 'text-muted-foreground hover:bg-muted'"
                            class="px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                        HTML Code
                    </button>
                </div>
                <div class="flex items-center gap-4">
                    <button @click="zoomLevel = Math.max(0.3, zoomLevel - 0.05)" class="text-slate-500 hover:text-primary transition-colors"><i data-lucide="minus-circle" class="w-4 h-4"></i></button>
                    <span class="mono text-[10px] font-black text-slate-400" x-text="Math.round(zoomLevel * 100) + '%'"></span>
                    <button @click="zoomLevel = Math.min(1.0, zoomLevel + 0.05)" class="text-slate-500 hover:text-primary transition-colors"><i data-lucide="plus-circle" class="w-4 h-4"></i></button>
                </div>
            </div>

            <div class="bg-card border border-border rounded-[40px] min-h-[900px] shadow-sm relative flex flex-col items-center p-10 overflow-hidden">
                <!-- Grid Canvas Pattern -->
                <div class="absolute inset-0 grid-canvas pointer-events-none opacity-20"></div>

                <!-- Loading Overlay -->
                <div x-show="isGenerating || isLoadingPreview" x-cloak class="absolute inset-0 bg-black/60 backdrop-blur-md z-50 flex items-center justify-center rounded-[40px]">
                    <div class="text-center space-y-6">
                        <div class="relative inline-block">
                            <div class="w-20 h-20 border-4 border-primary/20 border-t-primary rounded-full animate-spin"></div>
                            <i data-lucide="sparkles" class="w-8 h-8 text-primary absolute inset-0 m-auto animate-pulse"></i>
                        </div>
                        <p class="mono text-[10px] font-black uppercase tracking-[0.4em] text-primary animate-pulse">Architecting Protocol...</p>
                    </div>
                </div>

                <!-- Preview Tab Content -->
                <div x-show="activeTab === 'preview'" class="w-full flex flex-col items-center">
                    <!-- Document Frame -->
                    <div x-show="htmlPreview" 
                         class="shadow-2xl bg-white ring-1 ring-slate-900/10 overflow-hidden origin-top transition-transform duration-300" 
                         :style="`width: 210mm; min-height: 297mm; transform: scale(${zoomLevel});`" x-transition>
                        <iframe :srcdoc="htmlPreview" class="w-full border-none" style="height: 297mm;" sandbox="allow-same-origin allow-scripts"></iframe>
                    </div>

                    <!-- Empty State -->
                    <div x-show="!htmlPreview && !isGenerating && !isLoadingPreview" class="flex-1 min-h-[600px] flex flex-col items-center justify-center text-center opacity-30 italic">
                        <i data-lucide="file-text" class="w-16 h-16 mb-6"></i>
                        <p class="text-sm font-bold uppercase tracking-widest">Protocol Registry Awaiting Build</p>
                    </div>
                </div>

                <!-- HTML Tab Content -->
                <div x-show="activeTab === 'html'" class="w-full h-full flex flex-col pt-10 px-4 relative z-10">
                    <div x-show="htmlPreview" class="bg-slate-950 rounded-3xl p-8 overflow-auto border border-slate-800 shadow-2xl max-h-[800px] custom-scrollbar">
                        <pre class="mono text-[11px] text-emerald-400 whitespace-pre-wrap"><code x-text="htmlPreview"></code></pre>
                    </div>
                    <div x-show="!htmlPreview" class="flex-1 min-h-[600px] flex flex-col items-center justify-center text-center opacity-30 italic">
                        <i data-lucide="code" class="w-16 h-16 mb-6"></i>
                        <p class="text-sm font-bold uppercase tracking-widest">No Source Code Available</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection