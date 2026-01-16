/**
 * Batch Manager Alpine.js Component
 * 
 * Handles batch-level operations for content viewer:
 * - Copy all posts
 * - Delete entire batch
 * - Toast notifications
 */

/**
 * Factory function to create the batch manager component
 * @param {Object} config - Configuration with routes, csrfToken, postsData
 * @returns {Object} - Alpine.js component data object
 */
export function createBatchManagerComponent(config) {
    return {
        showDeleteModal: false,
        isDeleting: false,
        showCopyAllToast: false,

        /**
         * Copy all posts content to clipboard
         */
        copyAllPosts() {
            const allPosts = config.postsData.map(p => p.raw).join('\n\n---\n\n');
            navigator.clipboard.writeText(allPosts).then(() => {
                this.showCopyAllToast = true;
                setTimeout(() => { this.showCopyAllToast = false; }, 2500);
            }).catch(err => {
                // Fallback for older browsers
                const textarea = document.createElement('textarea');
                textarea.value = allPosts;
                textarea.style.position = 'fixed';
                textarea.style.opacity = '0';
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                this.showCopyAllToast = true;
                setTimeout(() => { this.showCopyAllToast = false; }, 2500);
            });
        },

        /**
         * Delete the entire batch
         */
        deleteBatch() {
            this.isDeleting = true;
            fetch(config.deleteUrl, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': config.csrfToken,
                    'Accept': 'application/json'
                }
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = config.redirectUrl;
                    } else {
                        alert('Delete failed: ' + (data.message || 'Unknown error'));
                        this.isDeleting = false;
                        this.showDeleteModal = false;
                    }
                })
                .catch(e => {
                    console.error(e);
                    alert('An error occurred during deletion.');
                    this.isDeleting = false;
                });
        }
    };
}
