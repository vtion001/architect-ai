/**
 * Document Builder Preview Manager
 * 
 * Handles preview state, generation progress, and document polling.
 * Optimized with debouncing to prevent excessive API calls.
 */

import { debounce } from './api-client.js';

export function createPreviewManager(apiClient) {
    let pollInterval = null;
    let pollTimeout = null;

    return {
        // UI State
        activeTab: 'preview',
        zoomLevel: 0.45,
        showVariantModal: false,
        selectedCategory: null,

        // Loading States
        isLoadingPreview: false,
        isGenerating: false,
        isParsing: false,
        isUploadingPhoto: false,

        // Generation Progress
        generateStage: '',
        generateProgress: 0,
        pendingDocumentId: null,

        // Content
        htmlPreview: '',
        tailoringReport: '',

        // Stage Labels
        get stageTitle() {
            const titles = {
                'initializing': 'Initializing Build Protocol',
                'analyzing': 'Analyzing Content & Context',
                'generating': 'Generating Document',
                'rendering': 'Rendering Preview',
                'complete': 'Build Complete!'
            };
            return titles[this.generateStage] || 'Loading Preview...';
        },

        get stageShortLabel() {
            const labels = {
                'initializing': 'Initializing...',
                'analyzing': 'Analyzing...',
                'generating': 'Generating...',
                'rendering': 'Rendering...',
                'complete': 'Complete!'
            };
            return labels[this.generateStage] || 'Processing...';
        },

        /**
         * Fetch preview HTML (debounced internally via API client abort)
         */
        async fetchPreview(payload) {
            this.isLoadingPreview = true;

            try {
                const data = await apiClient.fetchPreview(payload);
                this.htmlPreview = data.html;
            } catch (error) {
                // Ignore abort errors (expected when a new request supersedes)
                if (error.name !== 'AbortError') {
                    console.error('Preview fetch error:', error);
                }
            } finally {
                this.isLoadingPreview = false;
            }
        },

        /**
         * Create a debounced version of fetchPreview for use with watchers
         */
        createDebouncedFetchPreview(delay = 300) {
            return debounce((payload) => this.fetchPreview(payload), delay);
        },

        /**
         * Start document generation
         */
        async generate(payload, onHtmlReady) {
            this.isGenerating = true;
            this.generateStage = 'initializing';
            this.generateProgress = 5;

            // Progress animation
            const progressInterval = setInterval(() => {
                if (this.generateProgress < 90) {
                    this.generateProgress += Math.random() * 8;
                }

                // Update stage based on progress
                if (this.generateProgress > 20 && this.generateStage === 'initializing') {
                    this.generateStage = 'analyzing';
                } else if (this.generateProgress > 50 && this.generateStage === 'analyzing') {
                    this.generateStage = 'generating';
                } else if (this.generateProgress > 80 && this.generateStage === 'generating') {
                    this.generateStage = 'rendering';
                }
            }, 500);

            try {
                const data = await apiClient.generate(payload);

                clearInterval(progressInterval);
                this.generateProgress = 100;
                this.generateStage = 'complete';

                if (data.status === 'processing' && data.document_id) {
                    this.startPolling(data.document_id, onHtmlReady);
                } else if (data.html) {
                    this.processGeneratedHtml(data.html, onHtmlReady);
                }
            } catch (error) {
                clearInterval(progressInterval);
                this.resetGenerationState();
                throw error;
            }
        },

        /**
         * Poll document status until complete
         */
        startPolling(documentId, onHtmlReady) {
            this.pendingDocumentId = documentId;

            pollInterval = setInterval(async () => {
                try {
                    const doc = await apiClient.getDocumentStatus(documentId);

                    if (doc.status === 'processing') {
                        this.generateStage = 'generating';
                        if (this.generateProgress < 85) {
                            this.generateProgress += 2;
                        }
                    } else if (doc.status === 'completed') {
                        this.stopPolling();
                        this.generateProgress = 100;
                        this.generateStage = 'complete';
                        this.processGeneratedHtml(doc.content, onHtmlReady);
                    } else if (doc.status === 'failed') {
                        this.stopPolling();
                        this.resetGenerationState();
                        throw new Error(doc.metadata?.error || 'Document generation failed');
                    }
                } catch (error) {
                    console.error('Polling error:', error);
                }
            }, 3000);

            // Timeout after 5 minutes
            pollTimeout = setTimeout(() => {
                if (this.isGenerating && this.pendingDocumentId === documentId) {
                    this.stopPolling();
                    this.resetGenerationState();
                    alert('Generation timed out.');
                }
            }, 5 * 60 * 1000);
        },

        /**
         * Stop polling
         */
        stopPolling() {
            if (pollInterval) {
                clearInterval(pollInterval);
                pollInterval = null;
            }
            if (pollTimeout) {
                clearTimeout(pollTimeout);
                pollTimeout = null;
            }
            this.pendingDocumentId = null;
        },

        /**
         * Process generated HTML and extract tailoring report
         */
        processGeneratedHtml(html, onHtmlReady) {
            let finalHtml = html;

            // Extract tailoring report if present
            const pattern = /<!-- TAILORING_REPORT_START -->([\s\S]*?)<!-- TAILORING_REPORT_END -->/;
            const match = finalHtml.match(pattern);

            if (match) {
                this.tailoringReport = match[1];
                finalHtml = finalHtml.replace(match[0], '');
            } else {
                this.tailoringReport = '';
            }

            this.htmlPreview = finalHtml;
            this.resetGenerationState();
            this.activeTab = 'preview';

            if (typeof onHtmlReady === 'function') {
                onHtmlReady(finalHtml);
            }
        },

        /**
         * Reset generation state
         */
        resetGenerationState() {
            this.isGenerating = false;
            this.generateStage = '';
            this.generateProgress = 0;
        },

        /**
         * Cleanup on destroy
         */
        destroy() {
            this.stopPolling();
        }
    };
}
