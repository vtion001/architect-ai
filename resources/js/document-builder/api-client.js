/**
 * Document Builder API Client
 * 
 * Centralized API communication layer with built-in debouncing.
 */

export function createApiClient(csrfToken, routes) {
    let previewAbortController = null;

    const headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken
    };

    /**
     * Generic fetch wrapper with error handling
     */
    async function request(url, options = {}) {
        try {
            const response = await fetch(url, {
                headers,
                ...options
            });

            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Server returned HTML instead of JSON');
            }

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || `HTTP ${response.status}`);
            }

            return data;
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    }

    return {
        /**
         * Fetch preview HTML with abort support (cancels previous request)
         */
        async fetchPreview(payload) {
            // Cancel any in-flight preview request
            if (previewAbortController) {
                previewAbortController.abort();
            }
            previewAbortController = new AbortController();

            return request(routes.preview, {
                method: 'POST',
                body: JSON.stringify(payload),
                signal: previewAbortController.signal
            });
        },

        /**
         * Generate document
         */
        async generate(payload) {
            return request(routes.generate, {
                method: 'POST',
                body: JSON.stringify(payload)
            });
        },

        /**
         * Poll document status
         */
        async getDocumentStatus(documentId) {
            return request(`/documents/${documentId}`, {
                method: 'GET',
                headers: {
                    ...headers,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
        },

        /**
         * Upload file (FormData)
         */
        async uploadFile(url, formData) {
            const response = await fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: formData
            });
            return response.json();
        },

        /**
         * Save to knowledge base
         */
        async saveToKnowledgeBase(payload) {
            return request(routes.knowledgeBase, {
                method: 'POST',
                body: JSON.stringify(payload)
            });
        },

        /**
         * Draft cover letter
         */
        async draftCoverLetter(targetRole, sourceContent) {
            return request(routes.draftCoverLetter, {
                method: 'POST',
                body: JSON.stringify({ target_role: targetRole, source_content: sourceContent })
            });
        }
    };
}

/**
 * Debounce utility
 */
export function debounce(fn, delay = 300) {
    let timeoutId;
    return function (...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn.apply(this, args), delay);
    };
}
