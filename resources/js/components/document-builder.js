/**
 * Document Builder Alpine.js Component
 * 
 * Extracted from document-builder.blade.php for modularity.
 * This file contains all document generation logic and form handling.
 */

/**
 * Factory function to create the document builder component configuration
 * @param {Object} config - Configuration object containing routes, csrf token, and initial data
 * @returns {Object} - Alpine.js component data object
 */
export function createDocumentBuilderComponent(config) {
    return {
        // Template & Brand Selection
        categories: config.templateCategories || [],
        brands: config.brands || [],
        selectedBrandId: '',
        template: 'executive-summary',
        templateVariant: 'exec-corporate',

        // Sender/Recipient Info
        senderName: config.senderName || '',
        senderTitle: '',
        recipientName: '',
        recipientTitle: '',
        companyAddress: '',

        // CV/Cover Letter Fields
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

        // Proposal/Contract Financials
        financials: {
            totalInvestment: '1000',
            currency: 'USD',
            timeline: '4-5 weeks',
            paymentMilestones: [
                { name: 'Project Kickoff', percentage: 50 },
                { name: 'Development Complete', percentage: 30 },
                { name: 'Launch & Final Handoff', percentage: 20 }
            ]
        },

        // Contract Details
        contractDetails: {
            clientAddress: '',
            clientCity: '',
            clientCountry: 'United States',
            clientEmail: '',
            clientTaxId: '',
            startDate: new Date().toISOString().split('T')[0],
            duration: '12 months',
            providerBusiness: '',
            providerAddress: '',
            providerTaxId: ''
        },

        // Loading States
        isUploadingPhoto: false,
        isGenerating: false,
        isParsing: false,
        isLoadingPreview: false,
        generateStage: '',
        generateProgress: 0,
        pendingDocumentId: null,

        // Analysis & Content
        analysisType: 'Comparative Analysis',
        prompt: config.selectedResearchTitle || '',
        sourceContent: '',
        researchTopic: config.selectedResearchTitle || '',

        // UI State
        activeTab: 'preview',
        htmlPreview: '',
        tailoringReport: '',
        zoomLevel: 0.45,
        showVariantModal: false,
        selectedCategory: null,

        // Computed Properties
        get selectedCategoryData() {
            return this.categories.find(c => c.id === this.template);
        },

        get selectedVariantData() {
            if (!this.selectedCategoryData) return null;
            return this.selectedCategoryData.variants.find(v => v.id === this.templateVariant);
        },

        get availableObjectives() {
            if (this.template === 'proposal') return ['Project Proposal', 'Sales Pitch', 'Grant Application', 'Partnership Offer'];
            if (this.template === 'contract') return ['Service Agreement', 'Non-Disclosure Agreement', 'Employment Contract', 'Vendor Contract'];
            if (this.template === 'cover-letter') return ['Job Application', 'Networking Letter', 'Follow-Up', 'Prospecting Letter'];
            return ['Comparative Analysis', 'Growth Strategy', 'Financial Audit', 'SWOT Matrix'];
        },

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

        /**
         * Upload profile photo (CV only)
         */
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
            }).then(res => res.json()).then(data => {
                if (data.success) this.profilePhotoUrl = data.url;
                else alert('Upload failed');
            }).finally(() => { this.isUploadingPhoto = false; });
        },

        /**
         * Parse uploaded resume
         */
        parseResume(event) {
            const file = event.target.files[0];
            if (!file) return;
            this.isParsing = true;
            const formData = new FormData();
            formData.append('resume', file);
            fetch(config.routes.parseResume, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': config.csrfToken, 'Accept': 'application/json' },
                body: formData
            }).then(async res => {
                const data = await res.json();
                if (!res.ok) {
                    if (res.status === 422 && data.errors) {
                        const errorMessages = Object.values(data.errors).flat().join('\n');
                        throw new Error(errorMessages || data.message || 'Validation failed');
                    }
                    throw new Error(data.message || `Server error: ${res.status}`);
                }
                return data;
            }).then(data => {
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
                            const stringifiedInfo = {};
                            for (const [key, value] of Object.entries(ex.personal_info)) {
                                stringifiedInfo[key] = value === null || value === undefined ? '' : String(value);
                            }
                            this.personalInfo = { ...this.personalInfo, ...stringifiedInfo };
                        }
                        alert('Resume parsed and candidate identity autofilled!');
                    }
                } else {
                    throw new Error(data.message || 'Failed to parse resume.');
                }
            }).catch(err => {
                console.error(err);
                alert('Error: ' + err.message);
            }).finally(() => {
                this.isParsing = false;
                event.target.value = '';
            });
        },

        /**
         * Draft cover letter with AI
         */
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
            }).then(res => res.json()).then(data => {
                if (data.success) {
                    this.sourceContent = data.draft;
                    alert('Cover letter drafted!');
                } else alert(data.message || 'Drafting failed.');
            }).catch(err => {
                console.error(err);
                alert('Error drafting cover letter.');
            }).finally(() => { this.isParsing = false; });
        },

        /**
         * Fetch template preview
         */
        fetchPreview() {
            this.isLoadingPreview = true;
            const payload = {
                template: this.template,
                variant: this.templateVariant,
                brand_id: this.selectedBrandId,
                contractDetails: this.contractDetails,
                recipientName: this.recipientName,
                recipientTitle: this.recipientTitle
            };
            fetch(config.routes.preview, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': config.csrfToken },
                body: JSON.stringify(payload)
            }).then(response => response.json()).then(data => {
                this.htmlPreview = data.html;
                this.isLoadingPreview = false;
            }).catch(error => {
                console.error('Preview error:', error);
                this.isLoadingPreview = false;
            });
        },

        /**
         * Generate the document
         */
        generateReport() {
            this.isGenerating = true;
            this.generateStage = 'initializing';
            this.generateProgress = 5;

            const progressInterval = setInterval(() => {
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
            }).then(async response => {
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Server returned HTML instead of JSON.');
                }
                const data = await response.json();
                if (!response.ok || !data.success) throw new Error(data.message || 'Generation failed.');
                return data;
            }).then(data => {
                clearInterval(progressInterval);
                this.generateProgress = 100;
                this.generateStage = 'complete';
                if (data.status === 'processing' && data.document_id) this.pollDocumentStatus(data.document_id);
                else if (data.html) this.processGeneratedHtml(data.html);
            }).catch(error => {
                clearInterval(progressInterval);
                console.error('Generation Error:', error);
                alert('Error: ' + error.message);
                this.isGenerating = false;
                this.generateStage = '';
                this.generateProgress = 0;
            });
        },

        /**
         * Poll document generation status
         */
        pollDocumentStatus(id) {
            this.pendingDocumentId = id;
            const poll = setInterval(() => {
                fetch(`/documents/${id}`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(res => res.json()).then(doc => {
                        const status = doc.status;
                        if (status === 'processing') {
                            this.generateStage = 'generating';
                            if (this.generateProgress < 85) this.generateProgress += 2;
                        }
                        if (status === 'completed') {
                            clearInterval(poll);
                            this.generateProgress = 100;
                            this.generateStage = 'complete';
                            this.processGeneratedHtml(doc.content);
                            this.pendingDocumentId = null;
                        } else if (status === 'failed') {
                            clearInterval(poll);
                            this.isGenerating = false;
                            this.generateStage = '';
                            this.generateProgress = 0;
                            this.pendingDocumentId = null;
                            alert('Report generation failed: ' + (doc.metadata?.error || 'Unknown error'));
                        }
                    }).catch(err => { console.error('Polling error:', err); });
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

        /**
         * Process generated HTML
         */
        processGeneratedHtml(html) {
            let finalHtml = html;
            const splitPattern = /<!-- TAILORING_REPORT_START -->([\s\S]*?)<!-- TAILORING_REPORT_END -->/;
            const match = finalHtml.match(splitPattern);
            if (match) {
                this.tailoringReport = match[1];
                finalHtml = finalHtml.replace(match[0], '');
            } else this.tailoringReport = '';
            this.htmlPreview = finalHtml;
            this.isGenerating = false;
            this.generateStage = '';
            this.generateProgress = 0;
            this.activeTab = 'preview';
        },

        /**
         * Save to knowledge base
         */
        saveToKb() {
            if (!this.htmlPreview) return;
            this.isGenerating = true;
            fetch(config.routes.saveToKb, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': config.csrfToken },
                body: JSON.stringify({
                    title: (this.researchTopic || 'Generated Document') + ' (Architected)',
                    type: 'text',
                    content: this.htmlPreview,
                    category: 'Documents'
                })
            }).then(res => res.json()).then(data => {
                if (data.success) alert('Document indexed.');
                this.isGenerating = false;
            });
        },

        /**
         * Initialize component
         */
        init() {
            this.fetchPreview();
            this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
            this.$watch('template', () => {
                if (this.selectedCategoryData && this.selectedCategoryData.variants.length > 0) {
                    this.templateVariant = this.selectedCategoryData.variants[0].id;
                }
                this.analysisType = this.availableObjectives[0];
                this.fetchPreview();
                this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
            });
            this.$watch('templateVariant', () => {
                this.fetchPreview();
                this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
            });
            this.$watch('selectedBrandId', () => { this.fetchPreview(); });
            this.$watch('showVariantModal', (value) => {
                if (value) this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
            });
        }
    };
}

// Auto-register with Alpine if available globally
if (typeof window !== 'undefined' && window.Alpine) {
    document.addEventListener('alpine:init', () => {
        // Component will be registered by inline script with config
    });
}
