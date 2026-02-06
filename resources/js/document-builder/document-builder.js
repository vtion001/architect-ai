/**
 * Document Builder - Main Entry Point
 * 
 * Combines form state, preview manager, and API client into a single Alpine component.
 * This modular architecture allows for easier testing and maintenance.
 * 
 * Usage in Blade:
 * <div x-data="documentBuilder(config)">...</div>
 */

import { createApiClient, debounce } from './api-client.js';
import { createFormState } from './form-state.js';
import { createPreviewManager } from './preview-manager.js';

/**
 * Create Document Builder Alpine component
 * 
 * @param {Object} config Configuration object containing:
 *   - categories: Template categories array
 *   - brands: Available brands array
 *   - selectedResearch: Pre-selected research object
 *   - user: Current user
 *   - routes: API route URLs
 *   - csrfToken: CSRF token
 */
export function documentBuilder(config) {
    const { categories, brands, selectedResearch, user, routes, csrfToken } = config;

    // Initialize modules
    const apiClient = createApiClient(csrfToken, routes);
    const formState = createFormState({ categories, brands, selectedResearch, user });
    const previewManager = createPreviewManager(apiClient);

    // Create debounced preview fetch
    const debouncedFetchPreview = debounce(() => {
        previewManager.fetchPreview(formState.getPreviewPayload());
    }, 300);

    return {
        // Spread in form state properties
        ...formState,

        // Spread in preview manager properties
        ...previewManager,

        // File handling state
        isUploadingPhoto: false,
        isParsing: false,

        /**
         * Initialize component
         */
        init() {
            // Initial preview fetch
            previewManager.fetchPreview(formState.getPreviewPayload());

            // Reinit Lucide icons after DOM updates
            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });

            // Watch template changes
            this.$watch('template', (newTemplate) => {
                formState.setTemplate(newTemplate);
                debouncedFetchPreview();
                this.$nextTick(() => {
                    if (window.lucide) window.lucide.createIcons();
                });
            });

            // Watch variant changes
            this.$watch('templateVariant', () => {
                debouncedFetchPreview();
                this.$nextTick(() => {
                    if (window.lucide) window.lucide.createIcons();
                });
            });

            // Watch brand changes - debounced to prevent spam
            this.$watch('selectedBrandId', () => {
                debouncedFetchPreview();
            });

            // Watch variant modal for icon refresh
            this.$watch('showVariantModal', (isOpen) => {
                if (isOpen) {
                    this.$nextTick(() => {
                        if (window.lucide) window.lucide.createIcons();
                    });
                }
            });
        },

        /**
         * Upload profile photo
         */
        async uploadPhoto(event) {
            const file = event.target.files[0];
            if (!file) return;

            this.isUploadingPhoto = true;
            const formData = new FormData();
            formData.append('photo', file);

            try {
                const data = await apiClient.uploadFile(routes.uploadPhoto, formData);
                if (data.success) {
                    this.profilePhotoUrl = data.url;
                } else {
                    alert('Upload failed');
                }
            } catch (error) {
                console.error('Photo upload error:', error);
                alert('Upload error');
            } finally {
                this.isUploadingPhoto = false;
            }
        },

        /**
         * Parse resume and auto-fill fields
         */
        async parseResume(event) {
            const file = event.target.files[0];
            if (!file) return;

            this.isParsing = true;
            const formData = new FormData();
            formData.append('resume', file);

            try {
                const data = await apiClient.uploadFile(routes.parseResume, formData);

                if (data.success) {
                    const parsed = this.normalizeResumeParse(data);
                    this.applyResumeAutofill(parsed);
                    alert('Resume parsed and candidate identity autofilled!');
                } else {
                    alert(data.message || 'Failed to parse resume.');
                }
            } catch (error) {
                console.error('Resume parse error:', error);
                alert('Error parsing document.');
            } finally {
                this.isParsing = false;
                event.target.value = '';
            }
        },

        normalizeResumeParse(data) {
            const extracted = data?.extracted_data || {};
            const personalInfo = extracted.personal_info || {};

            const normalizedPersonalInfo = {};
            for (const [k, v] of Object.entries(personalInfo)) {
                normalizedPersonalInfo[k] = v == null ? '' : String(v);
            }

            return {
                sourceContent: data?.text || '',
                fullName: extracted.full_name || '',
                title: extracted.title || '',
                email: extracted.email || '',
                phone: extracted.phone || '',
                location: extracted.location || '',
                website: extracted.website || '',
                targetRole: extracted.target_role || '',
                jobDescription: extracted.job_description || '',
                personalInfo: normalizedPersonalInfo
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

        /**
         * Draft cover letter using AI
         */
        async draftCoverLetter() {
            if (!this.sourceContent || !this.targetRole) {
                alert('Please import your CV and paste a Target Role first.');
                return;
            }

            this.isParsing = true;

            try {
                const data = await apiClient.draftCoverLetter(this.targetRole, this.sourceContent);

                if (data.success) {
                    this.sourceContent = data.draft;
                    alert('Cover letter drafted!');
                } else {
                    alert(data.message || 'Drafting failed.');
                }
            } catch (error) {
                console.error('Draft error:', error);
                alert('Error drafting cover letter.');
            } finally {
                this.isParsing = false;
            }
        },

        /**
         * Manually trigger preview fetch
         */
        fetchPreview() {
            previewManager.fetchPreview(formState.getPreviewPayload());
        },

        /**
         * Generate document
         */
        async generateReport() {
            try {
                await previewManager.generate(
                    formState.getGeneratePayload(),
                    (html) => {
                        // Callback when HTML is ready
                        this.htmlPreview = html;
                    }
                );
            } catch (error) {
                alert('Error: ' + error.message);
            }
        },

        /**
         * Save generated document to Knowledge Base
         */
        async saveToKb() {
            if (!this.htmlPreview) return;

            this.isGenerating = true;

            try {
                const data = await apiClient.saveToKnowledgeBase({
                    title: (this.researchTopic || 'Generated Document') + ' (Architected)',
                    type: 'text',
                    content: this.htmlPreview,
                    category: 'Documents'
                });

                if (data.success) {
                    alert('Document indexed.');
                }
            } catch (error) {
                console.error('Save error:', error);
            } finally {
                this.isGenerating = false;
            }
        },

        /**
         * Cleanup on destroy
         */
        destroy() {
            previewManager.destroy();
        }
    };
}

// Register globally if Alpine is available
if (typeof window !== 'undefined') {
    window.documentBuilder = documentBuilder;
}

export default documentBuilder;
