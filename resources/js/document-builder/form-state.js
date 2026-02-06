/**
 * Document Builder Form State
 * 
 * Manages all form-related state (template, variant, sender/recipient info).
 * Separated from preview logic for better maintainability.
 */

export function createFormState(initialData) {
    const { categories, brands, selectedResearch, user } = initialData;

    return {
        // Template Selection
        categories,
        brands,
        template: 'executive-summary',
        templateVariant: 'exec-corporate',
        selectedBrandId: '',

        // Sender Identity
        senderName: user?.name || '',
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
            clientAddress: '', clientCity: '',
            clientCountry: 'United States',
            clientEmail: '', clientTaxId: '',
            startDate: new Date().toISOString().split('T')[0],
            duration: '12 months',
            providerBusiness: '', providerAddress: '', providerTaxId: ''
        },

        // Content
        analysisType: 'Comparative Analysis',
        prompt: selectedResearch?.title || '',
        sourceContent: '',
        researchTopic: selectedResearch?.title || '',

        // Computed Getters
        get selectedCategoryData() {
            return this.categories.find(c => c.id === this.template);
        },

        get selectedVariantData() {
            return this.selectedCategoryData?.variants.find(v => v.id === this.templateVariant) ?? null;
        },

        get currentBrandColor() {
            if (!this.selectedBrandId) return '#00F2FF';
            const brand = this.brands.find(b => b.id === this.selectedBrandId);
            return brand?.colors?.primary || '#00F2FF';
        },

        get availableObjectives() {
            const mapping = {
                'proposal': ['Project Proposal', 'Sales Pitch', 'Grant Application', 'Partnership Offer'],
                'contract': ['Service Agreement', 'Non-Disclosure Agreement', 'Employment Contract', 'Vendor Contract'],
                'cover-letter': ['Job Application', 'Networking Letter', 'Follow-Up', 'Prospecting Letter']
            };
            return mapping[this.template] || ['Comparative Analysis', 'Growth Strategy', 'Financial Audit', 'SWOT Matrix'];
        },

        /**
         * Build preview request payload
         */
        getPreviewPayload() {
            return {
                template: this.template,
                variant: this.templateVariant,
                brand_id: this.selectedBrandId,
                contractDetails: this.contractDetails,
                recipientName: this.recipientName,
                recipientTitle: this.recipientTitle,
                senderName: this.senderName,
                senderTitle: this.senderTitle,
                companyAddress: this.companyAddress,
                profilePhotoUrl: this.profilePhotoUrl,
                targetRole: this.targetRole,
                email: this.email,
                phone: this.phone,
                location: this.location,
                website: this.website,
                personalInfo: this.personalInfo
            };
        },

        /**
         * Build generation request payload
         */
        getGeneratePayload() {
            return {
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
            };
        },

        /**
         * Update template and auto-select first variant
         */
        setTemplate(templateId) {
            this.template = templateId;
            const category = this.categories.find(c => c.id === templateId);
            if (category?.variants.length > 0) {
                this.templateVariant = category.variants[0].id;
            }
            this.analysisType = this.availableObjectives[0];
        }
    };
}
